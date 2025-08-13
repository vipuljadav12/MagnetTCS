<?php

namespace App\Modules\ProcessSelection\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\School\Models\Grade;
use App\Modules\ProcessSelection\Models\{Availability,ProgramSwingData,PreliminaryScore,ProcessSelection};
use App\Modules\setEligibility\Models\setEligibility;
use App\Modules\Form\Models\Form;
use App\Modules\Program\Models\{Program,ProgramEligibility,ProgramGradeMapping};
use App\Modules\DistrictConfiguration\Models\DistrictConfiguration;
use App\Modules\Enrollment\Models\{Enrollment,EnrollmentRaceComposition,ADMData};
use App\Modules\Application\Models\ApplicationProgram;
use App\Modules\Application\Models\Application;
use App\Modules\LateSubmission\Models\{LateSubmissionProcessLogs,LateSubmissionAvailabilityLog,LateSubmissionAvailabilityProcessLog,LateSubmissionIndividualAvailability};

use App\Modules\Waitlist\Models\{WaitlistProcessLogs,WaitlistAvailabilityLog,WaitlistAvailabilityProcessLog,WaitlistIndividualAvailability};
use App\Modules\Submissions\Models\{Submissions,SubmissionGrade,SubmissionConductDisciplinaryInfo,SubmissionsFinalStatus,SubmissionsStatusLog,SubmissionsStatusUniqueLog,SubmissionCommitteeScore,SubmissionCompositeScore,SubmissionsSelectionReportMaster,SubmissionsRaceCompositionReport,LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus,SubmissionInterviewScore,SubmissionsLatestFinalStatus,SubmissionAudition,SubmissionsStudentProfileScore};
use App\Modules\School\Models\School;
use Auth;
use DB;
use Session;
use Config;
use App\SubmissionRaw;

class ProcessSelectionController extends Controller
{
    /* This function will generate Racial composition according to program group 
    set in Applicaiton Filter level at "Selection" tab of program */

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


    public function validateAllNecessity($application_id)
    {

        $rs = Submissions::where("form_id", $application_id)->where("submission_status", "Offered")->count();
        if($rs > 0)
            return "Selected Applications has still open offered submissions.";
        else
            return "OK";
       // return "OK";

    }

   

    public function application_index($type='')
    {
        
        $applications = Form::where("status", "y")->get();
        return view("ProcessSelection::application_index", compact("applications"));
    }

    public function validateApplication($application_id)
    {
        //echo "OK";exit;
        $error_msg = $this->validateAllNecessity($application_id);
        if($error_msg == "")
            $error_msg = "OK";
        echo $error_msg;
    }

    public function checkStudentProfileLevel($submission_id)
    {
        $student_profile = SubmissionsStudentProfileScore::where("submission_id", $submission_id)->first();
        if(!empty($student_profile))
            return $student_profile->student_profile_score;
        else
            return 0;
    }

    public function checkSubmissionAuditionValue($submission_id)
    {
        $submission_audition = SubmissionAudition::where("submission_id", $submission_id)->first();
        if(!empty($submission_audition))
        {
            if($submission_audition->data != '')
            {
                $data = json_decode($submission_audition->data);
                if(isset($data->first_data) && in_array($data->first_data, array('Ready', 'Exceptional')))
                {
                    return [$data->first_data, "Pass"];
                }
                if(isset($data->second_data) && in_array($data->second_data, array('Ready', 'Exceptional')))
                {
                    return [$data->second_data, "Pass"];
                }
            }
        } 
        return ["", "Fail"];
    }

    public function checkSubmissionCommitteeValue($submission_id, $program_id)
    {
        $submission_committee = SubmissionCommitteeScore::where("program_id", $program_id)->where("submission_id", $submission_id)->first();
        if(!empty($submission_committee))
        {
            if($submission_committee->data == 'Recommend')
            {
                return [$submission_committee->data, "Pass"];
            }
        } 
        return ["", "Fail"];
    }

