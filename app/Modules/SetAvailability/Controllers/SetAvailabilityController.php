<?php

namespace App\Modules\SetAvailability\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Program\Models\Program;
use App\Modules\SetAvailability\Models\Availability;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Submissions\Models\SubmissionsStatusUniqueLog;
use Session;

class SetAvailabilityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $af = ['applicant_filter1', 'applicant_filter2', 'applicant_filter3'];
        if(Session::get("district_id") != '0'){
            $programs=Program::where('status','!=','T')->where('district_id', Session::get('district_id'))->get();
        }
        else
            $programs=Program::where('status','!=','T')->get();

        // Application Filters
        $af_programs = [];
        if (!empty($programs)) {
            foreach ($programs as $key => $program) {
                if($program->applicant_filter1 == '' && $program->applicant_filter1 == '' && $program->applicant_filter3 == '' )
                {
                    array_push($af_programs, $program->name);
                }
                else
                {
                    foreach ($af as $key => $af_field) {
                        if (($program->$af_field != '') && !in_array($program->$af_field, $af_programs)) {
                            array_push($af_programs, $program->$af_field);
                        }
                    }
                }
            }
        }
        // return $af_programs;

        // return $programs;
        return view("SetAvailability::index",compact("af_programs"));
        // return view("SetAvailability::index",compact("programs", "af_programs"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOptionsByProgram(Program $program)
    {
        $display_outcome = SubmissionsStatusUniqueLog::where("enrollment_id", Session::get('enrollment_id'))->count();
       // echo $display_outcome;exit;

        // $display_outcome = 0;

        $availabilities =  Availability::where("program_id",$program->id)->where('district_id',$program->district_id)->get()->keyBy('grade');
        $enrollment = Enrollment::where('status','Y')->where("district_id",$program->district_id)
                ->get()->last();
        // return $availabilities;
        return view("SetAvailability::options",compact("program","availabilities","enrollment","display_outcome"));
    }   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isset($request['grades']) && !empty($request['grades']))
        {
            foreach($request['grades'] as $g => $grade) 
            {   
                if(isset($request["grades"][$g]['total_seats'])){ 
                    // Home Zone
                    $home_zone_data = [];
                    $total = 0;
                    if (isset($grade['home_zone']) && !empty($grade['home_zone'])) {
                        foreach ($grade['home_zone'] as $school_name => $zsvalue) {
                            $home_zone_data[$school_name] = (int)($request['grades'][$g]['home_zone'][$school_name] ?? 0);
                            $total += $home_zone_data[$school_name];
                        }
                    }

                    $ins = array();
                    $ins['program_id'] = $request['program_id'];
                    $ins['grade'] = $g;
                    $ins['district_id'] = Session::get("district_id");
                    $gs_total = $ins['black_seats'] = $request["grades"][$g]['black_seats'] ?? 0;
                    $gs_total += $ins['white_seats'] = $request["grades"][$g]['white_seats'] ?? 0;
                    $gs_total += $ins['other_seats'] = $request["grades"][$g]['other_seats'] ?? 0;
                    $gs_total += $ins['not_specified_seats'] = $request["grades"][$g]['not_specified_seats'] ?? 0;
                    $ins['total_seats'] = $request["grades"][$g]['total_seats'] ?? 0;
                    $ins['available_seats'] = $ins['total_seats'] - $total;
                    $ins['year'] = $request["year"];
                    $ins['enrollment_id'] = Session::get("enrollment_id");
                    $ins['home_zone'] = !empty($home_zone_data) ? json_encode($home_zone_data) : NULL;
                     //return $home_zone_data;
                    // $newData[] = $ins;
                   // dd($ins);
                    $exist = Availability::where("program_id",$ins['program_id'])->where('district_id',$ins['district_id'])->where("grade",$ins['grade'])->where("enrollment_id",Session::get('enrollment_id'))->first();
                    if(isset($exist->id))
                    {
                       // dd($ins);
                        $exist->available_seats = $ins['available_seats'];
                        $exist->black_seats = $ins['black_seats'];
                        $exist->white_seats = $ins['white_seats'];
                        $exist->other_seats = $ins['other_seats'];
                        $exist->not_specified_seats = $ins['not_specified_seats'];
                        $exist->total_seats = $ins['total_seats'];
                        $exist->home_zone = $ins['home_zone'];
                        $result[] = $exist->save();
                    }
                    else
                    {
                        //dd($ins);
                        $result[] = Availability::create($ins);
                    }
                }
            }
            //exit;
        }

        if(isset($result) && count($result) > 0)
        {
            Session::flash("success","Availability saved successfully");
        }
        else
        {
            Session::flash("warning","Something went wrong, Please try again.");
        }
        return redirect('admin/Availability');
        // return $newData;

    }

    
    public function getPrograms(Request $request)
    {
        $af = [
            'application_filter_1' => 'applicant_filter1', 
            'application_filter_2' => 'applicant_filter2', 
            'application_filter_3' => 'applicant_filter3'
        ]; 
        /*$seat_type = [
            'black_seats' => 'Black', 
            'white_seats' => 'White',
            'other_seats' => 'Other'
        ];*/

        $req_filter = $request->application_filter;
        $programs = Program::where('status','!=','T')->where("enrollment_id", Session::get("enrollment_id"));
        if(Session::get("district_id") != '0'){
            $programs = $programs->where('district_id', Session::get('district_id'));
        }
        if ($req_filter == '') {
            // return all programs
            $data = [ 'data' => $programs->get() ];
        } else {
            $programs = $programs->where(function($q) use ($req_filter) {
                $q->where('applicant_filter1', $req_filter);
                $q->orWhere('applicant_filter2', $req_filter);
                $q->orWhere('applicant_filter3', $req_filter);
                $q->orWhere('name', $req_filter);
            })->get();
            
            // Filter by application filter
            $filtered_programs = [];
            $avg_data = [];
            if (!empty($programs)) {
                $programs_avg = [];
                foreach ($programs as $key => $program) {
                    if($program->selection_by == "Program Name")
                        $selection_by = "name";
                    else
                        $selection_by = strtolower(str_replace(' ', '_', $program->selection_by));

                    if($selection_by == "name" && $program->{$selection_by} == $req_filter)
                    {
                        $filtered_programs[] = $program;
                        array_push($programs_avg, $program->id);
                    }
                    elseif (
                        isset($af[$selection_by]) &&
                        ($program->{$af[$selection_by]} != '') && 
                        $program->{$af[$selection_by]} == $req_filter) 
                    {
                        $filtered_programs[] = $program;
                        array_push($programs_avg, $program->id);
                    }
                }

                // avg availability
                /*if (!empty($programs_avg)) {
                    $total = 0;
                    $availabilities =  Availability::whereIn("program_id",$programs_avg)->where('district_id',Session('district_id'))->get(array_keys($seat_type));

                    foreach ($seat_type as $stype => $svalue) {
                        $sum = $availabilities->sum($stype);
                        $total += $sum;
                        $avg_data['data'][$svalue] = $sum;
                    }
                    $avg_data['total'] = $total;
                }*/
            }
            $data = [
                'data' => $filtered_programs,
                'avg_data' => $avg_data
            ];
        }
        return json_encode($data);
    }

}
