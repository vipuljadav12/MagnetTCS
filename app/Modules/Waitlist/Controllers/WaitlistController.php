<?php

namespace App\Modules\Waitlist\Controllers;

use Illuminate\Http\Request;
use App\Modules\School\Models\Grade;
use App\Http\Controllers\Controller;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\{Program,ProgramEligibility,ProgramGradeMapping};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Enrollment\Models\{Enrollment,EnrollmentRaceComposition,ADMData};
use App\Modules\Application\Models\Application;
use App\Modules\ProcessSelection\Models\{Availability,ProgramSwingData,PreliminaryScore,ProcessSelection};
use App\Modules\SetAvailability\Models\WaitlistAvailability;
use App\Modules\Waitlist\Models\{WaitlistProcessLogs,WaitlistAvailabilityLog,WaitlistAvailabilityProcessLog,WaitlistIndividualAvailability};
use App\Modules\LateSubmission\Models\{LateSubmissionProcessLogs,LateSubmissionAvailabilityLog,LateSubmissionAvailabilityProcessLog,LateSubmissionIndividualAvailability};
use App\Modules\Submissions\Models\{Submissions,SubmissionGrade,SubmissionConductDisciplinaryInfo,SubmissionsFinalStatus,SubmissionsWaitlistFinalStatus,SubmissionsStatusLog,SubmissionsWaitlistStatusUniqueLog,SubmissionsSelectionReportMaster,SubmissionsRaceCompositionReport,SubmissionsLatestFinalStatus,SubmissionsTmpFinalStatus,LateSubmissionFinalStatus};
use App\Modules\School\Models\School;

use Auth;
use DB;
use Session;
use Config;

class WaitlistController extends Controller
{

    //public $eligibility_grade_pass = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $group_racial_composition = array();
    public $program_group = array();
    public $enrollment_race_data = array();
    public $waitlistRaceArr = array();
    public $offered_ids = array();
    public $magnet_offered_data = array();
    public $adm_data = array();
    public $availabilityArray = array();
    public $magnet_thresold_limit = array();
    public $sort_arr = array();

    public function validateApplication($application_id)
    {
        $rs = Submissions::where("form_id", $application_id)->where("submission_status", "Offered")->count();
        if($rs > 0)
            echo "Selected Applications has still open offered submissions.";
        else
            echo "OK";
    }

    public function application_index()
    {
        $selection = "";
        $applications = Form::where("status","y")->get();
        return view("Waitlist::application_index", compact("applications", "selection"));
    }

    public function selection_application_index()
    {
        $selection = "Y";
        $applications = Form::where("status","y")->get();
        return view("Waitlist::application_index", compact("applications", "selection"));

    }

    public function index($application_id=0)
    {
    	$displayother = 0;

    	$rs = $exist_process = ProcessSelection::where("last_date_online_acceptance", ">", date("Y-m-d H:i:s"))->where("form_id", $application_id)->where('type', 'waitlist')->where("enrollment_id", Session::get("enrollment_id"))->orderBy("created_at", "DESC")->first();
        $display_outcome = $displayother = 0;

        $updated_id = 0;
        $version = 0;

        $last_date_online_acceptance = $last_date_offline_acceptance = "";
        if(!empty($rs))
        {
//            dd($rs);
            $displayother = 1;
            $version = $rs->version;

            $displayother = SubmissionsWaitlistFinalStatus::where("version", $version)->count();
                
		    if($rs->commited == "Yes")
            {
		        $display_outcome = 1;
                $updated_id = $rs->id;
                

            }
            else
            {
                $last_date_online_acceptance = "";
                $last_date_offline_acceptance = "";

            }
            $last_date_online_acceptance = date('m/d/Y H:i', strtotime($rs->last_date_online_acceptance));
            $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($rs->last_date_offline_acceptance));
        }
        else
        {

            $rs = $exist_process = ProcessSelection::where("form_id", $application_id)->where("enrollment_id", Session::get("enrollment_id"))->where('type', 'waitlist')->orderBy("created_at", "DESC")->where("commited", "No")->first();
            if(!empty($rs))
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

        foreach($pvalues as $key=>$value)
        {
        	$tmp_val_arr = [];
        	foreach($program_group as $pk=>$pv)
        	{
    		    $isMagnetEnabledProgram = Program::where('id', $pk)->where('process_logic', 'Magnet')->first()->id ?? NULL;
                $program_name = getProgramName($pk);
                $program_data = Program::where("id", $pk)->first();
                $grade_lavel = explode(",", $program_data->grade_lavel);
                if($program_name == $value)
                {
                    foreach($grade_lavel as $gval)
                    {
                        $pdata = [];
                        $pdata['id'] = $pk;
                        $pdata['grade'] = $gval;
                        $rs_availability = Availability::where("program_id", $pk)->where("enrollment_id", Session::get("enrollment_id"))->where("grade", $gval)->first();
                        $pdata['withdrawn_allowed'] = "Yes";
                        if(!empty($rs_availability))
                        {
                            if($rs_availability->white_seats == 0 && $rs_availability->white_seats == 0 && $rs_availability->other_seats == 0)
                            {
                                $pdata['withdrawn_allowed'] = "No";
                            }
                        }
                        $pdata['name'] = $program_name . " - Grade ".$gval;
                        $pdata['waitlist_count'] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_waitlist_count($application_id, $pk, $gval);
                        $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $pk, $gval);


                        // if($_SERVER['REMOTE_ADDR'] == '49.36.64.176')
                        // {
                        //     if($pk == 24 && $gval == "4")
                        //     {
                        //         dd($data);
                        //     }
                        // }    
                        $homezone = 0;
                        
                        $dt  = WaitlistProcessLogs::where("process_selection.enrollment_id", Session::get("enrollment_id"))->where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->get();
                        foreach($dt as $k=>$v)
                        {
                            
                            if($v->homezone != '')
                            {
                                 $hData = json_decode($v->homezone);

                                foreach($hData as $dk=>$dv)
                                {
                                    $homezone += $dv;
                                }

                            }
                        }
                        
                        $pdata['available_count'] = $pdata['available_slot'] = $homezone + $data['available_seats']-$data['offered_seats'];

                            //  if($pk == 24 && $gval == "4")
                            // {
                            //     dd($homezone, $data['available_seats'], $pdata);
                            // }

                        if($pdata['available_slot'] < 0)
                            $pdata['available_slot'] = 0;


                        $additional = WaitlistProcessLogs::where("process_selection.enrollment_id", Session::get("enrollment_id"))->where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum('additional_seats');

                        

                        $pdata['total_seats'] = $data['available_seats'] + $additional + $homezone;


                         
                        $pdata['withdrawn_student'] = "No";
                        $pdata['black_withdrawn'] = 0;
                        $pdata['white_withdrawn'] = 0;
                        $pdata['other_withdrawn'] = 0;
                        $pdata['additional_seats'] = 0;
                        $pdata['visible'] = "N";
                        $black = $white = $other = $black1 = $white1 = $other1 = 0;
                        if(!empty($exist_process))
                        {
                            $tmp_data = WaitlistProcessLogs::where("process_log_id", $exist_process->id)->where("program_id", $pk)->where("grade", $gval)->first();

                            if(!empty($tmp_data))
                            {
                                
                                $pdata['visible'] = "Y";
                                $pdata['withdrawn_student'] = $tmp_data->withdrawn_student;
                                $pdata['black_withdrawn'] = $tmp_data->black_withdrawn;
                                $pdata['white_withdrawn'] = $tmp_data->white_withdrawn;
                                $pdata['other_withdrawn'] = $tmp_data->other_withdrawn;
                                $pdata['available_slot'] = $tmp_data->slots_to_awards;
                                $pdata['additional_seats'] = $tmp_data->additional_seats;
                                //$pdata['available_count'] = $tmp_data->available_slots;
                                $pdata['homezone_val'] = json_decode($tmp_data->homezone, true);
                                $pdata['available_count']  +=  $pdata['black_withdrawn'] + $pdata['white_withdrawn'] + $pdata['other_withdrawn'] + $pdata['additional_seats'];
                                $pdata['available_slot'] = $pdata['available_count'];
                            }
                        }
                        else
                        {

                            $black = WaitlistProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                            $white = WaitlistProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                            $other = WaitlistProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

                            $black1 = LateSubmissionProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                            $white1 = LateSubmissionProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                            $other1 = LateSubmissionProcessLogs::where("program_id", $pk)->where("grade", $gval)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

                            //$pdata['available_count']  += $black + $black1 + $white1 + $white + $other + $other1;
                            $pdata['available_slot'] = $pdata['available_count'];
                            ///
                        }

                            //  if($pk == 24 && $gval == "4")
                            // {
                            //     dd($pdata);
                            // }
                        
                        $rs = Grade::where("name", $gval)->select("id")->first();
                        $application_program_id = ProgramGradeMapping::where("program_id", $pk)->where("grade", $gval)->first();
                        if(!empty($application_program_id))
                        {
                            // home zone schools
                            if ($isMagnetEnabledProgram) {
                                $pdata['homezone_schools'] = School::where('district_id', session('district_id'))
                                    ->where('magnet', 'No')
                                    ->where('status','Y')
                                    ->whereRaw("find_in_set('".$pdata['grade']."',grade_id)")
                                    ->pluck('name')
                                    ->toArray();
                            } else {
                                $pdata['homezone_schools'] = [];
                            }
                            $pdata['application_program_id'] = $application_program_id->id;
                            $tmp_val_arr[] = $pdata;
                        }

                    }
                }

        	}
        	$disp_arr[$value] = $tmp_val_arr;
        }

// dd($disp_arr);
    // $display_outcome = 1;$updated_id=4; 
       
        $waitlist_process_logs = [];
        if($display_outcome == 1)
        {
            $waitlist_process_logs = WaitlistProcessLogs::where("process_log_id", $updated_id)->orderBy("id", "DESC")->get();
        }
        else
        {
            //$last_date_online_acceptance = $last_date_offline_acceptance = "";
        }

        $actual_version = $version;
        return view("Waitlist::all_availability_index", compact("application_id", "disp_arr", "display_outcome", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance", "waitlist_process_logs", "version","actual_version"));
    }


    /* Seat Status functions */
    public function seatStatus($enrollment_id = 0)
    {
        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $enrollment = Enrollment::where("district_id", Session::get('district_id'))->get();
        $district_id = Session::get("district_id");
        $submissions = Submissions::where("enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->orderByRaw('FIELD(next_grade,'.implode(",",$ids).')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade']);


        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        $prgCount = array();;
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if($value->$choice != 0)
                    {
                        if (!isset($programs[$value->$choice])) {
                            $programs[$value->$choice] = [];
                        }
                        if (!in_array($value->next_grade, $programs[$value->$choice])) {
                            array_push($programs[$value->$choice], $value->next_grade);
                        }
                    }
                }
            }
        }

