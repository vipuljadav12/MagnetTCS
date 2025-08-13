<?php

namespace App\Modules\LateSubmission\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\{Program, ProgramEligibility, ProgramGradeMapping};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\Enrollment\Models\{Enrollment, EnrollmentRaceComposition};
use App\Modules\ProcessSelection\Models\{Availability, ProgramSwingData, PreliminaryScore, ProcessSelection};
use App\Modules\SetAvailability\Models\{WaitlistAvailability, LateSubmissionAvailability};
use App\Modules\LateSubmission\Models\{LateSubmissionProcessLogs, LateSubmissionAvailabilityLog, LateSubmissionAvailabilityProcessLog, LateSubmissionIndividualAvailability};
use App\Modules\Submissions\Models\{Submissions, SubmissionGrade, SubmissionConductDisciplinaryInfo, SubmissionsFinalStatus, SubmissionsWaitlistFinalStatus, SubmissionsStatusLog, SubmissionsWaitlistStatusUniqueLog, SubmissionsSelectionReportMaster, SubmissionsRaceCompositionReport, SubmissionsLatestFinalStatus, SubmissionsTmpFinalStatus, LateSubmissionFinalStatus, LateSubmissionsStatusUniqueLog, SubmissionInterviewScore, SubmissionCompositeScore, SubmissionCommitteeScore};
use App\Modules\Waitlist\Models\{WaitlistProcessLogs, WaitlistAvailabilityLog, WaitlistAvailabilityProcessLog, WaitlistIndividualAvailability};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LateSubmissionController extends Controller
{

    //public $eligibility_grade_pass = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $group_racial_composition = [];
    public $program_group = [];
    public $enrollment_race_data = [];

    public function validateApplication($application_id)
    {
        $rs = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("submission_status", "Offered")->count();
        if ($rs > 0)
            echo "Selected Applications has still open offered submissions.";
        else {
            $enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();
            $error_msg = "";
            if ($enrollment_racial->black == '' || $enrollment_racial->black == '0')
                $error_msg .= "<li>Racial Composition for <strong>Black</strong> not at set Enrollment Level.</li>";
            if ($enrollment_racial->white == '' || $enrollment_racial->white == '0')
                $error_msg .= "<li>Racial Composition for <strong>White</strong> not at set Enrollment Level.</li>";
            if ($enrollment_racial->other == '' || $enrollment_racial->other == '0')
                $error_msg .= "<li>Racial Composition for <strong>Other</strong> not at set Enrollment Level.</li>";
            if ($enrollment_racial->swing == '' || $enrollment_racial->swing == '0')
                $error_msg .= "<li><strong>Swing %</strong> is not configured at set Enrollment Level.</li>";

            /* Find out Preliminary Processing is enabled or not */
            $rsApplication = Application::where("form_id", $application_id)->get();
            $preliminary = false;
            foreach ($rsApplication as $apkey => $apvalue) {
                /* Find out Minimum Committee Score is set or not */
                if ($apvalue->preliminary_processing == "Y") {
                    $preliminary = true;
                    $ap_id = $apvalue->id;
                    $count = PreliminaryScore::whereIn("submission_id", function ($query) use ($ap_id) {
                        $query->select('id')->from('submissions')->where("application_id", $ap_id);
                    })->count();
                    if ($count == 0) {
                        $error_msg .= "<li>Preliminary Score calculation is pending.</li>";
                    }
                }
            }
            if (!$preliminary) {
                $rsPrograms = Program::where("enrollment_id", Session::get("enrollment_id"))->where("parent_submission_form", $application_id)->select("id as program_id")->get();
                $prgArr = [];

                foreach ($rsPrograms as $rkey => $rsvalue) {
                    $committee_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->join("program", "program.id", "program_eligibility.program_id")->where("program.parent_submission_form", $application_id)->where("program_eligibility.status", "Y")->where("program.id", $rsvalue->program_id)->where("eligibility_template.name", "Committee Score")->select("program.id")->get()->toArray();
                    if (count($committee_eligibility) > 0) {
                        $prgArr[] = $rsvalue->program_id;
                        $rs = DB::table('seteligibility_extravalue')->where('program_id', $rsvalue->program_id)->where('eligibility_type', 7)->first();
                        if (!empty($rs)) {
                            $data = json_decode($rs->extra_values);
                            if (!isset($data->minimum_score)) {
                                $error_msg .= "<li>Minimum Committee Score not setup for <strong>" . getProgramName($rsvalue->program_id) . "</strong></li>";
                            }
                        } else {
                            $error_msg .= "<li>Minimum Committee Score not setup for <strong>" . getProgramName($rsvalue->program_id) . "</strong></li>";
                        }
                    }
                }

                foreach ($prgArr as $k => $v) {
                    $rsP = Submissions::where("form_id", $application_id)
                        ->where("first_choice_program_id", $v)->where("enrollment_id", Session::get("enrollment_id"))->where("late_submission", "Y")->whereIn('submission_status', array('Active', 'Pending'))->get();

                    $cmt_count = 0;
                    foreach ($rsP as $rk => $rv) {
                        $rsT = SubmissionCommitteeScore::where(
                            "submission_id",
                            $rv->id
                        )->where("program_id", $v)->first();
                        if (empty($rsT)) {
                            $cmt_count++;
                        }
                    }

                    $rsP = Submissions::where("form_id", $application_id)
                        ->where("second_choice_program_id", $v)->where("enrollment_id", Session::get("enrollment_id"))->where("late_submission", "Y")->whereIn('submission_status', array('Active', 'Pending'))->get();

                    foreach ($rsP as $rk => $rv) {
                        $rsT = SubmissionCommitteeScore::where(
                            "submission_id",
                            $rv->id
                        )->where("program_id", $v)->first();
                        if (empty($rsT)) {
                            $cmt_count++;
                        }
                    }



                    if ($cmt_count > 0) {

                        $error_msg .= "<li><strong>" . getProgramName($v) . "</strong> has <strong>" . $cmt_count . "</strong> has missing committee score.</li>";
                    }
                }
            }



            if ($error_msg == "")
                $error_msg = "OK";
            echo $error_msg;
        }
    }

    public function application_index()
    {
        $selection = "";
        $applications = Form::where("status", "y")->get();
        return view("LateSubmission::application_index", compact("applications", "selection"));
    }

    public function index($application_id = 0)
    {
        $displayother = 0;
        $district_id = Session::get("district_id");
        $rs = $exist_process = ProcessSelection::where("last_date_online_acceptance", ">", date("Y-m-d H:i:s"))->where("form_id", $application_id)->where('type', 'late_submission')->where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "DESC")->first();
        $display_outcome = $displayother = 0;

        $updated_id = 0;

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        if (!empty($rs)) {
            $displayother = 1;
            if ($rs->commited == "Yes") {
                $display_outcome = 1;
                $updated_id = $rs->id;
            } else {
                $last_date_online_acceptance = "";
                $last_date_offline_acceptance = "";
            }
            $last_date_online_acceptance = date('m/d/Y H:i', strtotime($rs->last_date_online_acceptance));
            $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($rs->last_date_offline_acceptance));
        } else {
            $rs = $exist_process = ProcessSelection::where("form_id", $application_id)->where("enrollment_id", Session::get("enrollment_id"))->where('type', 'late_submission')->orderBy("created_at", "DESC")->first();
            if (!empty($rs))
                $displayother = 1;
            $last_date_online_acceptance = "";
            $last_date_offline_acceptance = "";
        }

        $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where("parent_submission_form", $application_id)->where('status', 'Y')->get();

        $af_programs = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->fetch_programs_group($application_id);

        $tmp = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->groupByRacism($af_programs);

        /* Fetch all program groups */
        $program_group = $program_group_array = $tmp['program_group'];


        /* Get Program Values by unique value and by sorting */
        $pvalues = array_unique(array_values($program_group));
        sort($pvalues);
        $disp_arr = $program_arr = [];
        foreach ($pvalues as $key => $value) {
            $tmp_val_arr = [];
            foreach ($program_group as $pk => $pv) {
                if ($pv == $value) {
                    $program_name = getProgramName($pk);
                    $program_data = Program::where("id", $pk)->first();
                    $grade_lavel = explode(",", $program_data->grade_lavel);

                    foreach ($grade_lavel as $gval) {
                        $pdata = [];
                        $pdata['id'] = $pk;
                        $pdata['grade'] = $gval;
                        $pdata['withdrawn_allowed'] = "Yes";
                        $rs_availability = Availability::where("program_id", $pk)->where("enrollment_id", Session::get("enrollment_id"))->where("grade", $gval)->first();
                        if (!empty($rs_availability)) {
                            if ($rs_availability->white_seats == 0 && $rs_availability->white_seats == 0 && $rs_availability->other_seats == 0) {
                                $pdata['withdrawn_allowed'] = "No";
                            }
                        }
                        $pdata['name'] = $program_name . " - Grade " . $gval;
                        $pdata['waitlist_count'] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_waitlist_count($application_id, $pk, $gval);
                        $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $pk, $gval);


                        $pdata['available_count'] = $pdata['available_slot'] = $data['available_seats'] - $data['offered_seats'];


                        if ($pdata['available_slot'] < 0)
                            $pdata['available_slot'] = 0;


                        $additional = LateSubmissionProcessLogs::where("process_selection.enrollment_id", Session::get("enrollment_id"))->where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum('additional_seats');
                        $pdata['total_seats'] = $data['available_seats'] + $additional;

                        $total_applicants = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("late_submission", "Y")->where(function ($query) use ($pk) {
                            $query->where('first_choice_program_id', $pk);
                            $query->orWhere('second_choice_program_id', $pk);
                        })->where('next_grade', $gval)->get()->count();

                        $pdata['late_application_count'] = $total_applicants;
                        $pdata['withdrawn_student'] = "No";
                        $pdata['black_withdrawn'] = 0;
                        $pdata['white_withdrawn'] = 0;
                        $pdata['other_withdrawn'] = 0;
                        $pdata['additional_seats'] = 0;
                        $pdata['visible'] = "N";
                        if (!empty($exist_process)) {
                            $tmp_data = LateSubmissionProcessLogs::where("process_log_id", $exist_process->id)->where("program_id", $pk)->where("grade", $gval)->first();
                            if (!empty($tmp_data)) {
                                $pdata['visible'] = "Y";
                                $pdata['withdrawn_student'] = $tmp_data->withdrawn_student;
                                $pdata['black_withdrawn'] = $tmp_data->black_withdrawn;
                                $pdata['white_withdrawn'] = $tmp_data->white_withdrawn;
                                $pdata['other_withdrawn'] = $tmp_data->other_withdrawn;
                                $pdata['available_slot'] = $tmp_data->slots_to_awards;
                                $pdata['additional_seats'] = $tmp_data->additional_seats;
                                $pdata['late_application_count'] = $tmp_data->late_application_count;
                            }
                        }
                        $application_program_id = ProgramGradeMapping::where("program_id", $pk)->where("grade", $gval)->first();
                        //                        echo $gval." - " .$pk;exit; 
                        if (!empty($application_program_id)) {
                            $pdata['application_program_id'] = $application_program_id->id;
                            $tmp_val_arr[] = $pdata;
                        }
                    }
                }
            }
            $disp_arr[$value] = $tmp_val_arr;
        }

        /* echo "<pre>";
        print_r($disp_arr);
        exit;
        */
        $waitlist_process_logs = [];
        if ($display_outcome == 1) {
            $waitlist_process_logs = LateSubmissionProcessLogs::where("process_log_id", $updated_id)->orderBy("id", "DESC")->get();
        } else {
            //$last_date_online_acceptance = $last_date_offline_acceptance = "";
        }
        return view("LateSubmission::all_availability_index", compact("application_id", "disp_arr", "display_outcome", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance", "waitlist_process_logs"));
    }


    public function storeAllAvailability(Request $request, $application_id)
    {
        $req = $request->all();
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();

        $version = 0;
        if (!empty($process_selection)) {
            if ($process_selection->commited == 'Yes') {
                $version = $process_selection->version + 1;
            } else {
                $version = $process_selection->version;
            }
        }
        $type = "";
        if (isset($req['type']))
            $type = $req['type'];

        $selected_programs = [];
        $process = false;
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {
                $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(" . $value . ", selected_programs)")->where("type", "late_submission")->where("version", $version)->orderBy("created_at", "DESC")->first();

                if (!empty($process_selection)) {
                    if ($process_selection->commited != 'Yes') {
                        $process = true;
                    }
                } else {
                    $process = true;
                }
                $selected_programs[] = $value;
            }
        }

        if ($req['last_date_online_acceptance'] != '' || $req['process_event'] == "saveonly") {
            if ($req['last_date_online_acceptance'] != '') {
                $data['last_date_online_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_online_acceptance']));
                $data['last_date_offline_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_offline_acceptance']));
            }

            $data['district_id'] = Session::get("district_id");
            $data['enrollment_id'] = Session::get("enrollment_id");
            $data['application_id'] = $application_id;
            $data['district_id'] = Session::get("district_id");
            $data['type'] = "late_submission";
            $data['version'] = $version;
            $data['selected_programs'] = implode(",", $selected_programs);
            $rs = ProcessSelection::updateOrCreate(['form_id' => $data['application_id'], "version" => $version, "type" => "late_submission", "enrollment_id" => Session::get("enrollment_id")], $data);

            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where('form_id', $data['application_id'])->where("version", $version)->where("type", "late_submission")->first();


            $t = LateSubmissionProcessLogs::where("process_log_id", $rs->id)->delete();
            foreach ($req['application_program_id'] as $key => $value) {
                if ($req['awardslot' . $value] > 0) {
                    $insert = [];
                    $insert['process_log_id'] = $rs->id;
                    $insert['program_id'] = $req['program_id' . $value];
                    $insert['grade'] = $req['grade' . $value];
                    $insert['application_id'] = $rs->application_id;
                    $insert['version'] = $version;
                    $insert['program_name'] = $req['program_name' . $value];
                    $insert['total_seats'] = $req['total_seats' . $value];
                    $insert['additional_seats'] = $req['additional_seats' . $value];
                    $insert['withdrawn_student'] = $req['withdrawn_student' . $value];
                    if ($req['withdrawn_student' . $value] != "Yes") {
                        $insert['black_withdrawn'] = 0;
                        $insert['white_withdrawn'] = 0;
                        $insert['other_withdrawn'] = 0;
                    } else {
                        $insert['black_withdrawn'] = $req['black' . $value];
                        $insert['white_withdrawn'] = $req['white' . $value];
                        $insert['other_withdrawn'] = $req['other' . $value];
                    }
                    $insert['waitlisted'] = $req['waitlist_count' . $value];
                    $insert['late_application_count'] = $req['late_application_count' . $value];
                    $insert['available_slots'] = $req['available_slot' . $value];
                    $insert['slots_to_awards'] = $req['awardslot' . $value];
                    $insert['generated_by'] = Auth::user()->id;
                    $insert['enrollment_id'] = Session::get("enrollment_id");
                    $rs1 = LateSubmissionProcessLogs::updateOrCreate(["process_log_id" => $rs->id, "program_name" => $insert['program_name']], $insert);
                }
            }
        }

        $data = array();

        if ($process && $req['process_event'] != "saveonly") {
            $rdel = LateSubmissionFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->delete();
            $test = $this->processLateSubmission($req, $application_id, $version, $type);
        }
        echo "done";
    }


    /* Seats Status Functions */
    public function seatStatusVersion($id = 0)
    {
        $rs = ProcessSelection::where("id", $id)->first();
        $application_id = $rs->application_id;
        $version = $rs->version;

        $version_data = $rs;
        $selected_programs = explode(",", $rs->selected_programs);

        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            $program_ids[] = getApplicationProgramId($value);
        }

        $tmp_version_data = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->get();

        $parray = [];
        //$rs = WaitlistAvailabilityProcessLog::where("version", $version)->get();
        foreach ($tmp_version_data as $key => $value) {
            if (!isset($parray[$value->program_id])) {
                $parray[$value->program_id] = [];
            }
            array_push($parray[$value->program_id], $value->grade);
        }



        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $district_id = Session::get("district_id");
        $submissions = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade']);


        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        $prgCount = array();;
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if ($value->$choice != 0) {
                        if (!isset($programs[$value->$choice]) && in_array($value->$choice, array_keys($parray))) {
                            $programs[$value->$choice] = [];
                        }
                        if (isset($programs[$value->$choice]) && !in_array($value->next_grade, $programs[$value->$choice])) {
                            if (in_array($value->next_grade, $parray[$value->$choice])) {
                                array_push($programs[$value->$choice], $value->next_grade);
                            }
                        }
                    }
                }
            }
        }

        ksort($programs);
        $final_data = array();
        foreach ($programs as $key => $value) {
            foreach ($value as $ikey => $ivalue) {
                $tmp = array();
                $rs = Availability::where("program_id", $key)->where("grade", $ivalue)->where("enrollment_id", Session::get("enrollment_id"))->first();
                $available_seats = $rs->available_seats;

                $seat_data = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("program_id", $key)->where("grade", $ivalue)->where("application_id", $application_id)->where("version", $version)->first();
                //echo $ivalue."<BR>";
                $tmp['original_capacity'] = $rs->total_seats;
                $tmp['total_seats'] = $rs->available_seats;
                $tmp['available_seats'] = $seat_data->available_slots;
                //echo $tmp['available_seats'];exit;
                $tmp['process_seats'] = $seat_data->slots_to_awards;
                $tmp['total_applicants'] = $seat_data->waitlisted;
                $tmp['program_name'] = $seat_data->program_name;
                $tmp['black_withdrawn'] = $seat_data->black_withdrawn;
                $tmp['white_withdrawn'] = $seat_data->white_withdrawn;
                $tmp['other_withdrawn'] = $seat_data->other_withdrawn;
                $tmp['additional_seats'] = $seat_data->additional_seats;

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Offered")
                    ->where("first_choice_program_id", $key)
                    ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)->where("late_submissions_final_status.application_id", $application_id)->where("late_submissions_final_status.version", $version)
                    ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                    ->where("second_choice_program_id", $key)
                    ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
                    ->where('next_grade', $ivalue)->where("late_submissions_final_status.application_id", $application_id)->where("late_submissions_final_status.version", $version)
                    ->get()->count();
                $tmp['offered'] = $rs1 + $rs2;






                $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $key, $ivalue);
                $accepted = $data['offered_seats'];


                $current_accepted = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $ivalue)->where(function ($q1) use ($key) {
                    $q1->where(function ($q) use ($key) {
                        $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $key);
                    })->orWhere(function ($q) use ($key) {
                        $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $key);
                    });
                })->where("late_submissions_final_status.version", $version)->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->count();
                $accepted = $accepted - $current_accepted;

                $tmp['accepted'] = $accepted;
                $tmp['remaining'] = $tmp['available_seats']  + $tmp['black_withdrawn'] + $tmp['white_withdrawn'] + $tmp['other_withdrawn'] + $tmp['additional_seats'];
                $final_data[] = $tmp;
            }
        }




        //print_r($final_data);exit;
        return view("LateSubmission::seats_status", compact("final_data", "version_data"));
    }


    /* Population Change */
    public function population_change_application($application_id = 1, $version = 0)
    {
        // Processing
        $pid = $application_id;
        $from = "form";

        $selected_programs = [];
        if ($version == 0) {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $selected_programs = explode(",", $rs->selected_programs);
        }
        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id; //getApplicationProgramId($value);
        }

        $additional_data = $this->get_additional_info($application_id, $version);
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];


        $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();

        // Population Changes
        $programs = [];
        $district_id = \Session('district_id');

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'" . implode(',', $ids) . "'"));

        $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($program_ids) {
            $q->whereIn("first_choice_program_id", $program_ids)
                ->orWhereIn("second_choice_program_id", $program_ids);
        })
            ->where('district_id', $district_id)
            ->where("submissions.form_id", $application_id)
            ->where("submissions.enrollment_id", Session::get('enrollment_id'))
            ->where("late_submissions_final_status.version", $version)
            ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for']);

        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if (in_array($value->$choice, $program_ids)) {
                        if (!isset($programs[$value->$choice])) {
                            if ($value->$choice != 0)
                                $programs[$value->$choice] = [];
                        }
                        if ($value->$choice != 0 && !in_array($value->next_grade, $programs[$value->$choice])) {
                            array_push($programs[$value->$choice], $value->next_grade);
                        }
                    }
                }
            }
        }
        ksort($programs);
        $data_ary = [];
        $race_ary = [];
        foreach ($programs as $program_id => $grades) {
            foreach ($grades as $grade) {
                $availability = Availability::where("enrollment_id", Session::get("enrollment_id"))->where('program_id', $program_id)
                    ->where('grade', $grade)->first(['total_seats', 'available_seats']);
                $race_count = [];
                if (!empty($availability)) {
                    foreach ($choices as $choice) {
                        if ($choice == "first_choice_program_id") {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('first_choice_final_status', "Offered")
                                ->where('next_grade', $grade);
                        } else {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('second_choice_final_status', "Offered")
                                ->where('next_grade', $grade);
                        }

                        $race = $submission_race_data->groupBy('calculated_race')->map->count();
                        //echo "<pre>";
                        //print_r($race);
                        if (count($race) > 0) {
                            $race_ary = array_merge($race_ary, $race->toArray());

                            if (count($race_count) > 0) {
                                foreach ($race as $key => $value) {

                                    if (isset($race_count[$key])) {
                                        $race_count[$key] = $race_count[$key] + $value;
                                    } else {
                                        $race_count[$key] = $value;
                                    }
                                }
                            } else {


                                $race_count = $race;
                            }
                        }
                    }

                    $rsproc = LateSubmissionProcessLogs::where("version", $version)->where("application_id", $application_id)->where("program_id", $program_id)->where("grade", $grade)->first();

                    if (!isset($race_ary['Black']))
                        $race_ary['Black'] = 0;
                    if (!isset($race_ary['White']))
                        $race_ary['White'] = 0;
                    if (!isset($race_ary['Other']))
                        $race_ary['Other'] = 0;


                    $data = [
                        'program_id' => $program_id,
                        'grade' => $grade,
                        'total_seats' => $availability->total_seats ?? 0,
                        'available_seats' => $rsproc->slots_to_awards ?? 0,
                        'race_count' => $race_count,
                    ];
                    $data_ary[] = $data;
                    // sorting race in ascending
                    ksort($race_ary);
                }
            }
            // exit;
        }

        //exit;
        // Submissions Result
        return view("LateSubmission::population_change", compact('data_ary', 'race_ary', 'pid', 'from', "display_outcome", "application_id", "last_date_online_acceptance", "last_date_offline_acceptance"));
    }


    public function population_change_version($application_id, $version = 0)
    {
        // Population Changes
        $selected_programs = [];
        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->orderBy("created_at", "DESC")->first();
        $version_data = $rs;
        $version = $rs->version;
        $selected_programs = explode(",", $rs->selected_programs);

        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id; //getApplicationProgramId($value);
        }


        $programs = [];
        $district_id = \Session('district_id');

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $ids_ordered = implode(',', $ids);

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'" . implode(',', $ids) . "'"));

        $submissions = Submissions::where('district_id', $district_id)->where(function ($q) use ($program_ids) {
            $q->whereIn("first_choice_program_id", $program_ids)
                ->orWhereIn("second_choice_program_id", $program_ids);
        })
            ->where('district_id', $district_id)
            ->where("submissions.form_id", $application_id)
            ->where("submissions.enrollment_id", Session::get('enrollment_id'))
            ->where("late_submissions_final_status.version", $version)
            ->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->orderByRaw('FIELD(next_grade,' . implode(",", $ids) . ')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for']);



        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if (in_array($value->$choice, $program_ids)) {
                        if (!isset($programs[$value->$choice])) {
                            if ($value->$choice != 0)
                                $programs[$value->$choice] = [];
                        }
                        if ($value->$choice != 0 && !in_array($value->next_grade, $programs[$value->$choice])) {
                            array_push($programs[$value->$choice], $value->next_grade);
                        }
                    }
                }
            }
        }
        ksort($programs);
        $data_ary = [];
        $race_ary = [];
        //dd($programs);
        foreach ($programs as $program_id => $grades) {
            foreach ($grades as $grade) {
                $availability = Availability::where("enrollment_id", Session::get("enrollment_id"))->where('program_id', $program_id)
                    ->where('grade', $grade)->first(['total_seats', 'available_seats']);
                $race_count = [];
                if (!empty($availability)) {
                    foreach ($choices as $choice) {
                        if ($choice == "first_choice_program_id") {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('first_choice_final_status', "Offered")
                                ->where('next_grade', $grade);
                        } else {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('second_choice_final_status', "Offered")
                                ->where('next_grade', $grade);
                        }

                        $race = $submission_race_data->groupBy('calculated_race')->map->count();
                        //echo "<pre>";
                        //print_r($race);
                        if (count($race) > 0) {
                            $race_ary = array_merge($race_ary, $race->toArray());

                            if (count($race_count) > 0) {
                                foreach ($race as $key => $value) {

                                    if (isset($race_count[$key])) {
                                        $race_count[$key] = $race_count[$key] + $value;
                                    } else {
                                        $race_count[$key] = $value;
                                    }
                                }
                            } else {


                                $race_count = $race;
                            }
                        }
                    }
                    $rsproc = LateSubmissionProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("version", $version)->where("application_id", $application_id)->where("program_id", $program_id)->where("grade", $grade)->first();
                    if (!isset($race_ary['Black']))
                        $race_ary['Black'] = 0;
                    if (!isset($race_ary['White']))
                        $race_ary['White'] = 0;
                    if (!isset($race_ary['Other']))
                        $race_ary['Other'] = 0;
                    $data = [
                        'program_id' => $program_id,
                        'grade' => $grade,
                        'total_seats' => $rsproc->available_slots ?? 0,
                        'available_seats' => $rsproc->slots_to_awards ?? 0,
                        'race_count' => $race_count,
                    ];
                    $data_ary[] = $data;
                    // sorting race in ascending
                    ksort($race_ary);
                }
            }
            // exit;
        }
        return view("LateSubmission::population_change_report", compact('data_ary', 'race_ary', "version", "version_data"));
    }


    /* sUBMISSION RESULTS */

    public function submissions_results_application($application_id = 1, $version = 0)
    {
        $selected_programs = [];
        if ($version == 0) {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $selected_programs = explode(",", $rs->selected_programs);
        }
        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id; //getApplicationProgramId($value);
        }


        $additional_data = $this->get_additional_info($application_id, $version);
        $displayother = $additional_data['displayother'];
        $display_outcome = $additional_data['display_outcome'];
        $last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
        $last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];

        $pid = $application_id;
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where("submissions.form_id", $application_id)->where("late_submissions_final_status.version", $version)->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status']);
        $final_data = array();
        foreach ($submissions as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $value->next_grade;
            $tmp['school'] = $value->current_school;
            $tmp['choice'] = 1;
            $tmp['race'] = $value->calculated_race;
            $tmp['program'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
            $tmp['program_name'] = getProgramName($value->first_choice_program_id);
            $tmp['offered_status'] = $value->first_choice_final_status;
            if ($value->first_choice_final_status == "Offered")
                $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
            elseif ($value->first_choice_final_status == "Denied due to Ineligibility")
                $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
            elseif ($value->first_choice_final_status == "Waitlisted")
                $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
            elseif ($value->first_choice_final_status == "Denied due to Incomplete Records")
                $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
            else
                $tmp['outcome'] = "";

            /* if(!in_array($value->first_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                        $final_data[] = $tmp;
                        */

            if (!in_array($value->first_choice_final_status, array("Pending")) && in_array($value->first_choice_program_id, $program_ids))
                $final_data[] = $tmp;
            if ($value->second_choice_program_id != 0) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
                $tmp['race'] = $value->calculated_race;
                $tmp['choice'] = 2;
                $tmp['program'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                $tmp['offered_status'] = $value->second_choice_final_status;

                if ($value->second_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif ($value->second_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif ($value->second_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif ($value->second_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";

                /*if(!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                        $final_data[] = $tmp;*/

                if (!in_array($value->second_choice_final_status, array("Pending")) && in_array($value->second_choice_program_id, $program_ids))
                    $final_data[] = $tmp;
            }
        }
        $grade = $outcome = array();
        foreach ($final_data as $key => $value) {
            $grade['grade'][] = $value['grade'];
            $outcome['outcome'][] = $value['outcome'];
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);


        return view("LateSubmission::submissions_result", compact('final_data', 'pid', 'display_outcome', "application_id", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance"));
    }
    public function submissions_results_version($application_id, $version = 0)
    {
        $selected_programs = [];
        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->orderBy("created_at", "DESC")->first();
        $version_data = $rs;
        $version = $rs->version;
        $selected_programs = explode(",", $rs->selected_programs);

        $program_ids = [];
        foreach ($selected_programs as $key => $value) {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id; //getApplicationProgramId($value);
        }



        $pid = $application_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where('district_id', $district_id)
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where("submissions.form_id", $application_id)->where('late_submissions_final_status.version', $version)->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status']);

        $final_data = array();
        foreach ($submissions as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value->id;
            $tmp['name'] = $value->first_name . " " . $value->last_name;
            $tmp['grade'] = $value->next_grade;
            $tmp['school'] = $value->current_school;
            $tmp['choice'] = 1;
            $tmp['race'] = $value->calculated_race;
            $tmp['program'] = getProgramName($value->first_choice_program_id) . " - Grade " . $value->next_grade;
            $tmp['program_name'] = getProgramName($value->first_choice_program_id);
            $tmp['offered_status'] = $value->first_choice_final_status;
            if ($value->first_choice_final_status == "Offered")
                $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
            elseif ($value->first_choice_final_status == "Denied due to Ineligibility")
                $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
            elseif ($value->first_choice_final_status == "Waitlisted")
                $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
            elseif ($value->first_choice_final_status == "Denied due to Incomplete Records")
                $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
            else
                $tmp['outcome'] = "";

            /*if(!in_array($value->first_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                    $final_data[] = $tmp;  
                    */

            if (!in_array($value->first_choice_final_status, array("Pending")) && in_array($value->first_choice_program_id, $program_ids))
                $final_data[] = $tmp;

            if ($value->second_choice_program_id != 0) {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name . " " . $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
                $tmp['race'] = $value->calculated_race;
                $tmp['choice'] = 2;
                $tmp['program'] = getProgramName($value->second_choice_program_id) . " - Grade " . $value->next_grade;
                $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                $tmp['offered_status'] = $value->second_choice_final_status;

                if ($value->second_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif ($value->second_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif ($value->second_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif ($value->second_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";
                /*if(!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                        $final_data[] = $tmp;*/
                if (!in_array($value->second_choice_final_status, array("Pending")) && in_array($value->second_choice_program_id, $program_ids))
                    $final_data[] = $tmp;
            }
        }
        $grade = $outcome = array();
        foreach ($final_data as $key => $value) {
            $grade['grade'][] = $value['grade'];
            $outcome['outcome'][] = $value['outcome'];
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);


        return view("LateSubmission::submissions_result_report", compact('final_data', "application_id",  "version", "version_data"));
    }


    public function processLateSubmission($req, $application_id, $actual_version, $type = "")
    {
        $group_racial_composition = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updated_racial_composition($application_id, $actual_version);
        foreach ($group_racial_composition as $key => $value) {
            $group_racial_composition[$key]['no_previous'] = 'N';
        }


        $af_programs = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->fetch_programs_group($application_id);

        $this->group_racial_composition = $group_race_array = $group_racial_composition;



        $tmp = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->groupByRacism($af_programs);

        $this->program_group = $program_group_array = $tmp['program_group'];



        $process_program = $awardslot  = $program_process_ids = $availabilityArray = [];
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {

                $rs = ProgramGradeMapping::where("id", $value)->select("program_id", "grade")->where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->first();
                $program_id = $rs->program_id;
                $grade = $rs->grade;
                $program_process_ids[] = $program_id;
                $availabilityArray[$program_id][$grade] = $req['awardslot' . $value];

                if (isset($process_program[$program_id])) {
                    $tmp = $process_program[$program_id];
                    $tmp[] = $grade;
                    $process_program[$program_id] = $tmp;
                } else {
                    $process_program[$program_id][] = $grade;
                }
                $awardslot[$program_id . "-" . $grade] = $req['awardslot' . $value];
            }
        }

        /*
            Need to identify whether process selection is done for particular program/grade. If Yes, then we have to take that table based on process selection type and version.
        */

        $submission_data = [];
        $keys = array_keys($process_program);
        $subids = [];


        $selected_programs = [];
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {
                $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(" . $value . ", selected_programs)")->where("commited", "Yes")->orderBy("created_at", "desc")->first();

                $table_name = "submissions_final_status";
                $version = 0;
                if (!empty($rs)) {
                    if ($rs->type == "regular") {
                        $table_name = "submissions_final_status";
                        $version = 0;
                    } elseif ($rs->type == "waitlist") {
                        $table_name = "submissions_waitlist_final_status";
                        $version = $rs->version;
                    } elseif ($rs->type == "late_submission") {
                        $table_name = "late_submissions_final_status";
                        $version = $rs->version;
                    }
                }

                $submissions = Submissions::where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                    $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                })->where("submissions.enrollment_id", Session::get("enrollment_id"))->join($table_name, $table_name . ".submission_id", "submissions.id")->where($table_name . ".application_id", $application_id)->where($table_name . ".version", $version)->select("submissions.*", $table_name . ".first_offer_status", $table_name . ".second_offer_status", $table_name . ".first_choice_final_status", $table_name . ".second_choice_final_status")
                    ->get();

                foreach ($submissions as $sk => $sv) {
                    $insert = false;



                    if (in_array($sv->first_choice_program_id, $keys)) {
                        if (in_array($sv->next_grade, $process_program[$sv->first_choice_program_id])) {
                            $insert = true;
                        }

                        if (($sv->first_choice_final_status == "Waitlisted" && !in_array($sv->second_offer_status, array("Pending", "Declined & Waitlisted", "Declined"))) || $sv->first_choice_final_status == "Denied due to Ineligibility" || $sv->first_choice_final_status == "Denied due to Incomplete Records") {
                            $insert = false;
                        }
                    }
                    if (in_array($sv->second_choice_program_id, $keys)) {
                        if (in_array($sv->next_grade, $process_program[$sv->second_choice_program_id])) {
                            $insert = true;
                        }
                        if (($sv->second_choice_final_status == "Waitlisted" && !in_array($sv->first_offer_status, array("Pending", "Declined & Waitlisted", "Declined"))) || $sv->second_choice_final_status == "Denied due to Ineligibility" || $sv->second_choice_final_status == "Denied due to Incomplete Records") {
                            $insert = false;
                        }
                        if ($sv->second_choice_final_status != "Waitlisted") {

                            $insert = false;
                        }
                    }
                    if ($insert && !in_array($sv->id, $subids)) {
                        $submission_data[] = $sv;
                        $subids[] = $sv->id;
                    }
                }
            }
        }


        //gecho count($submission_data);exit;

        /* From here code is pending - All list of submission we have fetches from different table. Now we have to save unique submissions options in separate table which will update regularly */

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("last_date_online_acceptance", ">", date("Y-m-d H:i:s"))->where("form_id", $application_id)->where('type', 'late_submission')->first();

        $preliminary_score = false;

        $application_data = Application::where("form_id", $application_id)->first();
        if (!empty($application_data) && $application_data->preliminary_processing == "Y")
            $preliminary_score = true;
        if (!empty($process_selection) && $process_selection->commited == "Yes") {
            $final_data = $group_racial_composition = $incomplete_arr = $failed_arr = [];
            return view("ProcessSelection::test_index", compact("final_data", "incomplete_arr", "failed_arr", "group_racial_composition", "preliminary_score"));
        }
        $processType = Config::get('variables.process_separate_first_second_choice');
        $gradeWiseProcessing = Config::get('variables.grade_wise_processing');


        /* save enrollment wise race composition with swing % Data */
        $enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();
        $swing = $enrollment_racial->swing;

        /* Create Application Filter Group Array for Program */
        $tmpGroup = array_values($program_group_array);
        $arr = array_unique($tmpGroup);
        $race_enroll_arr = [];
        foreach ($arr as $key => $value) {
            $rs = ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("program_name", $value)->first();
            $program_swing = $swing;
            if (!empty($rs)) {
                if (is_numeric($rs->swing_percentage) && $rs->swing_percentage > 0) {
                    $program_swing = $rs->swing_percentage;
                }
            }
            $tmp = [];
            $tmp['min'] = $enrollment_racial->black - $program_swing;
            $tmp['max'] = $enrollment_racial->black + $program_swing;
            $race_enroll_arr[$value]['black'] = $tmp;

            $tmp = [];
            $tmp['min'] = $enrollment_racial->white - $program_swing;
            $tmp['max'] = $enrollment_racial->white + $program_swing;
            $race_enroll_arr[$value]['white'] = $tmp;

            $tmp = [];
            $tmp['min'] = $enrollment_racial->other - $program_swing;
            $tmp['max'] = $enrollment_racial->other + $program_swing;
            $race_enroll_arr[$value]['other'] = $tmp;
        }
        $this->enrollment_race_data = $race_enroll_arr;

        $firstData = Submissions::distinct()->where("enrollment_id", Session::get("enrollment_id"))->where("submission_status", "<>",  "Application Withdrawn")->where("form_id", $application_id)->get(["first_choice"]);
        $secondData = Submissions::distinct()->where("enrollment_id", Session::get("enrollment_id"))->where("submission_status", "<>",  "Application Withdrawn")->where("form_id", $application_id)->get(["second_choice"]);

        /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
         */
        $subjects = $terms = $programArr = $test_scores_titles = array();
        $eligibilityArr = array();


        foreach ($firstData as $key => $value) {
            if ($value->first_choice != "" && !in_array($value->first_choice, $programArr)) {
                $programArr[] = $value->first_choice;
                $data = getSetEligibilityDataDynamic($value->first_choice, 12);
                if (isset($data->ts_scores)) {
                    foreach ($data->ts_scores as $ts => $tv) {
                        if (!in_array($tv, $test_scores_titles)) {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }
        foreach ($secondData as $key => $value) {
            if ($value->second_choice != "" && !in_array($value->second_choice, $programArr)) {
                $programArr[] = $value->second_choice;
                $data = getSetEligibilityDataDynamic($value->second_choice, 12);
                if (isset($data->ts_scores)) {
                    foreach ($data->ts_scores as $ts => $tv) {
                        if (!in_array($tv, $test_scores_titles)) {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }


        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = $setCommitteScoreEligibility = array();
        foreach ($firstData as $value) {
            if (!in_array($value->first_choice, array_keys($setCommitteScoreEligibility))) {
                $data = getSetEligibilityDataDynamic($value->first_choice, 7);
                if (isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->first_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->first_choice] = 2;
            }
        }

        foreach ($secondData as $value) {
            if (!in_array($value->second_choice, array_keys($setCommitteScoreEligibility))) {
                $data = getSetEligibilityDataDynamic($value->second_choice, 7);
                if (isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->second_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->second_choice] = 2;
            }
        }

        /* Code to fetch all selection properties of each program
        and Create array after soring. So we can get idea what is 
        selection ranking for each program */


        $programSortArr = [];
        foreach ($programArr as $key => $val) {
            $rsProgram = Program::where("id", getApplicationProgramId($val))->first();
            if (!empty($rsProgram)) {
                $tmp = array();
                if ($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if ($rsProgram->committee_score != '')
                    $tmp['committee_score'] = $rsProgram->committee_score;
                if ($rsProgram->audition_score != '')
                    $tmp['audition_score'] = $rsProgram->audition_score;
                if ($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if ($rsProgram->combine_score != '')
                    $tmp['combine_score'] = $rsProgram->combine_score;
                if ($rsProgram->lottery_number != '')
                    $tmp['lottery_number'] = $rsProgram->lottery_number;
                if ($rsProgram->final_score != '')
                    $tmp['final_score'] = $rsProgram->final_score;
                asort($tmp);
                $programSortArr[$rsProgram->id] = $tmp;
            }
        }
        /* Get CDI Set Eligibility Data Set for first choice program and second choice program
         */

        $setCDIEligibilityData = array();

        $committee_eligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->join("program", "program.id", "program_eligibility.program_id")->where("program.parent_submission_form", $application_id)->where("program.enrollment_id", Session::get("enrollment_id"))->where("eligibility_template.name", "Committee Score")->where("program_eligibility.status", "Y")->select("program.id")->get()->toArray();
        $committee_program_id = [];
        foreach ($committee_eligibility as $key => $value) {
            $committee_program_id[] = $value['id'];
        }

        /* Get CDI Data */
        $submissions = $submission_data;


        $firstdata = $seconddata = array();
        $incomplete_arr = $failed_arr = $interview_arr = array();

        $programGrades = array();
        $committee_count = 0;



        foreach ($submissions as $key => $value) {
            $interview_passed = true;
            $composite_score = 0;

            if (in_array($value->first_choice_program_id, $program_process_ids)) {
                $failed = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $tmp['composite_score'] = $composite_score;
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);
                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice]) {
                    $tmp['committee_score_status'] = 'Pass';
                } elseif ($tmp['committee_score'] != "NA") {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['choice'] = 1;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");

                if ($value->submission_status == "Pending") {
                    $incomplete_arr[] = $tmp;
                } elseif (!$interview_passed) {
                    $interview_arr[] = $tmp;
                } else {
                    if ($value->first_offer_status != "Declined & Waitlisted" && $value->first_choice_final_status != "Denied due to Ineligibility"  && $value->first_choice_final_status != "Denied due to Incomplete Records") {
                        if ($failed)
                            $failed_arr[] = $tmp;
                        elseif ($incomplete)
                            $incomplete_arr[] = $tmp;
                        else {
                            $firstdata[$value->first_choice_program_id][] = $tmp;
                        }
                    }
                }
            }

            if (in_array($value->second_choice_program_id, $program_process_ids)) {
                $failed = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $tmp['composite_score'] = $composite_score;
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);

                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);
                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif (!$interview_passed) {
                    $interview_arr[] = $tmp;
                } elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice]) {
                    $tmp['committee_score_status'] = 'Pass';
                } elseif ($tmp['committee_score'] != "NA") {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['choice'] = 2;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");

                if ($value->submission_status == "Pending") {
                    $incomplete_arr[] = $tmp;
                } else {
                    if ($value->second_offer_status != "Declined & Waitlisted"  && $value->second_choice_final_status != "Denied due to Ineligibility"  && $value->second_choice_final_status != "Denied due to Incomplete Records") {
                        if ($failed)
                            $failed_arr[] = $tmp;
                        elseif ($incomplete)
                            $incomplete_arr[] = $tmp;
                        else {
                            $seconddata[$value->second_choice_program_id][] = $tmp;
                        }
                    }
                }
            }
        }


        /* Need to update code from here */

        if ($type == "update") {
            foreach ($interview_arr as $flkey => $flvalue) {
                $insert = [];
                $insert['submission_id'] = $flvalue['id'];
                $insert['first_choice_final_status'] = "Denied due to Ineligibility";
                $insert['first_waitlist_for'] = $flvalue['first_choice_program_id'];
                $insert['first_choice_eligibility_reason'] = "Denied due to 0 Interview Score";

                $tmpData = [];
                $tmpData['submission_id'] = $flvalue['id'];
                $tmpData['choice_type'] = "first";
                $tmpData['status'] = "Denied due to Ineligibility";
                $tmpData['reason'] = "Denied due to 0 Interview Score";
                $rs = SubmissionsTmpFinalStatus::create($tmpData);

                if ($flvalue['second_choice_program_id'] != 0 && $flvalue['second_choice_program_id'] != "") {
                    $insert['second_waitlist_for'] = $flvalue['second_choice_program_id'];
                    $insert['second_choice_final_status'] = "Denied due to Ineligibility";
                    $insert['second_choice_eligibility_reason'] = "Denied due to 0 Interview Score";

                    $tmpData = [];
                    $tmpData['submission_id'] = $flvalue['id'];
                    $tmpData['choice_type'] = "second";
                    $tmpData['status'] = "Denied due to Ineligibility";
                    $tmpData['reason'] = "Denied due to 0 Interview Score";
                    $rs = SubmissionsTmpFinalStatus::create($tmpData);
                }
                $insert['enrollment_id'] = Session::get("enrollment_id");
                $insert['application_id'] = $application_id;
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $flvalue['id'], "version" => $actual_version], $insert);
            }
        }


        /* Sort all submission based on selection rank set 
        by each program array */
        $ids = array('PreK', 'K', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
        $firstPrgData = $secondPrgData = [];
        $loopArr = array("first", "second");
        if (!$processType) {
            $dataStoreArr = array("first", "first");
        } else {
            $dataStoreArr = array("first", "second");
        }

        $append_second = $append_first = [];
        foreach ($loopArr as $lkey => $lvalue) {
            $str = $lvalue . "data";
            $arrvar = ${$str};
            foreach ($arrvar as $key => $value) {

                $parray = $value;
                if (isset($programSortArr[$key])) {
                    $array = $value;
                    $sortingParams = [];
                    $first_column = "";
                    $i = 0;
                    foreach ($programSortArr[$key] as $pk => $pv) {
                        if ($i == 0)
                            $first_column = $pk;
                        $i++;
                    }

                    $first_col_arr = array_column($array, $first_column);

                    $tmpStr = $dataStoreArr[$lkey] . "PrgData";

                    if ($first_column != "committee_score" && $first_column != "combine_score" && (in_array($key, $committee_program_id) || $key == 19)) {
                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${"append_" . $lvalue}[] = $pv;
                        }

                        foreach ($programSortArr[$key] as $pk => $pv) {
                            if ($pk == "rating_priority") {
                                $sort_field = "rank";
                                $sort_type = SORT_ASC;
                            } else {
                                $sort_field = $pk;
                                $sort_type = SORT_DESC;
                            }


                            $sortingParams[] = array_column(${"append_" . $dataStoreArr[$lkey]}, $sort_field);
                            $sortingParams[] = $sort_type;
                        }
                        $sortingParams[] = &${"append_" . $dataStoreArr[$lkey]};
                        array_multisort(...$sortingParams);
                    } else {

                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${$tmpStr}[] = $pv;
                        }
                    }
                }
            }
        }
        $firstdata = $firstPrgData;

        if (!empty($firstdata)) {
            $committee_score  = $priority = $lottery_number = $choices = $next_grade = array();
            foreach ($firstdata as $key => $value) {
                try {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                } catch (\Exception $e) {
                    echo $value['id'];
                    exit;
                }
                $priority['rank'][] = $value['rank'];
                $lottery_number['lottery_number'][] = $value['lottery_number'];
                $choices['choice'][] = $value['choice'];
                //$next_grade['next_grade'][] = $value['next_grade'];

            }
            if (!$processType) {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $choices['choice'], SORT_ASC, $firstdata);
            } else {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $firstdata);
            }
        }
        if ($committee_count > 0) {
            $firstdata = array_merge($append_first, $firstdata);
            if (!$processType) {
                $firstdata = array_merge($firstdata, $append_second);
            }
        }

        if ($gradeWiseProcessing) {
            $tmp = [];
            foreach ($ids as $ik => $iv) {
                foreach ($firstdata as $fk => $fv) {
                    if ($fv['next_grade'] == $iv) {
                        $tmp[] = $fv;
                    }
                }
            }


            $firstdata = $tmp;
        }


        if ($processType) {
            $seconddata = $secondPrgData;
            if (!empty($seconddata)) {
                $committee_score  = $priority = $lottery_number = array();
                foreach ($seconddata as $key => $value) {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                    $priority['rank'][] = $value['rank'];
                    $lottery_number['lottery_number'][] = $value['lottery_number'];
                }
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $seconddata);
            }
            $seconddata = array_merge($seconddata, $append_second);
        } else {
            $seconddata = [];
        }



        $tmpAvailability = $availabilityArray;
        $waitlistOfferArray  = $offeredRank = $firstOffered = array();
        $final_data = [];


        foreach ($firstdata as $key => $value) {
            if ($value['choice'] == 1)
                $program_id = $value['first_choice_program_id'];
            else
                $program_id = $value['second_choice_program_id'];

            $offered = false;
            $offered_race = "";
            $offered_submission_id = "";
            $offer_program_id = 0;
            if (in_array($value['id'], $firstOffered)) {
                $value['final_status'] = "Waitlist By Already Offered";
            } else {
                if (isset($availabilityArray[$program_id][$value['next_grade']]) && $availabilityArray[$program_id][$value['next_grade']] > 0) {
                    $race = strtolower($value['race']);
                    $group_name = $this->program_group[$program_id];

                    $offer_type = "normal";
                    if ($this->group_racial_composition[$group_name]['no_previous'] == 'Y') {
                        $offer_type = $this->check_race_previous_data($group_name, $race);
                        //echo $value['id']." - " .getProgramName($program_id)." - ".$offer_type."<BR>";//exit;
                    }

                    $total_seats = $this->group_racial_composition[$group_name]['total'];
                    $race_percent = $this->group_racial_composition[$group_name][$race];
                    $total_seats++;
                    $race_percent++;

                    $new_percent = number_format($race_percent * 100 / $total_seats, 2);


                    if ($this->check_all_race_range($group_name, $race, $value['id']) || in_array($offer_type, array('OnlyThisOffered', 'OfferedWaitlisted'))) {

                        $update = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $this->group_racial_composition[$group_name]['total'], $race, "N");
                        $update .= "<br>-----------<br>";
                        $this->group_racial_composition[$group_name]['total'] = $total_seats;
                        $this->group_racial_composition[$group_name][$race] = $race_percent;

                        $value['final_status'] = "Offered";

                        $offered_race = $race;
                        $offered_submission_id = $value['id'];
                        $value['update'] = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race);

                        $value['availability'] = $availabilityArray[$program_id][$value['next_grade']];

                        $firstOffered[] = $value['id'];
                        $offered = true;
                        $offer_program_id = $group_name;
                        $tmp_stock = $availabilityArray[$program_id][$value['next_grade']];
                        $tmp_stock--;
                        $availabilityArray[$program_id][$value['next_grade']] = $tmp_stock;

                        /*if(in_array($value['id'], $this->waitlistRaceArr))
                        {
                            $tmp_final_arr = [];
                            foreach($final_data as $flkey=>$flvalue)
                            {
                                if($flvalue['id'] == $value['id'] && $flvalue['choice'] != $value['choice'])
                                {
                                    $flvalue['final_status'] = "Waitlisted By Already Offered";
                                }
                                $tmp_final_arr[] = $flvalue;

                            }
                            if (($key_index = array_search($value['id'], $this->waitlistRaceArr)) !== false) {
                                unset($this->waitlistRaceArr[$key_index]);
                            }
                            $final_data = $tmp_final_arr;
                        }*/
                    } else {
                        $value['final_status'] = "Waitlist By Race";

                        $total_seats = $this->group_racial_composition[$group_name]['total'];
                        $value['update'] = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race, "W");
                        $value['availability'] = $availabilityArray[$program_id][$value['next_grade']];
                    }
                } else {
                    $value['final_status'] = "No Availability";
                }
            }

            $final_data[] = $value;
            if ($offered && in_array($offer_type, array('normal', 'OfferedWaitlisted'))) {
                $tmpArr = [];
                foreach ($final_data as $fkey => $fvalue) {
                    if ($fvalue['choice'] == 1)
                        $program_id = $fvalue['first_choice_program_id'];
                    else
                        $program_id = $fvalue['second_choice_program_id'];
                    $group_name = $this->program_group[$program_id];
                    if ($fvalue['final_status'] == "Waitlist By Race" && $offer_program_id == $group_name) { //1
                        if (!in_array($fvalue['id'], $firstOffered)) {
                            if (isset($availabilityArray[$program_id][$fvalue['next_grade']]) && $availabilityArray[$program_id][$fvalue['next_grade']] > 0) {

                                $race = strtolower($fvalue['race']);
                                $group_name = $this->program_group[$program_id];

                                $total_seats = $this->group_racial_composition[$group_name]['total'];
                                $race_percent = $this->group_racial_composition[$group_name][$race];
                                $total_seats++;
                                $race_percent++;
                                $new_percent = number_format($race_percent * 100 / $total_seats, 2);

                                if ($this->check_all_race_range($group_name, $race, $fvalue['id'])) {
                                    $this->group_racial_composition[$group_name]['total'] = $total_seats;
                                    $this->group_racial_composition[$group_name][$race] = $race_percent;

                                    $fvalue['final_status'] = "Offered";
                                    if (isset($fvalue['update']))
                                        $update = $fvalue['update'] . "<br>-----------<br>Offered ID: " . $offered_submission_id . " (" . $offered_race . ")<br>";
                                    else
                                        $update = "";
                                    $fvalue['update'] = $update . $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race);


                                    $firstOffered[] = $fvalue['id'];
                                    $fvalue['availability'] = $availabilityArray[$program_id][$fvalue['next_grade']];

                                    $tmp_stock = $availabilityArray[$program_id][$fvalue['next_grade']];
                                    $tmp_stock--;
                                    $availabilityArray[$program_id][$fvalue['next_grade']] = $tmp_stock;
                                } else {
                                    if (isset($fvalue['update']))
                                        $update = $fvalue['update'] . "<br>-----------<br>Offered ID: " . $offered_submission_id . " (" . $offered_race . ")<br>";
                                    else
                                        $update = "";
                                    $total_seats = $this->group_racial_composition[$group_name]['total'];
                                    $fvalue['update'] = $update . $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race, "W");
                                    $fvalue['availability'] = $availabilityArray[$program_id][$fvalue['next_grade']];
                                }
                            } else {
                                $fvalue['final_status'] = "No Availability";
                                $fvalue['availability'] = 0; //$availabilityArray[$program_id][$fvalue['next_grade']];

                            }
                        } else {
                            $fvalue['final_status'] = "Waitlist By Already Offered";
                        }
                    } //1
                    $tmpArr[] = $fvalue;
                }
                $tmpArr1 = [];
                foreach ($tmpArr as $tkey => $tvalue) {
                    if ($tvalue['final_status'] == "Waitlisted By Race" && in_array($tvalue['id'], $firstOffered)) {
                        $tvalue['final_status'] = "Waitlisted By Already Offered";
                    }
                    $tmpArr1[] = $tvalue;
                }
                $final_data = $tmpArr1;
            }
        }

        //        echo "" from here pending

        /* Here coding start for Late Submission With Active/Inactive Status */
        $submission_data = [];
        foreach ($req['application_program_id'] as $key => $value) {
            if ($req['awardslot' . $value] > 0) {
                $submissions = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where('submissions.district_id', Session::get('district_id'))->whereIn('submission_status', array('Active', 'Pending'))->get();

                foreach ($submissions as $sk => $sv) {
                    $insert = false;

                    if (in_array($sv->first_choice_program_id, $keys)) {
                        if (in_array($sv->next_grade, $process_program[$sv->first_choice_program_id])) {
                            $insert = true;
                        }
                    }
                    if (in_array($sv->second_choice_program_id, $keys)) {
                        if (in_array($sv->next_grade, $process_program[$sv->second_choice_program_id])) {
                            $insert = true;
                        }
                    }
                    if ($insert && !in_array($sv->id, $subids)) {
                        $submission_data[] = $sv;
                        $subids[] = $sv->id;
                    }
                }
            }
        }

        $submissions = $submission_data;


        $firstdata = $seconddata = array();
        $incomplete_arr = $failed_arr = $interview_arr = array();

        $programGrades = array();
        $committee_count = 0;
        foreach ($submissions as $key => $value) {
            $interview_passed = true;
            $composite_score = 0;
            if ($preliminary_score) {
                $interview_score = SubmissionInterviewScore::where("submission_id", $value->id)->first();
                if (!empty($interview_score)) {
                    if ($interview_score->data == 0) {
                        $interview_passed = false;
                    }
                } else
                    $interview_passed = false;

                generateCompositeScore($value->id);
                $rs = SubmissionCompositeScore::where("submission_id", $value->id)->first();
                if (!empty($rs)) {
                    $composite_score = $rs->score;
                }
            }
            if ($value->first_choice != "" && $value->second_choice != "") {
                $failed = false;
                $tmpfirstdata = [];
                $firstfailed = $firstincomplete = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);

                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null) {
                    $firstincomplete = true;
                    $incomplete = true;
                } elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice]) {
                    $tmp['committee_score_status'] = 'Pass';
                } elseif ($tmp['committee_score'] != "NA") {
                    $tmp['committee_score_status'] = 'Fail';
                    $firstfailed = true;
                    $failed = true;
                }
                $tmp['choice'] = 1;
                $tmp['composite_score'] = $composite_score;

                if ($value->submission_status == "Pending") {
                    $incomplete_arr[] = $tmp;
                    $firstincomplete = true;
                } elseif (!$interview_passed) {
                    $interview_arr[] = $tmp;
                } else {
                    if ($failed)
                        $failed_arr[] = $tmp;
                    elseif ($incomplete)
                        $incomplete_arr[] = $tmp;
                    else {
                        $tmpfirstdata = $tmp;
                    }
                }


                $failed = false;
                $incomplete = false;
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);

                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                }

                if ($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice]) {
                    $tmp['committee_score_status'] = 'Pass';
                } elseif ($tmp['committee_score'] != "NA") {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");


                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                $tmp['choice'] = 2;
                /*
                if($failed && !$firstfailed)
                {
                    $tmp['committee_score_status'] = "Fail";
                    $failed_arr[] = $tmp;
                }
                elseif($incomplete && !$firstincomplete)
                {
                    $tmp['committee_score_status'] = "NA";
                    $incomplete_arr[] = $tmp;
                }
                elseif(!$firstfailed && !$firstincomplete)
                {
                    $firstdata[$value->first_choice_program_id][] = $tmpfirstdata;
                    $seconddata[$value->second_choice_program_id][] = $tmp;
                }*/
                if (!$firstfailed && !$firstincomplete) {
                    $firstdata[$value->first_choice_program_id][] = $tmpfirstdata;
                }

                if ($value->submission_status == "Pending") {
                    $incomplete_arr[] = $tmp;
                } elseif (!$interview_passed) {
                    $interview_arr[] = $tmp;
                } else {
                    if ($failed) {
                        $failed_arr[] = $tmp;
                    } elseif ($incomplete) {
                        $incomplete_arr[] = $tmp;
                    } else {
                        $seconddata[$value->second_choice_program_id][] = $tmp;
                    }
                }
            } elseif ($value->first_choice != "") {
                $failed = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $tmp['composite_score'] = $composite_score;
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] = getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);
                if (!in_array($value->first_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice]) {
                    $tmp['committee_score_status'] = 'Pass';
                } elseif ($tmp['committee_score'] != "NA") {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['choice'] = 1;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");

                if ($value->submission_status == "Pending") {
                    $incomplete_arr[] = $tmp;
                } elseif (!$interview_passed) {
                    $interview_arr[] = $tmp;
                } else {
                    if ($failed)
                        $failed_arr[] = $tmp;
                    elseif ($incomplete)
                        $incomplete_arr[] = $tmp;
                    else
                        $firstdata[$value->first_choice_program_id][] = $tmp;
                }
            } else {
                $failed = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $tmp['composite_score'] = $composite_score;
                $tmp['test_scores'] = $test_scores;
                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['first_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);

                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");
                $tmp['magnet_employee'] = $value->mcp_employee;
                $tmp['magnet_program_employee'] = $value->magnet_program_employee;
                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);
                if (!in_array($value->second_choice_program_id, $committee_program_id)) {
                    $tmp['committee_score'] = "NA";
                } else {
                    $committee_count++;
                }

                if ($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif (!$interview_passed) {
                    $interview_arr[] = $tmp;
                } elseif (is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice]) {
                    $tmp['committee_score_status'] = 'Pass';
                } elseif ($tmp['committee_score'] != "NA") {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['choice'] = 2;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");

                if ($value->submission_status == "Pending") {
                    $incomplete_arr[] = $tmp;
                } else {

                    if ($failed)
                        $failed_arr[] = $tmp;
                    elseif ($incomplete)
                        $incomplete_arr[] = $tmp;
                    else
                        $seconddata[$value->second_choice_program_id][] = $tmp;
                }
            }
        }


        if ($type == "update") {
            foreach ($interview_arr as $flkey => $flvalue) {
                $insert = [];
                $insert['submission_id'] = $flvalue['id'];
                $insert['first_choice_final_status'] = "Denied due to Ineligibility";
                $insert['first_waitlist_for'] = $flvalue['first_choice_program_id'];
                $insert['first_choice_eligibility_reason'] = "Denied due to 0 Interview Score";
                if ($flvalue['second_choice_program_id'] != 0 && $flvalue['second_choice_program_id'] != "") {
                    $insert['second_waitlist_for'] = $flvalue['second_choice_program_id'];
                    $insert['second_choice_final_status'] = "Denied due to Ineligibility";
                    $insert['second_choice_eligibility_reason'] = "Denied due to 0 Interview Score";
                }
                $insert['enrollment_id'] = Session::get("enrollment_id");
                $insert['application_id'] = $application_id;
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $flvalue['id'], "version" => $actual_version], $insert);
            }

            foreach ($failed_arr as $flkey => $flvalue) {
                $committee_score_status = $flvalue['committee_score_status'];
                if ($committee_score_status == "Fail") {
                    $insert = [];
                    $insert['submission_id'] = $flvalue['id'];
                    $insert['first_choice_final_status'] = "Denied due to Ineligibility";
                    $insert['first_waitlist_for'] = $flvalue['first_choice_program_id'];
                    $insert['first_choice_eligibility_reason'] = "Denied due to Ineligible Records";
                    if ($flvalue['second_choice_program_id'] != 0 && $flvalue['second_choice_program_id'] != "") {
                        $insert['second_waitlist_for'] = $flvalue['second_choice_program_id'];
                        $insert['second_choice_final_status'] = "Denied due to Ineligibility";
                        $insert['second_choice_eligibility_reason'] = "Denied due to Ineligible Records";
                    }
                    $insert['enrollment_id'] = Session::get("enrollment_id");
                    $insert['application_id'] = $application_id;
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $flvalue['id'], "version" => $actual_version], $insert);
                }
            }

            foreach ($incomplete_arr as $flkey => $flvalue) {
                $insert = [];
                $insert['submission_id'] = $flvalue['id'];
                $insert['first_choice_final_status'] = "Denied due to Incomplete Records";
                $insert['first_waitlist_for'] = $flvalue['first_choice_program_id'];
                $insert['first_choice_eligibility_reason'] = "Incomplete Committee Score";
                if ($flvalue['second_choice_program_id'] != 0 && $flvalue['second_choice_program_id'] != "") {
                    $insert['second_waitlist_for'] = $flvalue['second_choice_program_id'];
                    $insert['second_choice_final_status'] = "Denied due to Incomplete Records";
                    $insert['second_choice_eligibility_reason'] = "Incomplete Committee Score";
                }
                $insert['application_id'] = $application_id;
                $insert['enrollment_id'] = Session::get("enrollment_id");
                $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $flvalue['id'], "version" => $actual_version], $insert);
            }
        }

        /* Sort all submission based on selection rank set 
        by each program array */
        $ids = array('PreK', 'K', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
        $firstPrgData = $secondPrgData = [];
        $loopArr = array("first", "second");
        if (!$processType) {
            $dataStoreArr = array("first", "first");
        } else {
            $dataStoreArr = array("first", "second");
        }


        $append_second = $append_first = [];
        foreach ($loopArr as $lkey => $lvalue) {
            $str = $lvalue . "data";
            $arrvar = ${$str};
            foreach ($arrvar as $key => $value) {

                $parray = $value;
                if (isset($programSortArr[$key])) {
                    $array = $value;
                    $sortingParams = [];
                    $first_column = "";
                    $i = 0;
                    foreach ($programSortArr[$key] as $pk => $pv) {
                        if ($i == 0)
                            $first_column = $pk;
                        $i++;
                    }

                    $first_col_arr = array_column($array, $first_column);

                    $tmpStr = $dataStoreArr[$lkey] . "PrgData";

                    if ($first_column != "committee_score" && $first_column != "combine_score" && (in_array($key, $committee_program_id) || $key == 19)) {
                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${"append_" . $lvalue}[] = $pv;
                        }

                        foreach ($programSortArr[$key] as $pk => $pv) {
                            if ($pk == "rating_priority") {
                                $sort_field = "rank";
                                $sort_type = SORT_ASC;
                            } else {
                                $sort_field = $pk;
                                $sort_type = SORT_DESC;
                            }


                            $sortingParams[] = array_column(${"append_" . $dataStoreArr[$lkey]}, $sort_field);
                            $sortingParams[] = $sort_type;
                        }
                        $sortingParams[] = &${"append_" . $dataStoreArr[$lkey]};
                        array_multisort(...$sortingParams);
                    } else {

                        foreach ($array as $pk => $pv) {
                            $pv['final_score'] = 0;
                            ${$tmpStr}[] = $pv;
                        }
                    }
                }
            }
        }
        $firstdata = $firstPrgData;
        if (!empty($firstdata)) {
            $committee_score  = $priority = $lottery_number = $choices = $next_grade = array();
            foreach ($firstdata as $key => $value) {
                try {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                } catch (\Exception $e) {
                    echo $value['id'];
                    exit;
                }
                $priority['rank'][] = $value['rank'];
                $lottery_number['lottery_number'][] = $value['lottery_number'];
                $choices['choice'][] = $value['choice'];
                //$next_grade['next_grade'][] = $value['next_grade'];

            }
            if (!$processType) {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $choices['choice'], SORT_ASC, $firstdata);
            } else {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $firstdata);
            }
        }
        if ($committee_count > 0) {
            $firstdata = array_merge($append_first, $firstdata);
            if (!$processType) {
                $firstdata = array_merge($firstdata, $append_second);
            }
        }

        if ($gradeWiseProcessing) {
            $tmp = [];
            foreach ($ids as $ik => $iv) {
                foreach ($firstdata as $fk => $fv) {
                    if ($fv['next_grade'] == $iv) {
                        $tmp[] = $fv;
                    }
                }
            }


            $firstdata = $tmp;
        }


        if ($processType) {
            $seconddata = $secondPrgData;
            if (!empty($seconddata)) {
                $committee_score  = $priority = $lottery_number = array();
                foreach ($seconddata as $key => $value) {
                    if ($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];
                    $priority['rank'][] = $value['rank'];
                    $lottery_number['lottery_number'][] = $value['lottery_number'];
                }
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $seconddata);
            }
            $seconddata = array_merge($seconddata, $append_second);
        } else {
            $seconddata = [];
        }



        $tmpAvailability = $availabilityArray;
        //        echo "<pre>";
        //        print_r($availabilityArray);exit;
        $waitlistOfferArray  = $offeredRank = $firstOffered = array();
        //$final_data = [];
        foreach ($firstdata as $key => $value) {
            if ($value['choice'] == 1)
                $program_id = $value['first_choice_program_id'];
            else
                $program_id = $value['second_choice_program_id'];

            $offered = false;
            $offered_race = "";
            $offered_submission_id = "";
            $offer_program_id = 0;
            if (in_array($value['id'], $firstOffered)) {
                $value['final_status'] = "Waitlist By Already Offered";
            } else {
                if (isset($availabilityArray[$program_id][$value['next_grade']]) && $availabilityArray[$program_id][$value['next_grade']] > 0) {
                    $race = strtolower($value['race']);
                    $group_name = $this->program_group[$program_id];

                    $offer_type = "normal";
                    if ($this->group_racial_composition[$group_name]['no_previous'] == 'Y') {
                        $offer_type = $this->check_race_previous_data($group_name, $race);
                        //echo $value['id']." - " .getProgramName($program_id)." - ".$offer_type."<BR>";//exit;
                    }

                    $total_seats = $this->group_racial_composition[$group_name]['total'];
                    $race_percent = $this->group_racial_composition[$group_name][$race];
                    $total_seats++;
                    $race_percent++;

                    $new_percent = number_format($race_percent * 100 / $total_seats, 2);


                    if ($this->check_all_race_range($group_name, $race, $value['id']) || in_array($offer_type, array('OnlyThisOffered', 'OfferedWaitlisted'))) {

                        $update = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $this->group_racial_composition[$group_name]['total'], $race, "N");
                        $update .= "<br>-----------<br>";
                        $this->group_racial_composition[$group_name]['total'] = $total_seats;
                        $this->group_racial_composition[$group_name][$race] = $race_percent;

                        $value['final_status'] = "Offered";

                        $offered_race = $race;
                        $offered_submission_id = $value['id'];
                        $value['update'] = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race);

                        $value['availability'] = $availabilityArray[$program_id][$value['next_grade']];

                        $firstOffered[] = $value['id'];
                        $offered = true;
                        $offer_program_id = $group_name;
                        $tmp_stock = $availabilityArray[$program_id][$value['next_grade']];
                        $tmp_stock--;
                        $availabilityArray[$program_id][$value['next_grade']] = $tmp_stock;

                        /*if(in_array($value['id'], $this->waitlistRaceArr))
                        {
                            $tmp_final_arr = [];
                            foreach($final_data as $flkey=>$flvalue)
                            {
                                if($flvalue['id'] == $value['id'] && $flvalue['choice'] != $value['choice'])
                                {
                                    $flvalue['final_status'] = "Waitlisted By Already Offered";
                                }
                                $tmp_final_arr[] = $flvalue;

                            }
                            if (($key_index = array_search($value['id'], $this->waitlistRaceArr)) !== false) {
                                unset($this->waitlistRaceArr[$key_index]);
                            }
                            $final_data = $tmp_final_arr;
                        }*/
                    } else {
                        $value['final_status'] = "Waitlist By Race";

                        $total_seats = $this->group_racial_composition[$group_name]['total'];
                        $value['update'] = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race, "W");
                        $value['availability'] = $availabilityArray[$program_id][$value['next_grade']];
                    }
                } else {
                    $value['final_status'] = "No Availability";
                }
            }

            $final_data[] = $value;
            if ($offered && in_array($offer_type, array('normal', 'OfferedWaitlisted'))) {
                $tmpArr = [];
                foreach ($final_data as $fkey => $fvalue) {
                    if ($fvalue['choice'] == 1)
                        $program_id = $fvalue['first_choice_program_id'];
                    else
                        $program_id = $fvalue['second_choice_program_id'];
                    $group_name = $this->program_group[$program_id];
                    if ($fvalue['final_status'] == "Waitlist By Race" && $offer_program_id == $group_name) { //1
                        if (!in_array($fvalue['id'], $firstOffered)) {
                            if (isset($availabilityArray[$program_id][$fvalue['next_grade']]) && $availabilityArray[$program_id][$fvalue['next_grade']] > 0) {

                                $race = strtolower($fvalue['race']);
                                $group_name = $this->program_group[$program_id];

                                $total_seats = $this->group_racial_composition[$group_name]['total'];
                                $race_percent = $this->group_racial_composition[$group_name][$race];
                                $total_seats++;
                                $race_percent++;
                                $new_percent = number_format($race_percent * 100 / $total_seats, 2);

                                if ($this->check_all_race_range($group_name, $race, $fvalue['id'])) {
                                    $this->group_racial_composition[$group_name]['total'] = $total_seats;
                                    $this->group_racial_composition[$group_name][$race] = $race_percent;

                                    $fvalue['final_status'] = "Offered";
                                    if (isset($fvalue['update']))
                                        $update = $fvalue['update'] . "<br>-----------<br>Offered ID: " . $offered_submission_id . " (" . $offered_race . ")<br>";
                                    else
                                        $update = "";
                                    $fvalue['update'] = $update . $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race);


                                    $firstOffered[] = $fvalue['id'];
                                    $fvalue['availability'] = $availabilityArray[$program_id][$fvalue['next_grade']];

                                    $tmp_stock = $availabilityArray[$program_id][$fvalue['next_grade']];
                                    $tmp_stock--;
                                    $availabilityArray[$program_id][$fvalue['next_grade']] = $tmp_stock;
                                } else {
                                    if (isset($fvalue['update']))
                                        $update = $fvalue['update'] . "<br>-----------<br>Offered ID: " . $offered_submission_id . " (" . $offered_race . ")<br>";
                                    else
                                        $update = "";
                                    $total_seats = $this->group_racial_composition[$group_name]['total'];
                                    $fvalue['update'] = $update . $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race, "W");
                                    $fvalue['availability'] = $availabilityArray[$program_id][$fvalue['next_grade']];
                                }
                            } else {
                                $fvalue['final_status'] = "No Availability";
                                $fvalue['availability'] = 0; //$availabilityArray[$program_id][$fvalue['next_grade']];

                            }
                        } else {
                            $fvalue['final_status'] = "Waitlist By Already Offered";
                        }
                    } //1
                    $tmpArr[] = $fvalue;
                }
                $tmpArr1 = [];
                foreach ($tmpArr as $tkey => $tvalue) {
                    if ($tvalue['final_status'] == "Waitlisted By Race" && in_array($tvalue['id'], $firstOffered)) {
                        $tvalue['final_status'] = "Waitlisted By Already Offered";
                    }
                    $tmpArr1[] = $tvalue;
                }
                $final_data = $tmpArr1;
            }
        }



        if ($type == "update") {
            SubmissionsTmpFinalStatus::truncate();
            SubmissionsSelectionReportMaster::where("application_id", $application_id)->where("type", "late_submission")->where("version", $actual_version)->delete();
            SubmissionsRaceCompositionReport::where("application_id", $application_id)->where("type", "late_submission")->where("version", $actual_version)->delete();

            $first_offer = $second_offer = [];
            foreach ($final_data as $key => $value) {
                if ($value['final_status'] == "Offered") {
                    $insert = [];
                    do {
                        $code = mt_rand(100000, 999999);
                        $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
                        $user_code1 = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                        $user_code2 = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();
                    } while (!empty($user_code));

                    if ($value['choice'] == 1) {
                        $insert['first_waitlist_for'] = $value['first_choice_program_id'];
                        $insert['first_choice_final_status'] = "Offered";
                        $awarded_school = getProgramName($insert['first_waitlist_for']);
                        $insert['first_choice_eligibility_reason'] = "";

                        $tmpData = [];
                        $tmpData['submission_id'] = $value['id'];
                        $tmpData['choice_type'] = "first";
                        $tmpData['status'] = "Offered";
                        $tmpData['offer_slug'] = $code;
                        $tmpData['reason'] = "";
                        $rs = SubmissionsTmpFinalStatus::create($tmpData);
                    } else {
                        $insert['second_waitlist_for'] = $value['second_choice_program_id'];
                        $insert['second_choice_final_status'] = "Offered";
                        $awarded_school = getProgramName($insert['second_waitlist_for']);
                        $insert['second_choice_eligibility_reason'] = "";
                        $tmpData = [];
                        $tmpData['submission_id'] = $value['id'];
                        $tmpData['choice_type'] = "second";
                        $tmpData['status'] = "Offered";
                        $tmpData['offer_slug'] = $code;
                        $tmpData['reason'] = "";
                        $rs = SubmissionsTmpFinalStatus::create($tmpData);
                    }

                    $insert['submission_id'] = $value['id'];
                    $insert['offer_slug'] = $code;
                    $insert['application_id'] = $application_id;
                    $insert['enrollment_id'] = Session::get("enrollment_id");
                    $rs = Submissions::where("id", $value['id'])->update(array("awarded_school" => $awarded_school));


                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $actual_version], $insert);
                } else {
                    $insert = [];
                    if ($value['choice'] == 1) {
                        $insert['first_waitlist_for'] = $value['first_choice_program_id'];
                        $insert['first_choice_final_status'] = "Waitlisted";
                        $insert['first_choice_eligibility_reason'] = "";
                        $tmpData = [];
                        $tmpData['submission_id'] = $value['id'];
                        $tmpData['choice_type'] = "first";
                        $tmpData['status'] = "Waitlisted";
                        $tmpData['reason'] = "";
                        $rs = SubmissionsTmpFinalStatus::create($tmpData);
                    } else {
                        $insert['second_waitlist_for'] = $value['second_choice_program_id'];
                        $insert['second_choice_final_status'] = "Waitlisted";
                        $insert['second_choice_eligibility_reason'] = "";
                        $tmpData = [];
                        $tmpData['submission_id'] = $value['id'];
                        $tmpData['choice_type'] = "second";
                        $tmpData['status'] = "Waitlisted";
                        $tmpData['reason'] = "";
                        $rs = SubmissionsTmpFinalStatus::create($tmpData);
                    }
                    $insert['submission_id'] = $value['id'];
                    $insert['application_id'] = $application_id;
                    $insert['enrollment_id'] = Session::get("enrollment_id");
                    $rs = LateSubmissionFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version" => $actual_version], $insert);
                }
                $reportData = [];
                $reportData['submission_id'] = $value['id'];
                $reportData['application_id'] = $application_id;
                $reportData['first_choice'] = $value['first_program'];
                $reportData['second_choice'] = $value['second_program'];
                $reportData['priority'] = $value['rank'];
                $reportData['committee_score'] = $value['committee_score'];
                $reportData['composite_score'] = $value['composite_score'];
                $reportData['type'] = "late_submission";
                $reportData['version'] = $actual_version;
                $reportData['enrollment_id'] = Session::get("enrollment_id");
                if (!$preliminary_score)
                    $reportData['score_type'] = "composite_score";
                else
                    $reportData['score_type'] = "committee_score";
                $reportData['final_status'] = $value['final_status'];
                $reportData['race_composition_update'] = (isset($value['update']) ? $value['update'] : "");
                $reportData['available_seats'] = (isset($value['availability']) ? $value['availability'] : 0);
                $rp = SubmissionsSelectionReportMaster::create($reportData);
                //1023
            }

            $rsUpdate = LateSubmissionFinalStatus::where("first_choice_final_status", "Offered")->where("application_id", $application_id)->where('version', $actual_version)->where("second_choice_final_status", "Waitlisted")->get();
            foreach ($rsUpdate as $ukey => $uvalue) {
                $rs = SubmissionsTmpFinalStatus::where("submission_id", $uvalue->submission_id)->where("choice_type", "second")->update(["status" => "Pending"]);
            }


            $rsUpdate = LateSubmissionFinalStatus::where("first_choice_final_status", "Offered")->where("application_id", $application_id)->where('version', $actual_version)->where("second_choice_final_status", "Waitlisted")->update(array("second_choice_final_status" => "Pending", "second_waitlist_for" => 0));
            $data = $this->group_racial_composition;
            foreach ($data as $key => $value) {
                $composition_data = [];
                $composition_data['application_id'] = $application_id;
                $composition_data['enrollment_id'] = Session::get("enrollment_id");
                $composition_data['program_group'] = $key;
                $composition_data['type'] = 'waitlist';
                $composition_data['black'] = $value['black'];
                $composition_data['white'] = $value['white'];
                $composition_data['other'] = $value['other'];
                $composition_data['version'] = $actual_version;
                $rs = SubmissionsRaceCompositionReport::updateOrCreate(["application_id" => $application_id, "program_group" => $key, "type" => "late_submission", "version" => $actual_version], $composition_data);
            }
            return true; //echo "done";
            //exit;
        } else {
            $group_racial_composition = $this->group_racial_composition;
            return view("ProcessSelection::test_index", compact("final_data", "incomplete_arr", "failed_arr", "group_racial_composition", "preliminary_score"));
        }
    }


    public function check_all_race_range($group_name, $race, $id)
    {

        $tmp_enroll = $this->enrollment_race_data[$group_name];
        $tmp = $this->group_racial_composition[$group_name];
        $total_seats = $tmp['total'];
        $race_percent = $tmp[$race];
        if ($total_seats > 0)
            $original_race_percent = number_format($race_percent * 100 / $total_seats, 2);
        else
            $original_race_percent = 0;
        $total_seats++;
        $race_percent++;
        $new_percent = number_format($race_percent * 100 / $total_seats, 2);

        /*if($group_name == "Academy of Science and Foreign Language - K")
        {
            if($id == 1672)
            {
                echo "<pre>";
                print_r($tmp);
                echo "<pre>";
                print_r($tmp_enroll);
                echo "<Pre>";
                echo $new_percent;exit;
            }
        }*/


        if ($new_percent >= $tmp_enroll[$race]['min'] && $new_percent <= $tmp_enroll[$race]['max']) {
            $in_range = true;
            $max = 0;
            foreach ($tmp as $key => $value) {
                if ($key != $race && $key != "total" && $key != "no_previous") {
                    $total = $tmp['total'] + 1;
                    $new_percent = number_format($value * 100 / $total, 2);
                    if ($new_percent < $tmp_enroll[$key]['min']) {
                        $in_range = false;
                    } elseif ($new_percent > $tmp_enroll[$key]['max']) {
                        $in_range = false;
                        $max++;
                    }
                }
            }

            if (!$in_range) {
                if ($max > 0)
                    return true;
                else
                    return false;
            } else {
                return true;
            }
        } else {
            if ($original_race_percent < $tmp_enroll[$race]['min'])
                return true;
            else
                return false;
        }
    }


    public function get_additional_info($application_id = 0, $version = 0)
    {
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "late_submission")->where("version", $version)->first();

        $display_outcome = 0;
        $displayother = 0;

        if (!empty($process_selection)) {
            $displayother = 1;

            if ($process_selection->commited == "Yes") {
                $display_outcome = 1;
                $last_date_online_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_online_acceptance));
                $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_offline_acceptance));
            } else {
                $last_date_online_acceptance = "";
                $last_date_offline_acceptance = "";
            }
        } else {
            $last_date_online_acceptance = "";
            $last_date_offline_acceptance = "";
        }

        return array("display_outcome" => $display_outcome, "displayother" => $displayother, "last_date_online_acceptance" => $last_date_online_acceptance, "last_date_offline_acceptance" => $last_date_offline_acceptance);
    }

    public function generate_race_composition_update($group_data, $total_seats, $race, $type = "S")
    {
        $update = "";
        $tst = $group_data;
        $total_seats = $tst['total'];
        foreach ($tst as $tstk => $tstv) {
            if ($tstk != "total" && $tstk != "no_previous") {
                if ($tstv > 0)
                    $tst_percent = number_format($tstv * 100 / $total_seats, 2);
                else
                    $tst_percent = 0;
                if ($tstk == $race) {
                    if ($type == "W")
                        $clname = "text-danger";
                    elseif ($type == "S")
                        $clname = "text-success";
                    else
                        $clname = "";
                } else
                    $clname = "";
                $update .= "<div><span><strong>" . ucfirst($tstk) . "</strong> :</span> <span class='" . $clname . "'>" . $tst_percent . "% (" . $tstv . ")</span></div>";
            }
        }
        return $update;
    }


    public function selection_accept(Request $request, $application_id)
    {

        $form_id = 1;
        $district_id = \Session('district_id');

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("commited", "No")->where("type", "late_submission")->orderBy("created_at", "DESC")->first();
        $update_id = $rs->id;
        $version = $rs->version;

        $data = LateSubmissionFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->get();
        foreach ($data as $key => $value) {
            $status = $value->first_choice_final_status;
            if ($value->second_choice_final_status == "Offered")
                $status = "Offered";

            if ($value->first_choice_final_status == "Pending")
                $status = $value->second_choice_final_status;

            $submission_id = $value->submission_id;
            $rs = Submissions::where("id", $submission_id)->select("submission_status")->first();
            $old_status = $rs->submission_status;

            $comment = "By Accept and Commit Event";
            if ($status == "Offered") {
                $submission = Submissions::where("id", $value->submission_id)->first();
                if ($value->first_choice_final_status == "Offered") {
                    $program_name = getProgramName($submission->first_choice_program_id);
                } else if ($value->second_choice_final_status == "Offered") {
                    $program_name = getProgramName($submission->second_choice_program_id);
                } else {
                    $program_name = "";
                }

                $program_name .= " - Grade " . $submission->next_grade;
                $comment = "System has Offered " . $program_name . " to Parent";
            } else if ($status == "Denied due to Ineligibility") {
                if ($value->first_choice_eligibility_reason != '') {
                    if ($value->first_choice_eligibility_reason == "Both") {
                        $comment = "System has denied the application because of Grades and CDI Ineligibility";
                    } else if ($value->first_choice_eligibility_reason == "Grade") {
                        $comment = "System has denied the application because of Grades Ineligibility";
                    } else {
                        $comment = $value->first_choice_eligibility_reason;
                    }
                }
            } else if ($status == "Denied due to Incomplete Records") {
                if ($value->incomplete_reason != '') {
                    if ($value->incomplete_reason == "Both") {
                        $comment = "System has denied the application because of Grades and CDI Ineligibility";
                    } else if ($value->incomplete_reason == "Grade") {
                        $comment = "System has denied the application because of Incomplete Grades";
                    } else {
                        $comment = "System has denied the application because of Incomplete Records";
                    }
                }
            }
            $rs = SubmissionsStatusLog::create(array("submission_id" => $submission_id, "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id, "comment" => "Waitlist Process :: " . $comment));
            $rs = LateSubmissionsStatusUniqueLog::updateOrCreate(["submission_id" => $submission_id], array("submission_id" => $submission_id, "new_status" => $status, "old_status" => $old_status, "updated_by" => Auth::user()->id, "version" => $version));
            $rs = Submissions::where("id", $submission_id)->update(["submission_status" => $status]);
        }

        $rs = SubmissionsTmpFinalStatus::get();
        foreach ($rs as $key => $value) {
            $rs1 = Submissions::where("id", $value->submission_id)->first();
            $var = $value->choice_type . "_choice_program_id";
            $program_id = $rs1->{$var};
            if ($value->offer_slug != '')
                $rsupdate = SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $value->submission_id], array("submission_id" => $value->submission_id,  "application_id" => $application_id, "enrollment_id" => Session::get("enrollment_id"), $value->choice_type . "_choice_final_status" => $value->status, $value->choice_type . "_choice_eligibility_reason" => $value->reason, $value->choice_type . "_waitlist_for" => $program_id, "offer_slug" => $value->offer_slug));
            else
                $rsupdate = SubmissionsLatestFinalStatus::updateOrCreate(["submission_id" => $value->submission_id], array("submission_id" => $value->submission_id, "application_id" => $application_id, "enrollment_id" => Session::get("enrollment_id"), $value->choice_type . "_choice_final_status" => $value->status, $value->choice_type . "_choice_eligibility_reason" => $value->reason,  $value->choice_type . "_waitlist_for" => $program_id,));
        }
        $rs = ProcessSelection::where("id", $update_id)->update(array("commited" => "Yes"));
        echo "Done";
        exit;
    }

    public function checkWailistOpen()
    {
        $rs = LateSubmissionProcessLogs::where("last_date_online", ">", date("Y-m-d H:i:s"))->first();
        if (!empty($rs))
            return 1;
        else
            return 0;
    }


    public function selection_revert()
    {
        $version = $this->checkWailistOpen();
        $quotations = LateSubmissionsStatusUniqueLog::orderBy('created_at', 'ASC')->where("version", $version)
            ->get()
            ->unique('submission_id');

        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_late_submission_online_acceptance")->delete();
        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_late_submission_offline_acceptance")->delete();


        foreach ($quotations as $key => $value) {
            $rs = Submissions::where("id", $value->submission_id)->update(array("submission_status" => $value->old_status));
        }
        LateSubmissionsStatusUniqueLog::where("version", $version)->delete();
        LateSubmissionFinalStatus::where("version", $version)->delete();
        LateSubmissionProcessLogs::where("version", $version)->delete();
        LateSubmissionAvailabilityLog::truncate();
        LateSubmissionAvailabilityProcessLog::where("version", $version)->where("type", "Late Submission")->delete();
        //SubmissionsStatusUniquesLog::truncate();

    }


    public function get_offer_count($program_id, $grade, $district_id, $form_id)
    {
        $offer_count = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))->where('district_id', $district_id)->where('form_id', $form_id)->where(function ($q) use ($program_id, $grade) {
            $q->where(function ($q1)  use ($program_id, $grade) {
                $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $program_id)->where('next_grade', $grade);
            })->orWhere(function ($q1) use ($program_id, $grade) {
                $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $program_id)->where('next_grade', $grade);
            });
        })->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")->count();


        $offer_count1 = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))->where('district_id', $district_id)->where('form_id', $form_id)->where(function ($q) use ($program_id, $grade) {
            $q->where(function ($q1)  use ($program_id, $grade) {
                $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $program_id)->where('next_grade', $grade);
            })->orWhere(function ($q1) use ($program_id, $grade) {
                $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $program_id)->where('next_grade', $grade);
            });
        })->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->count();

        $offer_count2 = Submissions::where('submissions.enrollment_id', Session::get('enrollment_id'))->where('district_id', $district_id)->where('form_id', $form_id)->where(function ($q) use ($program_id, $grade) {
            $q->where(function ($q1)  use ($program_id, $grade) {
                $q1->where('first_choice_final_status', 'Offered')->where('first_offer_status', 'Accepted')->where('first_choice_program_id', $program_id)->where('next_grade', $grade);
            })->orWhere(function ($q1) use ($program_id, $grade) {
                $q1->where('second_choice_final_status', 'Offered')->where('second_offer_status', 'Accepted')->where('second_choice_program_id', $program_id)->where('next_grade', $grade);
            });
        })->join("late_submissions_final_status", "late_submissions_final_status.submission_id", "submissions.id")->count();
        return $offer_count + $offer_count1 + $offer_count2;
    }
}
