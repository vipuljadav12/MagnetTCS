<?php

namespace App\Modules\CustomCommunication\Controllers;

use App\Modules\School\Models\School;
use App\Modules\District\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\Application\Models\Application;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Program\Models\Program;
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo, SubmissionData, SubmissionRecommendation};
use App\Modules\CustomCommunication\Models\CustomCommunication;
use App\Modules\CustomCommunication\Models\CustomCommunicationData;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\CustomCommunication\Export\{CustomCommunicationEmails};
use App\Modules\Program\Models\ProgramEligibility;
use App\Modules\WritingPrompt\Models\WritingPrompt;
use App\StudentData;
use App\Modules\Submissions\Models\{SubmissionsFinalStatus, SubmissionsWaitlistFinalStatus};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Traits\AuditTrail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class CustomCommunicationController extends Controller
{
    use AuditTrail;
    public function index()
    {
        $data = CustomCommunication::where("district_id", Session::get('district_id'))->get();
        return view("CustomCommunication::index", compact('data'));
    }

    public function create()
    {
        $allowed_programs = Auth::user()->programs;
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $programs = Program::where("district_id", Session::get('district_id'))->where('status', 'Y');
        if (!empty($allowed_programs))
            $programs = $programs->whereIn("id", $allowed_programs);
        $programs = $programs->orderBy('name')->get();
        //$programs = Program::where("district_id", Session::get('district_id'))->where('status','Y')->orderBy('name')->get();
        $grades = Submissions::where("district_id", Session::get('district_id'))->orderBy('next_grade')->select(DB::raw("DISTINCT(next_grade)"))->get();
        $submission_status = Submissions::where("district_id", Session::get('district_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(submission_status)"))->get();

        return view("CustomCommunication::create", compact('enrollment', 'programs', 'grades', 'submission_status'));
    }

    public function edit($id)
    {
        $data = CustomCommunication::where("id", $id)->first();
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        //$programs = Program::where("district_id", Session::get('district_id'))->where('status','Y')->orderBy('name')->get();
        $programs = Program::where("district_id", Session::get('district_id'))->where('status', 'Y');
        if (!empty($allowed_programs))
            $programs = $programs->whereIn("id", $allowed_programs);
        $programs = $programs->orderBy('name')->get();
        $grades = Submissions::where("district_id", Session::get('district_id'))->orderBy('next_grade')->select(DB::raw("DISTINCT(next_grade)"))->get();
        $submission_status = Submissions::where("district_id", Session::get('district_id'))->orderBy('submission_status')->select(DB::raw("DISTINCT(submission_status)"))->get();


        return view("CustomCommunication::edit", compact('enrollment', 'programs', 'grades', 'data', 'submission_status'));
    }

    public function store(Request $request)
    {
        //print_r($request->all());exit;
        $validateData = $request->validate([
            'template_name' => 'required|max:255',
            'enrollment_id' => 'required',
            'program' => 'required',
            'grade' => 'required',
            'submission_status' => 'required',
            //'mail_subject' =>'required',
            //'mail_body' =>'required',
            //'letter_subject' =>'required',
            //'letter_body' =>'required'
        ]);
        $req = $request->all();
        $data = array();
        $data['district_id'] = Session::get('district_id');
        $data['template_name'] = $req['template_name'];
        $data['enrollment_id'] = $req['enrollment_id'];
        $data['program'] = $req['program'];
        $data['grade'] = $req['grade'];
        $data['submission_status'] = $req['submission_status'];
        if (isset($request->send_email_now) || isset($request->save_email)) {
            $data['mail_subject'] = $req['mail_subject'];
            $data['mail_body'] = $req['mail_body'];
        }
        if (isset($request->generate_letter_now) || isset($request->save_letter)) {
            $data['letter_body'] = $req['letter_body'];
        }

        $id = CustomCommunication::create($data)->id;
        //$newObj=CustomCommunication::where('id',$id)->first();
        //$this->modelCreate($newObj,"custom_communication");

        if (isset($request->generate_letter_now)) {
            return $this->generate_letter_now($id);
        } elseif (isset($request->send_email_now)) {
            $this->send_email_now($id);
        }

        if (isset($id)) {
            Session::flash("success", "Custom Communication added successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        return redirect('admin/CustomCommunication/edit/' . $id);
    }


    public function update(Request $request, $id)
    {
        //print_r($request->all());exit;
        $validateData = $request->validate([
            'template_name' => 'required|max:255',
            'enrollment_id' => 'required',
            'program' => 'required',
            'grade' => 'required',
            'submission_status' => 'required',
            //'mail_subject' =>'required',
            //'mail_body' =>'required',
            //'letter_subject' =>'required',
            //'letter_body' =>'required'
        ]);
        $req = $request->all();

        $data = array();
        $data['district_id'] = Session::get('district_id');
        $data['template_name'] = $req['template_name'];
        $data['enrollment_id'] = $req['enrollment_id'];
        $data['program'] = $req['program'];
        $data['grade'] = $req['grade'];
        $data['submission_status'] = $req['submission_status'];
        if (isset($request->send_email_now) || isset($request->save_email)) {
            $data['mail_subject'] = $req['mail_subject'];
            $data['mail_body'] = $req['mail_body'];
        }
        if (isset($request->generate_letter_now) || isset($request->save_letter)) {
            $data['letter_body'] = $req['letter_body'];
        }

        $initObj = CustomCommunication::where("id", $id)->first();
        $result = CustomCommunication::where("id", $id)->update($data);
        $newObj = CustomCommunication::where("id", $id)->first();
        //$this->modelChanges($initObj,$newObj,"custom-communication");

        if (isset($request->generate_letter_now)) {
            return $this->generate_letter_now($id);
            Session::flash("success", "Custom Communication generated successfully.");
        } elseif (isset($request->send_email_now)) {
            $this->send_email_now($id);
            Session::flash("success", "Custom Communication emails sent successfully.");
        } else {
            Session::flash("success", "Custom Communication updated successfully.");
        }


        return redirect('admin/CustomCommunication/edit/' . $id);
    }

    public function generate_letter_now($id, $preview = false)
    {
        set_time_limit(0);
        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = DistrictConfiguration::where("name", "last_date_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);


        $cdata = CustomCommunication::where("id", $id)->first();
        $program_id = $cdata->program;
        $enrollment_id = $cdata->enrollment_id;
        $status = $cdata->submission_status;
        $grade = $cdata->grade;
        //        $application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();

        $data = Submissions::where("submissions.district_id", Session::get("district_id"))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where("application.enrollment_id", $enrollment_id);
        if ($program_id != 0) {
            $data->where(function ($q) use ($program_id) {
                $q->where("first_choice_program_id", $program_id)
                    ->orWhere("second_choice_program_id", $program_id)->get();
            });
        }
        if ($grade != 'All') {
            $data->where('next_grade', $grade);
        }
        if ($status != 'All' && $status != "Missing Writing Samples") {
            if ($status == "Offer Accepted and Contract Pending") {
                $data->where('submission_status', "Offered and Accepted");
            } elseif ($status == "Missing Recommendations") {
                $data->whereIn('submission_status', array('Active', 'Pending'));
            } else {
                $data->where('submission_status', $status);
            }
        }
        $final_data = $data->get();
        $student_data = array();
        foreach ($final_data as $key => $value) {
            //$application = Application::where('id', $value->application_id)->first();
            $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();


            $subms = SubmissionsFinalStatus::where("submission_id", $value->id)->first();
            $subms1 = SubmissionsWaitlistFinalStatus::where("submission_id", $value->id)->where(function ($q) {
                $q->where("first_offer_status", "Accepted")->orWhere("second_offer_status", "Accepted")->get();
            })->first();

            if ($status == "Offer Accepted and Contract Pending") {
                if (!empty($subms1)) {
                    if ($subms1->contract_status != "UnSigned") {
                        continue;
                    }
                }
                if (!empty($subms)) {
                    if ($subms->contract_status != "UnSigned") {
                        continue;
                    }
                } else
                    continue;
            } elseif ($status == "Missing Recommendations") {
                $recomm_programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Recommendation Form")->join("program", "program.id", "program_eligibility.program_id")->where("program.enrollment_id", $enrollment_id)->pluck('program_eligibility.program_id')->all();

                if (in_array($value->first_choice_program_id, $recomm_programs) || in_array($value->second_choice_program_id, $recomm_programs)) {
                    $recommendation = SubmissionData::where('submission_id', $value->id)->where('config_name', 'LIKE', 'recommendation%')->pluck('config_value')->toArray();

                    if (isset($recommendation) && !empty($recommendation)) {
                        $submitted_recom = SubmissionRecommendation::where('submission_id', $value->id)->pluck('config_value')->toArray();
                        $missing_recom = array_diff($recommendation, $submitted_recom);

                        if (empty($missing_recom)) {
                            // dd($value->id, $missing_recom, !isset($missing_recom));
                            continue;
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } elseif ($status == "Missing Writing Samples") {
                $writing_prompt_programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Writing Prompt")->pluck('program_id')->all();
                $choice_writing_prompt = [];
                $foption = "";
                if (in_array($value->first_choice_program_id, $writing_prompt_programs)) {
                    $choice_writing_prompt[] = $value->first_choice_program_id;
                    $foption = "first";
                }

                if (in_array($value->second_choice_program_id, $writing_prompt_programs)) {
                    $choice_writing_prompt[] = $value->second_choice_program_id;
                    $foption = "second";
                }

                if (!empty($choice_writing_prompt)) {
                    $writing_prompt_data = WritingPrompt::where('submission_id', $value->id)->whereIn('program_id', $choice_writing_prompt)->first();

                    if (isset($writing_prompt_data) && !empty($writing_prompt_data)) {
                        continue;
                    } else {

                        $tmp['writing_prompt_due_date'] = getDateTimeFormat($application_data1->writing_prompt_due_date);
                        $rsT = DB::table("submission_data")->where("submission_id", $value->id)->where("config_name", "wp_" . $foption . "_choice_link")->first();
                        if (!empty($rsT))
                            $tmp['writing_prompt_link'] = url('/WritingPrompt/') . "/" . $rsT->config_value;
                        else
                            $tmp['writing_prompt_link'] = '';
                    }
                } else {
                    continue;
                }
            }

            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['student_id'] = $value->student_id;
            $tmp['confirmation_no'] = $value->confirmation_no;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
            $tmp['current_grade'] = $value->current_grade;
            $tmp['current_school'] = $value->current_school;
            $tmp['zoned_school'] = $value->zoned_school;
            $tmp['created_at'] = getDateFormat($value->created_at);
            $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
            $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
            $tmp['birth_date'] = getDateFormat($value->birthday);
            $tmp['student_name'] = $value->first_name . " " . $value->last_name;
            $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
            $tmp['parent_email'] = $value->parent_email;
            $tmp['student_id'] = $value->student_id;
            $tmp['submission_date'] = getDateTimeFormat($value->created_at);
            $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
            $tmp['application_url'] = url('/');
            $tmp['signature'] = get_signature('letter_signature');
            $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


            $program_name = "";
            $program_name_with_grade = "";
            $offer_program = "";
            $offer_program_with_grade = "";
            $waitlist_program = "";
            $waitlist_program_with_grade = "";
            $waitlist_program_1 = "";
            $waitlist_program_2 = "";
            $waitlist_program_1_with_grade = "";
            $waitlist_program_2_with_grade = "";

            $tmp['program_name'] = getProgramName($value->first_choice_program_id);
            $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];



            if ($status != "Denied due to Incomplete Records" && $status != "Denied due to Ineligibility") {
                if (!empty($subms)) {
                    if ($subms->first_choice_final_status == "Offered") {
                        $program_name = getProgramName($value->first_choice_program_id);
                        $program_name_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        $offer_program = $program_name;
                        $offer_program_with_grade = $program_name_with_grade;

                        if ($subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->second_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        }
                    } elseif ($subms->second_choice_final_status == "Offered") {
                        $program_name = getProgramName($value->second_choice_program_id);
                        $program_name_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        $offer_program = $program_name;
                        $offer_program_with_grade = $program_name_with_grade;

                        if ($subms->first_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        }
                    } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Waitlisted") {
                        $waitlist_program = getProgramName($value->first_choice_program_id);
                        $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                        $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                        $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                        $waitlist_program_2 = getProgramName($value->first_choice_program_id);
                        $waitlist_program_2_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Pending") {
                        $waitlist_program = getProgramName($value->first_choice_program_id);
                        $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                        $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                        $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                    } elseif ($subms->second_choice_final_status == "Waitlisted") {
                        $waitlist_program = getProgramName($value->second_choice_program_id);
                        $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                        $waitlist_program_2 = getProgramName($value->second_choice_program_id);
                        $waitlist_program_2_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                    }
                }
            }

            if ($program_name != "") {
                $tmp['program_name'] = $program_name;
                $tmp['program_name_with_grade'] = $program_name_with_grade;
            }
            $tmp['offer_program'] = $offer_program;
            $tmp['offer_program_with_grade'] = $offer_program_with_grade;
            $tmp['waitlist_program'] = $waitlist_program;
            $tmp['waitlist_program_with_grade'] = $waitlist_program_with_grade;
            $tmp['waitlist_program_1'] = $waitlist_program_1;
            $tmp['waitlist_program_2'] = $waitlist_program_2;
            $tmp['waitlist_program_1_with_grade'] = $waitlist_program_1_with_grade;
            $tmp['waitlist_program_2_with_grade'] = $waitlist_program_2_with_grade;





            $tmp['school_year'] = $application_data1->school_year;
            $tmp['enrollment_period'] = $tmp['school_year'];
            $t1 = explode("-", $tmp['school_year']);
            $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
            $tmp['next_year'] = date("Y") + 1;

            /* Offer link */
            $tmp['offer_link'] = "";
            $tmp['online_offer_last_date'] = $last_date_online_acceptance;
            $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;

            if (!empty($subms)) {
                $tmp['offer_link'] = url('/Offers/' . $subms->offer_slug);
                $tmp['contract_link'] = url('/Offers/Contract/Fill/' . $subms->offer_slug);
                if ($subms->last_date_online_acceptance != '') {
                    $tmp['online_offer_last_date'] = $subms->last_date_online_acceptance;
                    $tmp['offline_offer_last_date'] = $subms->last_date_offline_acceptance;
                }
            }

            if ($status == "Missing Recommendations") {
                $tmp['recommendation_due_date'] = getDateTimeFormat($application_data1->recommendation_due_date);

                $subjects = config('variables.recommendation_subject');
                foreach ($missing_recom as $mk => $mvalue) {
                    if ($mvalue != '') {
                        $config_value = explode('.', $mvalue);
                        $program_id = $config_value[count($config_value) - 2];
                        $missingSub = $config_value[0];

                        $tmp['program_name'] = getProgramName($program_id);
                        $tmp['program_name_with_grade'] = getProgramName($program_id) . " - Grade " . $tmp['next_grade'];


                        $link = url('/recommendation/') . "/" . $mvalue;
                        $tmp['recommendation_teacher_title'] = $subjects[$missingSub];
                        $tmp['recommendation_teacher_link'] = "<a href='" . $link . "'>" . $link . "</a>";
                        $tmp['recommendation_links_section'] = "";

                        $msg = find_replace_string($cdata->letter_body, $tmp);
                        $msg = str_replace("{", "", $msg);
                        $msg = str_replace("}", "", $msg);
                        $tmp['letter_body'] = $msg;
                        $student_data[] = $tmp;
                    }
                }
            } else {
                $msg = find_replace_string($cdata->letter_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['letter_body'] = $msg;
                $student_data[] = $tmp;
            }
        }

        if ($preview == true) {
            $student_data = array();
            $tmp = array();
            $tmp['id'] = "9999";
            $tmp['student_id'] = "1234567890";
            $tmp['confirmation_no'] = "MAGNET-2122-00000";
            $tmp['name'] = "Johnson William";
            $tmp['grade'] = $tmp['next_grade'] = "8";
            $tmp['current_grade'] = "7";
            $tmp['current_school'] = "MCPSS Elementary";
            $tmp['zoned_school'] = "Zoned School";
            $tmp['created_at'] = getDateFormat(date("Y-m-d H:i:S"));
            $tmp['first_choice'] = "Magnet Program 1";
            $tmp['second_choice'] = "Magnet Program 2";
            $tmp['birth_date'] = getDateFormat(date("Y-m-d"));
            $tmp['student_name'] = "Johnson William";
            $tmp['parent_name'] = "Mark William";
            $tmp['parent_email'] = "mark.william@gmail.com";
            $tmp['student_id'] = "1234567890";
            $tmp['submission_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['transcript_due_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['signature'] = get_signature('letter_signature');
            $tmp['application_url'] = url('/');

            //$msg = strtr($cdata->letter_body,$tmp);
            $msg = $cdata->letter_body;
            // $msg = str_replace("{","",$msg);
            //$msg = str_replace("}","",$msg);
            $tmp['letter_body'] = $msg;
            $student_data[] = $tmp;
        }
        view()->share('student_data', $student_data);
        view()->share("application_data", $application_data);

        $fileName =  "CustomCommunication-" . strtotime(date("Y-m-d H:i:s")) . '.pdf';
        $path = "resources/assets/admin/custom_communication";
        if ($preview) {
            $pdf = Pdf::loadView('CustomCommunication::letterview', ['student_data', 'application_data']);
            $fileName = "preview.pdf";
            $pdf->save($path . '/' . $fileName);
            return response()->file($path . "/" . $fileName);
        } else {
            $pdf = Pdf::loadView('CustomCommunication::letterview', ['student_data', 'application_data']);
            $pdf->save($path . '/' . $fileName);

            $data = array();
            $data['template_id'] = $id;
            $data['total_count'] = count($student_data);
            $data['generated_by'] = Auth::user()->id;
            $data['file_name'] = $fileName;
            $data['type'] = "Letter";
            $data['status'] = $status;
            CustomCommunicationData::create($data);
            return $pdf->download($fileName);
        }
    }


    public function generate_letter_now_individual(Request $request)
    {
        set_time_limit(0);
        $req = $request->all();

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = DistrictConfiguration::where("name", "last_date_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);


        //$application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();
        $id = $req['id'];
        if (isset($request->save)) {
            $data = ['letter_body' => $req['letter_body']];
            Submissions::where("id", $req['id'])->update($data);
            Session::flash("success", "Custom Communication saved successfully.");
            return redirect('admin/Submissions/edit/' . $req['id']);
        }
        if (isset($request->preview)) {
            $student_data = array();
            $tmp = array();
            $tmp['id'] = "9999";
            $tmp['student_id'] = "1234567890";
            $tmp['confirmation_no'] = "MAGNET-2122-00000";
            $tmp['name'] = "Johnson William";
            $tmp['grade'] = $tmp['next_grade'] = "8";
            $tmp['current_grade'] = "7";
            $tmp['current_school'] = "MCPSS Elementary";
            $tmp['zoned_school'] = "Zoned School";
            $tmp['created_at'] = getDateFormat(date("Y-m-d H:i:S"));
            $tmp['first_choice'] = "Magnet Program 1";
            $tmp['second_choice'] = "Magnet Program 2";
            $tmp['birth_date'] = getDateFormat(date("Y-m-d"));
            $tmp['student_name'] = "Johnson William";
            $tmp['parent_name'] = "Mark William";
            $tmp['parent_email'] = "mark.william@gmail.com";
            $tmp['student_id'] = "1234567890";
            $tmp['submission_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['transcript_due_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['signature'] = get_signature('letter_signature');
            $tmp['application_url'] = url('/');

            //$msg = strtr($cdata->letter_body,$tmp);
            $msg = $req['letter_body'];
            // $msg = str_replace("{","",$msg);
            //$msg = str_replace("}","",$msg);
            $tmp['letter_body'] = $msg;
            $student_data[] = $tmp;
        } else {


            $final_data = Submissions::where("submissions.district_id", Session::get("district_id"))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where('submissions.id', $id)->get();

            $data = ['letter_body' => $req['letter_body']];
            Submissions::where("id", $req['id'])->update($data);

            $student_data = array();
            foreach ($final_data as $key => $value) {
                //$application = Application::where('id', $value->application_id)->first();
                $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();

                $subms = SubmissionsFinalStatus::where("submission_id", $value->id)->first();

                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_id'] = $value->student_id;
                $tmp['confirmation_no'] = $value->confirmation_no;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                $tmp['current_grade'] = $value->current_grade;
                $tmp['current_school'] = $value->current_school;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['created_at'] = getDateFormat($value->created_at);
                $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                $tmp['birth_date'] = getDateFormat($value->birthday);
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $tmp['student_id'] = $value->student_id;
                $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                $tmp['application_url'] = url('/');

                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $program_name = "";
                $program_name_with_grade = "";
                $offer_program = "";
                $offer_program_with_grade = "";
                $waitlist_program = "";
                $waitlist_program_with_grade = "";
                $waitlist_program_1 = "";
                $waitlist_program_2 = "";
                $waitlist_program_1_with_grade = "";
                $waitlist_program_2_with_grade = "";

                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];



                if ($value->submission_status != "Denied due to Incomplete Records" && $value->submission_status != "Denied due to Ineligibility") {
                    if (!empty($subms)) {
                        if ($subms->first_choice_final_status == "Offered") {
                            $program_name = getProgramName($value->first_choice_program_id);
                            $program_name_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            $offer_program = $program_name;
                            $offer_program_with_grade = $program_name_with_grade;

                            if ($subms->second_choice_final_status == "Waitlisted") {
                                $waitlist_program = getProgramName($value->second_choice_program_id);
                                $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            }
                        } elseif ($subms->second_choice_final_status == "Offered") {
                            $program_name = getProgramName($value->second_choice_program_id);
                            $program_name_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            $offer_program = $program_name;
                            $offer_program_with_grade = $program_name_with_grade;

                            if ($subms->first_choice_final_status == "Waitlisted") {
                                $waitlist_program = getProgramName($value->first_choice_program_id);
                                $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            }
                        } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_2 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_2_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Pending") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        } elseif ($subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->second_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_2 = getProgramName($value->second_choice_program_id);
                            $waitlist_program_2_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        }
                    }
                }

                if ($program_name != "") {
                    $tmp['program_name'] = $program_name;
                    $tmp['program_name_with_grade'] = $program_name_with_grade;
                }
                $tmp['offer_program'] = $offer_program;
                $tmp['offer_program_with_grade'] = $offer_program_with_grade;
                $tmp['waitlist_program'] = $waitlist_program;
                $tmp['waitlist_program_with_grade'] = $waitlist_program_with_grade;
                $tmp['waitlist_program_1'] = $waitlist_program_1;
                $tmp['waitlist_program_2'] = $waitlist_program_2;
                $tmp['waitlist_program_1_with_grade'] = $waitlist_program_1_with_grade;
                $tmp['waitlist_program_2_with_grade'] = $waitlist_program_2_with_grade;

                /* Offer link */
                $tmp['offer_link'] = "";
                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;

                if (!empty($subms)) {
                    $tmp['offer_link'] = url('/Offers/' . $subms->offer_slug);
                    $tmp['contract_link'] = url('/Offers/Contract/Fill/' . $subms->offer_slug);

                    if ($subms->last_date_online_acceptance != '') {
                        $tmp['online_offer_last_date'] = $subms->last_date_online_acceptance;
                        $tmp['offline_offer_last_date'] = $subms->last_date_offline_acceptance;
                    }
                }


                $tmp['signature'] = get_signature('letter_signature');
                $msg = find_replace_string($req['letter_body'], $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['letter_body'] = $msg;
                $student_data[] = $tmp;
            }
        }
        //echo count($student_data);exit;
        view()->share('student_data', $student_data);
        view()->share("application_data", $application_data);

        $fileName =  "CustomCommunication-" . strtotime(date("Y-m-d H:i:s")) . '.pdf';
        $path = "resources/assets/admin/custom_communication";
        if (isset($request->preview)) {
            $pdf = PDF::loadView('CustomCommunication::letterview', ['student_data', 'application_data']);
            $fileName = "preview.pdf";
            $pdf->save($path . '/' . $fileName);
            return response()->file($path . "/" . $fileName);
        } else {
            $pdf = PDF::loadView('CustomCommunication::letterview', ['student_data', 'application_data']);
            $pdf->save($path . '/' . $fileName);

            $data = array();
            $data['template_id'] = 0;
            $data['submission_id'] = $id;
            $data['total_count'] = 1;
            $data['generated_by'] = Auth::user()->id;
            $data['file_name'] = $fileName;
            $data['type'] = "Letter";
            CustomCommunicationData::create($data);
            return $pdf->download($fileName);
        }
    }

    public function send_email_now($id, $preview = false)
    {
        $cdata = CustomCommunication::where("id", $id)->first();
        $program_id = $cdata->program;
        $enrollment_id = $cdata->enrollment_id;
        $status = $cdata->submission_status;
        $grade = $cdata->grade;

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = DistrictConfiguration::where("name", "last_date_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);


        //$application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
        $application_data = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->select("application.*", "enrollments.school_year")->first();

        $data = Submissions::where("submissions.district_id", Session::get("district_id"))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where("application.enrollment_id", $enrollment_id);
        if ($program_id != 0) {
            $data->where(function ($q) use ($program_id) {
                $q->where("first_choice_program_id", $program_id)
                    ->orWhere("second_choice_program_id", $program_id)->get();
            });
        }
        if ($grade != 'All') {
            $data->where('next_grade', $grade);
        }
        if ($status != 'All' && $status != "Missing Writing Samples") {
            if ($status == "Offer Accepted and Contract Pending") {
                $data->where('submission_status', "Offered and Accepted");
            } elseif ($status == "Missing Recommendations") {
                $data->whereIn('submission_status', array('Active', 'Pending'));
            } else {
                $data->where('submission_status', $status);
            }
        }
        $final_data = $data->get();

        $student_data = array();

        if ($preview == true) {
            $tmp = array();
            $tmp['id'] = "9999";
            $tmp['student_id'] = "1234567890";
            $tmp['confirmation_no'] = "MAGNET-2122-00000";
            $tmp['name'] = "Johnson William";
            $tmp['grade'] = $tmp['next_grade'] = "8";
            $tmp['current_grade'] = "7";
            $tmp['current_school'] = "MCPSS Elementary";
            $tmp['zoned_school'] = "Zoned School";
            $tmp['created_at'] = getDateFormat(date("Y-m-d H:i:S"));
            $tmp['first_choice'] = "Magnet Program 1";
            $tmp['second_choice'] = "Magnet Program 2";
            $tmp['birth_date'] = getDateFormat(date("Y-m-d"));
            $tmp['student_name'] = "Johnson William";
            $tmp['parent_name'] = "Mark William";
            $tmp['parent_email'] = "mark.william@gmail.com";
            $tmp['student_id'] = "1234567890";
            $tmp['submission_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['transcript_due_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['application_url'] = url('/');
            $tmp['signature'] = get_signature('email_signature');


            $msg = find_replace_string($cdata->mail_body, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $msg = $cdata->mail_body;
            $tmp['email_text'] = $msg;
            $tmp['logo'] = getDistrictLogo();


            $msg = find_replace_string($cdata->mail_subject, $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['subject'] = $msg;
            $data = $tmp;

            return view("emails.index", compact('data'));
        } else {
            foreach ($final_data as $key => $value) {
                //$application = Application::where('id', $value->application_id)->first();
                $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();

                $subms = SubmissionsFinalStatus::where("submission_id", $value->id)->first();
                $subms1 = SubmissionsWaitlistFinalStatus::where("submission_id", $value->id)->where(function ($q) {
                    $q->where("first_offer_status", "Accepted")->orWhere("second_offer_status", "Accepted")->get();
                })->first();

                $tmp = array();

                if ($status == "Offer Accepted and Contract Pending") {
                    if (!empty($subms1)) {
                        if ($subms1->contract_status != "UnSigned") {
                            continue;
                        }
                    }
                    if (!empty($subms)) {
                        if ($subms->contract_status != "UnSigned") {
                            continue;
                        }
                    } else
                        continue;
                } elseif ($status == "Missing Recommendations") {
                    $recomm_programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Recommendation Form")->join("program", "program.id", "program_eligibility.program_id")->where("program.enrollment_id", $enrollment_id)->pluck('program_eligibility.program_id')->all();

                    if (in_array($value->first_choice_program_id, $recomm_programs) || in_array($value->second_choice_program_id, $recomm_programs)) {
                        $recommendation = SubmissionData::where('submission_id', $value->id)->where('config_name', 'LIKE', 'recommendation%')->pluck('config_value')->toArray();

                        if (isset($recommendation) && !empty($recommendation)) {
                            $submitted_recom = SubmissionRecommendation::where('submission_id', $value->id)->pluck('config_value')->toArray();
                            $missing_recom = array_diff($recommendation, $submitted_recom);

                            if (empty($missing_recom)) {
                                continue;
                            } else {
                                $tmp['recommendation_due_date'] = getDateTimeFormat($application_data1->recommendation_due_date);
                                $recommendation_links = $subject_emails = [];

                                foreach ($missing_recom as $mk => $mvalue) {
                                    if ($mvalue != '') {
                                        $config_value = explode('.', $mvalue);
                                        $program_id = $config_value[count($config_value) - 2];
                                        $missingSub = $config_value[0];

                                        $recommendation_links[$missingSub] = $mvalue;

                                        $rs_student = StudentData::where("stateID", $value->student_id)->where("field_name", $missingSub . "_teacher_name")->first();
                                        if (!empty($rs_student)) {
                                            $subject_emails[$missingSub . "_name"] = $rs_student->field_value;
                                        }
                                        $rs_student = StudentData::where("stateID", $value->student_id)->where("field_name", $missingSub . "_teacher_email")->first();
                                        if (!empty($rs_student)) {
                                            $subject_emails[$missingSub . "_email"] = $rs_student->field_value;
                                        }
                                    }
                                }
                            }
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                } elseif ($status == "Missing Writing Samples") {
                    $writing_prompt_programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Writing Prompt")->pluck('program_id')->all();
                    $choice_writing_prompt = [];

                    $foption = "";
                    if (in_array($value->first_choice_program_id, $writing_prompt_programs)) {
                        $choice_writing_prompt[] = $value->first_choice_program_id;
                        $foption = "first";
                    }

                    if (in_array($value->second_choice_program_id, $writing_prompt_programs)) {
                        $choice_writing_prompt[] = $value->second_choice_program_id;
                        $foption = "second";
                    }

                    if (!empty($choice_writing_prompt)) {
                        $writing_prompt_data = WritingPrompt::where('submission_id', $value->id)->whereIn('program_id', $choice_writing_prompt)->first();

                        if (isset($writing_prompt_data) && !empty($writing_prompt_data)) {
                            continue;
                        } else {

                            $tmp['writing_prompt_due_date'] = getDateTimeFormat($application_data1->writing_prompt_due_date);
                            $rsT = DB::table("submission_data")->where("submission_id", $value->id)->where("config_name", "wp_" . $foption . "_choice_link")->first();
                            if (!empty($rsT))
                                $tmp['writing_prompt_link'] = url('/WritingPrompt/') . "/" . $rsT->config_value;
                            else
                                $tmp['writing_prompt_link'] = '';
                        }
                    } else {
                        continue;
                    }
                }

                $tmp['id'] = $value->id;
                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;

                $tmp['student_id'] = $value->student_id;
                $tmp['confirmation_no'] = $value->confirmation_no;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                $tmp['current_grade'] = $value->current_grade;
                $tmp['current_school'] = $value->current_school;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['created_at'] = getDateFormat($value->created_at);
                $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                $tmp['birth_date'] = getDateFormat($value->birthday);
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                // $tmp['student_id'] = $value->student_id;
                // $tmp['parent_email'] = $value->parent_email;
                // $tmp['student_id'] = $value->student_id;
                $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                $tmp['application_url'] = url('/');
                $tmp['signature'] = get_signature('email_signature');

                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];

                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                /* Offer link */
                $program_name = "";
                $program_name_with_grade = "";
                $offer_program = "";
                $offer_program_with_grade = "";
                $waitlist_program = "";
                $waitlist_program_with_grade = "";
                $waitlist_program_1 = "";
                $waitlist_program_2 = "";
                $waitlist_program_1_with_grade = "";
                $waitlist_program_2_with_grade = "";

                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];



                if ($status != "Denied due to Incomplete Records" && $status != "Denied due to Ineligibility") {
                    if (!empty($subms)) {
                        if ($subms->first_choice_final_status == "Offered") {
                            $program_name = getProgramName($value->first_choice_program_id);
                            $program_name_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            $offer_program = $program_name;
                            $offer_program_with_grade = $program_name_with_grade;

                            if ($subms->second_choice_final_status == "Waitlisted") {
                                $waitlist_program = getProgramName($value->second_choice_program_id);
                                $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            }
                        } elseif ($subms->second_choice_final_status == "Offered") {
                            $program_name = getProgramName($value->second_choice_program_id);
                            $program_name_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            $offer_program = $program_name;
                            $offer_program_with_grade = $program_name_with_grade;

                            if ($subms->first_choice_final_status == "Waitlisted") {
                                $waitlist_program = getProgramName($value->first_choice_program_id);
                                $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            }
                        } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_2 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_2_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Pending") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        } elseif ($subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->second_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_2 = getProgramName($value->second_choice_program_id);
                            $waitlist_program_2_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        }
                    }
                }

                if ($program_name != "") {
                    $tmp['program_name'] = $program_name;
                    $tmp['program_name_with_grade'] = $program_name_with_grade;
                }
                $tmp['offer_program'] = $offer_program;
                $tmp['offer_program_with_grade'] = $offer_program_with_grade;
                $tmp['waitlist_program'] = $waitlist_program;
                $tmp['waitlist_program_with_grade'] = $waitlist_program_with_grade;
                $tmp['waitlist_program_1'] = $waitlist_program_1;
                $tmp['waitlist_program_2'] = $waitlist_program_2;
                $tmp['waitlist_program_1_with_grade'] = $waitlist_program_1_with_grade;
                $tmp['waitlist_program_2_with_grade'] = $waitlist_program_2_with_grade;

                /* Offer link */
                $tmp['offer_link'] = "";
                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;

                if (!empty($subms)) {
                    $tmp['offer_link'] = url('/Offers/' . $subms->offer_slug);
                    $tmp['contract_link'] = url('/Offers/Contract/Fill/' . $subms->offer_slug);

                    if ($subms->last_date_online_acceptance != '') {
                        $tmp['online_offer_last_date'] = $subms->last_date_online_acceptance;
                        $tmp['offline_offer_last_date'] = $subms->last_date_offline_acceptance;
                    }
                }




                $msg = find_replace_string($cdata->mail_body, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['msg'] = $msg;

                $msg = find_replace_string($cdata->mail_subject, $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['subject'] = $msg;

                $tmp['email'] = $value->parent_email;


                if ($status == "Missing Recommendations") {
                    $student_data[] = $this->send_recommendation_email($tmp, $recommendation_links, $subject_emails);
                } else {
                    $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);
                    sendMail($tmp);
                }
            }
            ob_end_clean();
            ob_start();
            $fileName =  "CustomCommunication-" . strtotime(date("Y-m-d H:i:s")) . ".xlsx";

            $data = array();
            $data['template_id'] = $id;
            $data['total_count'] = count($student_data);
            $data['user_id'] = Auth::user()->id;
            $data['file_name'] = $fileName;
            $data['type'] = 'Email';
            $data['status'] = $status;
            CustomCommunicationData::create($data);


            Excel::store(new CustomCommunicationEmails(collect($student_data)), $fileName, 'custom_application');
        }


        // Excel::download(new SubmissionExport(collect($data_ary)), 'Submissions.xlsx');

    }

    public function send_recommendation_email($email_data, $links, $subject_emails)
    {
        $student_data = [];
        if ($email_data['msg'] != '') {
            $subjects = config('variables.recommendation_subject');

            foreach ($links as $key => $value) {
                $link = url('/recommendation/') . "/" . $value;
                $email_data['msg'] = str_replace("recommendation_teacher_title", $subjects[$key], $email_data['msg']);
                $email_data['msg'] = str_replace("recommendation_teacher_link", "<a href='" . $link . "'>" . $link . "</a>", $email_data['msg']);
                $email_data['msg'] = str_replace("recommendation_links_section", "", $email_data['msg']);

                $teacher_email_address = '';

                if ($email_data['student_id'] != '') {
                    $teacher_email_address = (isset($subject_emails[$key . '_email']) && $subject_emails[$key . '_email'] != '') ? $subject_emails[$key . '_email'] : '';
                }

                if ($teacher_email_address != '') {

                    $email_data['email'] = $teacher_email_address;

                    $student_data[] = array($email_data['id'], $email_data['name'], (isset($subject_emails[$key . '_name']) && $subject_emails[$key . '_name'] != '') ? $subject_emails[$key . '_name'] : $email_data['parent_name'], $email_data['email'], $email_data['grade']);;
                    sendMail($email_data);
                }
            }
        }
        return $student_data;
    }


    public function send_email_now_individual(Request $request)
    {
        $req = $request->all();

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        $rs = DistrictConfiguration::where("name", "last_date_online_acceptance")->select("value")->first();
        $last_date_online_acceptance = getDateTimeFormat($rs->value);

        $rs = DistrictConfiguration::where("name", "last_date_offline_acceptance")->select("value")->first();
        $last_date_offline_acceptance = getDateTimeFormat($rs->value);


        $application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();

        $student_data = array();
        if (isset($request->save)) {
            $data = ['email_body' => $req['mail_body'], 'email_subject' => $req['mail_subject']];
            Submissions::where("id", $req['id'])->update($data);
            Session::flash("success", "Custom Communication saved successfully.");
            return redirect('admin/Submissions/edit/' . $req['id']);
        }

        if (isset($request->preview)) {
            $tmp = array();
            $tmp['id'] = "9999";
            $tmp['student_id'] = "1234567890";
            $tmp['confirmation_no'] = "MAGNET-2122-00000";
            $tmp['name'] = "Johnson William";
            $tmp['grade'] = $tmp['next_grade'] = "8";
            $tmp['current_grade'] = "7";
            $tmp['current_school'] = "MCPSS Elementary";
            $tmp['zoned_school'] = "Zoned School";
            $tmp['created_at'] = getDateFormat(date("Y-m-d H:i:S"));
            $tmp['first_choice'] = "Magnet Program 1";
            $tmp['second_choice'] = "Magnet Program 2";
            $tmp['birth_date'] = getDateFormat(date("Y-m-d"));
            $tmp['student_name'] = "Johnson William";
            $tmp['parent_name'] = "Mark William";
            $tmp['parent_email'] = "mark.william@gmail.com";
            $tmp['student_id'] = "1234567890";
            $tmp['submission_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['transcript_due_date'] = getDateTimeFormat(date("Y-m-d H:i:S"));
            $tmp['application_url'] = url('/');
            $tmp['signature'] = get_signature('email_signature');


            $msg = find_replace_string($req['mail_body'], $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $msg = $req['mail_body'];
            $tmp['email_text'] = $msg;
            $tmp['logo'] = getDistrictLogo();


            $msg = find_replace_string($req['mail_subject'], $tmp);
            $msg = str_replace("{", "", $msg);
            $msg = str_replace("}", "", $msg);
            $tmp['subject'] = $msg;
            $data = $tmp;

            return view("emails.index", compact('data'));
        } else {
            $final_data = Submissions::where("submissions.district_id", Session::get("district_id"))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where("submissions.id", $request['id'])->get();

            $data = ['email_body' => $req['mail_body'], 'email_subject' => $req['mail_subject']];
            Submissions::where("id", $req['id'])->update($data);


            foreach ($final_data as $key => $value) {
                //$application = Application::where('id', $value->application_id)->first();
                $application_data1 = Application::join("enrollments", "enrollments.id", "application.enrollment_id")->where('application.district_id', Session::get('district_id'))->where("application.status", "Y")->where("application.id", $value->application_id)->select("application.*", "enrollments.school_year")->first();

                $subms = SubmissionsFinalStatus::where("submission_id", $value->id)->first();

                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_id'] = $value->student_id;
                $tmp['confirmation_no'] = $value->confirmation_no;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $tmp['next_grade'] = $value->next_grade;
                $tmp['current_grade'] = $value->current_grade;
                $tmp['current_school'] = $value->current_school;
                $tmp['zoned_school'] = $value->zoned_school;
                $tmp['created_at'] = getDateFormat($value->created_at);
                $tmp['first_choice'] = getProgramName($value->first_choice_program_id);
                $tmp['second_choice'] = getProgramName($value->second_choice_program_id);
                $tmp['birth_date'] = getDateFormat($value->birthday);
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $tmp['student_id'] = $value->student_id;
                $tmp['parent_email'] = $value->parent_email;
                $tmp['student_id'] = $value->student_id;
                $tmp['submission_date'] = getDateTimeFormat($value->created_at);
                $tmp['transcript_due_date'] = getDateTimeFormat($application_data1->transcript_due_date);
                $tmp['application_url'] = url('/');
                $tmp['signature'] = get_signature('email_signature');

                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];

                $tmp['parent_address'] = $value->address . "<br>" . $value->city . ", " . $value->state . " " . $value->zip;


                $tmp['school_year'] = $application_data1->school_year;
                $tmp['enrollment_period'] = $tmp['school_year'];
                $t1 = explode("-", $tmp['school_year']);
                $tmp['next_school_year'] = ($t1[0] + 1) . "-" . ($t1[1] + 1);
                $tmp['next_year'] = date("Y") + 1;

                $program_name = "";
                $program_name_with_grade = "";
                $offer_program = "";
                $offer_program_with_grade = "";
                $waitlist_program = "";
                $waitlist_program_with_grade = "";
                $waitlist_program_1 = "";
                $waitlist_program_2 = "";
                $waitlist_program_1_with_grade = "";
                $waitlist_program_2_with_grade = "";

                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['program_name_with_grade'] = getProgramName($value->first_choice_program_id) . " - Grade " . $tmp['next_grade'];



                if ($value->submission_status != "Denied due to Incomplete Records" && $value->submission_status != "Denied due to Ineligibility") {
                    if (!empty($subms)) {
                        if ($subms->first_choice_final_status == "Offered") {
                            $program_name = getProgramName($value->first_choice_program_id);
                            $program_name_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            $offer_program = $program_name;
                            $offer_program_with_grade = $program_name_with_grade;

                            if ($subms->second_choice_final_status == "Waitlisted") {
                                $waitlist_program = getProgramName($value->second_choice_program_id);
                                $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            }
                        } elseif ($subms->second_choice_final_status == "Offered") {
                            $program_name = getProgramName($value->second_choice_program_id);
                            $program_name_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                            $offer_program = $program_name;
                            $offer_program_with_grade = $program_name_with_grade;

                            if ($subms->first_choice_final_status == "Waitlisted") {
                                $waitlist_program = getProgramName($value->first_choice_program_id);
                                $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                            }
                        } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_2 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_2_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        } elseif ($subms->first_choice_final_status == "Waitlisted" && $subms->second_choice_final_status == "Pending") {
                            $waitlist_program = getProgramName($value->first_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_1 = getProgramName($value->first_choice_program_id);
                            $waitlist_program_1_with_grade = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
                        } elseif ($subms->second_choice_final_status == "Waitlisted") {
                            $waitlist_program = getProgramName($value->second_choice_program_id);
                            $waitlist_program_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;

                            $waitlist_program_2 = getProgramName($value->second_choice_program_id);
                            $waitlist_program_2_with_grade = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                        }
                    }
                }

                if ($program_name != "") {
                    $tmp['program_name'] = $program_name;
                    $tmp['program_name_with_grade'] = $program_name_with_grade;
                }
                $tmp['offer_program'] = $offer_program;
                $tmp['offer_program_with_grade'] = $offer_program_with_grade;
                $tmp['waitlist_program'] = $waitlist_program;
                $tmp['waitlist_program_with_grade'] = $waitlist_program_with_grade;
                $tmp['waitlist_program_1'] = $waitlist_program_1;
                $tmp['waitlist_program_2'] = $waitlist_program_2;
                $tmp['waitlist_program_1_with_grade'] = $waitlist_program_1_with_grade;
                $tmp['waitlist_program_2_with_grade'] = $waitlist_program_2_with_grade;

                /* Offer link */
                $tmp['offer_link'] = "";
                $tmp['online_offer_last_date'] = $last_date_online_acceptance;
                $tmp['offline_offer_last_date'] = $last_date_offline_acceptance;

                if (!empty($subms)) {
                    $tmp['offer_link'] = url('/Offers/' . $subms->offer_slug);
                    $tmp['contract_link'] = url('/Offers/Contract/Fill/' . $subms->offer_slug);

                    if ($subms->last_date_online_acceptance != '') {
                        $tmp['online_offer_last_date'] = $subms->last_date_online_acceptance;
                        $tmp['offline_offer_last_date'] = $subms->last_date_offline_acceptance;
                    }
                }



                $msg = find_replace_string($req['mail_body'], $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['msg'] = $msg;

                $msg = find_replace_string($req['mail_subject'], $tmp);
                $msg = str_replace("{", "", $msg);
                $msg = str_replace("}", "", $msg);
                $tmp['subject'] = $msg;

                $tmp['email'] = $value->parent_email;
                $student_data[] = array($value->id, $tmp['name'], $tmp['parent_name'], $tmp['parent_email'], $tmp['grade']);

                $data = array();
                $data['email_body'] = $tmp['msg'];
                $data['email_subject'] = $tmp['subject'];
                $data['email'] = $value->parent_email;
                $data['submission_id'] = $value->id;
                $data['generated_by'] = Auth::user()->id;
                $data['total_count'] = 1;
                CustomCommunicationData::create($data);



                sendMail($tmp);
                Session::flash("success", "Custom Communication email sent successfully.");
                return redirect('admin/Submissions/edit/' . $req['id']);
            }
        }


        //Excel::download(new SubmissionExport(collect($data_ary)), 'Submissions.xlsx');

    }

    public function preview_email_now_individual($id)
    {
        $data1 = CustomCommunicationData::where("id", $id)->first();

        $tmp['email_text'] = $data1->email_body;
        $tmp['logo'] = getDistrictLogo();
        $tmp['subject'] = $data1->subject;
        $data = $tmp;



        return view("emails.index", compact('data'));
    }

    public function fetchEmails(Request $request)
    {
        $program_id = $request->program;
        $enrollment_id = $request->enrollment_id;
        $status = $request->submission_status;
        $grade = $request->grade;

        $data = Submissions::where("submissions.district_id", Session::get("district_id"))->join("application", "application.id", "submissions.application_id")->select("submissions.*")->where("application.enrollment_id", $enrollment_id);
        if ($program_id != 0) {
            $data->where(function ($q) use ($program_id) {
                $q->where("first_choice_program_id", $program_id)
                    ->orWhere("second_choice_program_id", $program_id)->get();
            });
        }

        if ($grade != 'All') {
            $data->where('next_grade', $grade);
        }
        if ($status != 'All' && $status != "Missing Writing Samples") {
            if ($status == "Offer Accepted and Contract Pending") {
                $data->where('submission_status', "Offered and Accepted");
            } elseif ($status == "Missing Recommendations") {
                $data->whereIn('submission_status', array('Active', 'Pending'));
            } else {
                $data->where('submission_status', $status);
            }
        }
        $final_data = $data->get();

        $student_data = array();
        // dd($final_data);
        foreach ($final_data as $key => $value) {
            $subms = SubmissionsFinalStatus::where("submission_id", $value->id)->first();
            $subms1 = SubmissionsWaitlistFinalStatus::where("submission_id", $value->id)->where(function ($q) {
                $q->where("first_offer_status", "Accepted")->orWhere("second_offer_status", "Accepted")->get();
            })->first();

            if ($status == "Offer Accepted and Contract Pending") {
                if (!empty($subms1)) {
                    if ($subms1->contract_status != "UnSigned") {
                        continue;
                    }
                }
                if (!empty($subms)) {
                    if ($subms->contract_status != "UnSigned") {
                        continue;
                    }
                } else
                    continue;
            } elseif ($status == "Missing Recommendations") {
                $recomm_programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Recommendation Form")->join("program", "program.id", "program_eligibility.program_id")->where("program.enrollment_id", $enrollment_id)->pluck('program_eligibility.program_id')->all();

                if (in_array($value->first_choice_program_id, $recomm_programs) || in_array($value->second_choice_program_id, $recomm_programs)) {
                    $recommendation = SubmissionData::where('submission_id', $value->id)->where('config_name', 'LIKE', 'recommendation%')->pluck('config_value')->toArray();

                    if (isset($recommendation) && !empty($recommendation)) {
                        $submitted_recom = SubmissionRecommendation::where('submission_id', $value->id)->pluck('config_value')->toArray();
                        $missing_recom = array_diff($recommendation, $submitted_recom);

                        if (empty($missing_recom)) {
                            // dd($value->id, $missing_recom, !isset($missing_recom));
                            continue;
                        } else {
                            foreach ($missing_recom as $mk => $mvalue) {
                                if ($mvalue != '') {
                                    $config_value = explode('.', $mvalue);
                                    $program_id = $config_value[count($config_value) - 2];
                                    $missingSub = $config_value[0];

                                    $recommendation_links[$missingSub] = $mvalue;

                                    $rs_student = StudentData::where("stateID", $value->student_id)->where("field_name", $missingSub . "_teacher_name")->first();

                                    if (!empty($rs_student)) {
                                        $subject_emails[$missingSub . "_name"] = $rs_student->field_value;
                                    }
                                    $rs_student = StudentData::where("stateID", $value->student_id)->where("field_name", $missingSub . "_teacher_email")->first();
                                    $tmp = array();
                                    $tmp['id'] = $value->id;
                                    $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                                    $tmp['grade'] = $value->next_grade;
                                    if (!empty($rs_student)) {


                                        $tmp['parent_email'] = $rs_student->field_value;
                                        $rs_student = StudentData::where("stateID", $value->student_id)->where("field_name", $missingSub . "_teacher_name")->first();
                                        if (!empty($rs_student)) {
                                            $tmp['parent_name'] = $rs_student->field_value;
                                        } else {
                                            $tmp['parent_name'] = "";
                                        }
                                        $student_data[] = $tmp;
                                    }
                                }
                            }
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } elseif ($status == "Missing Writing Samples") {
                $writing_prompt_programs = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("eligibility_template.name", "Writing Prompt")->pluck('program_id')->all();
                $choice_writing_prompt = [];

                if (in_array($value->first_choice_program_id, $writing_prompt_programs)) {
                    $choice_writing_prompt[] = $value->first_choice_program_id;
                }

                if (in_array($value->second_choice_program_id, $writing_prompt_programs)) {
                    $choice_writing_prompt[] = $value->second_choice_program_id;
                }

                if (!empty($choice_writing_prompt)) {
                    $writing_prompt_data = WritingPrompt::where('submission_id', $value->id)->whereIn('program_id', $choice_writing_prompt)->first();

                    if (isset($writing_prompt_data) && !empty($writing_prompt_data)) {
                        continue;
                    }
                } else {
                    continue;
                }
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $student_data[] = $tmp;
            } else {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['student_name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['parent_name'] = $value->parent_first_name . " " . $value->parent_last_name;
                $tmp['parent_email'] = $value->parent_email;
                $student_data[] = $tmp;
            }
        }
        return json_encode($student_data);
    }


    public function existingData($type, $template_id)
    {
        $data = CustomCommunicationData::where("template_id", $template_id)->orderByDesc("custom_communication_data.created_at")->join("custom_communication", "custom_communication.id", "custom_communication_data.template_id")->join('enrollments', 'enrollments.id', 'custom_communication.enrollment_id')->select('custom_communication_data.*', 'custom_communication.program', 'custom_communication.program', 'custom_communication.enrollment_id', 'custom_communication.grade', 'custom_communication.submission_status', 'custom_communication.template_name', 'enrollments.school_year')->where('type', $type)->limit(10)->get();
        $download_data = array();
        foreach ($data as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['template_name'] = $value->template_name;
            $tmp['program'] = ($value->program != 0 ? getProgramName($value->program) : "All");
            $tmp['grade'] = $value->grade;
            //$tmp['status'] = $value->submission_status;
            $tmp['school_year'] = $value->school_year;
            $tmp['total_count'] = $value->total_count;
            $tmp['file_name'] = $value->file_name;
            $tmp['created_at'] = getDateTimeFormat($value->created_at);
            if ($value->status == '')
                $tmp['status'] = $value->submission_status;
            else
                $tmp['status'] = $value->status;
            $download_data[] = $tmp;
        }
        return view("CustomCommunication::generated", compact("download_data", "template_id", "type"));
    }

    public function downloadFile($id)
    {
        $data = CustomCommunicationData::where("id", $id)->first();
        if (!empty($data)) {
            $file_path = 'resources/assets/admin/custom_communication/' . $data->file_name;

            if ($data->type == "Email") {
                $headers = array(
                    'Content-Type: application/vnd.openxmlformats-officedocument',
                );
            } else {
                $headers = array(
                    'Content-Type: application/pdf',
                );
            }
            return Response::download($file_path, $data->file_name, $headers);
        }
    }

    public function status(Request $request)
    {
        $result = CustomCommunication::where('id', $request->id)->update(['status' => $request->status]);
        if (isset($result)) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }

    public function previewPDF($template_id)
    {
        return $this->generate_letter_now($template_id, true);
    }

    public function previewEmail($template_id)
    {
        return $this->send_email_now($template_id, true);
    }
}