        ksort($programs);
        $final_data = array();
        foreach($programs as $key=>$value)
        {
            foreach($value as $ikey=>$ivalue)
            {
                $tmp = array();
                $tmp['program_name'] = getProgramName($key) ." - Grade ".$ivalue;
                $rs = Availability::where("program_id", $key)->where("grade", $ivalue)->select("available_seats")->first();
                $tmp['total_seats'] = $rs->available_seats;
                $tmp['total_applicants'] = Submissions::where('district_id', $district_id)->where(function($query) use ($key){
                    $query->where('first_choice_program_id', $key);
                    $query->orWhere('second_choice_program_id', $key);
                })->where('next_grade', $ivalue)->get()->count();

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Offered")
                                  ->where("first_choice_program_id", $key)
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                                  ->where("second_choice_program_id", $key)
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $tmp['offered'] = $rs1 + $rs2;


                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Declined due to Eligibility")
                                  ->where("first_choice_program_id", $key)
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Declined due to Eligibility")
                                  ->where("second_choice_program_id", $key)
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $tmp['noteligible'] = $rs1 + $rs2;

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Denied Due To Incomplete Records")
                                  ->where("first_choice_program_id", $key)
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Denied Due To Incomplete Records")
                                  ->where("second_choice_program_id", $key)
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $tmp['Incomplete'] = $rs1 + $rs2;

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Offered")
                                  ->where("first_choice_program_id", $key)
                                  ->where("first_offer_status", 'Declined')
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                                  ->where("second_choice_program_id", $key)
                                  ->where("second_offer_status", 'Declined')
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $tmp['Decline'] = $rs1 + $rs2;

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Offered")
                                  ->where("first_choice_program_id", $key)
                                  ->where("first_offer_status", 'Declined & Waitlisted')
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                                  ->where("second_choice_program_id", $key)
                                  ->where("second_offer_status", 'Declined & Waitlisted')
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $tmp['Waitlisted'] = $rs1 + $rs2;

                $rs1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("first_choice_final_status", "Offered")
                                  ->where("first_choice_program_id", $key)
                                  ->where("first_offer_status", 'Accepted')
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                                  ->where("second_choice_program_id", $key)
                                  ->where("second_offer_status", 'Accepted')
                                  ->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)
                            ->get()->count();
                $tmp['Accepted'] = $rs1 + $rs2;

                $tmp['remaining'] = $tmp['total_seats'] - $tmp['Accepted'];
                $final_data[] = $tmp;

            }

        }

        //print_r($final_data);exit;
        return view("Reports::seats_status",compact("enrollment_id", "enrollment", "final_data"));
    }


    public function seatStatusVersion($id = 0)
    {
        $rs = ProcessSelection::where("id", $id)->first();
        $application_id = $rs->application_id;
        $version = $rs->version;

        $version_data = $rs;
        $selected_programs = explode(",", $rs->selected_programs);
        
        $program_ids = [];
        foreach($selected_programs as $key=>$value)
        {
            $program_ids[] = getApplicationProgramId($value);
        }

        $tmp_version_data = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->get();

        $parray = [];
        //$rs = WaitlistAvailabilityProcessLog::where("version", $version)->get();
        foreach($tmp_version_data as $key=>$value)
        {
            if(!isset($parray[$value->program_id]))
            {
                $parray[$value->program_id] = [];
            }
            array_push($parray[$value->program_id], $value->grade);
        }

        

        $ids = array('"PreK"', '"K"', '"1"', '"2"', '"3"', '"4"', '"5"', '"6"', '"7"', '"8"', '"9"', '"10"', '"11"', '"12"');
        $district_id = Session::get("district_id");
        $submissions = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->orderByRaw('FIELD(next_grade,'.implode(",",$ids).')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade']);


        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        $prgCount = array();;
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if($value->$choice != 0)
                    {
                        if (!isset($programs[$value->$choice]) && in_array($value->$choice, array_keys($parray))) {
                            $programs[$value->$choice] = [];
                        }
                        if (isset($programs[$value->$choice]) && !in_array($value->next_grade, $programs[$value->$choice])) {
                            if(in_array($value->next_grade, $parray[$value->$choice]))
                            {
                                array_push($programs[$value->$choice], $value->next_grade);
                            }
                        }
                    }
                }
            }
        }

        ksort($programs);
        $final_data = array();
        foreach($programs as $key=>$value)
        {
            foreach($value as $ikey=>$ivalue)
            {
                $tmp = array();
                $rs = Availability::where("program_id", $key)->where("grade", $ivalue)->where("enrollment_id", Session::get("enrollment_id"))->first();
                $available_seats = $rs->available_seats;

                $seat_data = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("program_id", $key)->where("grade", $ivalue)->where("application_id", $application_id)->where("version", $version)->first();
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
                                  ->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)->where("submissions_waitlist_final_status.application_id", $application_id)->where("submissions_waitlist_final_status.version", $version)
                            ->get()->count();
                $rs2 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where("second_choice_final_status", "Offered")
                                  ->where("second_choice_program_id", $key)
                                  ->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
                            ->where('next_grade',$ivalue)->where("submissions_waitlist_final_status.application_id", $application_id)->where("submissions_waitlist_final_status.version", $version)
                            ->get()->count();
                $tmp['offered'] = $rs1 + $rs2;




                $data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_available_count($application_id, $key, $ivalue);
                $accepted = $data['offered_seats'];


                $current_accepted = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $ivalue)->where(function($q1) use ($key) {
                    $q1->where(function ($q) use ($key) {
                            $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $key);
                        })->orWhere(function ($q) use ($key) {
                            $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $key);
                        });
                    })->where("submissions_waitlist_final_status.version", $version)->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->count();
                $accepted = $accepted - $current_accepted;

                $tmp['accepted'] = $accepted;
                $tmp['remaining'] = $tmp['available_seats']  + $tmp['black_withdrawn'] + $tmp['white_withdrawn'] + $tmp['other_withdrawn'] + $tmp['additional_seats'];
                $final_data[] = $tmp;

            }

        }
        



        //print_r($final_data);exit;
        return view("Waitlist::seats_status",compact("final_data", "version_data"));
    }


    /* Population changes function */
    public function population_change_application($application_id=1, $version=0)
    {
        // Processing
        $pid = $application_id;
        $from = "form";

        $selected_programs = [];
        if($version == 0)
        {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "waitlist")->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $selected_programs = explode(",", $rs->selected_programs);
        }
        $program_ids = [];
        foreach($selected_programs as $key=>$value)
        {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id;//getApplicationProgramId($value);
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

        $rawOrder = DB::raw(sprintf('FIELD(submissions.next_grade, %s)', "'".implode(',', $ids)."'"));

        $submissions = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', $district_id)->where(function ($q) {
                                $q->where("first_choice_final_status", "Offered")
                                  ->orWhere("second_choice_final_status", "Offered");  
                            })
                            ->where('district_id', $district_id)->where("submissions.form_id", $application_id)->where("submissions_waitlist_final_status.version", $version)->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
                            ->orderByRaw('FIELD(next_grade,'.implode(",",$ids).')')
            ->get(['first_choice_program_id', 'second_choice_program_id', 'next_grade', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for']);


        $choices = ['first_choice_program_id', 'second_choice_program_id'];
        if (isset($submissions)) {
            foreach ($choices as $choice) {
                foreach ($submissions as $key => $value) {
                    if(in_array($value->$choice, $program_ids))
                    {
                        if (!isset($programs[$value->$choice])) {
                            if($value->$choice != 0)
                                $programs[$value->$choice] = [];
                        }
                        if ($value->$choice != 0 && !in_array($value->next_grade, $programs[$value->$choice]))  {
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
                        if($choice == "first_choice_program_id")
                        {
                            $submission_race_data = $submissions->where($choice, $program_id)->where('first_choice_final_status', "Offered")
                                ->where('next_grade', $grade);
                         }
                         else
                         {
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
                                    }else{
                                        $race_count[$key] = $value;
                                    }
                                }
                            }else{
                                
                            
                             $race_count = $race;

                            }
                        }

                    }

                    $rsproc = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("version", $version)->where("application_id", $application_id)->where("program_id", $program_id)->where("grade", $grade)->first();
                    //echo $version . " - ".$program_id . " - " .$grade."<BR>";
                    
                    if(!isset($race_ary['Black']))
                        $race_ary['Black'] = 0;
                    if(!isset($race_ary['White']))
                        $race_ary['White'] = 0;
                    if(!isset($race_ary['Other']))
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
        return view("Waitlist::population_change", compact('data_ary', 'race_ary', 'pid', 'from', "display_outcome", "application_id", "last_date_online_acceptance", "last_date_offline_acceptance"));
    }

    public function population_change_version($application_id, $version=0)
    {

         $pid = $application_id;
        $from = "form";

        $magnet_programs = $ib_programs = $audition_programs = [];
        $magnet_programs = Program::where("process_logic", "Magnet")->select("id")->get()->pluck('id')->toArray();
        $ib_programs = Program::where("process_logic", "IB")->select("id")->get()->pluck('id')->toArray();
        $audition_programs = Program::where("process_logic", "Audition")->select("id")->get()->pluck('id')->toArray();

        

        $data = array("magnet" => "magnet_programs", "ib" => "ib_programs", "audition" => "audition_programs");

        $first_ib_processing = $second_ib_processing = $first_audition_processing= $second_audition_processing = $first_magnet_processing = $second_magnet_processing = [];

        $offered_arr = $no_availability_arr = $in_eligible_arr = $waitlisted_arr = [];
        foreach($data as $key=>$value)
        {
            $tmpid = ${$value};
            $submissions = Submissions::where('district_id', Session::get("district_id"))->where(function ($q) use ($tmpid){
                                $q->whereIn("first_choice_program_id", $tmpid)
                                  ->orWhereIn("second_choice_program_id", $tmpid);  
                            })
                            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
                            ->where("submissions.form_id", $application_id)->where("submissions_waitlist_final_status.version", $version)->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
            ->get(['submissions.*', 'first_choice_final_status', 'second_choice_final_status', 'first_waitlist_for', 'second_waitlist_for']);


            
            $testData = array("first", "second");
            
                

                

                foreach($submissions as $skey=>$svalue)
                {
                    $insert = "";

                    foreach($testData as $tkey=>$tvalue)
                    {
                        $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($svalue);
                        $status = $tvalue."_choice_final_status";
                        $program_id = $tvalue."_choice_program_id";
                        $tmp1 = $tmp;
                        $tmp1['choice'] = $tvalue;
                        $tmp1['program_id'] = $svalue->$program_id;
                        $tmp1['school_id'] = getSchoolMasterName($tmp1['zoned_school']);

                        if(in_array($svalue->$program_id, $tmpid))
                        {
                            $insert = $tvalue;
                            if($key == "ib")
                            {
                                $score = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->checkSubmissionCommitteeValue($svalue->id, $tmp1['program_id']);
                                $tmp1['student_profile_score'] = $score[0];
                                $tmp1['student_profile'] = $score[1];
                            }
                            elseif($key == "audition")
                            {
                                $score = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->checkSubmissionAuditionValue($svalue->id, $tmp1['program_id']);
                                $tmp1['student_profile_score'] = $score[0];
                                $tmp1['student_profile'] = $score[1];
                            }
                            elseif($key == "magnet")
                            {
                                $tmp1['student_profile'] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->checkStudentProfileLevel($svalue->id);

                            }
                            if($svalue->$status == "Offered")
                            {
                                $tmp1['offer_status'] = "Offered";
                                $offered_arr[$key][] = $tmp1;
                            }
                            elseif($svalue->$status == "Waitlisted")
                            {
                                $tmp1['offer_status'] = "Waitlisted";
                                $waitlisted_arr[$key][] = $tmp1;
                            }
                            /*elseif($svalue->$status == "Denied due to Ineligibility")
                            {
                                $tmp1['offer_status'] = "Denied due to Ineligibility";
                                $in_eligible_arr[$key][] = $tmp1;
                            }*/
                        }

                    }
                    

                }
                
            

        }

        foreach($offered_arr as $key=>$value)
        {
            $t_offered_arr = array("first"=>[], "second"=>[]);
            foreach($value as $tk=>$val)
            {
                
                $t_offered_arr[$val['choice']][] = $val;
            }
            $str = "first_".$key."_processing";
            ${$str}['offered_arr'] = $t_offered_arr['first'];

            $str = "second_".$key."_processing";
            ${$str}['offered_arr'] = $t_offered_arr['second'];

        }

        foreach($waitlisted_arr as $key=>$value)
        {
            $t_waitlisted_arr = array("first"=>[], "second"=>[]);
            foreach($value as $tk=>$val)
            {
                
                $t_waitlisted_arr[$val['choice']][] = $val;
            }
            $str = "first_".$key."_processing";
            ${$str}['waitlisted_arr'] = $t_waitlisted_arr['first'];

            $str = "second_".$key."_processing";
            ${$str}['waitlisted_arr'] = $t_waitlisted_arr['second'];

        }

        foreach($offered_arr as $key=>$value)
        {
            $t_in_eligible_arr = array("first"=>[], "second"=>[]);
            foreach($value as $tk=>$val)
            {
                
                $t_in_eligible_arr[$val['choice']][] = $val;
            }
            $str = "first_".$key."_processing";
            //${$str}['in_eligible'] = $t_in_eligible_arr['first'];

            $str = "second_".$key."_processing";
            //${$str}['in_eligible'] = $t_in_eligible_arr['second'];

        }

        $first_magnet_processing['no_availability_arr'] = $second_magnet_processing['no_availability_arr'] = $first_ib_processing['no_availability_arr'] = $second_ib_processing['no_availability_arr'] = $first_audition_processing['no_availability_arr'] = $second_audition_processing['no_availability_arr'] = [];
        if(!isset($first_magnet_processing['offered_arr']))
            $first_magnet_processing['offered_arr'] = [];
        if(!isset($second_magnet_processing['offered_arr']))
            $second_magnet_processing['offered_arr'] = [];
        $magnet_offer_data = array_merge($first_magnet_processing['offered_arr'], $second_magnet_processing['offered_arr']);
        
        $sort_position =  array();
        if(!empty($magnet_offer_data))
        {
                    foreach($magnet_offer_data as $key=>$value)
        {
            //$sort_position['sort_position'][] = $value['sort_position'];
            //$student_profile['student_profile'][] = $value['student_profile'];
            $next_grade['next_grade'][] = $value['next_grade'];

        }
        array_multisort($next_grade['next_grade'], SORT_ASC,  $magnet_offer_data);

        }

        $schools = School::where("status", "Y")->get();
        $popHTML = "<table class='table table-striped mb-0 w-100' id='datatable4'><thead>
                    <tr><th class='text-center'>Program</th><th class='text-center'>School Home Zone</th><th class='text-center'>Rising Population from Home Zone</th><th class='text-center'>Calculated 7% Slots</th><th class='text-center'>Starting Population</th><th>Starting %</th><th class='text-center'>Offered</th><th class='text-center'>Offered %</th></tr></thead><tbody>";

        $rsTmp = SubmissionsSelectionReportMaster::where("type", "waitlist")->where("version", $version)->where("enrollment_id", Session::get("enrollment_id"))->get();

        foreach($rsTmp as $key=>$value)
        {
            $data = [];
            $data['application_id'] = $application_id;
            $data['program_name'] = $value->name;
            $data['school_home_zone']= $value->name;
            $data['enrollment_id'] = Session::get("enrollment_id");
            $data['type'] = "regular";
            $data['version'] = 0;

            $popHTML .= "<tr>";
            $popHTML .= "<td class='text-center'>".$value->program_name."</td>";
            $popHTML .= "<td class='text-center'>". $value->school_home_zone."</td>";
            $popHTML .= "<td class='text-center'>". $value->rising_population_homezone."</td>";
            $popHTML .= "<td class='text-center'>".$value->calculated_target_slot."</td>";   
            $popHTML .= "<td class='text-center'>".$value->starting_population."</td>";   
            $popHTML .= "<td class='text-center'>".$value->starting_percent."</td>";   
            $popHTML .= "<td class='text-center'>".$value->offered."</td>";
            $popHTML .= "<td class='text-center'>".$value->offered_percent."</td>";
            $popHTML .= "</tr>";  

        }
        $popHTML .= "</tbody></table>";
        $display_outcome= 2;
        //dd($first_magnet_processing, $second_magnet_processing);
        $actual_version = $version;
        $rs = ProcessSelection::where("application_id", $application_id)->where("type", "waitlist")->where("version", $version)->orderBy("created_at", "DESC")->first();
        $type = "";
        if(!empty($rs))
        {
            if($rs->commited == "No")
            {
                $type = "update";
                $display_outcome = 0;
            }
        }
        return view("Waitlist::test_index",compact("magnet_offer_data", "first_ib_processing", "second_ib_processing", "first_audition_processing", "second_audition_processing", "first_magnet_processing", "second_magnet_processing", "popHTML", "application_id", "display_outcome","actual_version","type"));
    }


    /* Submissions results function */

    public function submissions_results($form_id=1)
    {
        $rs = WaitlistProcessLogs::count();
        $version = $rs + 1;

        $pid = $form_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $display_outcome = $this->checkWailistOpen();


        $rs = WaitlistAvailability::get();
        if(count($rs) > 0)
        {
            foreach($rs as $key=>$value)
            {
                if(!isset($parray[$value->program_id]))
                {
                    $parray[$value->program_id] = [];
                }
                array_push($parray[$value->program_id], $value->grade);
            }
        }
        else
        {
            $rs = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("last_date_online", ">", date("Y-m-d H:i:s"))->first();
            if(!empty($rs))
            {
                $version = $rs->version;
                $rs = WaitlistAvailabilityProcessLog::where("version", $version)->get();
                foreach($rs as $key=>$value)
                {
                    if(!isset($parray[$value->program_id]))
                    {
                        $parray[$value->program_id] = [];
                    }
                    array_push($parray[$value->program_id], $value->grade);
                }

            }
        }


        $submissions = Submissions::where('district_id', $district_id)
            ->where('district_id', $district_id)
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where("form_id", $form_id)->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")->where("submissions_waitlist_final_status.version", $version)
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'next_grade', 'race', 'first_choice_final_status', 'second_choice_final_status']);

        $final_data = array();
        foreach($submissions as $key=>$value)
        {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name. " ". $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
                $tmp['choice'] = 1;
                $tmp['race'] = $value->race;
                $tmp['program'] = getProgramName($value->first_choice_program_id). " - Grade ".$value->next_grade;
                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['offered_status'] = $value->first_choice_final_status;
                if($value->first_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif($value->first_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif($value->first_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif($value->first_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";

                if($value->first_choice_final_status != "Pending")
                {
                    if(in_array($value->first_choice_program_id, array_keys($parray)))
                    {
                        if(in_array($value->next_grade, $parray[$value->first_choice_program_id]))
                        {
                            $final_data[] = $tmp;
                        }
                    }

                    
                }

                if($value->second_choice_program_id != 0)
                {
                    $tmp = array();
                    $tmp['id'] = $value->id;
                    $tmp['name'] = $value->first_name. " ". $value->last_name;
                    $tmp['grade'] = $value->next_grade;
                    $tmp['school'] = $value->current_school;
                    $tmp['race'] = $value->race;
                    $tmp['choice'] = 2;
                    $tmp['program'] = getProgramName($value->second_choice_program_id). " - Grade ".$value->next_grade;
                    $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                    $tmp['offered_status'] = $value->second_choice_final_status;

                    if($value->second_choice_final_status == "Offered")
                        $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                    elseif($value->second_choice_final_status == "Denied due to Ineligibility")
                        $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                    elseif($value->second_choice_final_status == "Waitlisted")
                        $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                    elseif($value->second_choice_final_status == "Denied due to Incomplete Records")
                        $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                    else
                        $tmp['outcome'] = "";
                    if($value->second_choice_final_status != "Pending")
                    {
                        if(in_array($value->second_choice_program_id, array_keys($parray)))
                        {
                            if(in_array($value->next_grade, $parray[$value->second_choice_program_id]))
                            {
                                $final_data[] = $tmp;
                            }
                        }
                    }


                    //$final_data[] = $tmp;
                }

        }
        $grade = $outcome = array();
        foreach($final_data as $key=>$value)
        {
            $grade['grade'][] = $value['grade']; 
            $outcome['outcome'][] = $value['outcome']; 
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);

        return view("Waitlist::submissions_result", compact('final_data', 'pid', 'from', 'display_outcome', 'form_id'));

    }

    public function submissions_results_version($application_id, $version=0)       
    {
        $selected_programs = [];
        $rs = ProcessSelection::where("application_id", $application_id)->where("type", "waitlist")->where("version", $version)->orderBy("created_at", "DESC")->first();
        $version_data = $rs;
        $version = $rs->version;
        $selected_programs = explode(",", $rs->selected_programs);
        
        $program_ids = [];
        foreach($selected_programs as $key=>$value)
        {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id;//getApplicationProgramId($value);
        }

        

        $pid = $application_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where('submissions.enrollment_id', Session::get('enrollment_id'))
            ->where('district_id', $district_id)
            ->where("submissions.form_id", $application_id)->where('submissions_waitlist_final_status.version', $version)->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status']);

        $final_data = array();
        foreach($submissions as $key=>$value)
        {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name. " ". $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
                $tmp['choice'] = 1;
                $tmp['race'] = $value->calculated_race;
                $tmp['program'] = getProgramName($value->first_choice_program_id). " - Grade ".$value->next_grade;
                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['offered_status'] = $value->first_choice_final_status;
                if($value->first_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif($value->first_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif($value->first_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif($value->first_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";

                if(!in_array($value->first_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                    $final_data[] = $tmp;                


                if($value->second_choice_program_id != 0)
                {
                    $tmp = array();
                    $tmp['id'] = $value->id;
                    $tmp['name'] = $value->first_name. " ". $value->last_name;
                    $tmp['grade'] = $value->next_grade;
                    $tmp['school'] = $value->current_school;
                    $tmp['race'] = $value->calculated_race;
                    $tmp['choice'] = 2;
                    $tmp['program'] = getProgramName($value->second_choice_program_id). " - Grade ".$value->next_grade;
                    $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                    $tmp['offered_status'] = $value->second_choice_final_status;

                    if($value->second_choice_final_status == "Offered")
                        $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                    elseif($value->second_choice_final_status == "Denied due to Ineligibility")
                        $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                    elseif($value->second_choice_final_status == "Waitlisted")
                        $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                    elseif($value->second_choice_final_status == "Denied due to Incomplete Records")
                        $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                    else
                        $tmp['outcome'] = "";
                    if(!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                        $final_data[] = $tmp;
                    
                }

        }
        $grade = $outcome = array();
        foreach($final_data as $key=>$value)
        {
            $grade['grade'][] = $value['grade']; 
            $outcome['outcome'][] = $value['outcome']; 
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);


        return view("Waitlist::submissions_result_report", compact('final_data', "application_id",  "version", "version_data"));

    }

    public function admin_run_selection($application_id=1)
    {
        $processType = Config::get('variables.process_separate_first_second_choice');
        $gradeWiseProcessing = Config::get('variables.grade_wise_processing');

        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();
        if(!empty($application_data) && $application_data->preliminary_processing == "Y")
            $preliminary_score = true;



        $group_racial_composition = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->updated_racial_composition($application_id);
        foreach($group_racial_composition as $key=>$value)
        {
            $group_racial_composition[$key]['no_previous'] = 'N';
        }

        $af_programs = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->fetch_programs_group($application_id);

        $this->group_racial_composition = $group_race_array = $group_racial_composition;
        
        $tmp = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->groupByRacism($af_programs);

        $this->program_group = $program_group_array = $tmp['program_group'];

        $enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();
        $swing = $enrollment_racial->swing;

        /* Create Application Filter Group Array for Program */
        $tmpGroup = array_values($program_group_array);
        $arr = array_unique($tmpGroup);
        $race_enroll_arr = [];
        foreach($arr as $key=>$value)
        {
            $rs = ProgramSwingData::where("enrollment_id", Session::get('enrollment_id'))->where("application_id", $application_id)->where("program_name", $value)->first();
            $program_swing = $swing;
            if(!empty($rs))
            {
                if(is_numeric($rs->swing_percentage) && $rs->swing_percentage > 0)
                {
                    $program_swing = $rs->swing_percentage;
                }
            }
            
            $tmp = [];
            $tmp['min'] = $enrollment_racial->black-$program_swing;
            $tmp['max'] = $enrollment_racial->black+$program_swing;
            $race_enroll_arr[$value]['black'] = $tmp;

            $tmp = [];
            $tmp['min'] = $enrollment_racial->white-$program_swing;
            $tmp['max'] = $enrollment_racial->white+$program_swing;
            $race_enroll_arr[$value]['white'] = $tmp;

            $tmp = [];
            $tmp['min'] = $enrollment_racial->other-$program_swing;
            $tmp['max'] = $enrollment_racial->other+$program_swing;
            $race_enroll_arr[$value]['other'] = $tmp;
        }
        $this->enrollment_race_data = $race_enroll_arr;

        
        $rs = Program::where("parent_submission_form", $application_id)->get();



        $availabilityArray = array();
        foreach($rs as $pkey=>$pvalue)
        {
            $grade_lavel = $pvalue->grade_lavel;
            $tmp = explode(",", $grade_lavel);
            foreach($tmp as $value)
            {
                $offer_count = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_offered_count_programwise($pvalue->id, $value);


                $rs1 = Availability::where("enrollment_id", Session::get("enrollment_id"))->where("grade", $value)->where("program_id", $pvalue->id)->first();
                if(!empty($rs1))
                    $total = $rs1->available_seats;
                else
                    $total = 0;
                $availabilityArray[$pvalue->id][$value] = $total-$offer_count;

            }
        }

        $firstData = Submissions::distinct()->where("enrollment_id", Session::get('enrollment_id'))->where("submission_status", "<>",  "Application Withdrawn")->where("form_id", $application_id)->get(["first_choice"]);
        $secondData = Submissions::distinct()->where("enrollment_id", Session::get('enrollment_id'))->where("submission_status", "<>",  "Application Withdrawn")->where("form_id", $application_id)->get(["second_choice"]);
 /* Get Subject and Acardemic Term like Q1.1 Q1.2 etc set for Academic Grade Calculation 
                For all unique First Choice and Second Choice
         */
        $subjects = $terms = $programArr = $test_scores_titles = array();
        $eligibilityArr = array();


        foreach($firstData as $key=>$value)
        {
            if($value->first_choice != "" && !in_array($value->first_choice, $programArr))
            {
                $programArr[] = $value->first_choice;
                $data = getSetEligibilityDataDynamic($value->first_choice, 12);
                if(isset($data->ts_scores))
                {
                    foreach($data->ts_scores as $ts=>$tv)
                    {
                        if(!in_array($tv, $test_scores_titles))
                        {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }

            }
        }
        foreach($secondData as $key=>$value)
        {
            if($value->second_choice != "" && !in_array($value->second_choice, $programArr))
            {
                $programArr[] = $value->second_choice;
                $data = getSetEligibilityDataDynamic($value->second_choice, 12);
                if(isset($data->ts_scores))
                {
                    foreach($data->ts_scores as $ts=>$tv)
                    {
                        if(!in_array($tv, $test_scores_titles))
                        {
                            $test_scores_titles[] = $tv;
                        }
                    }
                }
            }
        }
              

        /* Get Set Eligibility Data Set for first choice program and second choice program
         */

        $setEligibilityData = $setCommitteScoreEligibility = array();
        foreach($firstData as $value)
        {
            if(!in_array($value->first_choice, array_keys($setCommitteScoreEligibility)))
            {
                $data = getSetEligibilityDataDynamic($value->first_choice, 7);
                if(isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->first_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->first_choice] = 2;                    
            }

        }

        foreach($secondData as $value)
        {
            if(!in_array($value->second_choice, array_keys($setCommitteScoreEligibility)))
            {
                $data = getSetEligibilityDataDynamic($value->second_choice, 7);
                if(isset($data->minimum_score))
                    $setCommitteScoreEligibility[$value->second_choice] = $data->minimum_score;
                else
                    $setCommitteScoreEligibility[$value->second_choice] = 2;                    
            }

        }

        /* Code to fetch all selection properties of each program
        and Create array after soring. So we can get idea what is 
        selection ranking for each program */

        
        $programSortArr = [];
        foreach($programArr as $key=>$val)
        {
            $rsProgram = Program::where("id", getApplicationProgramId($val))->first();
            if(!empty($rsProgram))
            {
                $tmp = array();
                if($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if($rsProgram->committee_score != '')
                    $tmp['committee_score'] = $rsProgram->committee_score;
                if($rsProgram->audition_score != '')
                    $tmp['audition_score'] = $rsProgram->audition_score;
                if($rsProgram->rating_priority != '')
                    $tmp['rating_priority'] = $rsProgram->rating_priority;
                if($rsProgram->combine_score != '')
                    $tmp['combine_score'] = $rsProgram->combine_score;
                if($rsProgram->lottery_number != '')
                    $tmp['lottery_number'] = $rsProgram->lottery_number;
                if($rsProgram->final_score != '')
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
        foreach($committee_eligibility as $key=>$value)
        {
            $committee_program_id[] = $value['id'];
        }

        
                /* Get CDI Data */
        $firstdata = $seconddata = array();
        $incomplete_arr = $failed_arr = $interview_arr = array();
        
        $programGrades = array();
        $committee_count = 0;

        $version = 0;

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "waitlist")->where("commited", "Yes")->orderBy("created_at", "DESC")->first();
        if(!empty($rs))
            $version = $rs->version;

        $program_list = [];
        $submission_data = $subids = [];
        $rs = ProgramGradeMapping::where("enrollment_id", Session::get("enrollment_id"))->get();
        foreach($rs as $key=>$value)
        {
            $id = $value->id;
            $rsExist = ProcessSelection::where('enrollment_id', Session::get('enrollment_id'))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(".$id.", selected_programs)")->where("commited", "Yes")->orderBy("created_at", "desc")->first();
            $table_name = "submissions_final_status";
            $version = 0;        
            if(!empty($rsExist))
            {
                if($rsExist->type == "regular")
                {
                    $table_name = "submissions_final_status";
                    $version = 0;
                }
                elseif($rsExist->type == "waitlist")
                {
                    $table_name = "submissions_waitlist_final_status";
                    $version = $rsExist->version;
                }
                elseif($rsExist->type == "late_submission")
                {
                    $table_name = "late_submissions_final_status";
                    $version = $rsExist->version;
                }

            }
            $submissions=Submissions::
                    where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                                        $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                                    })
                                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                                ->join($table_name, $table_name.".submission_id", "submissions.id")->where($table_name.".application_id", $application_id)->where($table_name.".version", $version)->select("submissions.*", $table_name.".first_offer_status", $table_name.".second_offer_status", $table_name.".first_choice_final_status", $table_name.".second_choice_final_status")
                    ->get();

            foreach($submissions as $sk=>$sv)
            {

                $insert = true;

                 if($sv->first_choice_final_status == "Waitlisted" && !in_array($sv->second_offer_status, array("Pending", "Waitlisted", "Declined & Waitlisted", "Declined")))
                 {
                    $insert = false;
                 }
                 if($insert && !in_array($sv->id, $subids))
                {
                    $submission_data[] = $sv;
                    $subids[] = $sv->id;
                }       

                 if($sv->second_choice_final_status == "Waitlisted" && !in_array($sv->first_offer_status, array("Pending", "Declined & Waitlisted", "Declined")))
                 {
                    $insert = false;
                 }
                 
                 if($sv->second_choice_final_status != "Waitlisted")
                 {
                    
                    $insert = false;
                 }       
                if($insert && !in_array($sv->id, $subids))
                {
                    $submission_data[] = $sv;
                    $subids[] = $sv->id;
                }

            }

        }
        
        $submissions = $submission_data;


        foreach($submissions as $key=>$value)
        {
            $interview_passed = true;
            $composite_score = 0;
            
            if($value->first_choice != "" && $value->second_choice != "")
            {
                $failed = false;
                $tmpfirstdata = [];
                $firstfailed = $firstincomplete = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] =getApplicationProgramName($value->first_choice);
                $tmp['first_choice'] = $value->first_choice;
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['second_program'] = "";
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);
                
                if(!in_array($value->first_choice_program_id, $committee_program_id))
                {
                    $tmp['committee_score'] = "NA";
                }
                else
                {
                    $committee_count++;
                }

                if($tmp['committee_score'] == null)
                {
                    $firstincomplete = true;
                    $incomplete = true;
                }
                elseif(is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice])
                {
                    $tmp['committee_score_status'] = 'Pass';
                }
                elseif($tmp['committee_score'] != "NA")
                {
                    $tmp['committee_score_status'] = 'Fail';
                    $firstfailed = true;
                    $failed = true;
                }
                $tmp['choice'] = 1;
                $tmp['composite_score'] = $composite_score;

                if($value->submission_status == "Pending")
                {
                    $incomplete_arr[] = $tmp;
                    $firstincomplete = true;
                }
                elseif(!$interview_passed)
                {
                    $interview_arr[] = $tmp;
                }
                else
                {
                    if($failed)
                        $failed_arr[] = $tmp;
                    elseif($incomplete)
                        $incomplete_arr[] = $tmp;
                    else
                    {
                        $tmpfirstdata = $tmp;
                        
                    }

                }    

                
                $failed = false;
                $incomplete = false;
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->second_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->second_choice_program_id);

                if(!in_array($value->second_choice_program_id, $committee_program_id))
                {
                    $tmp['committee_score'] = "NA";
                }

                if($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif(is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice])
                {
                    $tmp['committee_score_status'] = 'Pass';
                }
                elseif($tmp['committee_score'] != "NA")
                {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");


                $tmp['second_program'] = getApplicationProgramName($value->second_choice);
                $tmp['first_program'] = "";
                $tmp['choice'] = 2;
                

                if(!$firstfailed && !$firstincomplete)
                {
                    if($value->first_offer_status != "Declined & Waitlisted")
                    {
                        $firstdata[$value->first_choice_program_id][] = $tmpfirstdata;
                    }
                }

                if($value->submission_status == "Pending")
                {
                    $incomplete_arr[] = $tmp;
                }
                elseif(!$interview_passed)
                {
                    $interview_arr[] = $tmp;
                }
                else
                {
                    if($failed)
                    {
                        $failed_arr[] = $tmp;   
                    }
                    elseif($incomplete)
                    {
                        $incomplete_arr[] = $tmp;
                    }
                    else
                    {
                        if($value->second_offer_status != "Declined & Waitlisted")
                        {
                            $seconddata[$value->second_choice_program_id][] = $tmp;
                        }
                    }

                }


            }
            elseif($value->first_choice != "")
            {
                $failed = false;
                $incomplete = false;

                $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
                $tmp['composite_score'] = $composite_score;
                $choice = getApplicationProgramName($value->first_choice);
                $tmp['first_program'] =getApplicationProgramName($value->first_choice);
                $tmp['first_choice_program_id'] = $value->first_choice_program_id;
                $tmp['second_choice_program_id'] = $value->second_choice_program_id;
                $tmp['second_program'] = "";
                $tmp['first_choice'] = $value->first_choice;
                $tmp['second_choice'] = $value->second_choice;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");
                $tmp['test_scores'] = app('App\Modules\Reports\Controllers\ReportsController')->getProgramTestScores($value->first_choice_program_id, $value->id, $test_scores_titles);

                $tmp['committee_score'] = getSubmissionCommitteeScore($value->id, $value->first_choice_program_id);
                if(!in_array($value->first_choice_program_id, $committee_program_id))
                {
                    $tmp['committee_score'] = "NA";
                }
                else
                {
                    $committee_count++;
                }

                if($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif(is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->first_choice])
                {
                    $tmp['committee_score_status'] = 'Pass';
                }
                elseif($tmp['committee_score'] != "NA")
                {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['choice'] = 1;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "first");

                if($value->submission_status == "Pending")
                {
                    $incomplete_arr[] = $tmp;
                }
                elseif(!$interview_passed)
                {
                    $interview_arr[] = $tmp;
                }
                else
                {
                    if($failed)
                        $failed_arr[] = $tmp;
                    elseif($incomplete)
                        $incomplete_arr[] = $tmp;
                    else
                    {
                        if($value->first_offer_status != "Declined & Waitlisted" && $value->first_choice_final_status != "Denied due to Ineligibility"  && $value->first_choice_final_status != "Denied due to Incomplete Records")
                        {
                            if($failed)
                                $failed_arr[] = $tmp;
                            elseif($incomplete)
                                $incomplete_arr[] = $tmp;
                            else
                            {
                                $firstdata[$value->first_choice_program_id][] = $tmp;
                            }
                        }

                        
                    }
                }

            }
            else
            {
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
                if(!in_array($value->second_choice_program_id, $committee_program_id))
                {
                    $tmp['committee_score'] = "NA";
                }
                else
                {
                    $committee_count++;
                }

                if($tmp['committee_score'] == null)
                    $incomplete = true;
                elseif(!$interview_passed)
                {
                    $interview_arr[] = $tmp;
                }
                elseif(is_numeric($tmp['committee_score']) && $tmp['committee_score'] >= $setCommitteScoreEligibility[$value->second_choice])
                {
                    $tmp['committee_score_status'] = 'Pass';
                }
                elseif($tmp['committee_score'] != "NA")
                {
                    $tmp['committee_score_status'] = 'Fail';
                    $failed = true;
                }

                $tmp['choice'] = 2;
                $tmp['rank'] = app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($value, "second");

                if($value->submission_status == "Pending")
                {
                    $incomplete_arr[] = $tmp;
                }
                else
                {

                    if($failed)
                        $failed_arr[] = $tmp;
                    elseif($incomplete)
                        $incomplete_arr[] = $tmp;
                    else
                    {
                        if($value->second_offer_status != "Declined & Waitlisted"  && $value->second_choice_final_status != "Denied due to Ineligibility"  && $value->second_choice_final_status != "Denied due to Incomplete Records")
                        {
                            if($failed)
                                $failed_arr[] = $tmp;
                            elseif($incomplete)
                                $incomplete_arr[] = $tmp;
                            else
                            {
                                $seconddata[$value->second_choice_program_id][] = $tmp;
                            }
                        }
                    }
                }
            }
            

        }


                /* Sort all submission based on selection rank set 
        by each program array */ 
        $ids = array('PreK','K', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
        $firstPrgData = $secondPrgData = [];
        $loopArr = array("first", "second");
        if(!$processType)
        {
            $dataStoreArr = array("first", "first"); 
        }
        else
        {
            $dataStoreArr = array("first", "second");
        }

        $append_second = $append_first = [];
        foreach($loopArr as $lkey=>$lvalue)
        {
            $str = $lvalue."data";
            $arrvar = ${$str};
            foreach($arrvar as $key=>$value)
            {

                $parray = $value;
                if(isset($programSortArr[$key]))
                {
                    $array = $value;
                    $sortingParams = [];
                    $first_column = "";
                    $i = 0;
                    foreach($programSortArr[$key] as $pk=>$pv)
                    {
                        if($i==0)
                            $first_column = $pk;
                        $i++;

                    }

                    $first_col_arr = array_column($array, $first_column);

                    $tmpStr = $dataStoreArr[$lkey]."PrgData";                        

                    if($first_column != "committee_score" && $first_column != "combine_score" && (in_array($key, $committee_program_id) || $key == 19))
                    {
                        foreach($array as $pk=>$pv)
                        {
                            $pv['final_score'] = 0;
                            ${"append_".$lvalue}[] = $pv;
                        }

                        foreach($programSortArr[$key] as $pk=>$pv)
                        {
                            if($pk =="rating_priority")
                            {
                                $sort_field = "rank";
                                $sort_type = SORT_ASC;
                            }
                            else
                            {
                                $sort_field = $pk;
                                $sort_type = SORT_DESC;
                            }

            
                            $sortingParams[] = array_column(${"append_".$dataStoreArr[$lkey]}, $sort_field); 
                            $sortingParams[] = $sort_type;                        

                        }
                        $sortingParams[] = &${"append_".$dataStoreArr[$lkey]};  
                        array_multisort(...$sortingParams);

                    }
                    else
                    {
                        
                        foreach($array as $pk=>$pv)
                        {
                            $pv['final_score'] = 0;
                            ${$tmpStr}[] = $pv;
                        }
                    }
                }  
            }

        }


        $firstdata = $firstPrgData;
       if(!empty($firstdata))
       {
            $committee_score  = $priority = $lottery_number = $choices = $next_grade = array();
            foreach($firstdata as $key=>$value)
            {
                try{
                    if($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];  
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];  
                }catch(\Exception $e){

                    echo $e->getMessage();exit;
                }
                $priority['rank'][] = $value['rank'];
                $lottery_number['lottery_number'][] = $value['lottery_number'];
                $choices['choice'][] = $value['choice'];
                //$next_grade['next_grade'][] = $value['next_grade'];

            }
            if(!$processType)
            {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $choices['choice'], SORT_ASC, $firstdata);
            }
            else
            {
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $firstdata);
            }
            


        }
        if($committee_count > 0)
        {
            $firstdata = array_merge($append_first, $firstdata);
            if(!$processType)
            {
                $firstdata = array_merge($firstdata,$append_second);
            }

        }

        if($gradeWiseProcessing)
        {
            $tmp = [];
            foreach($ids as $ik=>$iv)
            {
                foreach($firstdata as $fk=>$fv)
                {
                    if($fv['next_grade'] == $iv)
                    {
                        $tmp[] = $fv;
                    }
                }
            }


            $firstdata = $tmp;
        }

    
        if($processType)
        {
            $seconddata = $secondPrgData;
           if(!empty($seconddata))
           {
                $committee_score  = $priority = $lottery_number = array();
                foreach($seconddata as $key=>$value)
                {
                    if($preliminary_score)
                        $committee_score['committee_score'][] = $value['composite_score'];  
                    else
                        $committee_score['committee_score'][] = $value['committee_score'];  
                    $priority['rank'][] = $value['rank'];
                    $lottery_number['lottery_number'][] = $value['lottery_number'];
                }
                array_multisort($committee_score['committee_score'], SORT_DESC, $priority['rank'], SORT_ASC, $lottery_number['lottery_number'], SORT_DESC, $seconddata);
            }
            $seconddata = array_merge($seconddata, $append_second);        
        }
        else
        {
            $seconddata = [];
        }



        $tmpAvailability = $availabilityArray;
        $waitlistOfferArray  = $offeredRank = $firstOffered = array();
        $final_data = [];

        foreach($firstdata as $key=>$value)
        {
            if($value['choice'] == 1)
                $program_id = $value['first_choice_program_id'];
            else
                $program_id = $value['second_choice_program_id'];

            $offered = false;
            $offered_race = "";
            $offered_submission_id = "";
            $offer_program_id = 0;
            if(in_array($value['id'], $firstOffered))
            {
                $value['final_status'] = "Waitlist By Already Offered";
            }
            else
            {
                if(isset($availabilityArray[$program_id][$value['next_grade']]) && $availabilityArray[$program_id][$value['next_grade']] > 0)
                {
                    $race = strtolower($value['race']);
                    $group_name = $this->program_group[$program_id];

                    $offer_type = "normal";
                    if($this->group_racial_composition[$group_name]['no_previous'] == 'Y')
                    {
                        $offer_type = $this->check_race_previous_data($group_name, $race);
                        //echo $value['id']." - " .getProgramName($program_id)." - ".$offer_type."<BR>";//exit;
                    }

                    $total_seats = $this->group_racial_composition[$group_name]['total'];
                    $race_percent = $this->group_racial_composition[$group_name][$race];
                    $total_seats++;
                    $race_percent++;

                    $new_percent = number_format($race_percent*100/$total_seats, 2);


                    if($this->check_all_race_range($group_name, $race, $value['id']) || in_array($offer_type, array('OnlyThisOffered', 'OfferedWaitlisted')))
                    {

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

                    }
                    else
                    {
                        $value['final_status'] = "Waitlist By Race";



                        $total_seats = $this->group_racial_composition[$group_name]['total'];
                        $value['update'] = $this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race, "W");
                        $value['availability'] = $availabilityArray[$program_id][$value['next_grade']];

                    }


                }
                else
                {
                    $value['final_status'] = "No Availability";
                }                
            }

            $final_data[] = $value;
            if($offered && in_array($offer_type, array('normal', 'OfferedWaitlisted')))
            {
                $tmpArr = [];
                foreach($final_data as $fkey=>$fvalue)
                {
                    if($fvalue['choice'] == 1)
                        $program_id = $fvalue['first_choice_program_id'];
                    else
                        $program_id = $fvalue['second_choice_program_id'];
                    $group_name = $this->program_group[$program_id];
                    if($fvalue['final_status'] == "Waitlist By Race" && $offer_program_id == $group_name)
                    { //1
                        if(!in_array($fvalue['id'], $firstOffered))
                        {
                            if(isset($availabilityArray[$program_id][$fvalue['next_grade']]) && $availabilityArray[$program_id][$fvalue['next_grade']] > 0)
                            {

                                $race = strtolower($fvalue['race']);
                                $group_name = $this->program_group[$program_id];

                                $total_seats = $this->group_racial_composition[$group_name]['total'];
                                $race_percent = $this->group_racial_composition[$group_name][$race];
                                $total_seats++;
                                $race_percent++;
                                $new_percent = number_format($race_percent*100/$total_seats, 2);

                                if($this->check_all_race_range($group_name, $race, $fvalue['id']))
                                {
                                    $this->group_racial_composition[$group_name]['total'] = $total_seats;
                                    $this->group_racial_composition[$group_name][$race] = $race_percent;

                                    $fvalue['final_status'] = "Offered";
                                    if(isset($fvalue['update']))
                                        $update = $fvalue['update']."<br>-----------<br>Offered ID: ".$offered_submission_id." (".$offered_race.")<br>";
                                    else
                                        $update = "";
                                    $fvalue['update'] = $update.$this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race);
           

                                    $firstOffered[] = $fvalue['id'];
                                    $fvalue['availability'] = $availabilityArray[$program_id][$fvalue['next_grade']];

                                    $tmp_stock = $availabilityArray[$program_id][$fvalue['next_grade']];
                                    $tmp_stock--;
                                    $availabilityArray[$program_id][$fvalue['next_grade']] = $tmp_stock;
                                }
                                else
                                {
                                    if(isset($fvalue['update']))
                                        $update = $fvalue['update']."<br>-----------<br>Offered ID: ".$offered_submission_id." (".$offered_race.")<br>";
                                    else
                                        $update = "";
                                    $total_seats = $this->group_racial_composition[$group_name]['total'];
                                    $fvalue['update'] = $update.$this->generate_race_composition_update($this->group_racial_composition[$group_name], $total_seats, $race, "W");
                                    $fvalue['availability'] = $availabilityArray[$program_id][$fvalue['next_grade']];


                                }
                            }
                            else
                            {
                                $fvalue['final_status'] = "No Availability";
                                $fvalue['availability'] = 0;//$availabilityArray[$program_id][$fvalue['next_grade']];

                            }

                        }
                        else
                        {
                            $fvalue['final_status'] = "Waitlist By Already Offered";
                        }
                    } //1
                    $tmpArr[] = $fvalue;
                }
                $tmpArr1 = [];
                foreach($tmpArr as $tkey=>$tvalue)
                {
                    if($tvalue['final_status'] == "Waitlisted By Race" && in_array($tvalue['id'], $firstOffered))
                    {
                        $tvalue['final_status'] = "Waitlisted By Already Offered";
                    }
                    $tmpArr1[] = $tvalue;
                }
                $final_data = $tmpArr1;
            }
        }
        $group_racial_composition = $this->group_racial_composition;
        return view("ProcessSelection::test_index",compact("final_data", "incomplete_arr", "failed_arr", "group_racial_composition", "preliminary_score"));



    }


    public function magnet_processing($magnet_data, $choice="first")
    {
        $eligible_arr = $in_eligible_arr = [];
        foreach($magnet_data as $key=>$value)
        {
            $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
            $tmp['program_id'] = $value->program_id;
            $tmp['choice'] = $choice;
            $tmp['student_profile'] = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->checkStudentProfileLevel($value->id);
            $tmp['school_id'] = getSchoolMasterName($tmp['zoned_school']);
            $eligible_arr[] = $tmp;
        }


        $student_profile  = $lottery_number =  $grade_arr = array();
        foreach($eligible_arr as $key=>$value)
        {
            $grade_arr['grade'][] = $value['next_grade'];
            $lottery_number['lottery_number'][] = $value['lottery_number'];
            $student_profile['student_profile'][] = $value['student_profile'];
            //$next_grade['next_grade'][] = $value['next_grade'];

        }
        
        $offered_arr = $hold_arr = $no_availability_arr = $waitlisted_arr = [];
        if(!empty($eligible_arr))
        {
            //array_multisort($grade_arr['grade'], SORT_ASC, $student_profile['student_profile'], SORT_DESC, $lottery_number['lottery_number'], SORT_DESC, $eligible_arr);

            array_multisort($student_profile['student_profile'], SORT_DESC, $lottery_number['lottery_number'], SORT_DESC, $eligible_arr);
            /*foreach($eligible_arr as $key=>$value)
            {
                echo $value['id']."-".$value['student_profile']."<BR>";
            }
            exit;*/
            /*echo "<pre>";
            print_r($eligible_arr);
            exit;
            */
            $srno = 1;
            $tmp_eligible = [];
            foreach($eligible_arr as $key=>$value)
            {
                $tmp = $value;
                $tmp['sort_position'] = $srno;
                $srno++;
                $tmp_eligible[] = $tmp;
            }
            $eligible_arr = $tmp_eligible;
//dd($this->availabilityArray);
            //dd($eligible_arr, $this->offered_ids);
           //echo "<pre>"; print_r($eligible_arr);exit;
            $program_keys = [];
            foreach($eligible_arr as $k=>$v)
            {
            	$program_keys[] = $v['program_id'];
            }
            foreach($eligible_arr as $key=>$value)
            {
                $tmp = $value;
                if($this->availabilityArray[$value['program_id']][$value['next_grade']] > 0)
                {
                    if(isset($this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist']))
                    {

                        $exist_count_percent = $this->magnet_thresold_limit[$value['program_id']][$tmp['school_id']][$tmp['next_grade']]['exist'];
                        $target_count = $this->magnet_thresold_limit[$value['program_id']][$tmp['school_id']][$tmp['next_grade']]['target'];
                        $adm_value = $this->adm_data[$tmp['school_id']][$tmp['next_grade']];
                        $exist_count = $this->magnet_thresold_limit[$value['program_id']][$tmp['school_id']][$tmp['next_grade']]['exist_count'];

                        if($exist_count_percent < Config::get('variables.prefer_magnet_percentage'))
                        {
                            $exist_count++;
                            $new_percent = number_format(($exist_count*100)/$adm_value, 2);

                            
                            if($new_percent <= Config::get('variables.prefer_magnet_percentage'))
                            {
                                if(!in_array($tmp['id'], $this->offered_ids))
                                {
                                    $tmp['offer_status'] = 'Offered';
                                    $avlQty = $this->availabilityArray[$value['program_id']][$value['next_grade']];
                                    $avlQty--;
                                    $str = "<span class='text-success'>Old % : ".$this->magnet_thresold_limit[$value['program_id']][$tmp['school_id']][$tmp['next_grade']]['exist']." <br> New % : ".number_format(($exist_count*100)/$adm_value, 2)."<br>Availability Qty : ".$avlQty."</span>";
                                    $tmp['percent_status'] = $str;
                                    $this->magnet_thresold_limit[$value['program_id']][$tmp['school_id']][$tmp['next_grade']]['exist_count'] = $exist_count;
                                    $this->magnet_thresold_limit[$value['program_id']][$tmp['school_id']][$tmp['next_grade']]['exist'] = number_format(($exist_count*100)/$adm_value, 2);
                                    $this->offered_ids[] = $tmp['id'];


                                    

                                    if(isset($this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']]))
                                    {
                                        $testval = $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']];
                                        $testval++;
                                        $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']] = $testval;

                                    }
                                    else
                                        $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']] = 1;

                                    
                                    //$avlQty--;
                                    $this->availabilityArray[$value['program_id']][$value['next_grade']] = $avlQty;
                                    $offered_arr[] = $tmp;
                                }
                                else
                                {
                                    $tmp['offer_status'] = "Waitlisted";
                                    $tmp['percent_status'] = "Already Offered";
                                    $waitlisted_arr[] = $tmp;
                                }

                            }
                            else
                            {
                                $tmp['offer_status'] = 'Hold'; 
                                $str = "<span class='text-danger'>Old % : ".$exist_count_percent." <br> New % : ".$new_percent."</span>";
                                $tmp['percent_status'] = $str;
                                $hold_arr[] = $tmp;  
                            }

                        }
                        else
                        {
                            $tmp['offer_status'] = 'Hold';
                            $str = "<span class='text-danger'>Old % : ".$exist_count_percent."<br></span>";
                            $tmp['percent_status'] = $str;
                            $hold_arr[] = $tmp;
                        }
                    }
                    else
                    {
                        //dd($value['program_id'], $value['school_id'], $value['id'], $this->magnet_thresold_limit);
                        $tmp['offer_status'] = "No Availability";
                        $tmp['percent_status'] = "No Availability";
                        $no_availability_arr[] = $tmp;
                    }

                }
                else
                {
                    $tmp['offer_status'] = "No Availability";
                    $tmp['percent_status'] = "No Availability";
                    $no_availability_arr[] = $tmp;
                }

            } 

            $hld_cnt = 0;//count($hold_arr);
            $actual_hld_cnt = count($hold_arr);


            while(!empty($hold_arr) && $hld_cnt != $actual_hld_cnt)
            { // 6
                    $actual_hld_cnt = count($hold_arr);
                    //echo $hld_cnt . " - ".$actual_hld_cnt."<BR>";
                    $sortedArray = $this->sort_array_rising_magnet();
                //dd($this->offered_ids, $sortedArray);
                    foreach($sortedArray as $skey=>$svalue)
                    { // 5

                        $tmp = explode(":", $skey);
                        $program_id = $tmp[0];
                        $school_id = $tmp[1];
                        $next_grade = $tmp[2];

                        $exist_count = $this->magnet_thresold_limit[$program_id][$school_id][$next_grade]['exist_count'];
                        $adm_value = $this->adm_data[$school_id][$next_grade];

                        $exist_count++;
                        $new_percent = number_format(($exist_count*100)/$adm_value, 2);
                        //dd($this->availabilityArray);
                        if($new_percent >= 7 && in_array($program_id, $program_keys))
                        { //4
                            
                            if($this->availabilityArray[$program_id][$next_grade] > 0)
                            { //3
                                $removedId = [];
                                $offered = false;
                                foreach($hold_arr as $key=>$value)
                                { // 2
                                    if($program_id == $value['program_id'] && $next_grade == $value['next_grade'] && $school_id == $value['school_id'])
                                    { //1
                                        /*if(!in_array($tmp['id'], $this->offered_ids))
                                        {*/
                                            $avlQty = $this->availabilityArray[$value['program_id']][$value['next_grade']];
                                            $avlQty--;
                                            $exist_count_percent = $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist'];
                                            $target_count = $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['target'];
                                            $adm_value = $this->adm_data[$value['school_id']][$value['next_grade']];
                                            $exist_count = $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist_count'];
                                            $exist_count++;
                                            $str = $value['percent_status'] ?? '';
                                            $str .= "<br>------<br><span class='text-success'>Old % : ".$this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist']." <br> New % : ".number_format(($exist_count*100)/$adm_value, 2)."<br>Availability Qty : ".$avlQty."</span>";
                                            $value['percent_status'] = $str;

                                            $value['offer_status'] = 'Offered';
                                            $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist_count'] = $exist_count;
                                            $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist'] = number_format(($exist_count*100)/$adm_value, 2);

                                            $this->offered_ids[] = $value['id'];

                                            

                                            $offered_arr[] = $value;

                                            if(isset($this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']]))
                                            {
                                                $testval = $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']];
                                                $testval++;
                                                $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']] = $testval;

                                            }
                                            else
                                                $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']] = 1;

                                            
                                            $this->availabilityArray[$value['program_id']][$value['next_grade']] = $avlQty;




                                            if($avlQty < 0)
                                            {
                                                foreach($hold_arr as $hk=>$hv)
                                                {
                                                    if($program_id == $hv['program_id'] && $next_grade == $hv['next_grade'])
                                                    {
                                                        $removedId[] = $hk;
                                                        $hv['offer_status'] = "No Availability";
                                                        $hv['percent_status'] = "No Availability";
                                                        $no_availability_arr[] = $hv;
                                                    }
                                                }
                                            }
                                            $removedId[] = $key;
                                            $offered = true;
                                            break;

                                        /*}
                                        else
                                        {
                                            $tmp['offer_status'] = "Waitlisted";
                                            $tmp['percent_status'] = "Already Offered";
                                            $removedId[] = $key;
                                            $waitlisted_arr[] = $tmp;
                                        }*/

                                    } // 1
                                } //2

                                if($offered)
                                {
                                    foreach($removedId as $rvalue)
                                    {
                                        unset($hold_arr[$rvalue]);
                                    }
                                    break;
                                }

                            } //3
                            else
                            { //3
                                $removedId = [];
                                $offered = false;
                                foreach($hold_arr as $key=>$value)
                                {
                                    if($program_id == $value['program_id'] && $next_grade == $value['next_grade'])
                                    {

                                        $removedId[] = $key;
                                        $hv['offer_status'] = "No Availability";
                                        $hv['percent_status'] = "No Availability";
                                        $no_availability_arr[] = $value;
                                    }
                                }
                                foreach($removedId as $rvalue)
                                {
                                    unset($hold_arr[$rvalue]);
                                }

                            } //3
                            
                        } //4
                    } // 5
                    $hld_cnt = count($hold_arr);
                } // 6

                
            }
           


            $removedId= [];
            foreach($hold_arr as $key=>$value)
            {
                $program_id = $value['program_id'];
                $next_grade = $value['next_grade'];
                if($this->availabilityArray[$program_id][$next_grade] > 0)
                {
                    //$removedId = [];
                    $avlQty = $this->availabilityArray[$value['program_id']][$value['next_grade']];
                    $avlQty--;

                    $exist_count_percent = $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist'];
                    $target_count = $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['target'];
                    $adm_value = $this->adm_data[$value['school_id']][$value['next_grade']];
                    $exist_count = $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist_count'];
                    $exist_count++;
                    $str = $value['percent_status'] ?? '';
                    $str .= "<br>------<br><span class='text-success'>Old % : ".$this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist']." <br> New % : ".number_format(($exist_count*100)/$adm_value, 2)."<br>Availability Qty : ".$avlQty."</span>";
                    $value['percent_status'] = $str;

                    $value['offer_status'] = 'Offered';
                    $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist_count'] = $exist_count;
                    $this->magnet_thresold_limit[$value['program_id']][$value['school_id']][$value['next_grade']]['exist'] = number_format(($exist_count*100)/$adm_value, 2);
                    
                    $offered_arr[] = $value;
                    $this->offered_ids[] = $value['id'];

                    if(isset($this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']]))
                    {
                        $testval = $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']];
                        $testval++;
                        $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']] = $testval;

                    }
                    else
                        $this->magnet_offered_data[$value['program_id']][$value['next_grade']][$value['school_id']] = 1;

                    
                    $this->availabilityArray[$value['program_id']][$value['next_grade']] = $avlQty;
                    $removedId[] = $key;

                }
            }
            
            foreach($removedId as $rvalue)
            {
                unset($hold_arr[$rvalue]);
            }

            $tmphold = [];
            foreach($hold_arr as $harr)
            {
                $tmp = $harr;
                $tmp['offer_status'] = "Waitlisted";
                $str = $tmp['percent_status']."<span class='text-danger'><br>Availability Qty : ".$avlQty."</span>";
                $tmp['percent_status'] = $str;
                $tmphold[] = $tmp;
            }
           // exit;
            $waitlisted_arr = array_merge($waitlisted_arr, $tmphold);
       // dd($hold_arr);
            //$in_eligible_arr = [];
        return array("offered_arr"=>$offered_arr, "no_availability_arr"=>$no_availability_arr, "in_eligible"=>$in_eligible_arr, "waitlisted_arr"=>$waitlisted_arr);
    }

	public function sort_array_rising_magnet()
    {
        $tmp = $this->magnet_thresold_limit;
        $tmp1 = [];
        foreach($tmp as $program_key=>$program_value)
        {
            foreach($program_value as $school_key=>$school_val)
            {
                foreach($school_val as $grade_key=>$grade_val)
                {
                    $tmp1[$program_key.":".$school_key.":".$grade_key] = $grade_val['exist'];
                }
            }
        }
        asort($tmp1);
        return $tmp1;
    }

 

    public function ib_processing($ib_data, $choice="first")
    {
        $eligible_arr = $in_eligible_arr = [];
        foreach($ib_data as $key=>$value)
        {
            $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
            $tmp['choice'] = $choice;
            $tmp['program_id'] = $value->program_id;
            $score = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->checkSubmissionCommitteeValue($value->id, $value->program_id);
            $tmp['student_profile_score'] = $score[0];
            $tmp['student_profile'] = $score[1];
            $tmp['school_id'] = getSchoolMasterName($tmp['zoned_school']);
            $eligible_arr[] = $tmp;
        }

        
        $lottery_number =  array();
        foreach($eligible_arr as $key=>$value)
        {
            $lottery_number['lottery_number'][] = $value['lottery_number'];
            //$student_profile['student_profile'][] = $value['student_profile'];
            //$next_grade['next_grade'][] = $value['next_grade'];

        }
        
        $offered_arr = $no_availability_arr = $waitlisted_arr = [];
        if(!empty($eligible_arr))
        {
            array_multisort($lottery_number['lottery_number'], SORT_DESC, $eligible_arr);
            
            
            foreach($eligible_arr as $key=>$value)
            {
                $tmp = $value;
                if(isset($this->availabilityArray[$value['program_id']][$value['next_grade']]) && $this->availabilityArray[$value['program_id']][$value['next_grade']] > 0)
                {
                    if(!in_array($tmp['id'], $this->offered_ids))
                    {
                        $tmp['offer_status'] = 'Offered';
                        $this->offered_ids[] = $tmp['id'];
                        $offered_arr[] = $tmp;

                        $avlQty = $this->availabilityArray[$value['program_id']][$value['next_grade']];
                        $avlQty--;
                        $this->availabilityArray[$value['program_id']][$value['next_grade']] = $avlQty;
                    }
                    else
                    {
                        $tmp['offer_status'] = "Waitlisted";
                        $waitlisted_arr[] = $tmp;
                    }
                }
                else
                {
                    $tmp['offer_status'] = "No Availability";
                    $no_availability_arr[] = $tmp;
                }

            } 

        }

        return array("offered_arr"=>$offered_arr, "no_availability_arr"=>$no_availability_arr, "in_eligible"=>$in_eligible_arr, "waitlisted_arr"=>$waitlisted_arr);
    }


    public function audition_processing($audition_data, $choice="first")
    {
        $eligible_arr = $in_eligible_arr = [];
        foreach($audition_data as $key=>$value)
        {
            $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
            $tmp['choice'] = $choice;
            $tmp['program_id'] = $value->program_id;
            $score = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->checkSubmissionAuditionValue($value->id);

            $tmp['student_profile_score'] = $score[0];
            $tmp['student_profile'] = $score[1];

            //$tmp['student_profile'] = $this->checkSubmissionAuditionValue($value->id);
            $tmp['school_id'] = getSchoolMasterName($tmp['zoned_school']);
            $eligible_arr[] = $tmp;
        }

        
        $lottery_number =  array();
        foreach($eligible_arr as $key=>$value)
        {
            $lottery_number['lottery_number'][] = $value['lottery_number'];
            //$student_profile['student_profile'][] = $value['student_profile'];
            //$next_grade['next_grade'][] = $value['next_grade'];

        }
        

        $offered_arr = $no_availability_arr = $waitlisted_arr = [];
        if(!empty($eligible_arr))
        {
            array_multisort($lottery_number['lottery_number'], SORT_DESC, $eligible_arr);
            
            
            foreach($eligible_arr as $key=>$value)
            {
                $tmp = $value;
                if(isset($this->availabilityArray[$value['program_id']][$value['next_grade']]) && $this->availabilityArray[$value['program_id']][$value['next_grade']] > 0)
                {
                    if(!in_array($tmp['id'], $this->offered_ids))
                    {
                        $tmp['offer_status'] = 'Offered';
                        $this->offered_ids[] = $tmp['id'];
                        $offered_arr[] = $tmp;

                        $avlQty = $this->availabilityArray[$value['program_id']][$value['next_grade']];
                        $avlQty--;
                        $this->availabilityArray[$value['program_id']][$value['next_grade']] = $avlQty;
                    }
                    else
                    {
                        $tmp['offer_status'] = "Waitlisted";
                        $waitlisted_arr[] = $tmp;
                    }
                }
                else
                {
                    $tmp['offer_status'] = "No Availability";
                    $no_availability_arr[] = $tmp;
                }

            } 

        }
        return array("offered_arr"=>$offered_arr, "no_availability_arr"=>$no_availability_arr, "in_eligible"=>$in_eligible_arr, "waitlisted_arr"=>$waitlisted_arr);
    }

    
   

    public function processWaitlist($req, $application_id, $actual_version, $type="")
    {
        set_time_limit(0);
        

        $exist_availability = $adm_data = $thresold_limit = [];
        

        $rs = ADMData::where("enrollment_id", Session::get("enrollment_id"))->get();
        $magnet_requirement_data = [];
        foreach($rs as $key=>$value)
        {
            $this->adm_data[$value->school_id][$value->grade] = $value->total;
        }
//return $req;
        $process_program = $awardslot  = $program_process_ids = $availabilityArray = $withdrawl_arr = [];
        foreach($req['application_program_id'] as $key=>$value)
        {
            if($req['awardslot'.$value] >= 0)
            {

                $rs = ProgramGradeMapping::where("id", $value)->select("program_id", "grade")->where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->first();
                $program_id = $rs->program_id;
                $grade = $rs->grade;
                $program_process_ids[] = $program_id;
                $availabilityArray[$program_id][$grade] = $req['awardslot'.$value];
                $this->availabilityArray[$program_id][$grade] = $req['awardslot'.$value];
            
                if(isset($process_program[$program_id]))
                {
                    $tmp = $process_program[$program_id];
                    $tmp[] = $grade;
                    $process_program[$program_id] = $tmp;

                }
                else
                {
                    $process_program[$program_id][] = $grade; 
                }
                $awardslot[$program_id."-".$grade] = $req['awardslot'.$value];

                $twithdrawn = 0;

                if (isset($req['homezone_schools'][$value]) && !empty($req['homezone_schools'][$value])) {

                    foreach($req['homezone_schools'][$value] as $wkey=>$warr)
                    {
                        $withdrawn_student[$program_id][$grade][$wkey] = $warr;
                        $twithdrawn += $warr;
                    }
                } 

                $this->availabilityArray[$program_id][$grade] = $req['awardslot'.$value];// + $twithdrawn;
                $availabilityArray[$program_id][$grade] = $req['awardslot'.$value];// + $twithdrawn;
                 

            }

        }
//dd($process_program);
        $programs = Program::where("status", "Y")->where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->whereIn("id", array_keys($process_program))->get();
        foreach($programs as $key=>$value)
        {
            $grades = explode(",",$value->grade_lavel);
            foreach($grades as $gvalue)
            {
                if(in_array($gvalue, $process_program[$value->id]))
                {
                    $rs = Availability::where("program_id", $value->id)->where("grade", $gvalue)->first();
                    if(!empty($rs))
                    {
                        $rsMap = ProgramGradeMapping::where("program_id", $value->id)->where('grade', $gvalue)->select("id")->where("district_id", Session::get("district_id"))->first();
                        $mapid = $rsMap->id;

                        $twithdrawn = 0;

                        if (isset($req['homezone_schools'][$mapid]) && !empty($req['homezone_schools'][$mapid])) {

                            foreach($req['homezone_schools'][$mapid] as $wkey=>$warr)
                            {
                                $twithdrawn += $warr;
                            }
                        } 


                        $this->availabilityArray[$value->id][$gvalue] = $req['awardslot'.$mapid];// + $twithdrawn;
                        if($value->id == 24 && $gvalue == 4)
                        {
                            //echo $this->availabilityArray[$value->id][$gvalue];exit;
                        }
                        if($rs->home_zone != '')
                        {
                            $data = json_decode($rs->home_zone);

                            foreach($data as $dk=>$dv)
                            {
                                $offer_count = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->get_offered_count_programwise1($value->id, $gvalue, getSchooName($dk));

                                if(isset($withdrawn_student[$value->id][$gvalue][$dk]))
                                    $withdrawl = $withdrawn_student[$value->id][$gvalue][$dk];
                                else
                                    $withdrawl = 0;

                                

                                $dv += $offer_count - $withdrawl;
                                $exist_availability[$value->id][$gvalue][$dk] = $dv;// + $offer_count; 
                                if(isset($this->adm_data[$dk][$gvalue]))
                                {
                                    /* Here we need to add withdrawl count to array */

                                    $thresold_limit[$value->id][$dk][$gvalue]['exist'] = number_format(($dv*100)/$this->adm_data[$dk][$gvalue], 2);
                                    $thresold_limit[$value->id][$dk][$gvalue]['exist_count'] = $dv;
                                    $thresold_limit[$value->id][$dk][$gvalue]['target'] = number_format(($this->adm_data[$dk][$gvalue]*Config::get('variables.prefer_magnet_percentage'))/100, 2);

                                }

                                

                            }
                        }
                    }
                }
                
            }
        }
        $original_data = $thresold_limit;
        $this->magnet_thresold_limit = $thresold_limit;
//dd($this->magnet_thresold_limit);



        

        $this->availabilityArray = $availabilityArray;
        //dd($this->availabilityArray);

        /*
            Need to identify whether process selection is done for particular program/grade. If Yes, then we have to take that table based on process selection type and version.
        */

        $submission_data = [];
        $keys = array_keys($process_program);
        $subids = [];


        $selected_programs = [];
        $last_process = "regular";
        $last_version = 0;
        $first_denied = $second_denied = [];
        foreach($req['application_program_id'] as $key=>$value)
        {
            if($req['awardslot'.$value] > 0)
            {
                $rs = ProcessSelection::where('enrollment_id', Session::get('enrollment_id'))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(".$value.", selected_programs)")->where("commited", "Yes")->orderBy("created_at", "desc")->first();
        
                $table_name = "submissions_final_status";
                $version = 0;        
                if(!empty($rs))
                {
                    if($rs->type == "regular")
                    {
                        $table_name = "submissions_final_status";
                        $version = 0;
                    }
                    elseif($rs->type == "waitlist")
                    {
                        $table_name = "submissions_waitlist_final_status";
                        $version = $rs->version;
                        $last_process = "waitlist";
                        $last_version = $rs->version;
                    }
                    elseif($rs->type == "late_submission")
                    {
                        $table_name = "late_submissions_final_status";
                        $version = $rs->version;
                        $last_process = "late_submission";
                        $last_version = $rs->version;
                    }
                }

                $submissions=Submissions::
                    where('submissions.district_id', Session::get('district_id'))->where(function ($q) {
                                        $q->where("submission_status", "Waitlisted")->orWhere("submission_status", "Declined / Waitlist for other");
                                    })
                                ->where('submissions.enrollment_id', Session::get("enrollment_id"))
                                ->join($table_name, $table_name.".submission_id", "submissions.id")->where($table_name.".application_id", $application_id)->where($table_name.".version", $version)->select("submissions.*", $table_name.".first_offer_status", $table_name.".second_offer_status", $table_name.".first_choice_final_status", $table_name.".second_choice_final_status")
                    ->get();

                    foreach($submissions as $sk=>$sv)
                    {

                        $insert = false;

                        if(in_array($sv->first_choice_program_id, $keys))
                        {

                             if(in_array($sv->next_grade, $process_program[$sv->first_choice_program_id]))
                             {
                                
                                $insert = true;
                             }
                             if(($sv->first_choice_final_status == "Waitlisted" && !in_array($sv->second_offer_status, array("Pending", "Declined & Waitlisted", "Declined"))) || $sv->first_choice_final_status == "Denied due to Ineligibility" || $sv->first_choice_final_status == "Denied due to Incomplete Records")
                             {
                                $insert = false;
                                $first_denied[] = $sv->id;
                             }       
                        }
                        if(in_array($sv->second_choice_program_id, $keys))
                        {

                             if(in_array($sv->next_grade, $process_program[$sv->second_choice_program_id]))
                             {
                                $insert = true;
                             }
                             if(($sv->second_choice_final_status == "Waitlisted" && !in_array($sv->first_offer_status, array("Pending", "Declined & Waitlisted", "Declined"))) || $sv->second_choice_final_status == "Denied due to Ineligibility" || $sv->second_choice_final_status == "Denied due to Incomplete Records")
                             {
                                $insert = false;
                                $second_denied[] = $sv->id;
                             }
                             
                             if($sv->second_choice_final_status != "Waitlisted")
                             {
                                $second_denied[] = $sv->id;
                                $insert = false;
                             }       
                        }
                        if($insert && !in_array($sv->id, $subids))
                        {
                            $submission_data[] = $sv;
                            $subids[] = $sv->id;
                        }

                    }
            }
        }    
        

        //echo count($submission_data);exit;

        /* From here code is pending - All list of submission we have fetches from different table. Now we have to save unique submissions options in separate table which will update regularly */

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("last_date_online_acceptance", ">", date("Y-m-d H:i:s"))->where("form_id", $application_id)->where('type', 'waitlist')->first();

        
        

            // dd($first_denied, $second_denied);



        $preliminary_score = false;

        $application_data = Application::where("form_id", $application_id)->first();
        if(!empty($application_data) && $application_data->preliminary_processing == "Y")
            $preliminary_score = true;
        if(!empty($process_selection) && $process_selection->commited == "Yes")
        {
           // $final_data = $group_racial_composition = $incomplete_arr = $failed_arr = [];
            //return view("ProcessSelection::test_index",compact("final_data", "incomplete_arr", "failed_arr", "group_racial_composition", "preliminary_score"));
        } 
        $processType = Config::get('variables.process_separate_first_second_choice');
        $gradeWiseProcessing = Config::get('variables.grade_wise_processing');

        $firstData = Submissions::join("application_programs", "application_programs.id", "submissions.first_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->whereIn("submissions.id", $subids)->whereNotIn("submissions.id", $first_denied)->whereIn("submission_status", array('Waitlisted', 'Declined / Waitlist for other'))->where("form_id", $application_id)->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);

        

        $secondData = Submissions::join("application_programs", "application_programs.id", "submissions.second_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->where("second_choice", "!=", "")->whereIn("submission_status", array('Waitlisted', 'Declined / Waitlist for other'))->whereIn("submissions.id", $subids)->whereNotIn("submissions.id", $second_denied)->where("form_id", $application_id)->where(function($query) use ($keys){
                    $query->whereIn('first_choice_program_id', $keys);
                    $query->orWhereIn('second_choice_program_id', $keys);
                })->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);



        $first_processing_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->separate_data_processing($firstData); 
        //echo "<pre>";
        
        $second_processing_data = app('App\Modules\ProcessSelection\Controllers\ProcessSelectionController')->separate_data_processing($secondData);

        //dd($firstData, $secondData);




        $first_magnet_processing = $this->magnet_processing($first_processing_data['Magnet'], "first");
        
        $first_ib_processing = $this->ib_processing($first_processing_data['IB'], "first");
        $first_audition_processing = $this->audition_processing($first_processing_data['Audition'], "first");


        $second_magnet_processing = $this->magnet_processing($second_processing_data['Magnet'], "second");
        $second_ib_processing = $this->ib_processing($second_processing_data['IB'], "second");
        $second_audition_processing = $this->audition_processing($second_processing_data['Audition'], "second");

       // dd($first_ib_processing, $second_ib_processing, $first_magnet_processing, $second_magnet_processing);

        $magnet_offer_data = array_merge($first_magnet_processing['offered_arr'], $second_magnet_processing['offered_arr']);
        
        $sort_position =  array();
        foreach($magnet_offer_data as $key=>$value)
        {
            $sort_position['sort_position'][] = $value['sort_position'];
            //$student_profile['student_profile'][] = $value['student_profile'];
            $next_grade['next_grade'][] = $value['next_grade'];

        }
        array_multisort($next_grade['next_grade'], SORT_ASC, $sort_position['sort_position'], SORT_ASC, $magnet_offer_data);


        $arr = array("ib", "audition", "magnet");

//        dd($first_ib_processing, $second_ib_processing, $first_audition_processing, $second_audition_processing);
            //$rs = SubmissionsWaitlistFinalStatus::truncate();
            foreach($arr as $k=>$v)
            {
                $arr1 = array("first", "second");
                foreach($arr1 as $k1=>$v1)
                {
                    $str = $v1."_".$v."_processing";
                    if(isset(${$str}))
                    {
                        $data = ${$str};
                        //dd($data);
                        foreach($data['offered_arr'] as $key=>$value)
                        {
                            if($value['choice'] == $v1)
                            {
                                $insert = [];
                                $choice = $value['choice'];
                                $insert[$choice.'_waitlist_for'] = $value['program_id']; 
                                $insert[$choice.'_choice_final_status'] = "Offered";
                                $awarded_school = getProgramName($insert[$choice.'_waitlist_for']); 
                                $insert[$choice.'_choice_eligibility_reason'] = "";
                                do
                                {
                                    $code = mt_rand(100000, 999999);
                                    $user_code = SubmissionsFinalStatus::where('offer_slug', $code)->first();
                                    $user_code1 = LateSubmissionFinalStatus::where('offer_slug', $code)->first();
                                    $user_code2 = SubmissionsWaitlistFinalStatus::where('offer_slug', $code)->first();

                                }
                                while(!empty($user_code) && !empty($user_code1) && !empty($user_code2));
                                $insert['submission_id'] = $value['id'];
                                $insert['version'] = $actual_version;
                                $insert['offer_slug'] = $code;
                                $insert['application_id'] = $application_id;
                                $insert['enrollment_id'] = Session::get("enrollment_id");
                                //$rs = Submissions::where("id", $value['id'])->update(array("awarded_school"=>$awarded_school));

                                //dd($insert);
                                $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version"=>$actual_version], $insert);  

                                $tmpData = [];
                                $tmpData['submission_id'] = $value['id'];
                                $tmpData['choice_type'] = $v1;
                                $tmpData['status'] = "Offered";
                                $tmpData['offer_slug'] = $code;
                                $tmpData['reason'] = "";
                                $rs = SubmissionsTmpFinalStatus::create($tmpData);
                            }
                            


                            
                        }

                        foreach($data['waitlisted_arr'] as $key=>$value)
                        {
                            $insert = [];
                            $choice = $value['choice'];
                            $insert[$choice.'_waitlist_for'] = $value['program_id']; 
                            $insert[$choice.'_choice_final_status'] = "Waitlisted";
                            $insert[$choice.'_choice_eligibility_reason'] = "";
                            $insert['submission_id'] = $value['id'];
                            $insert['application_id'] = $application_id;
                            $insert['enrollment_id'] = Session::get("enrollment_id");
                            $insert['version'] = $actual_version;
                            $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version"=>$actual_version], $insert);

                            $tmpData = [];
                            $tmpData['submission_id'] = $value['id'];
                            $tmpData['choice_type'] = $v1;
                            $tmpData['status'] = "Waitlisted";
                            $tmpData['reason'] = "";
                            $rs = SubmissionsTmpFinalStatus::create($tmpData);
                        }

                        foreach($data['no_availability_arr'] as $key=>$value)
                        {
                            $insert = [];
                            $choice = $value['choice'];
                            $insert[$choice.'_waitlist_for'] = $value['program_id']; 
                            $insert[$choice.'_choice_final_status'] = "Waitlisted";
                            $insert[$choice.'_choice_eligibility_reason'] = "";
                            $insert['submission_id'] = $value['id'];
                            $insert['application_id'] = $application_id;
                            $insert['enrollment_id'] = Session::get("enrollment_id");
                            $insert['version'] = $actual_version;
                            $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version"=>$actual_version], $insert);

                            $tmpData = [];
                            $tmpData['submission_id'] = $value['id'];
                            $tmpData['choice_type'] = $v1;
                            $tmpData['status'] = "Waitlisted";
                            $tmpData['reason'] = "";
                            $rs = SubmissionsTmpFinalStatus::create($tmpData);
                        }

                        foreach($data['in_eligible'] as $key=>$value)
                        {
                            $insert = [];
                            $choice = $value['choice'];
                            $insert[$choice.'_waitlist_for'] = $value['program_id']; 
                            $insert[$choice.'_choice_final_status'] = "Denied due to Ineligibility";
                            $insert[$choice.'_choice_eligibility_reason'] = "";
                            $insert['submission_id'] = $value['id'];
                            $insert['application_id'] = $application_id;
                            $insert['enrollment_id'] = Session::get("enrollment_id");
                            $insert['version'] = $actual_version;
                            $rs = SubmissionsWaitlistFinalStatus::updateOrCreate(["submission_id" => $value['id'], "version"=>$actual_version], $insert);
                        }                        
                    }


                    //echo "F";exit;
                }
            }

            $rsUpdate = SubmissionsWaitlistFinalStatus::where("first_choice_final_status", "Offered")->where("version", $actual_version)->where("second_choice_final_status", "Waitlisted")->update(array("second_choice_final_status"=>"Pending", "second_waitlist_for"=>0));


            $schools = School::where("status", "Y")->get();
        $popHTML = "<table class='table table-striped mb-0 w-100' id='datatable4'><thead>
                    <tr><th class='text-center'>Program</th><th class='text-center'>School Home Zone</th><th class='text-center'>Rising Population from Home Zone</th><th class='text-center'>Calculated 7% Slots</th><th class='text-center'>Starting Population</th><th>Starting %</th><th class='text-center'>Offered</th><th class='text-center'>Offered %</th></tr></thead><tbody>";

        if($type == "update")
        {
            $rsTmp = SubmissionsSelectionReportMaster::where("type", "waitlist")->where("version", $actual_version)->where("enrollment_id", Session::get("enrollment_id"))->delete();
        }

        foreach($programs as $key=>$value)
        {
            $grades = explode(",",$value->grade_lavel);
            foreach($grades as $gvalue)
            {
                if(in_array($gvalue, $process_program[$value->id]))
                {
                     foreach($schools as $val)
                    {
                        if($value->process_logic == "Magnet")
                        {
                            $data = [];
                            $data['application_id'] = $application_id;
                            $data['program_name'] = $value->name ." - Grade ".$gvalue;
                            $data['school_home_zone']= $val->name;
                            $data['enrollment_id'] = Session::get("enrollment_id");
                            $data['type'] = "waitlist";
                            $data['version'] = $actual_version;

                            $popHTML .= "<tr>";
                            $popHTML .= "<td class='text-center'>".$value->name ." - Grade ".$gvalue."</td>";
                            $popHTML .= "<td class='text-center'>". $val->name."</td>";
                            if(isset($this->adm_data[$val->id][$gvalue]))
                            {
                                $data['rising_population_homezone'] = $this->adm_data[$val->id][$gvalue];
                                $popHTML .= "<td class='text-center'>". $this->adm_data[$val->id][$gvalue]."</td>";
                            }
                            else
                            {
                                $popHTML .= "<td class='text-center'>0</td>";
                                $data['rising_population_homezone'] = 0;
                            }

                            if(isset($thresold_limit[$value->id][$val->id][$gvalue]['target']))
                            {
                                $popHTML .= "<td class='text-center'>".$thresold_limit[$value->id][$val->id][$gvalue]['target']."</td>";
                                $data['calculated_target_slot'] = $thresold_limit[$value->id][$val->id][$gvalue]['target'];
                            }
                            else
                            {
                                $popHTML .= "<td class='text-center'>0</td>"; 
                                $data['calculated_target_slot'] = 0;   
                            }

                            if(isset($exist_availability[$value->id][$gvalue][$val->id]))
                            {
                                $data['starting_population'] = $exist_availability[$value->id][$gvalue][$val->id];
                                $popHTML .= "<td class='text-center'>".$exist_availability[$value->id][$gvalue][$val->id]."</td>";
                            }
                            else
                            {
                                $popHTML .= "<td class='text-center'>0</td>"; 
                                $data['starting_population'] = 0; 
                            }

                            if(isset($thresold_limit[$value->id][$val->id][$gvalue]['exist']))
                            {
                                $data['starting_percent'] = $thresold_limit[$value->id][$val->id][$gvalue]['exist'];
                                $popHTML .= "<td class='text-center'>".$thresold_limit[$value->id][$val->id][$gvalue]['exist']."%</td>";
                            }
                            else
                            {
                                $popHTML .= "<td class='text-center'>0%</td>"; 
                                $data['starting_percent'] = 0;
                            }

                            if(isset($this->magnet_offered_data[$value->id][$gvalue][$val->id]))
                            {
                                $data['offered'] = $this->magnet_offered_data[$value->id][$gvalue][$val->id];
                                $popHTML .= "<td class='text-center'>".$this->magnet_offered_data[$value->id][$gvalue][$val->id]."</td>";
                            }
                            else
                            {
                                $data['offered'] = 0;
                                $popHTML .= "<td class='text-center'>0</td>";      
                            }

                            if(isset($this->magnet_thresold_limit[$value->id][$val->id][$gvalue]['exist']))
                            {
                                $data['offered_percent'] = $this->magnet_thresold_limit[$value->id][$val->id][$gvalue]['exist'];
                                $popHTML .= "<td class='text-center'>".$this->magnet_thresold_limit[$value->id][$val->id][$gvalue]['exist']."%</td>";
                            }
                            else
                            {
                                $data['offered_percent'] = 0;
                                $popHTML .= "<td class='text-center'>0%</td>"; 
                            }

                            $popHTML .= "</tr>";  

                            if($type == "update")
                            {
                                //dd($data);
                                $rsTmp = SubmissionsSelectionReportMaster::create($data);        
                            }
                           
                        }
                  

                    }
                }
                
               

            }
        }
        $popHTML .= "</tbody></table>";

        $additional_data = $this->get_additional_info($application_id); 
        $displayother = $additional_data['displayother'];
        $display_outcome = 0;//$additional_data['display_outcome'];


        $rsUpdate = SubmissionsWaitlistFinalStatus::where("first_choice_final_status", "Offered")->where("application_id", $application_id)->where('version', $actual_version)->where("second_choice_final_status", "Waitlisted")->get();
        foreach($rsUpdate as $ukey=>$uvalue)
        {
            $rs = SubmissionsTmpFinalStatus::where("submission_id", $uvalue->submission_id)->where("choice_type", "second")->update(["status"=>"Pending"]);
        }
            //dd($first_magnet_processing, $second_ib_processing);

        return view("Waitlist::test_index",compact("magnet_offer_data", "first_ib_processing", "second_ib_processing", "first_audition_processing", "second_audition_processing", "first_magnet_processing", "second_magnet_processing", "popHTML", "application_id", "display_outcome", "type", "actual_version"));

        /* ======================================================================== */



       

       
    }


    public function storeAllAvailability(Request $request, $application_id)
    {
      //   return $request;
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "waitlist")->orderBy("created_at", "DESC")->first();
        
        $version = 0;
        if(!empty($process_selection))
        {
            if($process_selection->commited == 'Yes')
            {
                $version = $process_selection->version + 1;
            }
            else
            {
                $version = $process_selection->version;
            }

        }

        $req = $request->all();
        $type = "";
        if(isset($req['type']))
            $type = $req['type'];


        $selected_programs = [];
        $process = false;
        foreach($req['application_program_id'] as $key=>$value)
        {
            if($req['awardslot'.$value] > 0)
            {
                $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->whereRaw("FIND_IN_SET(".$value.", selected_programs)")->where("type", "waitlist")->where("version", $version)->orderBy("created_at", "DESC")->first();
                
                if(!empty($process_selection))
                {
                    if($process_selection->commited != 'Yes')
                    {
                        $process = true;
                    }
                }
                else
                {
                    $process = true;
                }
                $selected_programs[] = $value;
            }
        }

        if($req['last_date_online_acceptance'] != '' || $req['process_event'] == "saveonly")
        {

            //if($req['last_date_online_acceptance'] != '' && $req['process_event'] != "saveonly")
            //{
                if($req['last_date_online_acceptance'] != '')
                {
                    $data['last_date_online_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_online_acceptance']));
                    $data['last_date_offline_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_offline_acceptance']));
                }

                $data['district_id'] = Session::get("district_id");
                $data['enrollment_id'] = Session::get("enrollment_id");
                $data['application_id'] = $application_id;
                $data['district_id'] = Session::get("district_id");
                $data['type'] = "waitlist";
                $data['version'] = $version;
                $data['selected_programs'] = implode(",", $selected_programs);
                
                $rs = ProcessSelection::updateOrCreate(['form_id'=>$data['application_id'], "version" => $version, "type"=>"waitlist", "enrollment_id"=>Session::get("enrollment_id")], $data);

                $rs = ProcessSelection::where('form_id', $data['application_id'])->where("enrollment_id", Session::get("enrollment_id"))->where("version", $version)->where("type", "waitlist")->first();

            //}

            $t = WaitlistProcessLogs::where("process_log_id", $rs->id)->delete();
//dd($req['application_program_id']);
           // dd($req);
            foreach($req['application_program_id'] as $key=>$value)
            {
                if($req['awardslot'.$value] > 0)
                {
                    $insert = [];
                    $insert['process_log_id'] = $rs->id;
                    $insert['enrollment_id'] = Session::get("enrollment_id");
                    $insert['program_id'] = $req['program_id'.$value];
                    $insert['grade'] = $req['grade'.$value];
                    $insert['application_id'] = $rs->application_id;
                    $insert['version'] = $version;
                    $insert['program_name'] = $req['program_name'.$value];
                    $insert['total_seats'] = $req['total_seats'.$value];
                    $insert['additional_seats'] = $req['additional_seats'.$value];
                    $insert['withdrawn_student'] = $req['withdrawn_student'.$value];
                    if($req['withdrawn_student'.$value] != "Yes")
                    {
                        $insert['black_withdrawn'] = 0;
                        $insert['white_withdrawn'] = 0;
                        $insert['other_withdrawn'] = 0;
                    }
                    else
                    {
                        $insert['black_withdrawn'] = $req['black'.$value];
                        $insert['white_withdrawn'] = $req['white'.$value];
                        $insert['other_withdrawn'] = $req['other'.$value];
                    }
                    $insert['waitlisted'] = $req['waitlist_count'.$value];
                    $insert['available_slots'] = $req['available_slot'.$value];
                    $insert['slots_to_awards'] = $req['awardslot'.$value];
                    $insert['generated_by'] = Auth::user()->id;  

                    if (isset($req['homezone_schools'][$value]) && !empty($req['homezone_schools'][$value])) {
                        $insert['homezone'] = json_encode($req['homezone_schools'][$value]);
                    } else {
                        $insert['homezone'] = NULL;
                    }
                    
                    $rs1 = WaitlistProcessLogs::updateOrCreate(["process_log_id"=>$rs->id, "program_name"=>$insert['program_name']], $insert);    

                }
            }


        }

        $data = array();
        
        if($process && $req['process_event'] != "saveonly")
        {
            $rdel = SubmissionsWaitlistFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->delete();
            return $this->processWaitlist($req, $application_id, $version, "update");
        }

        echo "done";
            
    }


    public function get_additional_info($application_id=0, $version=0)
    {
        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "waitlist")->where("version", $version)->first();

        $display_outcome = 0;
        $displayother = 0;
        
        if(!empty($process_selection))
        {
            $displayother = 1;
            
            if($process_selection->commited == "Yes")
            {
                $display_outcome = 1;
                $last_date_online_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_online_acceptance));
                $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_offline_acceptance));

            }
            else
            {
                $last_date_online_acceptance = "";
                $last_date_offline_acceptance = "";

            }

        }
        else
        {
            $last_date_online_acceptance = "";
            $last_date_offline_acceptance = "";
        }

        return array("display_outcome"=>$display_outcome, "displayother"=>$displayother, "last_date_online_acceptance"=>$last_date_online_acceptance, "last_date_offline_acceptance"=>$last_date_offline_acceptance);
    }


    public function submissions_results_application($application_id=59, $version=0)
    {
        $selected_programs = [];
        if($version == 0)
        {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "waitlist")->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $selected_programs = explode(",", $rs->selected_programs);
        }
        else
        {
            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("type", "waitlist")->where("version", $version)->orderBy("created_at", "DESC")->first();

            $version = $rs->version;
            $selected_programs = explode(",", $rs->selected_programs);
        }
        $program_ids = [];
        foreach($selected_programs as $key=>$value)
        {
            $rs = ProgramGradeMapping::where("id", $value)->first();

            $program_ids[] =   $rs->program_id;//getApplicationProgramId($value);
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
            ->where('district_id', $district_id)
            ->where("submissions.enrollment_id", Session::get("enrollment_id"))
            ->where("submissions.form_id", $application_id)->where("submissions_waitlist_final_status.version", $version)->join("submissions_waitlist_final_status", "submissions_waitlist_final_status.submission_id", "submissions.id")
            ->get(['submissions.id', 'first_name', 'last_name', 'current_school', 'first_offered_rank', 'second_offered_rank', 'first_choice_program_id', 'second_choice_program_id', 'next_grade', 'race', 'calculated_race', 'first_choice_final_status', 'second_choice_final_status']);

        $final_data = array();
        foreach($submissions as $key=>$value)
        {
                $tmp = array();
                $tmp['id'] = $value->id;
                $tmp['name'] = $value->first_name. " ". $value->last_name;
                $tmp['grade'] = $value->next_grade;
                $tmp['school'] = $value->current_school;
                $tmp['choice'] = 1;
                $tmp['race'] = $value->calculated_race;
                $tmp['program'] = getProgramName($value->first_choice_program_id). " - Grade ".$value->next_grade;
                $tmp['program_name'] = getProgramName($value->first_choice_program_id);
                $tmp['offered_status'] = $value->first_choice_final_status;
                if($value->first_choice_final_status == "Offered")
                    $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                elseif($value->first_choice_final_status == "Denied due to Ineligibility")
                    $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                elseif($value->first_choice_final_status == "Waitlisted")
                    $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                elseif($value->first_choice_final_status == "Denied due to Incomplete Records")
                    $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                else
                    $tmp['outcome'] = "";

                if(!in_array($value->first_choice_final_status, array("Denied due to Ineligibility",  "Denied due to Incomplete Records")) && in_array($value->first_choice_program_id, $program_ids))
                        $final_data[] = $tmp;

                if($value->second_choice_program_id != 0)
                {
                    $tmp = array();
                    $tmp['id'] = $value->id;
                    $tmp['name'] = $value->first_name. " ". $value->last_name;
                    $tmp['grade'] = $value->next_grade;
                    $tmp['school'] = $value->current_school;
                    $tmp['race'] = $value->calculated_race;
                    $tmp['choice'] = 2;
                    $tmp['program'] = getProgramName($value->second_choice_program_id). " - Grade ".$value->next_grade;
                    $tmp['program_name'] = getProgramName($value->second_choice_program_id);
                    $tmp['offered_status'] = $value->second_choice_final_status;

                    if($value->second_choice_final_status == "Offered")
                        $tmp['outcome'] = "<div class='alert1 alert-success text-center'>Offered</div>";
                    elseif($value->second_choice_final_status == "Denied due to Ineligibility")
                        $tmp['outcome'] = "<div class='alert1 alert-info text-center'>Denied due to Ineligibility</div>";
                    elseif($value->second_choice_final_status == "Waitlisted")
                        $tmp['outcome'] = "<div class='alert1 alert-warning text-center'>Waitlist</div>";
                    elseif($value->second_choice_final_status == "Denied due to Incomplete Records")
                        $tmp['outcome'] = "<div class='alert1 alert-danger text-center'>Denied due to Incomplete Records</div>";
                    else
                        $tmp['outcome'] = "";

                    if(!in_array($value->second_choice_final_status, array("Denied due to Ineligibility", "Pending", "Denied due to Incomplete Records")) && in_array($value->second_choice_program_id, $program_ids))
                        $final_data[] = $tmp;

                }

        }
        //dd($final_data);    
        $grade = $outcome = array();
        foreach($final_data as $key=>$value)
        {
            $grade['grade'][] = $value['grade']; 
            $outcome['outcome'][] = $value['outcome']; 
        }
        array_multisort($grade['grade'], SORT_ASC, $outcome['outcome'], SORT_DESC, $final_data);


        return view("Waitlist::submissions_result", compact('final_data', 'pid', 'display_outcome', "application_id", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance"));

    }


    
    public function generate_race_composition_update($group_data, $total_seats, $race, $type="S")   
    {
        $update = "";
        $tst = $group_data;
        $total_seats = $tst['total'];
        foreach($tst as $tstk=>$tstv)
        {
            if($tstk != "total" && $tstk != "no_previous")
            {
                if($tstv > 0)
                    $tst_percent = number_format($tstv*100/$total_seats, 2);
                else
                    $tst_percent = 0;
                if($tstk == $race)
                {
                    if($type=="W")
                        $clname = "text-danger";
                    elseif($type=="S")
                        $clname = "text-success";
                    else
                        $clname = "";
                }
                else
                    $clname = "";
                $update .= "<div><span><strong>".ucfirst($tstk)."</strong> :</span> <span class='".$clname."'>".$tst_percent."% (".$tstv.")</span></div>";

            }
        }
        return $update;

    } 

    public function check_race_previous_data($group_name, $race)
    {
        $data = $this->group_racial_composition[$group_name];

        $zero = 0;

        foreach($data as $key=>$value)
        {
            if($key != 'total' && $key != "no_previous" && $key != $race)
            {
                if($value == 0)
                {
                    $zero++;
                }
            }
        }
        if($zero > 0)
        {
            return "OnlyThisOffered";
        }
        else
        {
            if($data[$race] == 0)
            {
                return "OnlyThisOffered";
            }
            else
            {
                $is_lower = $is_self_lower = false;
                foreach($data as $key=>$value)
                {
                    if($key != 'total' && $key != "no_previous" )
                    {
                        if($key != $race)
                            $tmp = $value;
                        else
                            $tmp = $value+1;
                        $total = $data['total'] + 1;
                        $new_percent = number_format($tmp*100/$total, 2);
                        if($new_percent < $this->enrollment_race_data[$group_name][$key]['min'] || $new_percent > $this->enrollment_race_data[$group_name][$key]['max'])
                            $is_lower = true;

                    }
                }
//                exit;
                if($is_lower)
                    return "SkipOffered";
                elseif($is_self_lower)
                    return "OnlyThisOffered";
                else
                {
                    //$this->group_racial_composition[$group_name]['no_previous'] = 'N'; 
                    return "OfferedWaitlisted";
                }
            }
        }
    }


    public function check_all_race_range($group_name, $race, $id)
    {

        $tmp_enroll = $this->enrollment_race_data[$group_name];
        $tmp = $this->group_racial_composition[$group_name];
        $total_seats = $tmp['total'];
        $race_percent = $tmp[$race];
        if($total_seats > 0)
            $original_race_percent = number_format($race_percent*100/$total_seats, 2);
        else
            $original_race_percent = 0;
        $total_seats++;
        $race_percent++;
        $new_percent = number_format($race_percent*100/$total_seats, 2);

       

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


        if($new_percent >= $tmp_enroll[$race]['min'] && $new_percent <= $tmp_enroll[$race]['max'])
        {
            $in_range = true;
            $max = 0;
            foreach($tmp as $key=>$value)
            {
                if($key != $race && $key != "total" && $key != "no_previous")
                {
                    $total = $tmp['total'] + 1;
                    $new_percent = number_format($value*100/$total, 2);
                    if($new_percent < $tmp_enroll[$key]['min'])
                    {
                        $in_range = false;
                    }
                    elseif($new_percent > $tmp_enroll[$key]['max'])
                    {
                        $in_range = false;
                        $max++;
                    }

                }
            }

            if(!$in_range)
            {
                if($max > 0)
                    return true;
                else
                    return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if($original_race_percent < $tmp_enroll[$race]['min'])
                return true;
            else
                return false;
        }
    }


    public function selection_accept(Request $request, $application_id)
    {

        $form_id = 1;
        $district_id = \Session('district_id');

        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("commited", "No")->where("type", "waitlist")->orderBy("created_at", "DESC")->first();
        $update_id = $rs->id;
        $version = $rs->version;




        $data = SubmissionsWaitlistFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("version", $version)->get();
        foreach($data as $key=>$value)
        {
            $status = $value->first_choice_final_status;
            if($value->second_choice_final_status == "Offered")
                $status = "Offered";

            if($value->first_choice_final_status == "Pending")
                $status = $value->second_choice_final_status;

            $submission_id = $value->submission_id;
            $rs = Submissions::where("id", $submission_id)->select("submission_status")->first();
            $old_status = $rs->submission_status;
            $awarded_school = "";

            $comment = "By Accept and Commit Event";
            if($status == "Offered")
            {
                $submission = Submissions::where("id", $value->submission_id)->first();
                if($value->first_choice_final_status == "Offered")
                {
                    $program_name = getProgramName($submission->first_choice_program_id);
                    $awarded_school = $program_name;
                }
                else if($value->second_choice_final_status == "Offered")
                {
                    $program_name = getProgramName($submission->second_choice_program_id);
                    $awarded_school = $program_name;
                }
                else
                {
                    $program_name = "";
                }

                $program_name .= " - Grade ".$submission->next_grade;
                $comment = "System has Offered ".$program_name." to Parent";
            }
            else if($status == "Denied due to Ineligibility")
            {
                if($value->first_choice_eligibility_reason != '')
                {
                    if($value->first_choice_eligibility_reason == "Both")
                    {
                        $comment = "System has denied the application because of Grades and CDI Ineligibility";
                    }
                    else if($value->first_choice_eligibility_reason == "Grade")
                    {
                        $comment = "System has denied the application because of Grades Ineligibility";
                    }
                    else
                    {
                        $comment = $value->first_choice_eligibility_reason;   
                    }
                }
            }
            else if($status == "Denied due to Incomplete Records")
            {
                if($value->incomplete_reason != '')
                {
                    if($value->incomplete_reason == "Both")
                    {
                       $comment = "System has denied the application because of Grades and CDI Ineligibility";
                    }
                    else if($value->incomplete_reason == "Grade")
                    {
                        $comment = "System has denied the application because of Incomplete Grades";
                    }
                    else
                    {
                        $comment = "System has denied the application because of Incomplete Records";   
                    }
                }
            }
            $rs = SubmissionsStatusLog::create(array("submission_id"=>$submission_id, "new_status"=>$status, "old_status"=>$old_status, "updated_by"=>Auth::user()->id, "comment"=>"Waitlist Process :: " . $comment));
            $rs = SubmissionsWaitlistStatusUniqueLog::updateOrCreate(["submission_id" => $submission_id], array("submission_id"=>$submission_id, "new_status"=>$status, "old_status"=>$old_status, "updated_by"=>Auth::user()->id, "version"=>$version));
            $rs = Submissions::where("id", $submission_id)->update(["submission_status" => $status, "awarded_school"=>$awarded_school]);
        }

        $rs = SubmissionsTmpFinalStatus::get();
        foreach($rs as $key=>$value)
        {
            if($value->offer_slug != '')
            {
                
                $tmp = [];
                $str = $value->choice_type."_choice_final_status";
                $tmp[$str] = $value->status;
                $tmp['offer_slug'] = $value->offer_slug;
                $str = $value->choice_type."_choice_eligibility_reason";
                $tmp[$str] = $value->reason;


                
                $rsExist = DB::table("submissions_latest_final_status")->where("submission_id", $value->submission_id)->first();
                if(!empty($rsExist))
                {
                    $rsupdate = DB::table("submissions_latest_final_status")->where("submission_id", $value->submission_id)->update($tmp);
                }
                else
                {
                    $rsupdate = DB::table("submissions_latest_final_status")->insert($tmp);
                }

            }
            else
            {
                $rsExist = DB::table("submissions_latest_final_status")->where("submission_id", $value->submission_id)->first();
                if(!empty($rsExist))
                {
                    $rsupdate = DB::table("submissions_latest_final_status")->where("submission_id", $value->submission_id)->update(array($value->choice_type."_choice_final_status"=>$value->status, $value->choice_type."_choice_eligibility_reason"=>$value->reason));

                }
                else
                {
                    $rsupdate = DB::table("submissions_latest_final_status")->insert(array($value->choice_type."_choice_final_status"=>$value->status, $value->choice_type."_choice_eligibility_reason"=>$value->reason));

                }
            }
        }
        $rs = ProcessSelection::where("id", $update_id)->update(array("commited"=>"Yes"));
        echo "Done";
        exit;
    }

    public function checkWailistOpen()
    {
        $rs = WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("last_date_online", ">", date("Y-m-d H:i:s"))->first();
        if(!empty($rs))
            return 1;
        else
            return 0;
    }


    public function selection_revert()
    {
        $version = $this->checkWailistOpen();
        $quotations = SubmissionsWaitlistStatusUniqueLog::orderBy('created_at','ASC')->where("version", $version)
                ->get()
                ->unique('submission_id');

        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_waitlist_online_acceptance")->delete();
        $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_waitlist_offline_acceptance")->delete();


        foreach($quotations as $key=>$value)
        {
            $rs = Submissions::where("id", $value->submission_id)->update(array("submission_status"=>$value->old_status));
        }
        SubmissionsWaitlistStatusUniqueLog::where("version", $version)->delete();
        SubmissionsWaitlistFinalStatus::where("version", $version)->delete();
        WaitlistProcessLogs::where("enrollment_id", Session::get("enrollment_id"))->where("version", $version)->delete();
        WaitlistAvailabilityLog::truncate();
        WaitlistAvailabilityProcessLog::where("version", $version)->where("type", "Waitlist")->delete();
        //SubmissionsStatusUniquesLog::truncate();

    }


    public function check_last_process()
    {
        $count = LateSubmissionProcessLogs::count();
        $count1 = WaitlistProcessLogs::count();
        if($count > 0 && $count1 > 0)
        {
            $rs = LateSubmissionProcessLogs::orderBy("created_at","desc")->first();
            $rs1 = WaitlistProcessLogs::orderBy("created_at","desc")->first();
            if($rs1->create_at > $rs->created_at)
                return "waitlist";
            else
                return "late_submission";
        }
        elseif($count1 > 0)
            return "waitlist";
        elseif($count > 0)
            return "late_submission";
        else
            return "regular";
    }

    
}