    public function fetch_programs_group($application_id)
    {
        $af = ['applicant_filter1', 'applicant_filter2', 'applicant_filter3'];
        $programs=Program::where('status','!=','T')->where('district_id', Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->where("program.parent_submission_form", $application_id)->get();

        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();
        if($application_data->preliminary_processing == "Y")
            $preliminary_score = true;

        // Application Filters
        $af_programs = [];
        if (!empty($programs)) {
            foreach ($programs as $key => $program) {
                $cnt = 0;
                foreach ($af as $key => $af_field) {
                    if ($program->$af_field == '')
                        $cnt++;
                    if (($program->$af_field != '') && !in_array($program->$af_field, $af_programs)) {
                        array_push($af_programs, $program->$af_field);
                    }
                }
                if($cnt == count($af))
                {
                    array_push($af_programs, $program->name);
                }
            }
        }
        return $af_programs;
    }

    public function groupByRacism($af_programs)
    {
        $af = [
                'application_filter_1' => 'applicant_filter1', 
                'application_filter_2' => 'applicant_filter2', 
                'application_filter_3' => 'applicant_filter3'
            ]; 
        $seat_type = [
            'black_seats' => 'Black', 
            'white_seats' => 'White',
            'other_seats' => 'Other'
        ];
        $group_race_array = $program_group = [];
        foreach($af_programs as $key=>$value)
        {
            $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where('status','!=','T')->where(function($q) use ($value) {
                        $q->where('applicant_filter1', $value);
                        $q->orWhere('applicant_filter2', $value);
                        $q->orWhere('applicant_filter3', $value);
                        $q->orWhere('name', $value);
                    })->get();
            $filtered_programs = [];
            $avg_data = [];
            if(count($programs) <= 0)
            {
                $programs = Program::where("district_id", Session::get("district_id"))->where("enrollment_id", Session::get("enrollment_id"))->where('status','!=','T')->where("name", $value)->get();
            }
            if (!empty($programs)) {
                 $programs_avg = [];
                 foreach ($programs as $pkey => $program) {
                    if($program->selection_by == "Program Name")
                        $selection_by = "name";
                    else
                        $selection_by = strtolower(str_replace(' ', '_', $program->selection_by));
                    if (
                        isset($af[$selection_by]) &&
                        ($program->{$af[$selection_by]} != '') && 
                        $program->{$af[$selection_by]} == $value) 
                    {
                        $filtered_programs[] = $program;
                        array_push($programs_avg, $program->id);
                        $program_group[$program->id] = $value;
                    }
                    elseif($selection_by == "name" || $selection_by == "program_name" || $selection_by == "")
                    {
                        if($program->name == $value)
                        {
                            $filtered_programs[] = $program;
                            array_push($programs_avg, $program->id);
                            $program_group[$program->id] = $value;
                        }
                    }
                }

                if (!empty($programs_avg)) {
                    $total = 0;
                    $availabilities =  Availability::whereIn("program_id",$programs_avg)->where('district_id',Session('district_id'))->get(array_keys($seat_type));

                    foreach ($seat_type as $stype => $svalue) {
                        $sum = $availabilities->sum($stype);
                        $total += $sum;
                        if($sum == 0)
                            $avg_data['no_previous'] = "Y";
                        else
                            $avg_data['no_previous'] = "N";
                        $avg_data[strtolower($svalue)] = $sum;
                    }
                    $avg_data['total'] = $total;

                }
                $group_race_array[$value] = $avg_data;

            }
            

        }
        return array("group_race" => $group_race_array, "program_group"=>$program_group);
    }

    public function separate_data_processing($dataArr)
    {
        $magnet_arr = $ib_arr = $audition_arr = [];

        foreach($dataArr as $key=>$value)
        {
            if($value->process_logic == "Magnet")
                $magnet_arr[] = $value;
            elseif($value->process_logic == "Audition")
                $audition_arr[] = $value;
            elseif($value->process_logic == "IB")
                $ib_arr[] = $value;
        }
        return array("IB"=>$ib_arr, "Audition"=>$audition_arr, "Magnet"=>$magnet_arr);
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

 
    public function magnet_processing($magnet_data, $choice="first")
    {
        $eligible_arr = $in_eligible_arr = [];
        foreach($magnet_data as $key=>$value)
        {
            $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
            $tmp['program_id'] = $value->program_id;
            $tmp['choice'] = $choice;
            $tmp['student_profile'] = $this->checkStudentProfileLevel($value->id);
            $tmp['school_id'] = getSchoolMasterName($tmp['zoned_school']);
            if($tmp['student_profile'] >= 73 && $tmp['submission_status'] == 'Active')
                $eligible_arr[] = $tmp;
            else
                $in_eligible_arr[] = $tmp;
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

                        if($new_percent >= 7)
                        { //4
                            
                            if($this->availabilityArray[$program_id][$next_grade] > 0)
                            { //3
                                $removedId = [];
                                $offered = false;
                                foreach($hold_arr as $key=>$value)
                                { // 2
                                    if($program_id == $value['program_id'] && $next_grade == $value['next_grade'] && $school_id == $value['school_id'])
                                    { //1
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
        return array("offered_arr"=>$offered_arr, "no_availability_arr"=>$no_availability_arr, "in_eligible"=>$in_eligible_arr, "waitlisted_arr"=>$waitlisted_arr);
    }


    public function ib_processing($ib_data, $choice="first")
    {
        $eligible_arr = $in_eligible_arr = [];
        foreach($ib_data as $key=>$value)
        {
            $tmp = app('App\Modules\Reports\Controllers\ReportsController')->convertToArray($value);
            $tmp['choice'] = $choice;
            $tmp['program_id'] = $value->program_id;
            $score = $this->checkSubmissionCommitteeValue($value->id, $value->program_id);
            $tmp['student_profile_score'] = $score[0];
            $tmp['student_profile'] = $score[1];
            $tmp['school_id'] = getSchoolMasterName($tmp['zoned_school']);
            if($tmp['student_profile'] == "Pass" && $tmp['submission_status'] == 'Active')
                $eligible_arr[] = $tmp;
            else
                $in_eligible_arr[] = $tmp;
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
            $score = $this->checkSubmissionAuditionValue($value->id);

            $tmp['student_profile_score'] = $score[0];
            $tmp['student_profile'] = $score[1];

            //$tmp['student_profile'] = $this->checkSubmissionAuditionValue($value->id);
            $tmp['school_id'] = getSchoolMasterName($tmp['zoned_school']);
            if($tmp['student_profile'] == "Pass" && $tmp['submission_status'] == 'Active')
                $eligible_arr[] = $tmp;
            else
                $in_eligible_arr[] = $tmp;
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

    public function calculateAllStudentProfile($application_id)
    {
        set_time_limit(0);
        /* from here */
        $firstData = Submissions::join("application_programs", "application_programs.id", "submissions.first_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->whereIn("submission_status", array('Active', 'Pending'))->where("form_id", $application_id)->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);

        

        $secondData = Submissions::join("application_programs", "application_programs.id", "submissions.second_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->where("second_choice", "!=", "")->whereIn("submission_status", array('Active', 'Pending'))->where("form_id", $application_id)->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);

        $first_processing_data = $this->separate_data_processing($firstData);
        $second_processing_data = $this->separate_data_processing($secondData);

        $arr1 = array("first", "second");
        foreach($arr1 as $k1=>$v1)
        {
            $str = $v1."_processing_data";
            $data = ${$str}['Magnet'];
            foreach($data as $dk=>$dv)
            {
                $student_profile = app('App\Modules\Submissions\Controllers\SubmissionsController')->calculateStudentProfile($dv['id']);
                

                $score = $this->checkStudentProfileLevel($dv['id']);
                $data = [];
                $data['submission_id'] = $dv['id'];
                $data['student_profile_score'] = $student_profile['profile']['final_percent'] ?? 0;


                $rs = SubmissionsStudentProfileScore::updateOrCreate(
                    ['submission_id' => $dv['id']],
                    $data
                );
            }

        }



        $msg = '';

        Session::flash("success", 'Profile score calculated successfully.');
        return redirect('/admin/Process/Selection/step2/'.$application_id);
    }

    public function checkAllStudentProfile($first_processing_data, $second_processing_data)
    {
        $missing_profile_score = 0;

        $arr1 = array("first", "second");
        foreach($arr1 as $k1=>$v1)
        {
            $str = $v1."_processing_data";
            $data = ${$str};
            foreach($data as $dk=>$dv)
            {
                $rs = SubmissionsStudentProfileScore::where("submission_id", $dv['id'])->first();
                if(empty($rs))
                    $missing_profile_score++;
            }

        }
        $msg = "";
        if($missing_profile_score > 0)
        {
            $msg = "Total ".$missing_profile_score." submissions have missing Student Profile Score. Please click on 'Calculate Profile Score' button before processing.";
        }
        return $msg;
    }

    public function processTest($application_id, $type="")
    {
        /* Test Code Ends */
        set_time_limit(0);
        $programs = Program::where("status", "Y")->where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->get();

        $exist_availability = $adm_data = $thresold_limit = [];
        

        $rs = ADMData::where("enrollment_id", Session::get("enrollment_id"))->get();
        $magnet_requirement_data = [];
        foreach($rs as $key=>$value)
        {
            $this->adm_data[$value->school_id][$value->grade] = $value->total;
        }

        foreach($programs as $key=>$value)
        {
            $grades = explode(",",$value->grade_lavel);
            foreach($grades as $gvalue)
            {
                $rs = Availability::where("program_id", $value->id)->where("grade", $gvalue)->first();
                if(!empty($rs))
                {
                    $this->availabilityArray[$value->id][$gvalue] = $rs->available_seats;
                    if($rs->home_zone != '')
                    {
                        $data = json_decode($rs->home_zone);
                        foreach($data as $dk=>$dv)
                        {
                            $exist_availability[$value->id][$gvalue][$dk] = $dv; 
                            if(isset($this->adm_data[$dk][$gvalue]))
                            {
                                $thresold_limit[$value->id][$dk][$gvalue]['exist'] = number_format(($dv*100)/$this->adm_data[$dk][$gvalue], 2);
                                $thresold_limit[$value->id][$dk][$gvalue]['exist_count'] = $dv;
                                $thresold_limit[$value->id][$dk][$gvalue]['target'] = number_format(($this->adm_data[$dk][$gvalue]*Config::get('variables.prefer_magnet_percentage'))/100, 2);

                            }
                        }
                    }
                }
            }
        }
        $original_data = $thresold_limit;
        $this->magnet_thresold_limit = $thresold_limit;



        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("type", "regular")->first();

        $preliminary_score = false;
        $application_data = Application::where("form_id", $application_id)->first();

        if(!empty($process_selection) && $process_selection->commited == "Yes")
        {
            //$final_data = $group_racial_composition = $incomplete_arr = $failed_arr = [];
            //return view("ProcessSelection::test_index",compact("final_data", "incomplete_arr", "failed_arr", "group_racial_composition", "preliminary_score"));
        } 




        
        /* from here */

        if($type == "update")
        {
            $rs = SubmissionsFinalStatus::truncate();
            $pendingData = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->whereIn("submission_status", array('Pending'))->where("form_id", $application_id)->get(["submissions.*"]);

            foreach($pendingData as $pk=>$pv)
            {
                 $insert = [];
                 $insert['first_choice_final_status'] = "Denied due to Incomplete Records";
                 if($pv->second_choice != '' && $pv->second_choice != 0)
                 {
                    $insert['second_choice_final_status'] = "Denied due to Incomplete Records";
                 }
                                        
                $insert['submission_id'] = $pv->id;
                $insert['application_id'] = $application_id;
                $insert['enrollment_id'] = Session::get("enrollment_id");
                $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $pv->id], $insert);
            }
        }
        

        $secondData = Submissions::join("application_programs", "application_programs.id", "submissions.second_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->where("second_choice", "!=", "")->whereIn("submission_status", array('Pending'))->where("form_id", $application_id)->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);



        $firstData = Submissions::join("application_programs", "application_programs.id", "submissions.first_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->whereIn("submission_status", array('Active'))->where("form_id", $application_id)->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);

        

        $secondData = Submissions::join("application_programs", "application_programs.id", "submissions.second_choice")->join("program", "program.id", "application_programs.program_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->where("second_choice", "!=", "")->whereIn("submission_status", array('Active'))->where("form_id", $application_id)->get(["submissions.*", "application_programs.program_id", "program.process_logic"]);

        $first_processing_data = $this->separate_data_processing($firstData);
        $second_processing_data = $this->separate_data_processing($secondData);

        $msg = $this->checkAllStudentProfile($first_processing_data['Magnet'], $second_processing_data['Magnet']);

        if($msg != '')
        {
            Session::flash("error", $msg);
            return redirect('/admin/Process/Selection/step2/'.$application_id);
        }
        
        $first_magnet_processing = $this->magnet_processing($first_processing_data['Magnet'], "first");
        
        $first_ib_processing = $this->ib_processing($first_processing_data['IB'], "first");
        $first_audition_processing = $this->audition_processing($first_processing_data['Audition'], "first");


        $second_magnet_processing = $this->magnet_processing($second_processing_data['Magnet'], "second");
        $second_ib_processing = $this->ib_processing($second_processing_data['IB'], "second");
        $second_audition_processing = $this->audition_processing($second_processing_data['Audition'], "second");

        $magnet_offer_data = array_merge($first_magnet_processing['offered_arr'], $second_magnet_processing['offered_arr']);
        
        $sort_position =  array();
        foreach($magnet_offer_data as $key=>$value)
        {
            $sort_position['sort_position'][] = $value['sort_position'];
            //$student_profile['student_profile'][] = $value['student_profile'];
            $next_grade['next_grade'][] = $value['next_grade'];

        }
        array_multisort($next_grade['next_grade'], SORT_ASC, $sort_position['sort_position'], SORT_ASC, $magnet_offer_data);



        if($type == "update")
        {
            $arr = array("ib", "audition", "magnet");
            //$rs = SubmissionsFinalStatus::truncate();
            foreach($arr as $k=>$v)
            {
                $arr1 = array("first", "second");
                foreach($arr1 as $k1=>$v1)
                {
                    $str = $v1."_".$v."_processing";
                    $data = ${$str};
                    //dd($data);
                    foreach($data['offered_arr'] as $key=>$value)
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
                        $insert['offer_slug'] = $code;
                        $insert['application_id'] = $application_id;
                        $insert['enrollment_id'] = Session::get("enrollment_id");
                        $rs = Submissions::where("id", $value['id'])->update(array("awarded_school"=>$awarded_school));
                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], $insert);
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
                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], $insert);
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
                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], $insert);
                    }

                    foreach($data['in_eligible'] as $key=>$value)
                    {
                        $insert = [];
                        if($value['submission_status'] == 'Pending')
                            $insert[$choice.'_choice_final_status'] = "Denied due to Incomplete Records";
                        else
                            $insert[$choice.'_choice_final_status'] = "Denied due to Ineligibility";
                        $choice = $value['choice'];
                        $insert[$choice.'_waitlist_for'] = $value['program_id']; 
                        $insert[$choice.'_choice_final_status'] = "Denied due to Ineligibility";
                        $insert[$choice.'_choice_eligibility_reason'] = "";
                        $insert['submission_id'] = $value['id'];
                        $insert['application_id'] = $application_id;
                        $insert['enrollment_id'] = Session::get("enrollment_id");
                        $rs = SubmissionsFinalStatus::updateOrCreate(["submission_id" => $value['id']], $insert);
                    }
                }
            }

            $rsUpdate = SubmissionsFinalStatus::where("first_choice_final_status", "Offered")->where("second_choice_final_status", "Waitlisted")->update(array("second_choice_final_status"=>"Pending", "second_waitlist_for"=>0));
        }





        $schools = School::where("status", "Y")->get();
        $popHTML = "<table class='table table-striped mb-0 w-100' id='datatable4'><thead>
                    <tr><th class='text-center'>Program</th><th class='text-center'>School Home Zone</th><th class='text-center'>Rising Population from Home Zone</th><th class='text-center'>Calculated 7% Slots</th><th class='text-center'>Starting Population</th><th>Starting %</th><th class='text-center'>Offered</th><th class='text-center'>Offered %</th></tr></thead><tbody>";

        if($type == "update")
        {
            $rsTmp = SubmissionsSelectionReportMaster::where("type", "regular")->where("enrollment_id", Session::get("enrollment_id"))->delete();
        }

        foreach($programs as $key=>$value)
        {
            $grades = explode(",",$value->grade_lavel);
            foreach($grades as $gvalue)
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
                        $data['type'] = "regular";
                        $data['version'] = 0;

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
                            $rsTmp = SubmissionsSelectionReportMaster::create($data);        
                        }
                       
                    }
              

                }

            }
        }
        $popHTML .= "</tbody></table>";

        $additional_data = $this->get_additional_info($application_id); 
        $displayother = $additional_data['displayother'];
        $display_outcome = 0;//$additional_data['display_outcome'];


        return view("ProcessSelection::test_index",compact("magnet_offer_data", "first_ib_processing", "second_ib_processing", "first_audition_processing", "second_audition_processing", "first_magnet_processing", "second_magnet_processing", "popHTML", "application_id", "display_outcome", "type"));


    }

    public function run_admin_selection()
    {
        return $this->processTest(1, "");
    }
    //public $eligibility_grade_pass = array();

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $applications = Form::where("status", "y")->get();

       $displayother = SubmissionsFinalStatus::where("enrollment_id", Session::get("enrollment_id"))->count();
       $tmp = DistrictConfiguration::where("district_id", Session::get("district_id"))->where("name", "last_date_online_acceptance")->first();
       
        return view("ProcessSelection::index", compact("applications"));
    }

