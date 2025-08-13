<?php

namespace App\Http\Controllers;
use Session;
use Illuminate\Http\Request;
use App\Modules\District\Models\District;
use App\Modules\Form\Models\Form;
use App\Modules\Form\Models\FormBuild;
use App\Modules\Form\Models\FormFields;
use App\Modules\Form\Models\FormContent;
use App\Modules\AddressOverride\Models\AddressOverride;
use App\Modules\Application\Models\{Application,ApplicationConfiguration,ApplicationProgram};
use App\Modules\Submissions\Models\{Submissions,SubmissionSteps,SubmissionData, SubmissionRecommendation,RecommendationException,RaceDivision,SubmissionGrade};
use App\Modules\ZonedSchool\Models\NoZonedSchool;
use App\Modules\Eligibility\Models\Eligibility;
use App\ZoneAPI;
use App\IncorrectStudent;
use App\Modules\School\Models\School;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Program\Models\{Program,ProgramEligibility};
use App\{SubmissionRaw,StudentGradeLatest,SubmissionDocuments};
use Mail;
use DB;
use Auth;
use PDF;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditTrail;
use App\IncorrectStudentBDay;
use App\StudentData;
use Response;
use App\Modules\Student\Models\Student;





class HomeController extends Controller
{
    use AuditTrail;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    public function copy_data()
    {
        $rs = DB::table("studentgrade_latest")->get();
        foreach($rs as $key=>$value)
        {
            $rs1 = DB::table("studentgrade_latest_cpy")->where("stateID", $value->stateID)->count();
            if($rs1 == 0)
            {
                $data = [];
                foreach($value as $vk=>$vv)
                {
                    if($vk != "id")
                        $data[$vk] = $vv;
                }
                $rs2 = DB::table("studentgrade_latest_cpy")->insert($data);
            }
        }
        echo "done";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function applicationIndex($from='')
    {
        //Session::put("district_id", 3);
        Session::forget("step_session");
        if($from == "")
            Session::forget("from_admin"); 

        if(Session::has("from_admin"))
        {
            $applications = Application::where('admin_starting_date','<=',date('Y-m-d H:i:s'))->where('admin_ending_date','>=',date('Y-m-d H:i:s'))->where('district_id', Session::get('district_id'))->where("status", "Y")->get();
        }
        else
        {
            $applications = Application::where('starting_date','<=',date('Y-m-d H:i:s'))->where('ending_date','>=',date('Y-m-d H:i:s'))->where('district_id', Session::get('district_id'))->where("status", "Y")->get();
        }

        if(Session::has("district_id") && Session::get("district_id") != 0)
        {
            $district = District::where("id", Session::get("district_id"))->first();
            if($district->mcpss_zone_api == "N")
            {
                Session::put("mcpss_zone_api", "N");
            }
            else
            {
                Session::forget("mcpss_zone_api");
            }
            if($district->zone_api == "N")
            {
                Session::put("zone_api", "N");
            }
            else
            {
                Session::forget("zone_api");
            }

            if(Session::has("from_admin") && $district->admin_mcpss_zone_api == "N")
            {
                Session::put("zone_api", "N");
                Session::put("mcpss_zone_api", "N");
            }
            
            Session::put("theme_color", $district->theme_color);

            if(isset($applications) && !empty($applications->toArray()))
            {
                $district = District::where("id", Session::get("district_id"))->first();
                $application_data = $applications[0];
                Session::put("enrollment_id", $applications[0]->enrollment_id);

                if(count($applications) == 1){
                    return redirect('/application/'.$applications[0]->id);
                }else{
                    return view('layouts.front.application_index',compact('district','application_data','applications'));
                }
            }
            else
            {
                $application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
                if(!empty($application_data))
                {
                    if(strtotime($application_data->ending_date) > strtotime(date("Y-m-d H:i:s")))
                        $msg_type="before_application_open_text";
                    else
                        $msg_type="after_application_open_text";
                }
                else
                {
                    $msg_type="after_application_open_text";
                }
                // dd($application_data, $msg_type, view('layouts.errors.msgs',compact("district","msg_type","application_data")));
                return view('layouts.errors.msgs',compact("district","msg_type","application_data"));  
            }
        }
        else
        {
           /* Session::put("district_id", 3);
            $district = District::where("id", 3)->first();
            Session::put("theme_color", $district->theme_color);
            if(empty($application_data))
                    $application_data = Application::where('district_id', Session::get("district_id"))->where("status", "Y")->first();
           return view('layouts.front.index',compact('district',"application_data"));*/
            return redirect('/login'); 
        }
    }

    public function index($id="")
    {
        /*if($_SERVER['REMOTE_ADDR'] == "49.36.73.248")
        {

            $recommendation_links = [];
            $last_id = 1031;
                $eligibility_data = getEligibilityContent1(33);
                if(!empty($eligibility_data))
                {
                    $subjects = $eligibility_data->subjects;
                    foreach($subjects as $skey=>$skvalue)
                    {
                        $sub_data = [];
                        $sub_data['submission_id'] = 1031;
                        $sub_data['config_name'] = "recommendation_".$skvalue."_url";
                        $sub_data['config_value'] = $skvalue.".".$last_id.".22";
                        $recommendation_links[$skvalue] = $sub_data['config_value']; 
                    }
                    $instructions_file_name = $this->generateRecommendationPDF($last_id, 22, $recommendation_links, 33);
                    echo $instructions_file_name;exit;
                }
        }*/
        //Session::put("district_id", 3);
        Session::forget("step_session");
        // if($from == "")

        $applications = Application::where('starting_date','<=',date('Y-m-d H:i:s'))->where('ending_date','>=',date('Y-m-d H:i:s'))->where('id', $id)->first();

        $applications1 = Application::where('admin_starting_date','<=',date('Y-m-d H:i:s'))->where('admin_ending_date','>=',date('Y-m-d H:i:s'))->where('id', $id)->first();

        if(empty($applications) && empty($applications1))
        {
            return redirect(url('/msgs/noapplication'));
        }
        else
        {
            if(!empty($applications))
                $enrol_id = $applications->enrollment_id;
            if(!empty($applications1))
                $enrol_id = $applications1->enrollment_id;

            $rs = Enrollment::where('begning_date','<=',date('Y-m-d H:i:s'))->where('ending_date','>=',date('Y-m-d H:i:s'))->where('id', $enrol_id)->first();

            //$rs = Enrollment::where('begning_date','<=',date('Y-m-d H:i:s'))->where('ending_date','>=',date('Y-m-d H:i:s'))->where('id', $enrol_id)->first();

            if(empty($rs))
            {
                return redirect(url('/msgs/noapplication'));
            }
        }
        //     Session::forget("from_admin"); 

        $application_data = Application::where('id',$id)->where('district_id', Session::get('district_id'))->where("status", "Y")->first();

        // dd($application_data->id);
        if(Session::has("district_id") && Session::get("district_id") != 0)
        {
            $district = District::where("id", Session::get("district_id"))->first();
            if($district->mcpss_zone_api == "N")
            {
                Session::put("mcpss_zone_api", "N");
            }
            else
            {
                Session::forget("mcpss_zone_api");
            }
            if($district->zone_api == "N")
            {
                Session::put("zone_api", "N");
            }
            else
            {
                Session::forget("zone_api");
            }

            if(Session::has("from_admin") && $district->admin_mcpss_zone_api == "N")
            {
                Session::put("zone_api", "N");
                Session::put("mcpss_zone_api", "N");
            }
            
            Session::put("theme_color", $district->theme_color);

            
            if(!empty($application_data))
            {
            	if((strtotime($application_data->starting_date)  <= strtotime(date("Y-m-d H:i:s")) &&  strtotime($application_data->ending_date) >  strtotime(date("Y-m-d H:i:s"))) || (strtotime($application_data->admin_starting_date)  <= strtotime(date("Y-m-d H:i:s")) &&  strtotime($application_data->admin_ending_date) >  strtotime(date("Y-m-d H:i:s"))))
                {
                    $district = District::where("id", Session::get("district_id"))->first();
                    Session::put("enrollment_id", $application_data->enrollment_id);
                    return view('layouts.front.index',compact('district','application_data'));

                }
                else
                {
                    return redirect('/'); 
                }
            }
            else
            {
                return redirect('/');
            }
        }
        else
        {
           /* Session::put("district_id", 3);
            $district = District::where("id", 3)->first();
            Session::put("theme_color", $district->theme_color);
            if(empty($application_data))
                    $application_data = Application::where('district_id', Session::get("district_id"))->where("status", "Y")->first();
           return view('layouts.front.index',compact('district',"application_data"));*/
            return redirect('/login'); 
        }
        
    }

    
    public function indexStage($from="")
    {
        Session::forget("step_session");
        if($from == "")
            Session::forget("from_admin"); 
        $application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
        if(Session::has("district_id") && Session::get("district_id") != 0)
        {
            $district = District::where("id", Session::get("district_id"))->first();

            if($district->mcpss_zone_api == "N")
            {
                Session::put("mcpss_zone_api", "N");
            }
            else
            {
                Session::forget("mcpss_zone_api");
            }
            if($district->zone_api == "N")
            {
                Session::put("zone_api", "N");
            }
            else
            {
                Session::forget("zone_api");
            }
            Session::put("theme_color", $district->theme_color);

            
            if(!empty($application_data))
            {
                $district = District::where("id", Session::get("district_id"))->first();
                return view('layouts.front.index',compact('district','application_data'));
            }
            else
            {
                $application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
                if(!empty($application_data))
                {
                    $msg_type="before_application_open_text";
                }
                else
                {
                    $msg_type="after_application_open_text";
                }
                return view('layouts.errors.msgs',compact("district","msg_type","application_data"));  
            }
        }
        else
        {
            //Session::put("district_id", 3);
            $district = District::where("id", Session::get("district_id"))->first();
            Session::put("theme_color", $district->theme_color);
            if(empty($application_data))
                    $application_data = Application::where('district_id', Session::get("district_id"))->where("status", "Y")->first();
           return view('layouts.front.index',compact('district',"application_data"));
            return redirect('/login'); 
        }
    }

    public function phoneSubmission()
    {
        if(Auth::check())
        {
            Session::put("from_admin", "Y");   
        }
        return $this->applicationIndex("Admin");
    }

    public function showProcessForm(Request $request)
    {
        Session::forget("form_data");
        $req = $request->all();
        
        if(!Session::has("step_session"))
        {
            Session::put("step_session", strtotime(date("Y-m-d H:i:s")));
        }
        $rsp = SubmissionSteps::updateOrCreate(["session_id" => Session::get("step_session")], ['step_no' => 1]);

        // return  $req;
        $page_id = $req['page_id'];
        $form_id = $req['form_id'];
        $application_id = $req['application_id'];
        Session::put("application_id", $application_id);
        Session::put("form_id", $form_id);

        $application_data = Application::where('id',$application_id)->first();

        $district = District::where("id", Session::get("district_id"))->first();
        if($req['student_status'] == "exist")
        {
            $data = $this->getFormbyId($form_id, $page_id,'exist');

            $form_type = "exist";
            return view("layouts.front.form_display", compact("district", "data", "page_id","form_id","form_type","application_id","application_data"));
        }
        else
        {
            $data = $this->getFormbyId($form_id, $page_id, 'new');
             //return $data;
            $form_type = "new";
            return view('layouts.front.form_display',compact("district", "data", "page_id","form_id","form_type","application_id","application_data"));
        }

    }

    public function previewForm($page_id, $form_id)
    {
        $district = District::where("id", Session::get("district_id"))->first();
        $data = $this->getFormbyId($form_id, $page_id);
        return view('layouts.front.preview_form',compact("district", "data", "page_id","form_id"));
    }

    public function getFormbyId($form_id, $page_id=1, $type='new'){
         $data = FormBuild::where('form_build.form_id',$form_id)->where('page_id',$page_id)->whereIn('form_build.id', function($query) use ($type)
                {
                        $query->select('build_id')->from('form_content')->where('field_property','show_in_exist')->where(function($query) use ($type) 
                                {
                                        return $query->where('field_value', $type)->orWhere('field_value', 'both');
                                    });
        })->orderBy('sort','ASC')->get();
        return $data;
    }

    public function showNextStep(Request $request)
    {
        if(!Session::has("step_session"))
        {
            Session::put("step_session", strtotime(date("Y-m-d H:i:s")));
        }
        $district = District::where("id", Session::get("district_id"))->first();

        $req = $request->all();
            
        if(isset($req['submit']) && $req['submit']=="Prev Step")
        {
            $req['page_id'] = $req['page_id']-1; 
        }        


        $form_id = $req['form_id'];
        $no_of_pages = $req['no_of_pages'];
        $page_id = $req['page_id'];
        $application_id = Session::get('application_id');
        $application_data = Application::where('id',$application_id)->first();
        $form_type = $req['form_type'];
        // /Session::push("form_type", $form_type);
        $tmpArray = array();
        if($req['form_type'] != "exist")
        {
            /* Code for find out Student is Gifted or Not for Enrolled student */
            if($form_id == 2)
            {
                $gifted_student_id = fetch_student_field_id($form_id, "gifted_student");
                if(isset($req['formdata'][$gifted_student_id]) && $req['formdata'][$gifted_student_id] == "No")
                {
                    $this->destroySessions();
                    return redirect(url('/msgs/nogifted'));
                }
                else
                {
                    Session::put("gifted_student", "Parent Identified as Gifted");
                }
            }
            else
            {
                Session::forget("gifted_student");
            }
        }
        if($req['form_type'] == "exist")
        {
            $student_field_id = fetch_student_field_id($form_id, 'student_id');
 
            if($student_field_id == 0)
            {
                $this->destroySessions();
                return redirect(url('/msgs/nostudent'));
            }
            else
            {
                if(isset($req['formdata'][$student_field_id]))
                {
                    $student_birthday_id = fetch_student_field_id($form_id, "birthday");
                    if($student_birthday_id  == 0)
                    {
                        return redirect(url('/msgs/nostudent'));
                    }
                    //      echo $req['formdata'][$student_birthday_id];exit;
                    $student_data = DB::table("student")->where("stateID",$req['formdata'][$student_field_id])->where('birthday', $req['formdata'][$student_birthday_id])->first();
                    if(empty($student_data))
                    {
                        $bd = IncorrectStudentBDay::create(["student_id"=>$req['formdata'][$student_field_id], "birthday"=>$req['formdata'][$student_birthday_id]]);

                        return redirect(url('/msgs/nostudent'));
                    }   
                    else
                    {
                 
                        /* Code for find out Student is Gifted or Not for Enrolled student */
                        if($form_id == 2)
                        {
                            $rsGifted = StudentData::where("stateID", $req['formdata'][$student_field_id])->where("field_name", "gifted_student")->first();
                            if(empty($rsGifted))
                            {
                                $this->destroySessions();
                                return redirect(url('/msgs/nogifted'));
                            }
                            else
                            {
                                Session::put("gifted_student", "Gifted");
                            }
                        }   
                        else
                        {
                            Session::forget("gifted_student");
                        } 

                        $db_fields = FormContent::where('form_id',$form_id)->where('field_property','db_field')->get();
                      //print_r($db_fields);
                        $dataArray = array();
                        $dataArray['formdata'] = array();
                        if(Session::has("form_data"))
                        {
                            if(isset(Session::get("form_data")[0]))
                            {
                                $dataArray =  Session::get("form_data")[0];
                            }
                            Session::forget("form_data");
                        }
                        
                        
                        $formdata = $dataArray['formdata'];
                        $current_grade = 404; 
                        $current_school = "";
                        foreach($db_fields as $key=>$value)
                        {
                            if(isset($student_data->{$value->field_value}))
                            {
                                if($value->field_value == "current_grade")
                                {
                                    $current_grade = $student_data->{$value->field_value};
                                        
                                    $formdata[$value->build_id] = $current_grade;    
                                    if($current_grade == "ASK-97" || $current_grade == "ASK-98")
                                    {
                                        $this->destroySessions();
                                        return redirect("/msgs/nograde");
                                    }   

                                }
                                elseif($value->field_value == "current_school")
                                {
                                    $current_school = $student_data->{$value->field_value};
                                    $formdata[$value->build_id] = $current_school;
                                }
                                elseif($value->field_value == "race")
                                {
                                    $sid = $student_data->student_id;
                                    $race = $student_data->{$value->field_value};
                                    $formdata[$value->build_id] = $race;
                                }
                                else
                                {
                                    if($value->field_value == "student_id")
                                        $formdata[$value->build_id] = $student_data->stateID;
                                    else if($value->field_value != "parent_first_name" && $value->field_value != "parent_last_name")
                                        $formdata[$value->build_id] = $student_data->{$value->field_value};
                                }
                                if($value->field_value != "parent_first_name" && $value->field_value != "parent_last_name")
                                        $tmpArray[] = $value->build_id;
                            }
                        }

                        //    print_r($formdata);exit;

                        if($current_grade != 404)
                        {
                            if($current_grade == "PreK")
                            {
                                $next_grade = "K";
                            }
                            elseif($current_grade == "K")
                            {
                                $next_grade = "1";
                            }                            
                            else
                            {
                                $next_grade = $current_grade + 1;
                            }
                            $rs_next = FormContent::where('field_property','db_field')->where('field_value','next_grade')->where('form_id',$form_id)->first();
                            if(!empty($rs_next))
                            {
//                                if($next_grade == 0)
  //                                  $formdata[$rs_next->build_id] = "1";
   //                             else
                                    $formdata[$rs_next->build_id] = $next_grade;
                            }
                        }
                        $dataArray['formdata'] = $formdata;
                        Session::push("form_data", $dataArray);

                        /* Magnet School Code */
                       /* $school_info = School::where("name", $current_school)->first();
                        if($school_info->magnet == "Yes")
                        {

                        }*/

                    }
                }
            }
        }
        $application_data = Application::where("id", $application_id)->first();
            

         if(Session::has("form_data"))
            {

                $dataArray =  Session::get("form_data")[0];
                Session::forget("form_data");
            }
            else
                $dataArray = array();

            if(isset($dataArray['formdata']))
            {
                $formdata = $dataArray['formdata'];
            }
            else
                $formdata = array();
            
            
            foreach($req as $key=>$value)
            {
                if(!in_array($key, array("_token","no_of_pages")) && !in_array($key, $tmpArray))
                {
                    $dataArray[$key] = $value;
                }
            }
            
            $newformdata = $dataArray['formdata'];
            $tmpformdata = array();
            
            foreach($formdata as $key=>$value)
            {
                $tmpformdata[$key] = $value;   
            }
            foreach($newformdata as $key=>$value)
            {
                if(!in_array($key, $tmpArray))
                    $tmpformdata[$key] = $value;   
            }

            $dataArray['formdata'] = $tmpformdata;
            $get_mcp_id = fetch_student_field_id($form_id, "mcp_employee");
            Session::push("form_data", $dataArray);

            $dupicate_submission = $this->find_duplicate_submission($form_id, $req['form_type']);
            if(count($dupicate_submission) > 0)
            {

                $this->destroySessions();
                if($dupicate_submission[0] == "processed")
                {
                    $msg_type="duplicate_processed_application_msg";
                    return redirect(url('/msgs/processed'));
                }
                else
                {
                    $msg_type="duplicate_application_msg";
                    return redirect(url('/msgs/duplicate'));
                }

                //$msg_type="duplicate_application_msg";
                //return view('layouts.errors.msgs',compact("district","msg_type","application_data"));  
            }



            $stopNext = false;
            if($req['form_type'] != "exist")
            {
                if(Session::has("from_admin") && $district->admin_mcpss_zone_api == "N")
                {
                    session()->forget('zonemsg');
                }
                else
                {
                    if($district->zone_api == "Yes")// && Session::has("from_admin"))
                    {
                        if(!$this->getZonedSchool($form_id) && $req['form_type'] != "exist")
                        {
                            if(isset($dataArray['formdata'][$get_mcp_id]) && $dataArray['formdata'][$get_mcp_id] == "Yes")
                            {
                                $zoned_field_id = fetch_student_field_id($form_id, "zoned_school");
                                

                                $formdata = $dataArray['formdata'];
                                $formdata[$zoned_field_id] = "Other";
                                Session::forget("form_data");
                                $dataArray['formdata'] = $formdata;
                                Session::push("form_data", $dataArray);
                                session()->forget('zonemsg');
                            }
                            else
                            {
                                $this->destroySessions();
                                return redirect(url('/msgs/nozone'));
                                session()->flash('zonemsg', getAlertMsg('zone_address_not_available'));
                                $stopNext = true;
                            }
                        }
                        else
                        {
                            session()->forget('zonemsg');

                        }

                    }
                    else
                    {
                        session()->forget('zonemsg');
                    }
                }
            }
            else
            {
                if($district->zone_api_existing == "Yes")
                {
                    $sid = $dataArray['formdata'][$student_field_id];
                    $rs = AddressOverride::where('state_id', $sid)->first();
                    if(!empty($rs) && $rs->zoned_school != '')
                    {
                        $zoned_field_id = fetch_student_field_id($form_id, "zoned_school");
                        $formdata[$zoned_field_id] = $rs->zoned_school;
                        session()->forget('zonemsg');
                    }
                    else
                    {
                        if(!$this->getZonedSchool($form_id))
                        {
                            if(isset($dataArray['formdata'][$get_mcp_id]) && $dataArray['formdata'][$get_mcp_id] == "Yes")
                            {
                                $zoned_field_id = fetch_student_field_id($form_id, "zoned_school");
                                $formdata[$zoned_field_id] = "Other";
                                session()->forget('zonemsg');
                            }
                            else
                            {
                                session()->flash('zonemsg', getAlertMsg('zone_address_not_available'));
                                $stopNext = true;
                            }
                        }
                        else
                        {
                            session()->forget('zonemsg');

                        }
                    }


                }
            }
        if($page_id < $no_of_pages)
        {
//            Session::forget("form_data");
            if(isset($req['submit']) && $req['submit']!="Prev Step" && !$stopNext)
            {
                $page_id = $page_id+1;
            }  
            $rsp = SubmissionSteps::updateOrCreate(["session_id" => Session::get("step_session")], ['step_no' => $page_id]);

            $data = $this->getFormbyId($form_id, $page_id, $req['form_type']);
            if(count($data) > 0)
            {
                $sessionarray = $tmpformdata;
                return view('layouts.front.form_display',compact("district", "data", "page_id","form_type","form_id","application_id","sessionarray","application_data"));
            }
            else
            {
                // return Session::all();
                return $this->submit_form($form_id, $form_type);
            }
        }
        else
        {
            /* Here for save code will be done */
            return $this->submit_form($form_id, $form_type);
        }    

    }


    public function submit_form($form_id, $form_type)
    {
        $dupicate_submission = array();//$this->find_duplicate_submission($form_id);
        $district = District::where("id", Session::get("district_id"))->first();
        if(count($dupicate_submission) > 0)
        {
            $this->destroySessions();
            $msg_type="duplicate_application_msg";
            return redirect(url('/msgs/duplicate'));
       }
        else
        {
            $confirmation_no = $this->saveFrontForm($form_type);

            $submission_data = Submissions::where('confirmation_no', $confirmation_no)->first();
            
            $field_id = fetch_student_field_id($form_id, "next_grade"); 
            $next_grade = Session::get("form_data")[0]["formdata"][$field_id];
            
            $field_id = fetch_student_field_id($form_id, "current_grade"); 
            $current_grade = Session::get("form_data")[0]["formdata"][$field_id];

            
            //$field_id = fetch_student_field_id($form_id, "next_grade"); $data = Session::get("form_data")[0]["formdata"][$field_id];
            
            $emailArr = array();
            $emailArr['application_id'] = Session::get("application_id");

            $field_id = fetch_student_field_id($form_id, "first_name"); 
            $first_name = Session::get("form_data")[0]["formdata"][$field_id];

            $field_id = fetch_student_field_id($form_id, "last_name"); 
            $last_name = Session::get("form_data")[0]["formdata"][$field_id];

            $field_id = fetch_student_field_id($form_id, "parent_first_name"); 
            $parent_first_name = Session::get("form_data")[0]["formdata"][$field_id];

            $field_id = fetch_student_field_id($form_id, "parent_last_name"); 
            $parent_last_name = Session::get("form_data")[0]["formdata"][$field_id];

            $field_id = fetch_student_field_id($form_id, "parent_email"); 
            $parent_email = Session::get("form_data")[0]["formdata"][$field_id];

            $emailArr['first_name'] = $first_name;
            $emailArr['last_name'] = $last_name;
            $emailArr['parent_first_name'] = $parent_first_name;
            $emailArr['parent_last_name'] = $parent_last_name;
            $emailArr['email'] = $parent_email;
            $emailArr['confirm_number'] = $confirmation_no;
            $emailArr['signature'] = get_signature('email_signature');
            $emailArr['student_id'] = $submission_data->student_id;
            $emailArr['next_grade'] = $next_grade;
            $emailArr['current_grade'] = $current_grade;
            $emailArr['submission_date'] = getDateTimeFormat($submission_data->created_at);
            $emailArr['current_school'] = $submission_data->current_school;
            $emailArr['zoned_school'] = $submission_data->current_school;
            $emailArr['parent_name'] = $parent_first_name." ".$parent_last_name;
            $emailArr['student_name'] = $first_name." ".$last_name;
            

            

            $msg_data = ApplicationConfiguration::where("application_id", $emailArr['application_id'])->first();
            $application_data = Application::where("id", $emailArr['application_id'])->first();
            $emailArr['transcript_due_date'] = getDateTimeFormat($application_data->transcript_due_date);

            if($form_type == "exist" || $submission_data->submission_status == "Active")
            {
                //here i'll write code for active email
                $student_type = "active";
                $emailArr['type'] = "active_email";
                $emailArr['msg'] = $msg_data->active_email;
                $confirm_msg = $msg_data->active_screen;

                //$this->sentSuccessEmail("active_email");
                $msg_type = "exists_success_application_msg";
                $emailArr['email'] =    $parent_email;//'mcpssparent@gmail.com';
                $subject = $msg_data->active_email_subject;
                $confirm_title = $msg_data->active_screen_title;
                $confirm_subject = $msg_data->active_screen_subject;
            }
            else
            {
                //here i'll write pending email code
                $emailArr['type'] = "pending_email";
                $student_type = "pending";
                //$this->sentSuccessEmail("pending_email");                
                $msg_type = "new_success_application_msg";
                $emailArr['email'] = $parent_email;
                $emailArr['msg'] = $msg_data->pending_email;
                $confirm_msg = $msg_data->pending_screen;
                $subject = $msg_data->pending_email_subject;
                $confirm_title = $msg_data->pending_screen_title;
                $confirm_subject = $msg_data->pending_screen_subject;


            }

            $subject = find_replace_string($subject,$emailArr);

            $subject = str_replace("{student_name}", $emailArr['first_name']." ".$emailArr['last_name'], $subject);
            $subject = str_replace("{parent_name}", $emailArr['parent_first_name']." ".$emailArr['parent_last_name'], $subject);
            $subject = str_replace("{confirm_number}", $emailArr['confirm_number'], $subject);
            $subject = str_replace("{confirmation_no}", $emailArr['confirm_number'], $subject);
            $subject = str_replace("{","",$subject);
            $subject = str_replace("}","",$subject);
            $emailArr['subject'] = $subject;


            $confirm_subject = find_replace_string($confirm_subject,$emailArr);
            $confirm_subject = str_replace("{student_name}", $emailArr['first_name']." ".$emailArr['last_name'], $confirm_subject);
            $confirm_subject = str_replace("{parent_name}", $emailArr['parent_first_name']." ".$emailArr['parent_last_name'], $confirm_subject);
            $confirm_subject = str_replace("{confirm_number}", $emailArr['confirm_number'], $confirm_subject);
            $confirm_subject = str_replace("{confirmation_no}", $emailArr['confirm_number'], $confirm_subject);
            $confirm_subject = str_replace("{","",$confirm_subject);
            $confirm_subject = str_replace("}","",$confirm_subject);

            $confirm_title = find_replace_string($confirm_title,$emailArr);
            $confirm_title = str_replace("{student_name}", $emailArr['first_name']." ".$emailArr['last_name'], $confirm_title);
            $confirm_title = str_replace("{parent_name}", $emailArr['parent_first_name']." ".$emailArr['parent_last_name'], $confirm_title);
            $confirm_title = str_replace("{confirm_number}", $emailArr['confirm_number'], $confirm_title);
            $confirm_title = str_replace("{confirmation_no}", $emailArr['confirm_number'], $confirm_title);
            $confirm_title = str_replace("{","",$confirm_title);
            $confirm_title = str_replace("}","",$confirm_title);


            $this->sentSuccessEmail($emailArr);
             $rsp = SubmissionSteps::updateOrCreate(["session_id" => Session::get("step_session")], ['step_no' => 4]);
            $this->destroySessions();
           

            
            $confirm_msg = str_replace("{student_name}", $emailArr['first_name']." ".$emailArr['last_name'], $confirm_msg);
            $confirm_msg = str_replace("{parent_name}", $emailArr['parent_first_name']." ".$emailArr['parent_last_name'], $confirm_msg);
            $confirm_msg = str_replace("{confirm_number}", $emailArr['confirm_number'], $confirm_msg);
            $confirm_msg = str_replace("{transcript_due_date}", getDateTimeFormat($application_data->transcript_due_date), $confirm_msg);
            $parent_upload_link = url('/')."/upload/".$emailArr['application_id']."/grade";
            $confirm_msg = str_replace("{parent_grade_cdi_upload_link}", $parent_upload_link, $confirm_msg);



            /* Code for Recommendation Form Instructions */
            $sid = $submission_data->id;
            
            $instructions = "";
            /*$application_data = Application::where("id", $submission_data->application_id)->first();
            if($application_data->recommendation_email_to_parent == "Yes")
            {
                $data = SubmissionData::where("submission_id", $sid)->where(function ($q) use ($sid){
                        $q->where("config_name", "first_choice_recommendation_instructions")->orWhere("config_name", "second_choice_recommendation_instructions");
                    })->first();
                if(!empty($data))
                {
                    $instructions = getConfig()['recommendation_link'];
                    $instructions .= "<p class='text-center'><a class='btn btn-secondary' href='".url('/download/instructions/'.$data->config_value)."'><i class='fa fa-download'></i>&nbsp;Download</a>";
                }

            }*/
            
            
            $instructions = trim($instructions);
            
            


            return view('layouts.errors.confirm_screen',compact("district","msg_type","confirmation_no","confirm_msg","confirm_subject","confirm_title","student_type","application_data", "instructions")); 

           // return view('layouts.errors.success_application',compact("district","confirmation_no"));

        }
       
    }

    public function getZonedSchool($form_id)
    {
        if(Session::has("form_data"))
        {
            $dataArray =  Session::get("form_data")[0];
        }
        $formdata = $dataArray['formdata'];
        $db_fields = FormContent::where('form_id',$form_id)->where('field_property','db_field')->get();
        
        $address = $city = $state = $zip = $zoned_school = "";
        $zoned_field_id = $next_grade = 0;
        
        foreach($db_fields as $key=>$value)
        {
            if($value->field_value == "zoned_school")
            {
                $zoned_field_id = $value->build_id;
            }
            elseif(in_array($value->field_value, array("address","city","zip","state","next_grade")))
            {
                if(isset($formdata[$value->build_id]))
                {
                    ${$value->field_value} = $formdata[$value->build_id]; 
                }
            }
        }


        if($address != "")
        {
            $zoned_school = app('App\Modules\ZonedSchool\Controllers\AddressValidateController')->getZonedSchool($address, $next_grade);

            if($zoned_school != '')
            {
                $formdata[$zoned_field_id] = $zoned_school;
                Session::forget("form_data");
                $dataArray['formdata'] = $formdata;
                Session::push("form_data", $dataArray);
                return true;
            }
            else
            {
                //$nz = NoZonedSchool::create($insert);
                return false;
            }
        }
        return true;
    }


    private function sentSuccessEmail($emailArr)
    {
        $msg_data = ApplicationConfiguration::where("application_id", $emailArr['application_id'])->first();
        $msg = $emailArr['msg'];

        $msg = find_replace_string($msg,$emailArr);
       

        $msg = str_replace("{student_name}", $emailArr['first_name']." ".$emailArr['last_name'], $msg);
        $msg = str_replace("{parent_name}", $emailArr['parent_first_name']." ".$emailArr['parent_last_name'], $msg);
        $msg = str_replace("{confirm_number}", $emailArr['confirm_number'], $msg);
        $msg = str_replace("{confirmation_no}", $emailArr['confirm_number'], $msg);
        $msg = str_replace("{transcript_due_date}", $emailArr['transcript_due_date'], $msg);
        $parent_upload_link = url('/')."/upload/".$emailArr['application_id']."/grade";
        $msg = str_replace("{parent_grade_cdi_upload_link}", $parent_upload_link, $msg);
        $msg = str_replace("{","",$msg);
        $msg = str_replace("}","",$msg);


        $emailArr['email_text'] = $msg;
        $emailArr['logo'] = getDistrictLogo();


        $data = array();
        $submission_data = Submissions::where('confirmation_no', $emailArr['confirm_number'])->first();
        $data['submission_id'] = $submission_data->id;
        $data['email_to'] = $emailArr['email'];
        $data['email_subject'] = $emailArr['subject'];
        $data['email_body'] = $emailArr['email_text'];
        $data['logo'] = $emailArr['logo'];
        $data['module'] = "Direct Submission Confirmation";

        $application_data = Application::where("id", $submission_data->application_id)->first();

            try{
                Mail::send('emails.index', ['data' => $emailArr], function($message) use ($emailArr, $data, $application_data, $submission_data){
                        $message->to($emailArr['email']);
                        $message->subject($emailArr['subject']);
                        /*if($application_data->recommendation_email_to_parent == "Yes" && $submission_data->student_id == "")
                        {
                            $files = SubmissionData::where("config_name", "first_choice_recommendation_instructions")->where("submission_id", $data['submission_id'])->first();
                            if(!empty($files))
                            {
                                $message->attach("resources/assets/admin/recommendation_instructions/".$files->config_value);
                            }

                            $files = SubmissionData::where("config_name", "second_choice_recommendation_instructions")->where("submission_id", $data['submission_id'])->first();
                            if(!empty($files))
                            {
                                $message->attach("resources/assets/admin/recommendation_instructions/".$files->config_value);
                            }

                        }*/


                    });
                $data['status'] = "Success";

                createEmailActivityLog($data);
            }
            catch(\Exception $e){
                // Get error here
                //echo 'Message: ' .$e->getMessage();exit;
                $data['status'] = $e->getMessage();
                createEmailActivityLog($data);
            }            
        
        


        
    }

    public function find_duplicate_submission($form_id, $type="new")
    {
        $duplicate_array = checkDuplicateFields($form_id);


        $condition = array();
        $formdata = Session::get("form_data")[0];

        foreach($duplicate_array as $key=>$value)
        {
            $db_field = get_field_value($value->build_id, $value->form_id, "db_field");

            if($db_field != "" && isset($formdata['formdata'][$value->build_id]))
            {
                if($type=="exist")
                {
                    if($db_field == "birthday" || $db_field == "student_id")
                        $condition[$db_field] = $formdata['formdata'][$value->build_id];
                }
                else
                    $condition[$db_field] = $formdata['formdata'][$value->build_id];
            }
        }
        if(!empty($condition))
        {
            $query = Submissions::where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'));
            foreach($condition as $key=>$value)
            {
                $query->where($key, $value);
            }
            $query->where("form_id", $form_id);
            $dt = $query->first();

            if(!empty($dt))
            {
                if(in_array($dt->submission_status, array("Offered and Declined", "Denied due to Ineligibility", "Denied due to Incomplete Records", "Application Withdrawn")))
                    return array();
                else if(in_array($dt->submission_status, array("Offered and Accepted", "Waitlisted")))
                {
                    return array("processed");
                }
                else{
                    return array("1","2");
                }
            }
            else
                return array();
        }
        else
            return array();
    }

    public function saveFrontForm($form_type = '')
    {
        // $form_data = isset($form_data) ? $form_data : Session::get("formdata");
        // dd("dd");
        $form_data = Session::get("form_data")[0];

        $form_details = Form::where("id", $form_data['form_id'])->first();
        if(!empty($form_details))
        {
            $confirmtion_style = $form_details->confirmation_style;
        }
        else
        {
            $confirmtion_style = "MAGNET";
        }
        
        // dd($form_data);
        $insert = array(
            "application_id" => $form_data["application_id"],
            "form_id" => $form_data["form_id"],
            "enrollment_id" => Session::get("enrollment_id")
        );
        $insert['first_choice'] = isset($form_data['first_choice']) ? $form_data['first_choice'] : null;
        $second_choice = isset($form_data['second_choice']) ? $form_data['second_choice'] : null;
        if($insert['first_choice'] != $second_choice)
        {
            $insert['second_choice'] = isset($form_data['second_choice']) ? $form_data['second_choice'] : null;
            $insert['second_sibling'] = isset($form_data['second_sibling']) ? $form_data['second_sibling'] : null;
        }
        else
        {
            $insert['second_choice'] = null;
            $insert['second_sibling'] = null;
        }
        $insert['first_sibling'] = isset($form_data['first_sibling']) ? $form_data['first_sibling'] : null;
        foreach ($form_data['formdata'] as $f => $input)
        {
            $db_field = FormContent::where('build_id',$f)->where('field_property',"db_field")->first();
            if(isset($db_field->field_value))
            {
                $insert[$db_field->field_value] = $input;
            }

        }

        if(isset($insert['race']))
        {
            $rs = RaceDivision::where("actual_race", $insert['race'])->first();
            if(!empty($rs))
            {
                $insert['calculated_race'] = $rs->calculated_race;
            }
        }
//        print_r($insert);exit;
        $insert['district_id'] = Session::get('district_id');
        $insert['lottery_number'] = generate_lottery_number();
        $insert['late_submission'] = "N";
        $rsApp = Application::where("id", $insert['application_id'])->first();
        if(!empty($rsApp))
        {
            if($rsApp->submission_type != "Regular")
            {
                $insert['late_submission'] = "Y";
            }

        }

        if($form_type=="new")
        {
            $insert['submission_status'] = 'Pending';
        }
        
        /*if(isset($insert['student_id']) && $insert['student_id'] != '')
        {
            $grade = DB::table("studentgrade_latest")->where("stateID", $insert['student_id'])->first();

            if(empty($grade))
            {
                $insert['grade_exists'] = 'N';
            }
            $cdi = DB::table("student_conduct_disciplinary")->where("stateID", $insert['student_id'])->first();
            if(empty($cdi))
            {
                $insert['cdi_exists'] = 'N';
            }
        }*/

        $active = false;
        $grade_needed = false;
        if($insert['first_choice'] != "")
        {
            $program_data = ApplicationProgram::where("id", $insert['first_choice'])->select('program_id')->first();
            if(!empty($program_data))
            {
                $insert['first_choice_program_id'] = $program_data->program_id;
                $eligibilities = ProgramEligibility::where("program_id", $program_data->program_id)->whereRaw("FIND_IN_SET('".$insert['next_grade']."', grade_lavel_or_recommendation_by)")->where("application_id", $insert['application_id'])->where('status','Y')->whereIn("eligibility_type",array(2,3))->first();
                if(empty($eligibilities))
                    $active = true;
                else
                {
                    $grade_needed = true;
                    $insert['grade_exists'] = "N";
                }
            }

        }
        if($insert['second_choice'] != "")
        {
            $program_data = ApplicationProgram::where("id", $insert['second_choice'])->select('program_id')->first();
            if(!empty($program_data))
            {
                $insert['second_choice_program_id'] = $program_data->program_id;
                $eligibilities = ProgramEligibility::where("program_id", $program_data->program_id)->whereRaw("FIND_IN_SET('".$insert['next_grade']."', grade_lavel_or_recommendation_by)")->where("application_id", $insert['application_id'])->where('status','Y')->whereIn("eligibility_type",array(2,3))->first();
                if(empty($eligibilities) && $active)
                    $active = true;
                else
                    $active = false;
                if(!empty($eligibilities))
                {
                    $grade_needed = true;
                    $insert['grade_exists'] = "N";
                }
            }
            
        }


        if($active == true)
        {
            $insert['submission_status'] = "Active";
        }
        if(Session::has("from_admin"))
        {
            if(Auth::check())
            {
                $insert['submitted_by'] = Auth::user()->id;
            }
        }        
        
        if(Session::has("gifted_student"))
        {
            $insert['gifted_student'] = Session::get("gifted_student");
        }   
        else
        {
            $insert['gifted_student'] = "Not Gifted";
        }

       
        $result = Submissions::create($insert);
        $last_id = DB::getPdo()->lastInsertId();
        

        $recommendation = false;
        $recommendation_links = [];
        $recommendation_eligibility_id = $recid = [];
        if(isset($insert['first_choice_program_id']))
        {
            /* Code to generate Writing prompt url for First choice program */
            $writeEligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("program_eligibility.application_id", $insert['application_id'])->where("eligibility_template.name", "Writing Prompt")->where("program_id", $insert['first_choice_program_id'])->first();
            if(!empty($writeEligibility))
            {
                $sub_data = [];
                $sub_data['submission_id'] = $last_id;
                $sub_data['config_name'] = "wp_first_choice_link";
                $sub_data['config_value'] = $last_id.".".$insert['first_choice_program_id'].".".rand(101, 999);
                $in = SubmissionData::create($sub_data);
            }

            /* Code to generate Writing prompt url for Recommendation Form  */
            $recoEligibilities = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("program_eligibility.application_id", $insert['application_id'])->where("eligibility_template.name", "Recommendation Form")->where("program_id", $insert['first_choice_program_id'])->get();
            
            if(isset($insert['student_id']) && $insert['student_id'] != "")
            {
               $this->update_recommendation_teacher_email($insert['student_id']);
            }

            //dd($recoEligibilities);
            if(!empty($recoEligibilities))
            {
                $recommendation = true;

                foreach($recoEligibilities as $recoEligibility)
                {
                    $eligibility_id = $recoEligibility->assigned_eigibility_name;
                    $recid[] = $eligibility_id;
                    $recommendation_eligibility_id[$insert['first_choice_program_id']] = $eligibility_id;
                    $eligibility_data = getEligibilityContent1($eligibility_id);
                    if(!empty($eligibility_data))
                    {
                        $rs = RecommendationException::where("program_id", $insert['first_choice_program_id'])->where("eligibility_id", $eligibility_id)->where("grade", $insert['next_grade'])->first();
                        if(!empty($rs))
                        {

                            $subjects = explode(",", $rs->subject_teacher);
                        }
                        else
                        {
                            $subjects = $eligibility_data->subjects;
                        }
                        $recommendation_links = $subject_emails = [];
                        
                        foreach($subjects as $skey=>$skvalue)
                        {
                            $sub_data = [];
                            $sub_data['submission_id'] = $last_id;
                            $sub_data['config_name'] = "recommendation_".$skvalue."_url";
                            $sub_data['config_value'] = $skvalue.".".$last_id.".".$insert['first_choice_program_id'].".".$eligibility_id;
                            $recommendation_links[$skvalue] = $sub_data['config_value']; 
                            // Commented on 2nd Jan 
                             if($rsApp->recommendation_email_to_parent == "No")
                             {
                                 if($skvalue == "lfd_admin")
                                 {
                                     $subject_emails[$skvalue."_name"] = (isset($eligibility_data->lfd_admin_name) ? $eligibility_data->lfd_admin_name : "");
                                     $subject_emails[$skvalue."_email"] = (isset($eligibility_data->lfd_admin_email) ? $eligibility_data->lfd_admin_email : "");
                                 }
                                 elseif(isset($insert['student_id']) && $insert['student_id'] != "")
                                 {
                                     $rs_student = StudentData::where('enrollment_id', Session::get('enrollment_id'))->where("stateID", $insert['student_id'])->where("field_name", strtolower($skvalue)."_teacher_name")->first();
                                     if(!empty($rs_student))
                                     {
                                         $subject_emails[$skvalue."_name"] = $rs_student->field_value;
                                     }
                                     $rs_student = StudentData::where('enrollment_id', Session::get('enrollment_id'))->where("stateID", $insert['student_id'])->where("field_name", strtolower($skvalue)."_teacher_email")->first();
                                     if(!empty($rs_student))
                                     {
                                         $subject_emails[$skvalue."_email"] = $rs_student->field_value;
                                     }

                                 }
                             }
                             else
                             {
                                 $subject_emails[$skvalue."_name"] = $insert['parent_first_name'] . " " . $insert['parent_last_name'];
                                 $subject_emails[$skvalue."_email"] = $insert['parent_email'];
                             }    
                            $in = SubmissionData::updateOrCreate(["submission_id"=>$last_id, "config_name"=>$sub_data['config_name']], $sub_data);

                        }

            

                        $instructions_file_name = $this->generateRecommendationPDF($last_id, $insert['first_choice_program_id'],$recommendation_links, $eligibility_id, $subject_emails);
                        if($instructions_file_name != "")
                        {
                            $sub_data = [];
                            $sub_data['submission_id'] = $last_id;
                            $sub_data['config_name'] = "first_choice_recommendation_instructions";
                            $sub_data['config_value'] = $instructions_file_name;
                            $in = SubmissionData::updateOrCreate(["submission_id"=>$last_id, "config_name"=>$sub_data['config_name']], $sub_data);
                        }

                    }
                }
                

            }
        }

        if(isset($insert['second_choice_program_id']))
        {
            /* Code to generate Writing prompt url for Recommendation Form  */
            $writeEligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("program_eligibility.application_id", $insert['application_id'])->where("eligibility_template.name", "Writing Prompt")->where("program_id", $insert['second_choice_program_id'])->first();
            if(!empty($writeEligibility))
            {
                $sub_data = [];
                $sub_data['submission_id'] = $last_id;
                $sub_data['config_name'] = "wp_second_choice_link";
                $sub_data['config_value'] = $last_id.".".$insert['second_choice_program_id'].".".rand(101, 999);
                $in = SubmissionData::create($sub_data);
            }

            /* Code to generate Writing prompt url for Recommendation Form  */
            $recoEligibilities = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("program_eligibility.application_id", $insert['application_id'])->where("eligibility_template.name", "Recommendation Form")->where("program_id", $insert['second_choice_program_id'])->get();
            if(!empty($recoEligibilities))
            {
                foreach($recoEligibilities as $recoEligibility)
                {
                    $eligibility_id = $recoEligibility->assigned_eigibility_name;
                    if(!in_array($eligibility_id, $recid))
                    {
                        $eligibility_data = getEligibilityContent1($eligibility_id);

                        $recommendation = true;
                        $recommendation_eligibility_id[$insert['second_choice_program_id']] = $eligibility_id;

                        if(!empty($eligibility_data))
                        {
                            $rs = RecommendationException::where("program_id", $insert['first_choice_program_id'])->where("eligibility_id", $eligibility_id)->where("grade", $insert['next_grade'])->first();
                            if(!empty($rs))
                            {

                                $subjects = explode(",", $rs->subject_teacher);
                            }
                            else
                            {
                                $subjects = $eligibility_data->subjects;
                            }

                            $recommendation_links = [];
                            foreach($subjects as $skey=>$skvalue)
                            {
                                $sub_data = [];
                                $sub_data['submission_id'] = $last_id;
                                $sub_data['config_name'] = "recommendation_".$skvalue."_url";
                                $sub_data['config_value'] = $skvalue.".".$last_id.".".$insert['second_choice_program_id'].".".$eligibility_id;
                                $in = SubmissionData::updateOrCreate(["submission_id"=>$last_id, "config_name"=>$sub_data['config_name']], $sub_data);
                                $recommendation_links[$skvalue] = $sub_data['config_value']; 

                                if($rsApp->recommendation_email_to_parent != "No")
                                {
                                    if($skvalue == "lfd_admin")
                                    {
                                        $subject_emails[$skvalue."_name"] = (isset($eligibility_data->lfd_admin_name) ? $eligibility_data->lfd_admin_name : "");
                                        $subject_emails[$skvalue."_email"] = (isset($eligibility_data->lfd_admin_email) ? $eligibility_data->lfd_admin_email : "");
                                    }
                                    elseif(isset($insert['student_id']) && $insert['student_id'] != "")
                                    {
                                        $rs_student = StudentData::where("stateID", $insert['student_id'])->where("field_name", $skvalue."_teacher_email")->first();
                                        if(!empty($rs_student))
                                        {
                                            $subject_emails[$skvalue."_name"] = $rs_student->field_value;
                                        }
                                        $rs_student = StudentData::where("stateID", $insert['student_id'])->where("field_name", $skvalue."_teacher_name")->first();
                                        if(!empty($rs_student))
                                        {
                                            $subject_emails[$skvalue."_email"] = $rs_student->field_value;
                                        }

                                    }
                                }
                                else
                                {
                                    $subject_emails[$skvalue."_name"] = $insert['parent_first_name'] . " " . $insert['parent_last_name'];
                                    $subject_emails[$skvalue."_email"] = $insert['parent_email'];
                                }  

                            }
                            $instructions_file_name = $this->generateRecommendationPDF($last_id, $insert['second_choice_program_id'],$recommendation_links, $eligibility_id, []);
                            if($instructions_file_name != "")
                            {
                                $sub_data = [];
                                $sub_data['submission_id'] = $last_id;
                                $sub_data['config_name'] = "second_choice_recommendation_instructions";
                                $sub_data['config_value'] = $instructions_file_name;
                                $in = SubmissionData::updateOrCreate(["submission_id"=>$last_id, "config_name"=>$sub_data['config_name']], $sub_data);
                            }
                        }


                    }
                }
                
            }
        }


        $sub_data =  Submissions::where('submissions.id',$last_id)->join("application", "application.id", "submissions.application_id")->select("submissions.*", "application.enrollment_id")->first();
        if(Session::has("from_admin"))
        {
            $this->modelCreate($sub_data,"Submission By Admin");
        }
        
        $form_data['formdata']['first_choice'] = isset($form_data['first_choice']) ? $form_data['first_choice'] : null;
        $form_data['formdata']['second_choice'] = isset($form_data['second_choice']) ? $form_data['second_choice'] : null;
        $form_data['formdata']['second_sibling'] = isset($form_data['second_sibling']) ? $form_data['second_sibling'] : null;
        $form_data['formdata']['first_sibling'] = isset($form_data['first_sibling']) ? $form_data['first_sibling'] : null;

        $form_data['formdata'] = json_encode($form_data['formdata']);
        $resultRaw = SubmissionRaw::create($form_data);     
        $confirmation_no = $confirmtion_style."-".getEnrolmentConfirmationStyle($form_data['application_id'])."-".str_pad($last_id, 4, "0", STR_PAD_LEFT);
        // dd($confirmation_no);
        $result->confirmation_no = $confirmation_no;
        $result->save();
        // dd($result);
        
        return $confirmation_no;
    }

    public function getPreviewFieldByTypeandId($type,$form_build_id,$form_id=''){

        $data = $this->getPropertyInArr($form_build_id);
        $return  = '';
        if (isset($type)) {
            //$type = DB::table('field_type')->where('field_type_id',$type_id)->pluck('name');
            $function = 'PreviewField'.ucfirst($type);
//            echo $function;
            try{
                $return  = $this->$function($data,$form_build_id,$form_id);
            }catch (\Exception $e) {
                $return  = '';
            }
        }
        return $return;
    }

    public function getFieldByTypeandId($type,$form_build_id,$form_id=''){

        $data = $this->getPropertyInArr($form_build_id);
        $return  = '';
        if (isset($type)) {
            //$type = DB::table('field_type')->where('field_type_id',$type_id)->pluck('name');
            $function = 'Field'.ucfirst($type);
//            echo $function;
            try{
                $return  = $this->$function($data,$form_build_id);
            }catch (\Exception $e) {
                $return  = '';
            }
        }
        return $return;
    }
    public function getPropertyInArr($form_build_id){
        $property = FormContent::where('build_id',$form_build_id)->orderBy('sort_option')->get();
        $data = [];
        foreach ($property as $key => $value) {
            $data[$value->field_property] = $value->field_value;
        }
        return $data;
    }

    /* Preview Form Field */
    public function PreviewFieldTermscheck($data,$form_field_id,$form_id){
        return view('layouts.front.Field.preview.Termscheck',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }


    public function PreviewFieldView($data,$form_field_id,$form_id){
        return view('layouts.front.Field.preview.View',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldTextbox($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.Textbox',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldTextarea($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.Textarea',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldDate($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.Date',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldText($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.Textbox',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldProgram_choice($data,$form_field_id,$form_id){
        // return "--here--";
        return view('layouts.front.Field.preview.Program_choice',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldAddress($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.Address',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldEmail($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.Email',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldRadio($data,$form_field_id,$form_id){
        return view('layouts.front.Field.preview.Radio',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldCheckBox($data,$form_field_id,$form_id){

        return view('layouts.front.Field.preview.CheckBox',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldCkEdittor($data,$form_field_id){

        return view('layouts.front.Field.preview.CkEdittor',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    public function PreviewFieldSelect($data,$form_field_id,$form_id){
        return view('layouts.front.Field.preview.Select',['data'=>$data,'field_id'=>$form_field_id,'form_id'=>$form_id]);
    }

    /* Normal Form Field */

    public function FieldTermscheck($data,$form_field_id){
        return view('layouts.front.Field.Termscheck',['data'=>$data,'field_id'=>$form_field_id]);
    }


    public function FieldView($data,$form_field_id){
        return view('layouts.front.Field.View',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldTextbox($data,$form_field_id){

        return view('layouts.front.Field.Textbox',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldTextarea($data,$form_field_id){

        return view('layouts.front.Field.Textarea',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldDate($data,$form_field_id){

        return view('layouts.front.Field.Date',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldText($data,$form_field_id){

        return view('layouts.front.Field.Textbox',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldProgram_choice($data,$form_field_id){
        // return "--here--";
        return view('layouts.front.Field.Program_choice',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldAddress($data,$form_field_id){

        return view('layouts.front.Field.Address',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldEmail($data,$form_field_id){

        return view('layouts.front.Field.Email',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldRadio($data,$form_field_id){
        return view('layouts.front.Field.Radio',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldCheckBox($data,$form_field_id){

        return view('layouts.front.Field.CheckBox',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldCkEdittor($data,$form_field_id){

        return view('layouts.front.Field.CkEdittor',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function FieldSelect($data,$form_field_id){
        return view('layouts.front.Field.Select',['data'=>$data,'field_id'=>$form_field_id]);
    }

    public function checkSibling($state_id, $program_id=0)
    {
        if(is_numeric($state_id))
        {
            
            if($program_id != 0)
            {
                $data = ApplicationProgram::where("application_programs.id",$program_id)->join("grade","grade.id","application_programs.grade_id")->join("program","program.id","application_programs.program_id")->select('program.id AS program','grade.name AS grade')->first();
                $program = $data->program;
                $grade = $data->grade;
                $program_data = Program::where("id", $program)->first();
                $schoolArr = [];
                if($program_data->silbling_check == 'Y')
                {
                    $checkSchools = [];
                    if($program_data->sibling_schools != '')
                    {
                        $checkSchools = explode(",", $program_data->sibling_schools);
                    }

                    if(count($checkSchools) > 0)
                    {
                        for($i=0; $i < count($checkSchools); $i++)
                        {
                            $schools = DB::table("school")->where("name", $checkSchools[$i])->orWhereRaw("FIND_IN_SET('".$checkSchools[$i]."', sis_name)")->get();
                            foreach($schools as $k=>$v)
                            {
                                $schoolArr[] = $v->name;
                                $tmp = explode(",",$v->sis_name);
                                foreach($tmp as $tv)
                                {
                                    $schoolArr[] = trim($tv);
                                }
                            }

                        }
                    }
                    else
                    {
                        $schools = DB::table("school")->where("name", $program_data->magnet_school)->orWhere("sis_name", $program_data->magnet_school)->get();
                    }

                    if(count($schools) > 0 && count($schoolArr) <= 0)
                    {
                        
                        foreach($schools as $sk=>$sv)
                        {
                            $schoolArr[] = $sv->name;
                            $schoolArr[] = $sv->sis_name;
                        }
                        $student = DB::table("student")->where('stateID', $state_id)->whereIn('current_school', $schoolArr)->first();
                    }
                    elseif(count($schoolArr) > 0)
                    {
                         $student = DB::table("student")->where('stateID', $state_id)->whereIn('current_school', $schoolArr)->first();
                    }
                    elseif(count($schoolArr) <= 0)
                        $student = DB::table("student")->where('stateID', $state_id)->where('current_school', $program_data->magnet_school)->first();

                    
                    if(isset($student->id))
                    {
                        return $student->first_name." ".$student->last_name;
                    }
                }
                else
                {
                    $student = DB::table("student")->where('stateID', $state_id)->first();
                    if(isset($student->id))
                    {
                        return $student->first_name." ".$student->last_name;
                    }
                }
            }
            else
            {
                $student = DB::table("student")->where('stateID', $state_id)->first();
                if(isset($student->id))
                {
                    return $student->first_name." ".$student->last_name;
                } 
            }
        }
        return "";
             
    }

    public function checkStudent($state_id)
    {
        if(!is_numeric($state_id))
            return "";
        $student = DB::table("student")->where('stateID', $state_id)->first();

        if(!empty($student))
        {
            if(isset($student->id))
            {
                if($student->current_grade == "ASK-97")
                {
                    return "Higher";//$next_grade = "K";
                }
                elseif($student->current_grade == "PreK")
                {
                    $next_grade = "K";
                }
                elseif($student->current_grade == "K")
                {
                    $next_grade = "1";
                }                            
                else
                {
                    $next_grade = $student->current_grade + 1;
                }
                //if($next_grade == "K")
                 //   return "NoMagnet";
                $rsG = DB::table("application_programs")->join("grade", "grade.id", "application_programs.grade_id")->where("grade.name", $next_grade)->where("application_programs.application_id", Session::get("application_id"))->first();
                if(empty($rsG))
                {
                    return "Higher";
                }
                 //if($student->current_grade == 5 || $student->current_grade > 7)
                   // return "NoMagnet";
                $schools = DB::table("school")->where("name", $student->current_school)->orWhere("sis_name", $student->current_school)->get();
                $schoolArr = [];
                $schoolArr[] = $student->current_school;
                if(count($schools) > 0)
                {
                    
                    foreach($schools as $sk=>$sv)
                    {
                        $schoolArr[] = $sv->name;
                        $schoolArr[] = $sv->sis_name;
                    }
                }

                if(Session::has("form_id"))
                {
                    $fdata = Program::where("parent_submission_form", Session::get("form_id"))->first();
                    if(!empty($fdata))
                    {
                        if($fdata->existing_magnet_program_alert == "N")
                        {
                            return "NoMagnet";
                        }
                    }
                }

                $data = Program::where("district_id", Session::get("district_id"))->whereIn("magnet_school", $schoolArr)->first();
                if(!empty($data))
                {
                    $grade_level = $data->exclude_grade_lavel;
                    $cgrade = explode(",",$grade_level);
                    //$cgrade = $grades[count($grades)-1];
                    
                    if(in_array($student->current_grade, $cgrade))
                        return "NoMagnet";

                    if(!empty($data)){
                        if($data->existing_magnet_program_alert == 'Y')
                            return "Magnet";
                        else
                            return "NoMagnet";
                    }
                    else
                    {
                        return "NoMagnet";
                    }

                }
                else
                    return "NoMagnet";
                /*$school = School::where("name", $student->current_school)->first();
                if(!empty($school))
                {
                    if($school->magnet == "Yes")
                        return "Magnet";
                    else
                        return "NoMagnet";
                }
                else
                {
                        return "NoMagnet";
                }*/
            }
            else
                return ""; 
        }
        else
        {
            return "";
        }        
        return "";
             
    }

    public function customError()
    {
        // dd('herew');
        // abort(404);
            // return view("error");

        throw new \Exception("TO Check Custom Errors message", 1);
        
    }

    public function getDOB($grade, $application_id)
    {
        $rs = Enrollment::join('application', 'application.enrollment_id', 'enrollments.id')->where("application.id",$application_id)->first();
        if(!empty($rs))
        {
            if($grade == "PreK")
            {
                $date = $rs->perk_birthday_cut_off;
            }
            else if($grade == "K")
            {
                $date = $rs->kindergarten_birthday_cut_off;
            }
            else if($grade == "1")
            {
                $date = $rs->first_grade_birthday_cut_off;
            }
            else
            {
                $date = $rs->perk_birthday_cut_off;
            }
        }
        else
            $date = date("Y-m-d");
        return $date;
    }

    public function formSubmittedDisp()
    {
        return view('layouts.errors.form_submitted');
    }

    public function msgDisp($msg_type, $submission_id=0)
    {
        $district = District::where("id", Session::get("district_id"))->first();
        $application_data = Application::where('district_id', Session::get('district_id'))->where("status", "Y")->first();
        $confirmation_no = "";
        if($submission_id > 0)
        {
            $confirmation_no = Submissions::where("id", $submission_id)->first()->confirmation_no;
        }
        if($msg_type == "nozone")
            $msg_type="no_zone_address_found";
        elseif($msg_type == "current_nozone")
            $msg_type="current_no_zone_address_found";
        elseif($msg_type == "duplicate")
            $msg_type="duplicate_application_msg";   
        elseif($msg_type == "processed")
            $msg_type="duplicate_processed_application_msg";   
        elseif($msg_type == "nostudent")
            $msg_type="no_student_msg";   
        elseif($msg_type == "incorrectinfo")
            $msg_type="incorrect_student_info";
        elseif($msg_type == "nograde")
            $msg_type="no_grade_info";
        elseif($msg_type == "noapplication")
            $msg_type = "no_application";
        
        return view('layouts.errors.msgs',compact("district","msg_type","application_data","confirmation_no"));  
   
    }
    
    public function checkSiblingEnabled($program_id)
    {
       $data = ApplicationProgram::where("application_programs.id",$program_id)->join("program","program.id","application_programs.program_id")->select('program.sibling_enabled')->first();
       if(!empty($data))
       {
            return $data->sibling_enabled; 
       }
       else
        return "N";
    }


    public function printApplicationMsg($confirmation_no)
    {
        $submission = Submissions::where("confirmation_no", $confirmation_no)->first();

        if(!empty($submission))
        {
            $application_data = Application::where("id", $submission->application_id)->first();
        
            $msg_data = ApplicationConfiguration::where("application_id", $submission->application_id)->first();
            $emailAdd = array();
            $emailArr['first_name'] = $submission->first_name;
            $emailArr['last_name'] = $submission->last_name;
            $emailArr['parent_first_name'] = $submission->parent_first_name;
            $emailArr['parent_last_name'] = $submission->parent_last_name;
            $emailArr['email'] = $submission->parent_email;
            $emailArr['confirm_number'] = $submission->confirmation_no;
            $emailArr['signature'] = get_signature('email_signature');
            $emailArr['student_id'] = $submission->student_id;
            $emailArr['next_grade'] = $submission->next_grade;
            $emailArr['current_grade'] = $submission->current_grade;
            $emailArr['submission_date'] = getDateTimeFormat($submission->created_at);
            $emailArr['current_school'] = $submission->current_school;
            $emailArr['zoned_school'] = $submission->current_school;
            $emailArr['parent_name'] = $submission->parent_first_name." ".$submission->parent_last_name;
            $emailArr['student_name'] = $submission->first_name." ".$submission->last_name;
            $emailArr['transcript_due_date'] = getDateTimeFormat($application_data->transcript_due_date);
            


            if($submission->submission_status == "Active")
            {
                $confirm_msg = $msg_data->active_screen;
                $confirm_title = $msg_data->active_screen_title;
                $confirm_subject = $msg_data->active_screen_subject;
            }
            else
            {
                $confirm_msg = $msg_data->pending_screen;
                $confirm_title = $msg_data->pending_screen_title;
                $confirm_subject = $msg_data->pending_screen_subject;
            }

            $confirm_subject = find_replace_string($confirm_subject,$emailArr);
            $confirm_subject = str_replace("{","",$confirm_subject);
            $confirm_subject = str_replace("}","",$confirm_subject);

            $confirm_title = find_replace_string($confirm_title,$emailArr);
            $confirm_title = str_replace("{","",$confirm_title);
            $confirm_title = str_replace("}","",$confirm_title);

            $confirm_msg = find_replace_string($confirm_msg,$emailArr);
            $confirm_msg = str_replace("{","",$confirm_msg);
            $confirm_msg = str_replace("}","",$confirm_msg);

            view()->share('confirm_title',$confirm_title);
            view()->share('confirm_msg',$confirm_msg);
            view()->share('confirm_subject',$confirm_subject);
            view()->share('application_data', $application_data);
            $pdf = PDF::loadView('layouts.errors.print_application', compact('confirm_title','confirm_msg','confirm_subject','application_data'));
            return $pdf->download('PrintApplication-'.$confirmation_no.".pdf");
        }
    }

    public function incorrectInfo($student_id)
    {
        $info = array();
        $info['student_id'] = $student_id;
        $info['status'] = "Pending";
        IncorrectStudent::updateOrCreate(['student_id' => $student_id], $info);
        $this->destroySessions();
        return redirect('/msgs/incorrectinfo');
    }

    
    public function destroySessions()
    {
        Session::forget("application_id");
        Session::forget("step_session");
        Session::forget("form_data");
        Session::forget("page_id");
        Session::forget("mcpss_zone_api");
        Session::forget("zone_api");
        Session::forget("enrollment_id");
        Session::forget('gifted_student');
    }

    public function documentUploadIndex(Request $request)
    {
        $submission_id = ($request->submission_id) ? $request->submission_id : '';
        $stu_dob = ($request->stu_dob) ? date('Y-m-d', strtotime($request->stu_dob)) : '';

        $data = Submissions::where('id',$submission_id)->where('birthday',$stu_dob)->first();        
        return view('layouts.front.document_upload_index',compact('data','submission_id','stu_dob'));
    }

    public function storeDocumentUpload(Request $request)
    {
        if(isset($request->grades_upload) && !empty($request->grades_upload)){
            foreach($request->grades_upload as $key => $value){
                if (isset($value)) {
                    $doc = $value;
                    $ext = $doc->getClientOriginalExtension();
                    if($ext == 'pdf'){
                        $name =  pathinfo($doc->getClientOriginalName(),PATHINFO_FILENAME);
                        $doc_path = base_path().'/resources/assets/admin/grade_cdi_uploads';
                        $doc_name = $name.'_'.time().'.'.$ext;
                        $doc->move($doc_path, $doc_name);

                        $doc_info = [
                            'submission_id'=>$request->submission_id,
                            'doc_grade'=>$doc_name ?? '',
                        ];
                        SubmissionDocuments::create($doc_info);
                    }
                }
            }
        }

        if(isset($request->cdi_upload) && !empty($request->cdi_upload)){
            foreach($request->cdi_upload as $key => $value){
                if (isset($value)) {
                    $doc = $value;
                    $ext = $doc->getClientOriginalExtension();
                    if($ext == 'pdf'){
                        $name =  pathinfo($doc->getClientOriginalName(),PATHINFO_FILENAME);
                        $doc_path = base_path().'/resources/assets/admin/grade_cdi_uploads';
                        $doc_name = $name.'_'.time().'.'.$ext;
                        $doc->move($doc_path, $doc_name);

                        $doc_info = [
                            'submission_id'=>$request->submission_id,
                            'doc_cdi'=>$doc_name ?? '',
                        ];
                        SubmissionDocuments::create($doc_info);
                    }
                }
            }
        }

        Session::flash('success','Document Uploaded successfully.');
        return redirect('Document?submission_id='.$request->submission_id.'&stu_dob='.$request->stu_dob);
        // dd($request->all());
    }

    public function recommendationForm($subject, $submission_id, $program_id, $eligibility_id)
    {
        $config_val = $subject.".".$submission_id.".".$program_id.".".$eligibility_id;

        // dd('test');
        $exist = SubmissionRecommendation::where('submission_id',$submission_id)->where('config_value',$config_val)->first();

        if(isset($exist) && !empty($exist)){
            return redirect("/msgs/recommendation_success/".$submission_id);
        }
        $submission = Submissions::where('id',$submission_id)->first();

        $teacher_email = $teacher_name = "";
        if($submission->student_id != '')
        {
            $rs_student = StudentData::where("stateID", $submission->student_id->where('enrollment_id', $submission->enrollment_id)->where("field_name", $subject."_teacher_email")->first();
            if(!empty($rs_student))
            {
                $teacher_email = $rs_student->field_value;
            }


            $rs_student = StudentData::where("stateID", $submission->student_id)->where('enrollment_id', $submission->enrollment_id)->where("field_name", $subject."_teacher_name")->first();
            if(!empty($rs_student))
            {
                $teacher_name  = $rs_student->field_value;
            }
        }
        
        $recommendation_due_date = Application::where('id',$submission->application_id)->first()->recommendation_due_date;

//         dd($submission->application_id, $recommendation_due_date, date('Y-m-d H:i:s'));
        if(isset($recommendation_due_date) && strtotime(date('Y-m-d H:i:s')) < strtotime($recommendation_due_date)){
//            echo $program_id . " - ".$submission->application_id;exit;

            $recoEligibility = ProgramEligibility::join("eligibility_template", "eligibility_template.id", "program_eligibility.eligibility_type")->where("assigned_eigibility_name", "!=", "")->where("application_id", $submission->application_id)->where("eligibility_template.name", "Recommendation Form")->where("program_id", $program_id)->where("assigned_eigibility_name", $eligibility_id)->first();


            $eligibility_id = $recoEligibility->assigned_eigibility_name;
            $content = getEligibilityContent1($eligibility_id);
            $eligibility = Eligibility::where('id',$eligibility_id)->first();

            $footer_text = getEligibilityConfigDynamic($program_id, $eligibility_id, "footer_text", $submission->application_id);
            $program_name = getEligibilityConfigDynamic($program_id, $eligibility_id, "form_title", $submission->application_id);
            $header_text =  getEligibilityConfigDynamic($program_id, $eligibility_id, "header_text", $submission->application_id);
            
            if(isset($eligibility) && !empty($eligibility)){
                if(isset($content->form_type) && $content->form_type == "IB")
                {
                    return view('layouts.front.ib_recommendation_form',compact('content','submission','eligibility', 'subject', 'program_name', "footer_text", "recommendation_due_date", "program_id", "header_text", "eligibility_id", "teacher_name", "teacher_email"));

                }
                else
                {
                    return view('layouts.front.recommendation_form',compact('content','submission','eligibility', 'subject', 'program_name', "footer_text", "recommendation_due_date", "program_id", "header_text", "eligibility_id", "teacher_name", "teacher_email"));
                }
            }
            return abort(404);
        }else{
            return abort(404);
        }


    }


    public function recommendationFormPreview($subject, $eligibility_id, $program_id, $application_id)
    {
        $submission = Submissions::where('first_choice_program_id',$program_id)->orWhere('second_choice_program_id',$program_id)->first();
        $program = Program::where('id',$program_id)->first();
        $content = getEligibilityContent1($eligibility_id);
        $eligibility = Eligibility::where('id',$eligibility_id)->first();

        $footer_text = getEligibilityConfigDynamic($program_id, $eligibility_id, "footer_text", $application_id);
        $program_name = getEligibilityConfigDynamic($program_id, $eligibility_id, "form_title", $application_id);

        if($eligibility->alias_name != '')
            $program_name = str_replace("Recommendation", $eligibility->alias_name, $program_name);

        $program_name = str_replace("{Program_Name}", getProgramName($program_id), $program_name);
        $header_text =  getEligibilityConfigDynamic($program_id, $eligibility_id, "header_text", $application_id);
        //dd($content);
        
        if(isset($eligibility) && !empty($eligibility)){
            if(isset($content->form_type) && $content->form_type == "IB")
                return view('layouts.front.ib_recommendation_form_preview',compact('content','submission','eligibility', 'subject', 'program_name', "footer_text", "program_id", "header_text"));
            else
                return view('layouts.front.recommendation_form_preview',compact('content','submission','eligibility', 'subject', 'program_name', "footer_text", "program_id", "header_text"));
        }
        return abort(404);
    }


    public function saveRecommendationForm(Request $request)
    {
        // return $request;
        $submission_id = $request['submission_id'];
        $program_id = $request['program_id'];      
        $subject = $request['subject'];
        $eligibility_id = $request['eligibility_id'];
        $ans = json_encode($request['extra']);

        $eligibility = Eligibility::where('id',$eligibility_id)->first();


        $st = $subject. '.' .$submission_id.'.'.$program_id.".".$eligibility_id;

        $rs = SubmissionRecommendation::where("config_value", $st)->where("submission_id", $submission_id)->first();
        if(!empty($rs))
        {
            return redirect("/formsubmitted/error");
        }
        $insert = [
            'submission_id' => $submission_id,
            'config_value' => $subject. '.' .$submission_id.'.'.$program_id.".".$eligibility_id,
            'answer' => $ans,
            'teacher_email' => $request['teacher_email'],
            'teacher_name' => $request['teacher_name'],
            'comment' => $request['comment'],
            'avg_score' => $request['avg_score']
        ];
        SubmissionRecommendation::create($insert);
        
        if($eligibility->alias_name == "")
            return redirect('msgs/recommendation_success/'.$submission_id);
        else
            return redirect('msgs/learner_profile_success/'.$submission_id);
    }

    public function generateRecommendationPDF($submission_id, $program_id, $links, $eligibility_id, $subject_emails=[])
    {
        $data = Submissions::where("id", $submission_id)->first();
        $application_data = Application::where("id", $data->application_id)->first();
        $instructions = getEligibilityConfig($program_id, $eligibility_id, "instructions");
        $recommendation_parent_email_body = getEligibilityConfig($program_id, $eligibility_id, "recommendation_parent_email_body");
        if($recommendation_parent_email_body != "")
            $instructions = $recommendation_parent_email_body;

        $teacher_link_text = getEligibilityConfig($program_id, $eligibility_id, "teacher_link_text");
 
        if($instructions != '')
        {

            $subjects = config('variables.recommendation_subject');
            $str = "";
            foreach($links as $key=>$value)
            {
                $key = strtolower($key);                 
                $link = url('/recommendation/')."/".$value;
                if($teacher_link_text != "")
                {
                    $tmp = $teacher_link_text;
                    $tmp = str_replace("{recommendation_teacher_title}", $subjects[$key], $tmp);
                    $tmp = str_replace("{recommendation_teacher_link}", "<a href='".$link."'>".$link."</a>", $tmp);
                    $tmp = str_replace("{student_name}", $data->first_name." ".$data->last_name, $tmp);
                    $tmp = str_replace("{program_name}", getProgramName($program_id), $tmp);
                    $tmp = str_replace("{recommendation_due_date}", getDateTimeFormat($application_data->recommendation_due_date), $tmp);
                    $tmp = str_replace("{confirmation_no}", $data->confirmation_no, $tmp);
                    $str .= $tmp."<p><hr /></p>";

                    if($data->student_id != '' && $application_data->recommendation_email_to_parent == 'No' && isset($subject_emails[$key."_email"]))
                    {
                        
                        $emailArr = [];
                        $emailArr['logo'] = getDistrictLogo();
                        $emailArr['email_text'] = $tmp;
                        $emailArr['subject'] = "Specialty School Application Teacher Recommendation Form";
                        $emailArr['email'] = $subject_emails[$key."_email"];

                        $email_data = [];
                        $email_data['submission_id'] = $data->id;
                        $email_data['email_to'] = $emailArr['email'];
                        $email_data['email_subject'] = $emailArr['subject'];
                        $email_data['email_body'] = $emailArr['email_text'];
                        $email_data['logo'] = $emailArr['logo'];
                        $email_data['module'] = "Submission Recommendation Form to ".$subjects[$key];
                        
                        try{
                            Mail::send('emails.index', ['data' => $emailArr], function($message) use ($emailArr, $email_data){
                                    $message->to($emailArr['email']);
                                    $message->subject($emailArr['subject']);
                                    


                                });
                            $email_data['status'] = "Success";

                            createEmailActivityLog($email_data);
                        }
                        catch(\Exception $e){
                            // Get error here
                            //echo 'Message: ' .$e->getMessage();exit;
                            $email_data['status'] = $e->getMessage();
                            createEmailActivityLog($email_data);
                        }  

                    }
                    else
                    {
                        if($data->student_id != '' && $application_data->recommendation_email_to_parent == 'Yes')
                        {
                            
                            $emailArr = [];
                            $emailArr['logo'] = getDistrictLogo();
                            $emailArr['email_text'] = $tmp;
                            $emailArr['subject'] = "Specialty School Application Teacher Recommendation Form";
                            $emailArr['email'] = $data->parent_email;

                            $email_data = [];
                            $email_data['submission_id'] = $data->id;
                            $email_data['email_to'] = $data->parent_email;
                            $email_data['email_subject'] = $emailArr['subject'];
                            $email_data['email_body'] = $emailArr['email_text'];
                            $email_data['logo'] = $emailArr['logo'];
                            $email_data['module'] = "Submission Recommendation Form to ".$subjects[$key];
                            //dd($email_data, $subjects);
                            try{
                                Mail::send('emails.index', ['data' => $emailArr], function($message) use ($emailArr, $email_data){
                                        $message->to($emailArr['email']);
                                        $message->subject($emailArr['subject']);
                                        


                                    });
                                $email_data['status'] = "Success";

                                createEmailActivityLog($email_data);
                            }
                            catch(\Exception $e){
                                // Get error here
                                //echo 'Message: ' .$e->getMessage();exit;
                                $email_data['status'] = $e->getMessage();
                                createEmailActivityLog($email_data);
                            }  

                        }
                    }
                    
                }
                else
                {

                    $str .= '<p><span style="left: 46.5023px; top: 427.816px; font-size: 14.7647px; font-family: sans-serif; transform: scaleX(1.16114);"><strong>'.$subjects[$key].'</p>';
                    $str .= '<p><span style="left: 46.5023px; top: 427.816px; font-size: 14.7647px; font-family: sans-serif; transform: scaleX(1.16114);"></span><span style="left: 46.5023px; top: 464.115px; font-size: 14.7647px; font-family: sans-serif; transform: scaleX(1.05502);">Please use the following link to complete the electronic recommendation form for '.$data->first_name." ".$data->last_name." ".$data->confirmation_no.'. This</span><span style="left: 46.5023px; top: 483.708px; font-size: 14.7647px; font-family: sans-serif; transform: scaleX(1.05373);">electronic form must be completed by '.getDateTimeFormat($application_data->recommendation_due_date).'.</span></p>';
                    $str .= '<p><span style="left: 46.5023px; top: 483.708px; font-size: 14.7647px; font-family: sans-serif; transform: scaleX(1.05373);"></span><span style="left: 46.5023px; top: 483.708px; font-size: 14.7647px; font-family: sans-serif; transform: scaleX(1.05373);"><a href="'.$link.'">'.$link.'</a></p>';
                    $str .= "<p><hr /></p>";
                }
            }

            $instructions = str_replace("{recommendation_links_section}", $str, $instructions);
            $instructions = str_replace("{recommendation_due_date}", getDateTimeFormat($application_data->recommendation_due_date), $instructions);


            if($data->student_id == '')// || $application_data->recommendation_email_to_parent == "Yes")
            {
                $emailArr = [];
                $emailArr['logo'] = getDistrictLogo();
                $emailArr['email_text'] = $instructions;
                $emailArr['subject'] = "Specialty School Application Teacher Recommendation Form";
                $emailArr['email'] = $data->parent_email;

                $email_data = [];
                $email_data['submission_id'] = $data->id;
                $email_data['email_to'] = $emailArr['email'];
                $email_data['email_subject'] = $emailArr['subject'];
                $email_data['email_body'] = $emailArr['email_text'];
                $email_data['logo'] = $emailArr['logo'];
                $email_data['module'] = "Submission Recommendation Form to Parent";
                
                try{
                    Mail::send('emails.index', ['data' => $emailArr], function($message) use ($emailArr, $email_data){
                            $message->to($emailArr['email']);
                            $message->subject($emailArr['subject']);
                            


                        });
                    $email_data['status'] = "Success";

                    createEmailActivityLog($email_data);
                }
                catch(\Exception $e){
                    // Get error here
                    //echo 'Message: ' .$e->getMessage();exit;
                    $email_data['status'] = $e->getMessage();
                    createEmailActivityLog($email_data);
                }  
            }


            view()->share('instructions',$instructions);
            $pdf = PDF::loadView('layouts.front.recommendation_instructions',['instructions']);

            $path = "resources/assets/admin/recommendation_instructions";
            $fileName =  "instruction-".$submission_id."-".$program_id. '.' . 'pdf' ;
            $pdf->save($path . '/' . $fileName);
            return $fileName;
        }
      
        return "";
    }

    public function downloadRecommendationInstructions($fileName)
    {
        $file_path = 'resources/assets/admin/recommendation_instructions/'.$fileName;
        $headers = array(
          'Content-Type: application/pdf',
        );

        return Response::download($file_path, $fileName, $headers);
    }

    public function update_recommendation_teacher_email($sid)
    {
        $student = Student::where('stateID', $sid)->first();
        if(!empty($student))
        {
            $termData = DB::table("ps_cc")->where('studentid',  $student->student_id)->distinct()->get(['termid']);
            $termid = 33;
            $homeroomData = [];

            /* For Home Room Teacher */
            $homeData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.ps_id', 'ps_cc.sectionid')
                                          ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                          ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                          ->where("ps_cc.studentid", $student->student_id)
                                          ->where("ps_cc.termid", 'like', $termid.'%')
                                          ->where('ps_cc.course_number', 'like', '22991%')
                                          ->first(['email_addr', 'first_name', 'last_name']);


         // SELECT ps_users.email_addr, ps_users.first_name, ps_users.last_name from ps_cc inner join ps_sections on ps_sections.ps_id = ps_cc.sectionid
         //    inner join ps_courses on ps_courses.course_number = ps_cc.course_number 
         //    inner join student on student.student_id = ps_cc.studentid
         //    inner join ps_schoolstaff on ps_schoolstaff.ps_id = ps_sections.teacher
         //    inner join ps_users on ps_users.dcid = ps_schoolstaff.users_dcid
         //    where student.stateID = 1976847598 AND ps_cc.termid like '%34%' AND ps_sections.external_expression = 'LA(A)'
            if(!empty($homeData))
            {
                $insert = [];
                $insert['enrollment_id'] = Session::get('enrollment_id');
                $insert['field_name'] = 'homeroom_teacher_name';
                $insert['stateID'] = $sid;
                $insert['field_value'] = $homeData->first_name." ".$homeData->last_name;
                $rs = StudentData::create($insert);

                $insert = [];
                $insert['enrollment_id'] = Session::get('enrollment_id');
                $insert['stateID'] = $sid;
                $insert['field_name'] = 'homeroom_teacher_email';
                $insert['field_value'] = $homeData->email_addr;
                $rs = StudentData::create($insert);
            }
            else
            {
                $homeData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.ps_id', 'ps_cc.sectionid')
                                          ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                          ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                          ->where("ps_cc.studentid", $student->student_id)
                                          ->where("ps_cc.termid", 'like', $termid.'%')
                                          ->where('ps_sections.external_expression', 'P4(A)')
                                          ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($homeData))
                {
                    $insert = [];
                    $insert['enrollment_id'] = Session::get('enrollment_id');
                    $insert['stateID'] = $sid;
                    $insert['field_name'] = 'homeroom_teacher_name';
                    $insert['field_value'] = $homeData->first_name." ".$homeData->last_name;
                    $rs = StudentData::create($insert);

                    $insert = [];
                    $insert['enrollment_id'] = Session::get('enrollment_id');
                    $insert['stateID'] = $sid;
                    $insert['field_name'] = 'homeroom_teacher_email';
                    $insert['field_value'] = $homeData->email_addr;
                    $rs = StudentData::create($insert);
                }
            }

            
            /* Maths teacher data */
            $mathData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_sections.course_number', 'like', '02%')
                                              ->where('ps_sections.grade_level', '!=', '')
                                              ->first(['email_addr', 'first_name', 'last_name']);
            if(!empty($mathData))
            {
                $insert = [];
                $insert['enrollment_id'] = Session::get('enrollment_id');
                $insert['stateID'] = $sid;
                $insert['field_name'] = 'math_teacher_name';
                $insert['field_value'] = $mathData->first_name." ".$mathData->last_name;
                $rs = StudentData::create($insert);

                $insert = [];
                $insert['enrollment_id'] = Session::get('enrollment_id');
                $insert['stateID'] = $sid;
                $insert['field_name'] = 'math_teacher_email';
                $insert['field_value'] = $mathData->email_addr;
                $rs = StudentData::create($insert);
            }
            else
            {
                $mathData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.ps_id', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_sections.course_number', 'like', '02%')
                                              ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($mathData))
                {
                    $insert = [];
                    $insert['enrollment_id'] = Session::get('enrollment_id');
                    $insert['stateID'] = $sid;
                    $insert['field_name'] = 'math_teacher_name';
                    $insert['field_value'] = $mathData->first_name." ".$mathData->last_name;
                    $rs = StudentData::create($insert);

                    $insert = [];
                    $insert['enrollment_id'] = Session::get('enrollment_id');
                    $insert['stateID'] = $sid;
                    $insert['field_name'] = 'math_teacher_email';
                    $insert['field_value'] = $mathData->email_addr;
                    $rs = StudentData::create($insert);

                }
            }

            $engData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.ps_id', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_sections.course_number', 'like', '01%')
                                              ->where('ps_sections.grade_level', '!=', '')
                                              ->first(['email_addr', 'first_name', 'last_name']);
            if(!empty($engData))
            {
                $insert = [];
                $insert['enrollment_id'] = Session::get('enrollment_id');
                $insert['field_name'] = 'eng_teacher_name';
                $insert['stateID'] = $sid;
                $insert['field_value'] = $engData->first_name." ".$engData->last_name;
                $rs = StudentData::create($insert);

                $insert = [];
                $insert['enrollment_id'] = Session::get('enrollment_id');
                $insert['stateID'] = $sid;
                $insert['field_name'] = 'eng_teacher_email';
                $insert['field_value'] = $engData->email_addr;
                $rs = StudentData::create($insert);
            }
            else
            {
                $engData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.ps_id', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_sections.course_number', 'like', '01%')
                                              ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($engData))
                {
                    $insert = [];
                    $insert['enrollment_id'] = Session::get('enrollment_id');
                    $insert['stateID'] = $sid;
                    $insert['field_name'] = 'eng_teacher_name';
                    $insert['field_value'] = $engData->first_name." ".$engData->last_name;
                    $rs = StudentData::create($insert);

                    $insert = [];
                    $insert['enrollment_id'] = Session::get('enrollment_id');
                    $insert['stateID'] = $sid;
                    $insert['field_name'] = 'eng_teacher_email';
                    $insert['field_value'] = $engData->email_addr;
                    $rs = StudentData::create($insert);
                }
            }
        }
    }
}
