<?php

namespace App\Modules\SetEligibility\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Application\Models\Application;
use App\Modules\District\Models\District;
use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Program\Models\Program;
use App\Modules\Program\Models\ProgramEligibility;
use App\Modules\Program\Models\ProgramEligibilityLateSubmission;
use App\Modules\Priority\Models\Priority;
use App\Modules\SetEligibility\Models\{SetEligibility,SetEligibilityConfiguration,SetEligibilityLateSubmission};
use App\Traits\AuditTrail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SetEligibilityController extends Controller
{
    use AuditTrail;
    protected $url;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->url = url("admin/SetEligibility");
        View::share(["module_url"=>$this->url]);
    }
    public function index()
    {
        if(Session::get("district_id") != '0')
            $programs=Program::where('status','!=','T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        else
            $programs=Program::where('status','!=','T')->where('enrollment_id', Session::get('enrollment_id'))->get();
        // return $programs;
        // return view("Program::index",compact('programs'));
        return view("SetEligibility::index",compact('programs'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
    public function edit($id, $application_id=0)
    {
         $district=District::where('id',session('district_id'))->first();
         $applications = Application::where("enrollment_id", Session::get("enrollment_id"))->get();
        $programeligibilities=ProgramEligibility::where('program_id',$id)->first();
        if(!empty($programeligibilities) && $application_id == 0)
        {
            $application_id = $programeligibilities->application_id;
        }

        if($application_id == 0)
        {
            if(count($applications) > 0)
                $application_id = $applications[0]->id;
        }

        $priorities = Priority::where('district_id', session('district_id'))->where('status', '!=', 'T')->get();
        $program=Program::where('id',$id)->first();
        $programeligibilities=ProgramEligibility::where('program_id',$id)->where("application_id", $application_id)->get();

        $programeligibilitiesArr = [];
        foreach ($programeligibilities as $k=>$programeligibility)
        {
            $programeligibilitiesArr[] = $programeligibility->eligibility_type;    
        }


        $eligibility_templates=EligibilityTemplate::all()->toArray();
        // $eligibility_templates[] = array("id"=>0,"name"=>"Template 2");
        // return $eligibility_templates;
        $eligibility_types=Eligibility::where('status','Y')->where('district_id', Session::get('district_id'))->get();
        $eligibilities= [];
        foreach ($eligibility_templates as $k=>$eligibility_template)
        {
            $eligibility=null;
            $exist_ids = [];
            for ($i=0; $i < $eligibility_template['max_count']; $i++) { 
                foreach ($eligibility_types as $key=>$eligibility_type)
                {
                    if ($eligibility_template['id']==$eligibility_type->template_id && !in_array($eligibility_type->id, $exist_ids))
                    {
                        
                        $eligibility[]=$eligibility_type;
                        $exist_ids[] = $eligibility_type->id;
                    }
                    /*if($eligibility_type->template_id == 0){
                        $eligibility[]=$eligibility_type;
                    }*/

                }
                if ($eligibility!=null)
                {
                    $eligibilities[]=array_merge($eligibility_template,array('eligibility_types'=>$eligibility));
                }
            }
        }
        $existArr = [];
        foreach ($eligibilities as $key=>$eligibility)
        {

            foreach ($programeligibilities as $k=>$programeligibility)
            {
                if ($programeligibility->eligibility_type==$eligibility['id'] && !isset($eligibilities[$key]['program_eligibility']))
                    {
                        // Check for recommendation form eligibility
                            
                        if (isset($et->id)) {
                            if ( ($et->id == $programeligibility->eligibility_type) &&
                                ($programeligibility->assigned_eigibility_name != '')
                            ) { 
                                $rec_form_data['eligibility_id'] = $programeligibility->assigned_eigibility_name;
                                $rec_form_data['status'] = true;
                            }
                        }
                        $newdata = app('App\Modules\Program\Controllers\ProgramController')->fetch_another_eligibility((isset($existArr[$eligibility['id']]) ? $existArr[$eligibility['id']] : []), $programeligibilities, $eligibility['id']);
                        if(!empty($newdata))
                        {
                            $eligibilities[$key]['program_eligibility']=$newdata;
                            $existArr[$eligibility['id']][] = $newdata->assigned_eigibility_name;
                        }

                    }
            }
            
        }
        
        $setEligibility = SetEligibility::where('program_id',$id)->where("application_id", $application_id)->get()->keyBy('eligibility_id');
 //return $setEligibility;
        
        return view('SetEligibility::edit',compact('program','eligibilities','priorities','district','setEligibility', 'applications', 'application_id'));

    }

    public function extra_values_session(Request $request) {
        $data['program_id'] = $request['program_id'];
        $data['eligibility_id'] = $request['eligibility_id'];
        $data['application_id'] = $request['application_id'];
        $data['eligibility_type'] = $request['eligibility_type'];
        Session::put('eligibility_extval', $data);
        return redirect('admin/SetEligibility/extra_values/');
    }

    public function extra_values(Request $req)
    {
        $req = session('eligibility_extval');
        $table = 'seteligibility_extravalue';
        $application_id = $req['application_id'];
        $setEligibilitySingle  = SetEligibility::where("program_id",$req['program_id'])->where('eligibility_type',$req['eligibility_id'])->where("application_id", $application_id)->first();


        $eligibility= Eligibility::
           join('eligibility_content','eligibility_content.eligibility_id','=','eligibiility.id')
           ->where('eligibiility.id',$req['eligibility_id'])
           ->select('eligibiility.*','eligibility_content.content')
           ->first();
        
        $eligibilityTemplate=EligibilityTemplate::where('id',$eligibility->template_id)->first();
        // dd($eligibilityTemplate->content_html);
        $extraValue = DB::table($table)->where('program_id',$req['program_id'])->where('application_id',$req['application_id'])->where('eligibility_type',$req['eligibility_type'])->first();

        // dd($extraValue);
        if(isset($extraValue->id))
        {
            $extraValue = json_decode($extraValue->extra_values,1);
        }
        else
        {
            $extraValue = null;
        }
        
        return view('SetEligibility::editExtra',compact('eligibility','eligibilityTemplate','setEligibilitySingle','req','extraValue','application_id'));
    }

    /*public function extra_values(Request $req)
    {
        $table = 'seteligibility_extravalue';
        $application_id = $req['application_id'];
        $setEligibilitySingle  = SetEligibility::where("program_id",$req['program_id'])->where('eligibility_type',$req['eligibility_id'])->where("application_id", $application_id)->first();


        $eligibility= Eligibility::
           join('eligibility_content','eligibility_content.eligibility_id','=','eligibiility.id')
           ->where('eligibiility.id',$req['eligibility_id'])
           ->select('eligibiility.*','eligibility_content.content')
           ->first();
        
        $eligibilityTemplate=EligibilityTemplate::where('id',$eligibility->template_id)->first();
        // dd($eligibilityTemplate->content_html);
        $extraValue = DB::table($table)->where('program_id',$req['program_id'])->where('application_id',$req['application_id'])->where('eligibility_type',$req['eligibility_type'])->first();

        // dd($extraValue);
        if(isset($extraValue->id))
        {
            $extraValue = json_decode($extraValue->extra_values,1);
        }
        else
        {
            $extraValue = null;
        }
        
        return view('SetEligibility::editExtra',compact('eligibility','eligibilityTemplate','setEligibilitySingle','req','extraValue','application_id'));
    }*/

    public function extra_value_save(Request $req)
    {
        $table = 'seteligibility_extravalue';
        $application_id = $req['application_id'];

        // dd($req['value'], json_encode($req['value']));
        // return $req;
        if(isset($req['value']))
        {
            $insert = array(
                "program_id" => $req['program_id'],
                "application_id" => $req['application_id'],
                "eligibility_type" => $req["eligibility_type"],
                "extra_values" => json_encode($req['value'])
            );

            $checkExist = DB::table($table)->where('program_id',$req['program_id'])->where("application_id", $application_id)->where('eligibility_type',$req['eligibility_type'])->first();
            if(isset($checkExist->id))
            {
                $result = DB::table($table)->where('program_id',$req['program_id'])->where('eligibility_type',$req['eligibility_type'])->where('application_id', $application_id)->update(["extra_values"=>$insert["extra_values"]]);
            }
            else
            {
                $result = DB::table($table)->insert($insert);
            }
        
            if(isset($result))
            {
                Session::flash("success", "Data saved successfully.");
                // return "true";
            }
        }
        else
        {
            Session::flash("error", "Something went wrong, please try again.");
            // return "false";
        }
        if (!session('success')) {
            Session::flash("error", "Something went wrong, please try again.");
        }
        return redirect()->back();
    }

    /* Configuration */
    public function configurations_session(Request $request) {
        $data['program_id'] = $request['program_id'];
        $data['eligibility_id'] = $request['eligibility_id'];
        $data['application_id'] = $request['application_id'];
        $data['eligibility_type'] = $request['eligibility_type'];
        Session::put('eligibility_conf', $data);
        return redirect('admin/SetEligibility/configurations/');
    }

    public function configurations(Request $req)
    {
        $req = session('eligibility_conf');
        $application_id = $req['application_id'];
        $table = 'seteligibility_extravalue';
        $setEligibilitySingle  = SetEligibility::where("program_id",$req['program_id'])->where("application_id", $req['application_id'])->where('eligibility_type',$req['eligibility_id'])->first();
        

        $eligibility= Eligibility::
           join('eligibility_content','eligibility_content.eligibility_id','=','eligibiility.id')
           ->where('eligibiility.id',$req['eligibility_id'])
           ->select('eligibiility.*','eligibility_content.content')
           ->first();
        
        $eligibilityTemplate=EligibilityTemplate::where('id',$eligibility->template_id)->first();

        $extraValue = DB::table($table)->where("application_id", $req['application_id'])->where('program_id',$req['program_id'])->where('eligibility_type',$req['eligibility_type'])->first();


        $setEligibilitySingle = array();


        $eligibility= Eligibility::
           join('eligibility_content','eligibility_content.eligibility_id','=','eligibiility.id')
           ->where('eligibiility.id',$req['eligibility_id'])
           ->select('eligibiility.*','eligibility_content.content')
           ->first();
        
        $eligibilityTemplate=EligibilityTemplate::where('id',$eligibility->template_id)->first();

        if(isset($extraValue->id))
        {
            $extraValue = json_decode($extraValue->extra_values,1);
        }
        else
        {
            $extraValue = null;
        }
        
        return view('SetEligibility::editConfiguration',compact('eligibility','eligibilityTemplate','setEligibilitySingle','req','extraValue', 'application_id'));
        // return view("SetEligibility::editExtra");
    }

    public function configurations_save(Request $req)
    {
        //return $req;
        $data = $req->all();
        $program_id = $data['program_id'];
        $eligibility_id = $data['eligibility_id'];
        $district_id = Session::get("district_id");
        $application_id = $data['application_id'];
        $eligibility_type = $data['eligibility_type'];

        foreach($data as $key=>$value)
        {
            if(!in_array($key, array("_token", "program_id", "eligibility_id", "eligibility_type", "late_submission", "application_id")))
            {
                $insert = array();
                $insert['program_id'] = $program_id;
                $insert['eligibility_id'] = $eligibility_id;
                $insert['application_id'] = $application_id;
                $insert['district_id'] = $district_id;
                $insert['eligibility_type'] = $eligibility_type;
                $insert['configuration_type'] = $key;
                if(is_array($value))
                    $insert['configuration_value'] = implode(",", $value);
                else
                    $insert['configuration_value'] = $value;

                
                $rs = SetEligibilityConfiguration::updateOrCreate(["program_id"=>$program_id, "eligibility_id"=>$eligibility_id, "configuration_type"=>$key, "eligibility_type"=>$eligibility_type, "application_id"=>$application_id],$insert);
            }
        }
        Session::flash("success", "Data saved successfully.");
        return redirect()->back();
        // return redirect('admin/SetEligibility/edit/'.$program_id."/".$application_id);
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
        // return $request;
        $data = $request->except("_token");
        $application_id = $data['application_id'];
        $district_id = Session::get("district_id");
        
        if (isset($data['eligibility_type'])) {
            $newData = array();
            foreach ($data['eligibility_type'] as $k => $v)
            {
                $single['program_id'] = $id;
                $single['district_id'] = $district_id;
                $single['application_id'] = $application_id;
                $single['eligibility_type'] = $v;
                $single['eligibility_id'] = isset($data['eligibility_id'][$v]) ? $data['eligibility_id'][$v][0] : '';
                $single['required'] = isset($data['required'][$v]) ? $data['required'][$v][0] : '';
                $single['eligibility_value'] = isset($data['eligibility_value'][$v]) ? $data['eligibility_value'][$v][0] : '';
                $single['status'] = isset($data['status'][$v]) ? 'Y': 'N';
                $newData[] = $single;
                $checkExist = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();
                if(isset($checkExist->id))
                {
                    $initObj = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();
                    $result[] = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->update($single);
                    $newObj = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();

                    //$this->modelChanges($initObj,$newObj,"SetEligibility");
                }
                else
                {
                    $result[] = SetEligibility::create($single);
                }
            }
        }

        if (isset($data['eligibility_type'])) {
            $newData = array();
            foreach ($data['eligibility_type'] as $k => $v)
            {
                if(count($data['eligibility_id'][$v]) == 0)
                {
                    $single['program_id'] = $id;
                    $single['district_id'] = $district_id;
                    $single['application_id'] = $application_id;
                    $single['eligibility_type'] = $v;
                    $single['eligibility_id'] = isset($data['eligibility_id'][$v]) ? $data['eligibility_id'][$v][0] : '';
                    $single['required'] = isset($data['required'][$v]) ? $data['required'][$v][0] : '';
                    $single['eligibility_value'] = isset($data['eligibility_value'][$v]) ? $data['eligibility_value'][$v][0] : '';
                    $single['status'] = isset($data['status'][$v]) ? 'Y': 'N';
                    $newData[] = $single;
                    $checkExist = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();
                    if(isset($checkExist->id))
                    {
                        $initObj = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();
                        $result[] = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->update($single);
                        $newObj = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();

                        //$this->modelChanges($initObj,$newObj,"SetEligibility");
                    }
                    else
                    {
                        $result[] = SetEligibility::create($single);
                    }
                }
                else
                {
                    foreach($data['eligibility_id'][$v] as $ek=>$dv)
                    {
                        $single['program_id'] = $id;
                        $single['district_id'] = $district_id;
                        $single['application_id'] = $application_id;
                        $single['eligibility_type'] = $v;
                        $single['eligibility_id'] = $dv;
                        $single['required'] = isset($data['required'][$v]) ? $data['required'][$v][$ek] : '';
                        $single['eligibility_value'] = isset($data['eligibility_value'][$v]) ? $data['eligibility_value'][$v][$ek] : '';
                        $single['status'] = isset($data['status'][$v]) ? 'Y': 'N';
                        $newData[] = $single;
                        $checkExist = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();
                        if(isset($checkExist->id))
                        {
                            $initObj = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();
                            $result[] = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->update($single);
                            $newObj = SetEligibility::where('program_id',$id)->where('application_id',$application_id)->where("eligibility_id",$single['eligibility_id'])->first();

                            //$this->modelChanges($initObj,$newObj,"SetEligibility");
                        }
                        else
                        {
                            $result[] = SetEligibility::create($single);
                        }
                    }
                }

            }
        }

        //print_r($data);exit;
        // For late submission
        

        if (isset($result)) {
            Session::flash("success", "Data saved successfully.");
        } else {
            Session::flash("error", "Please Try Again.");
        }
        if (isset($request->save_exit))
        {
            return redirect('admin/SetEligibility');
        }
        else
        {
            return redirect('admin/SetEligibility/edit/'.$id."/".$application_id);
        }
        if (isset($request->save_edit))
        {
            return redirect('admin/SetEligibility/edit/'.$id."/".$application_id);
        }
        return redirect('admin/SetEligibility');
        // return $result;
        // return $newData;
        // return $data;
        // return $id;
        // return $request;
    }

    public function destroy($id)
    {
        //
    }
}
  