    public function index_step2($application_id=0)
    {
        //$this->updated_racial_composition($application_id);exit;
		$applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
		$additional_data = $this->get_additional_info($application_id); 
		$displayother = $additional_data['displayother'];
		$display_outcome = $additional_data['display_outcome'];
		$enrollment_racial = [];//$additional_data['enrollment_racial'];
		$swingData = $additional_data['swingData'];
		$prgGroupArr = $additional_data['prgGroupArr'];
		$last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
		$last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];


		return view("ProcessSelection::index_step2", compact("application_id", "last_date_online_acceptance", "last_date_offline_acceptance", "applications", "displayother", "display_outcome", "enrollment_racial", "swingData", "prgGroupArr"));
    }

    public function get_additional_info($application_id=0)
    {
    	$process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->first();

		$display_outcome = 0;
		$displayother = 0;

		if(!empty($process_selection))
		{
            $displayother = 1;

            $last_date_online_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_online_acceptance));
            $last_date_offline_acceptance = date('m/d/Y H:i', strtotime($process_selection->last_date_offline_acceptance));
		    
		    if($process_selection->commited == "Yes")
            {
		        $display_outcome = 1;
                

            }

		}
		else
		{
		    $last_date_online_acceptance = "";
		    $last_date_offline_acceptance = "";
		}

		/* Swing Data Calculation */
		//$application_data = Application::where("form_id", $application_id)->first();
		$prgGroupArr = $swingData = [];
		

        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->where("parent_submission_form", $application_id)->where('status', 'Y')->get();
        foreach($programs as $key=>$value)
        {
            if($value->applicant_filter1 != '')
                $prgGroupArr[] = $value->applicant_filter1;
            if($value->applicant_filter2 != '')
                $prgGroupArr[] = $value->applicant_filter2;
            if($value->applicant_filter3 != '')
                $prgGroupArr[] = $value->applicant_filter3;
            if($value->applicant_filter1 == '' && $value->applicant_filter2 == '' && $value->applicant_filter3 == '')
            {
                $prgGroupArr[] = $value->name;
            }

        }
        $prgGroupArr = array_unique($prgGroupArr);
        foreach($prgGroupArr as $key=>$value)
        {
            $rs = ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("program_name", $value)->where("district_id", Session::get("district_id"))->first();
            if(!empty($rs))
            {
                $swingData[$value] = $rs->swing_percentage;
            }
        }
    

		$enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();
		return array("display_outcome"=>$display_outcome, "displayother"=>$displayother, "enrollment_racial"=>$enrollment_racial, "prgGroupArr"=>$prgGroupArr, "swingData"=>$swingData, "last_date_online_acceptance"=>$last_date_online_acceptance, "last_date_offline_acceptance"=>$last_date_offline_acceptance);
    }

    public function settings_index()
    {
       $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();

        return view("ProcessSelection::settings_index", compact("applications"));
    }

    public function settings_step_two($application_id = 0)
    {
         // Fetch All Forms - Applications
       $display_outcome = SubmissionsStatusUniqueLog::where("enrollment_id", Session::get("enrollment_id"))->count();

       $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
       //$application_data = Application::where("id", $application_id)->first();

       $prgGroupArr = $swingData = [];
       
        $programs = Program::where("enrollment_id", Session::get("enrollment_id"))->where("enrollment_id", Session::get("enrollment_id"))->where("district_id", Session::get("district_id"))->where("parent_submission_form", $application_id)->where('status', 'Y')->get();
        foreach($programs as $key=>$value)
        {
            if($value->applicant_filter1 != '')
                $prgGroupArr[] = $value->applicant_filter1;
            if($value->applicant_filter2 != '')
                $prgGroupArr[] = $value->applicant_filter2;
            if($value->applicant_filter3 != '')
                $prgGroupArr[] = $value->applicant_filter3;
            if($value->applicant_filter1 == '' && $value->applicant_filter2 == '' && $value->applicant_filter3 == '')
            {
                $prgGroupArr[] = $value->name;
            }

        }
        $prgGroupArr = array_unique($prgGroupArr);
        foreach($prgGroupArr as $key=>$value)
        {
            $rs = ProgramSwingData::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->where("program_name", $value)->where("district_id", Session::get("district_id"))->first();
            if(!empty($rs))
            {
                $swingData[$value] = $rs->swing_percentage;
            }
        }
   

       $enrollment_racial = EnrollmentRaceComposition::where("enrollment_id", Session::get("enrollment_id"))->first();



        return view("ProcessSelection::settings_step_two", compact("prgGroupArr","enrollment_racial", "applications", "application_id", "swingData"));
    }


    public function storeSettings(Request $request)
    {
        $req = $request->all();

        $swing_data = $req['swing_data'];
        foreach($swing_data as $key=>$value)
        {
            if($req['swing_value'][$key] != '')
            {
                $data = array();
                $data['application_id'] = $req['application_id'];
                $data['enrollment_id'] = Session::get('enrollment_id');
                $data['district_id'] = Session::get('district_id');
                $data['program_name'] = $value;
                $data['swing_percentage'] = $req['swing_value'][$key];
                $data['user_id'] = Auth::user()->id;
                $rs = ProgramSwingData::updateOrCreate(["application_id"=>$data['application_id'], "enrollment_id" => $data['enrollment_id'], "program_name" => $data['program_name']], $data);
            }
            else
            {
                $rs = ProgramSwingData::where("application_id", $req['application_id'])->where("enrollment_id", Session::get('enrollment_id'))->where("program_name", $value)->delete();   
            }
        }
        Session::flash("success", "Submission Updated successfully.");

        return redirect("/admin/Process/Selection/settings/".$req['application_id']);
    }

    public function store(Request $request)
    {
        //return $request;
        $req = $request->all();

        $process_selection = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $req['application_id'])->first();
        $process = true;
        if(!empty($process_selection) && $process_selection->commited == 'Yes')
        {
            $process = false;
        }

        $data = array();
        if($req['last_date_online_acceptance'] != '')
        {
            $data['last_date_online_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_online_acceptance']));
            $data['last_date_offline_acceptance'] = date("Y-m-d H:i:s", strtotime($req['last_date_offline_acceptance']));
            $data['district_id'] = Session::get("district_id");
            $data['enrollment_id'] = Session::get("enrollment_id");
            $data['form_id'] = $req['application_id'];
            $data['district_id'] = Session::get("district_id");
            $rs = ProcessSelection::updateOrCreate(['application_id'=>$data['form_id'], "enrollment_id"=>Session::get('enrollment_id')], $data);

        }

        if($process)
        {
            /* Store Program Swing Data only when Processed Selection is not accepted */

            return $this->processTest($req['application_id'], "update");
        }
  

        
        //echo "done";
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

    // from here pending

    public function population_change_application($application_id=1)
    {
        // Processing
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
                            ->where("submissions.form_id", $application_id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
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
                                $score = $this->checkSubmissionCommitteeValue($svalue->id, $tmp1['program_id']);
                                $tmp1['student_profile_score'] = $score[0];
                                $tmp1['student_profile'] = $score[1];
                            }
                            elseif($key == "audition")
                            {
                                $score = $this->checkSubmissionAuditionValue($svalue->id, $tmp1['program_id']);
                                $tmp1['student_profile_score'] = $score[0];
                                $tmp1['student_profile'] = $score[1];
                            }
                            elseif($key == "magnet")
                            {
                                $tmp1['student_profile'] = $this->checkStudentProfileLevel($svalue->id);

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
                            elseif($svalue->$status == "Denied due to Ineligibility")
                            {
                                $tmp1['offer_status'] = "Denied due to Ineligibility";
                                $in_eligible_arr[$key][] = $tmp1;
                            }
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
            ${$str}['in_eligible'] = $t_in_eligible_arr['first'];

            $str = "second_".$key."_processing";
            ${$str}['in_eligible'] = $t_in_eligible_arr['second'];

        }

        $first_magnet_processing['no_availability_arr'] = $second_magnet_processing['no_availability_arr'] = $first_ib_processing['no_availability_arr'] = $second_ib_processing['no_availability_arr'] = $first_audition_processing['no_availability_arr'] = $second_audition_processing['no_availability_arr'] = [];

        $magnet_offer_data = array_merge($first_magnet_processing['offered_arr'], $second_magnet_processing['offered_arr']);
        
        $sort_position =  array();
        foreach($magnet_offer_data as $key=>$value)
        {
            //$sort_position['sort_position'][] = $value['sort_position'];
            //$student_profile['student_profile'][] = $value['student_profile'];
            $next_grade['next_grade'][] = $value['next_grade'];

        }
        array_multisort($next_grade['next_grade'], SORT_ASC,  $magnet_offer_data);

        $schools = School::where("status", "Y")->get();
        $popHTML = "<table class='table table-striped mb-0 w-100' id='datatable4'><thead>
                    <tr><th class='text-center'>Program</th><th class='text-center'>School Home Zone</th><th class='text-center'>Rising Population from Home Zone</th><th class='text-center'>Calculated 7% Slots</th><th class='text-center'>Starting Population</th><th>Starting %</th><th class='text-center'>Offered</th><th class='text-center'>Offered %</th></tr></thead><tbody>";

        $rsTmp = SubmissionsSelectionReportMaster::where("type", "regular")->where("enrollment_id", Session::get("enrollment_id"))->get();

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
       // / dd($first_ib_processing);
        return view("ProcessSelection::test_index",compact("magnet_offer_data", "first_ib_processing", "second_ib_processing", "first_audition_processing", "second_audition_processing", "first_magnet_processing", "second_magnet_processing", "popHTML", "application_id", "display_outcome"));
    }


    public function submissions_results_application($application_id=1)
    {
        $pid = $application_id;
        $from = "form";
        $programs = [];
        $district_id = \Session('district_id');
        $submissions = Submissions::where('district_id', $district_id)
            ->where('district_id', $district_id)
            ->where('submissions.enrollment_id', SESSION::get('enrollment_id'))
            ->where("submissions.form_id", $application_id)->join("submissions_final_status", "submissions_final_status.submission_id", "submissions.id")
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

        $additional_data = $this->get_additional_info($application_id); 
		$displayother = $additional_data['displayother'];
		$display_outcome = $additional_data['display_outcome'];
		$enrollment_racial = $additional_data['enrollment_racial'];
		$swingData = $additional_data['swingData'];
		$prgGroupArr = $additional_data['prgGroupArr'];
		$last_date_online_acceptance = $additional_data['last_date_online_acceptance'];
		$last_date_offline_acceptance = $additional_data['last_date_offline_acceptance'];

        return view("ProcessSelection::submissions_result", compact('final_data', 'pid', 'from', 'display_outcome', "application_id", "displayother", "last_date_online_acceptance", "last_date_offline_acceptance", "prgGroupArr", "swingData", "enrollment_racial"));

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function selection_accept(Request $request)
    {
        $application_id = $request->application_id;
        $data = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get('enrollment_id'))->where("submissions_final_status.application_id", $application_id)->join("submissions", "submissions.id", "submissions_final_status.submission_id")->whereIn("submissions.submission_status", array('Active', 'Pending'))->get();


        foreach($data as $key=>$value)
        {

            $insert = [];
            $insert['submission_id'] = $value->submission_id;
            $insert['enrollment_id'] = $value->enrollment_id;
            $insert['application_id'] = $value->application_id;
            $insert['first_choice_final_status'] = $value->first_choice_final_status;
            $insert['second_choice_final_status'] = $value->second_choice_final_status;
            $insert['first_waitlist_number'] = $value->first_waitlist_number;
            $insert['second_waitlist_number'] = $value->second_waitlist_number;
            $insert['incomplete_reason'] = $value->incomplete_reason;
            $insert['first_choice_eligibility_reason'] = $value->first_choice_eligibility_reason;
            $insert['second_choice_eligibility_reason'] = $value->second_choice_eligibility_reason;
            $insert['first_offered_rank'] = $value->first_offered_rank;
            $insert['second_offered_rank'] = $value->second_offered_rank;
            $insert['first_waitlist_for'] = $value->first_waitlist_for;
            $insert['second_waitlist_for'] = $value->second_waitlist_for;
            $insert['offer_slug'] = $value->offer_slug;
            $insert['first_offer_update_at'] = $value->first_offer_update_at;
            $insert['second_offer_update_at'] = $value->second_offer_update_at;
            $insert['contract_status'] = $value->contract_status;
            $insert['contract_signed_on'] = $value->contract_signed_on;
            $insert['contract_name'] = $value->contract_name;
            $insert['offer_status_by'] = $value->offer_status_by;
            $insert['contract_status_by'] = $value->contract_status_by;
            $insert['contract_mode'] = $value->contract_mode;
            $insert['first_offer_status'] = $value->first_offer_status;
            $insert['second_offer_status'] = $value->second_offer_status;
            $insert['manually_updated'] = $value->manually_updated;
            $insert['communication_sent'] = $value->communication_sent;
            $insert['communication_text'] = $value->communication_text;
            $insert['version'] = $value['version'];
            $rs = SubmissionsLatestFinalStatus::create($insert);

            $status = $value->first_choice_final_status;
            if($value->second_choice_final_status == "Offered")
                $status = "Offered";
            elseif($status != "Offered" && $value->second_choice_final_status == "Waitlisted")
            	$status = "Waitlisted";
            $submission_id = $value->submission_id;
            $rs = Submissions::where("id", $submission_id)->select("submission_status")->first();
            $old_status = $rs->submission_status;

            $comment = "By Accept and Commit Event";
            if($status == "Offered")
            {
                $submission = Submissions::where("id", $value->submission_id)->first();
                if($value->first_choice_final_status == "Offered")
                {
                    $program_name = getProgramName($submission->first_choice_program_id);
                }
                else if($value->second_choice_final_status == "Offered")
                {
                    $program_name = getProgramName($submission->second_choice_program_id);
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
            $rs = SubmissionsStatusLog::create(array("submission_id"=>$submission_id, "enrollment_id"=>Session::get("enrollment_id"), "new_status"=>$status, "old_status"=>$old_status, "updated_by"=>Auth::user()->id, "comment"=>$comment));
            $rs = SubmissionsStatusUniqueLog::updateOrCreate(["submission_id" => $submission_id], array("submission_id"=>$submission_id, "new_status"=>$status, "old_status"=>$old_status, "updated_by"=>Auth::user()->id));
            $rs = Submissions::where("id", $submission_id)->update(["submission_status" => $status]);
        }
        $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("application_id", $application_id)->update(array("commited"=>"Yes"));
        echo "Done";
        exit;
    }

    public function selection_revert(Request $request)
    {
        $req = $request->all();
        $quotations = SubmissionsStatusLog::join("submissions", "submissions.id", "submissions_status_log.submission_id")->where("submissions.enrollment_id", Session::get("enrollment_id"))->where("submissions.application_id", $req['application_id'])->orderBy('submissions_status_log.created_at','ASC')
                ->get()
                ->unique('submission_id');
        $sub_id = [];
        foreach($quotations as $key=>$value)
        {
            $sub_id[] = $value->submission_id;
            $rs = Submissions::where("id", $value->submission_id)->update(array("submission_status"=>$value->old_status));
        }
        SubmissionsStatusUniqueLog::whereIn("submission_id", $sub_id)->delete();
        SubmissionsFinalStatus::whereIn("submission_id", $sub_id)->delete();
        SubmissionsRaceCompositionReport::where("application_id", $req['application_id'])->delete();
        SubmissionsSelectionReportMaster::where("application_id", $req['application_id'])->delete();
        
        $rs = ProcessSelection::where("application_id", $req['application_id'])->delete();//update(["commited"=>"No"]);

        //SubmissionsStatusUniquesLog::truncate();

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
                        $in_range = false;
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
                return true;
        }
        else
        {
            if($original_race_percent < $tmp_enroll[$race]['min'])
                return true;
            else
                return false;
        }
    }

    /* Function to find out updated Racial Commposition */
    public function updated_racial_composition($application_id, $from="")
    {
        /* Create Application Filter Group Array for Program */
        $af = ['applicant_filter1', 'applicant_filter2', 'applicant_filter3'];
        $programs=Program::where('status','!=','T')->where('district_id', Session::get('district_id'))->where("enrollment_id", Session::get('enrollment_id'))->join("application_programs", "application_programs.program_id", "program.id")->where("program.parent_submission_form", $application_id)->get();

        // Application Filters
        $af_programs = [];
        if (!empty($programs)) {
            foreach ($programs as $key => $program) {
                $cnt = 0;
                foreach ($af as $key => $af_field) {
                    if ($program->$af_field == '')
                        $cnt++;
                    if (($program->$af_field != '') && !in_array($program->$af_field, $af_programs)) {
                        array_push($af_programs, $program->$af_field);
                    }
                }
                if($cnt == count($af))
                {
                    array_push($af_programs, $program->name);
                }
            }
        }

        $tmp = $this->groupByRacism($af_programs);


        $this->group_racial_composition = $group_race_array = $tmp['group_race'];
        $this->program_group = $program_group_array = $tmp['program_group'];


        
        $group_racial_composition = [];
        foreach($this->program_group as $key=>$value)
        {
            $program_id = $key;
            $group_racial_composition[$value] = $this->calculate_offered_from_all($key, $value);
            //print_r($$group_racial_composition[$value]);exit;
        }
       

        /* Get Withdraw Student Count */
        $tmp = $this->program_group;
        $tmp_group = $this->group_racial_composition;


        foreach($tmp as $k=>$v)
        {
            if($from == "desktop" && !is_int($from))
            {
                $black = WaitlistProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                $white = WaitlistProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                $other = WaitlistProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "waitlist_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

                $black1 = LateSubmissionProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("black_withdrawn");
                $white1 = LateSubmissionProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("white_withdrawn");
                $other1 = LateSubmissionProcessLogs::where("program_id", $k)->join("process_selection", "process_selection.id", "late_submission_process_logs.process_log_id")->where("process_selection.commited", "Yes")->sum("other_withdrawn");

            }
            else
            {
                $black = WaitlistProcessLogs::where("program_id", $k)->sum("black_withdrawn");
                $white = WaitlistProcessLogs::where("program_id", $k)->sum("white_withdrawn");
                $other = WaitlistProcessLogs::where("program_id", $k)->sum("other_withdrawn");

                $black1 = LateSubmissionProcessLogs::where("program_id", $k)->sum("black_withdrawn");
                $white1 = LateSubmissionProcessLogs::where("program_id", $k)->sum("white_withdrawn");
                $other1 = LateSubmissionProcessLogs::where("program_id", $k)->sum("other_withdrawn");

            }


            $tmp_data = $tmp_group[$v];
            $black_data = $tmp_data['black'] - $black - $black1;
            $white_data = $tmp_data['white'] - $white - $white1;
            $other_data = $tmp_data['other'] - $other - $other1;

            if($black_data < 0)
                $black_data = 0;
            if($white_data < 0)
                $white_data = 0;
            if($other_data < 0)
                $other_data = 0;

            $tmp_data['black'] = $black_data; 
            $tmp_data['white'] = $white_data; 
            $tmp_data['other'] = $other_data; 
            $tmp_data['total'] = $black_data + $white_data + $other_data;


            $tmp_group[$v] = $tmp_data;


        }
        $this->group_racial_composition = $tmp_group;
        return $this->group_racial_composition;
    }

    public function calculate_offered_from_all($program_id, $group_name)
    {

    	$group_data = $this->group_racial_composition[$group_name];

    	/* From regular submissions Results */
    	$submission = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
            })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->groupBy('submissions.calculated_race')->select("calculated_race", DB::raw("count(calculated_race) as CNT"))->get();
                
        $total = $group_data['total'];


        foreach($submission as $sk=>$sv)
        {
            $group_data[strtolower($sv->calculated_race)]  = $group_data[strtolower($sv->calculated_race)] + $sv->CNT; 
            $total += $sv->CNT;
        }

        
    	/* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
    	$submission = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
            })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->groupBy('submissions.calculated_race')->select("calculated_race", DB::raw("count(calculated_race) as CNT"))->get();
        
        foreach($submission as $sk=>$sv)
        {
            $group_data[strtolower($sv->calculated_race)]  = $group_data[strtolower($sv->calculated_race)] + $sv->CNT; 
            $total += $sv->CNT;
        }

        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
    	$submission = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
            })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
            })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->groupBy('submissions.calculated_race')->select("calculated_race", DB::raw("count(calculated_race) as CNT"))->get();
        foreach($submission as $sk=>$sv)
        {
            $group_data[strtolower($sv->calculated_race)]  = $group_data[strtolower($sv->calculated_race)] + $sv->CNT; 
            $total += $sv->CNT;
        }



        $group_data['total'] = $total;
        $this->group_racial_composition[$group_name] = $group_data;
        return $group_data;
    }

    public function get_waitlist_count($application_id, $program_id, $grade)
    {
        
        //$grade_id = Grade::where("name", $grade)->first();
        $rs = ProgramGradeMapping::where("enrollment_id", Session::get("enrollment_id"))->where("program_id", $program_id)->where("grade", $grade)->first();
        if(!empty($rs))
        {
            $application_program_id = $rs->id;

            $rs = ProcessSelection::where("enrollment_id", Session::get("enrollment_id"))->where("form_id", $application_id)->where("commited", "Yes")->whereRaw("FIND_IN_SET(".$application_program_id.", selected_programs)")->orderBy("created_at", "desc")->first();
            
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
                }
                elseif($rs->type == "late_submission")
                {
                    $table_name = "late_submissions_final_status";
                    $version = $rs->version;
                }
            }
            $table_name = "submissions_latest_final_status";

            $waitlist_count1 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('first_choice_final_status', 'Waitlisted')->where('first_offer_status', 'Pending')->where('next_grade', $grade)->join($table_name, $table_name.".submission_id", "submissions.id")->where("first_choice_program_id", $program_id)->count();

            $waitlist_count5 = Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('second_choice_final_status', 'Waitlisted')->where('second_offer_status', 'Pending')->where('next_grade', $grade)->join($table_name, $table_name.".submission_id", "submissions.id")->where("second_choice_program_id", $program_id)->count();



            $waitlist_count2 = 0;//Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('first_choice_final_status', 'Waitlisted')->where('second_choice_final_status', '<>', 'Waitlisted')->whereIn('second_offer_status', array('Declined & Waitlisted', 'Pending'))->where('next_grade', $grade)->join($table_name, $table_name.".submission_id", "submissions.id")->where("first_choice_program_id", $program_id)->count();

            $waitlist_count3 = 0;//Submissions::where("submissions.enrollment_id", Session::get("enrollment_id"))->where('district_id', Session::get("district_id"))->where('submissions.form_id', $application_id)->where('second_choice_final_status', 'Waitlisted')->where('first_choice_final_status', '<>', 'Waitlisted')->where('first_offer_status', 'Declined & Waitlisted')->where('next_grade', $grade)->join($table_name, $table_name.".submission_id", "submissions.id")->where("second_choice_program_id", $program_id)->count();
            
            

            $waitlist_count4 = 0;//Submissions::where('district_id', Session::get("district_id"))->where('submissions.application_id', $application_id)->where('first_choice_final_status', 'Waitlisted')->where('second_choice_final_status', 'Pending')->where('next_grade', $grade)->join($table_name, $table_name.".submission_id", "submissions.id")->where("first_choice_program_id", $program_id)->count();


            return $waitlist_count1 + $waitlist_count2 + $waitlist_count3 + $waitlist_count4+  $waitlist_count5;
        }
        else
            return 0;
    }

    public function get_available_count($application_id, $program_id, $grade)
    {
    	  $total_offered = $this-> get_offered_count_programwise($program_id, $grade);
    	  $rs = Availability::where("program_id", $program_id)->where("grade", $grade)->first();
    	  return array("total_seats"=>$rs->total_seats, "available_seats"=>$rs->available_seats, "offered_seats"=>$total_offered);
    }

    public function get_offered_count_programwise($program_id, $grade)
    {
    	/* From regular submissions Results */
    	$count1 = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->count();
        

    	/* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
    	$count2 = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->count();
        
        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
    	$count3 = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->count();

        // if($program_id == 24 && $grade == 4 && $_SERVER['REMOTE_ADDR'] == '49.36.64.176')
        // {
        //     $count11 = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->where(function($q1) use ($program_id) {
        //     $q1->where(function ($q) use ($program_id) {
        //             $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
        //         })->orWhere(function ($q) use ($program_id) {
        //             $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
        //         });
        //     })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->get();

        //     foreach($count11 as $kk=>$kv)
        //     {
        //         echo $kv->submission_id."<BR>";
        //     }
        

        // /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        // $count21 = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->where(function($q1) use ($program_id) {
        //     $q1->where(function ($q) use ($program_id) {
        //             $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
        //         })->orWhere(function ($q) use ($program_id) {
        //             $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
        //         });
        //     })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->get();
        // foreach($count21 as $kk=>$kv)
        //     {
        //         echo $kv->submission_id."<BR>";
        //     }
        
        // /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        // $count31 = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->where(function($q1) use ($program_id) {
        //     $q1->where(function ($q) use ($program_id) {
        //             $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
        //         })->orWhere(function ($q) use ($program_id) {
        //             $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
        //         });
        //     })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->get();
        // foreach($count31 as $kk=>$kv)
        //     {
        //         echo $kv->submission_id."<BR>";
        //     }
        // }

        return $count1 + $count2 + $count3;
        
    }

    public function get_offered_count_programwise1($program_id, $grade, $school_name)
    {
        $rs = School::where("name", $school_name)->first();
        $school_arr = array($school_name);
        if(!empty($rs))
        {
            $school_arr[] = $rs->zoning_api_name;
            $school_arr[] = $rs->sis_name;
        }

        /* From regular submissions Results */
        $count1 = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->whereIn("submissions.zoned_school", $school_arr)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->count();
        

        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $count2 = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->whereIn("submissions.zoned_school", $school_arr)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->count();
        
        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $count3 = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->whereIn("submissions.zoned_school", $school_arr)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_offer_status", "Accepted")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_offer_status", "Accepted")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->count();

        return $count1 + $count2 + $count3;
        
    }

    /* Get Only offered count, ignore Accepted or not status */
    public function get_only_offered_count_programwise($program_id, $grade, $school_name)
    {
        $rs = School::where("name", $school_name)->first();
        $school_arr = array($school_name);
        if(!empty($rs))
        {
            $school_arr[] = $rs->zoning_api_name;
            $school_arr[] = $rs->sis_name;
        }

        /* From regular submissions Results */
        $count1 = SubmissionsFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->whereIn("submissions.zoned_school", $school_arr)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_choice_final_status", "Offered")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_choice_final_status", "Offered")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "submissions_final_status.submission_id")->count();
        

        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $count2 = LateSubmissionFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->whereIn("submissions.zoned_school", $school_arr)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_choice_final_status", "Offered")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_choice_final_status", "Offered")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "late_submissions_final_status.submission_id")->count();
        
        /* From regular submissions Results LateSubmissionFinalStatus,SubmissionsWaitlistFinalStatus*/
        $count3 = SubmissionsWaitlistFinalStatus::where("submissions.enrollment_id", Session::get("enrollment_id"))->where("next_grade", $grade)->whereIn("submissions.zoned_school", $school_arr)->where(function($q1) use ($program_id) {
            $q1->where(function ($q) use ($program_id) {
                    $q->where("first_choice_final_status", "Offered")->where("first_waitlist_for", $program_id);
                })->orWhere(function ($q) use ($program_id) {
                    $q->where("second_choice_final_status", "Offered")->where("second_waitlist_for", $program_id);
                });
            })->join("submissions", "submissions.id", "submissions_waitlist_final_status.submission_id")->count();

        return $count1 + $count2 + $count3;
        
    }

    /* Get Withdrawn Count from Waitlist and Late Submission Processing */
    public function get_homezone_withdrawn_count($program_id, $grade, $school_id)
    {
        $withdrawn_count = 0;
        $rs = WaitlistProcessLogs::where("program_id", $program_id)->where("grade", $grade)->get();
        foreach($rs as $value)
        {
            if($value->homezone != '')
            {
                $homezone = json_decode($value->homezone);
                foreach($homezone as $k=>$v)
                {
                    if($k==$school_id)
                    {
                        $withdrawn_count += $v;
                    }
                }

            }     
        }
        $rs = LateSubmissionProcessLogs::where("program_id", $program_id)->where("grade", $grade)->get();
        foreach($rs as $value)
        {
            if($value->homezone != '')
            {
                $homezone = json_decode($value->homezone);
                foreach($homezone as $k=>$v)
                {
                    if($k==$school_id)
                    {
                        $withdrawn_count += $v;
                    }
                }

            }     
        }

        return $withdrawn_count;
    }

}
