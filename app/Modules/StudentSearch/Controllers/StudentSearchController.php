<?php

namespace App\Modules\StudentSearch\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\Validator;
use DB;
class StudentSearchController extends Controller
{
    protected $module_url = 'admin/StudentSearch';
    public function index()
    {
        return view('StudentSearch::index')->with('module_url', $this->module_url);
    }

    public function data(Request $request)
    {
        $id = $request->id;
        $student = Student::where('stateID', $id)->first();
        if(!empty($student))
        {
            $data['student'] = $student;

            $termData = DB::table("ps_cc")->where('studentid',  $student->student_id)->distinct()->get(['termid']);
            $termIds = [33];
            //  foreach($termData as $termid)
            // {
            //     $tid = number_format(floor((str_replace("-", "", $termid->termid)/100)), 0);
            //     if(!in_array($tid, $termIds))
            //     {
            //         $termIds[] = $tid;
            //     }

            // }

            $homeroomData = [];
            foreach($termIds as $termid)
            {
                /* For Home Room Teacher */
                $homeData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.ps_id', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.ps_id', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_cc.course_number', 'like', '22991%')
                                              ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($homeData))
                {
                    $homeroomData[$termid] = $homeData;
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
                        $homeroomData[$termid] = $homeData;
                    }
                }

            }
            
            $mathTeacherData = $engTeacherData = [];
            foreach($termIds as $termid)
            {
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
                    $mathTeacherData[$termid] = $mathData;
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
                        $mathTeacherData[$termid] = $mathData;
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
                    $engTeacherData[$termid] = $engData;
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
                        $engTeacherData[$termid] = $engData;
                    }
                }
            }
        }
        $data['student'] = $student;

      //  dd($homeroomData);

        return view('StudentSearch::data', compact('data', 'engTeacherData', 'mathTeacherData', 'homeroomData', 'termIds'))->with('module_url', $this->module_url);
    }

    public function data1($id)
    {
        $student = Student::where('stateID', $id)->first();
        if(!empty($student))
        {
            $data['student'] = $student;
            $termIds = [33];
            // $termData = DB::table("ps_cc")->where('studentid',  $student->dcid)->distinct()->get(['termid']);
            // $termIds = [];
            //  foreach($termData as $termid)
            // {
            //     $tid = number_format(floor((str_replace("-", "", $termid->termid)/100)), 0);
            //     if(!in_array($tid, $termIds))
            //     {
            //         $termIds[] = $tid;
            //     }

            // }
           // $termIds[] = 33;

            $homeroomData = [];
            foreach($termIds as $termid)
            {
                /* For Home Room Teacher */
                $homeData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.dcid', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_cc.course_number', 'like', '22991%')
                                              ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($homeData))
                {
                    $homeroomData[$termid] = $homeData;
                }
                else
                {
                    $homeData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                              ->join('ps_schoolstaff', 'ps_schoolstaff.dcid', 'ps_sections.teacher')
                                              ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                              ->where("ps_cc.studentid", $student->student_id)
                                              ->where("ps_cc.termid", 'like', $termid.'%')
                                              ->where('ps_sections.external_expression', 'P4(A)')
                                              ->first(['email_addr', 'first_name', 'last_name']);
                    if(!empty($homeData))
                    {
                        $homeroomData[$termid] = $homeData;
                    }
                }

            }

            $mathTeacherData = $engTeacherData = [];
            foreach($termIds as $termid)
            {
                /* Maths teacher data */
                $mathData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                                  ->join('ps_schoolstaff', 'ps_schoolstaff.dcid', 'ps_sections.teacher')
                                                  ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                                  ->where("ps_cc.studentid", $student->student_id)
                                                  ->where("ps_cc.termid", 'like', $termid.'%')
                                                  ->where('ps_sections.course_number', 'like', '02%')
                                                  ->where('ps_sections.grade_level', '!=', '')
                                                  ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($mathData))
                {
                    $mathTeacherData[$termid] = $mathData;
                }
                else
                {
                    $mathData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                                  ->join('ps_schoolstaff', 'ps_schoolstaff.dcid', 'ps_sections.teacher')
                                                  ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                                  ->where("ps_cc.studentid", $student->dcid)
                                                  ->where("ps_cc.termid", 'like', $termid.'%')
                                                  ->where('ps_sections.course_number', 'like', '02%')
                                                  ->first(['email_addr', 'first_name', 'last_name']);
                    if(!empty($mathData))
                    {
                        $mathTeacherData[$termid] = $mathData;
                    }
                }

                $engData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                                  ->join('ps_schoolstaff', 'ps_schoolstaff.dcid', 'ps_sections.teacher')
                                                  ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                                  ->where("ps_cc.studentid", $student->student_id)
                                                  ->where("ps_cc.termid", 'like', $termid.'%')
                                                  ->where('ps_sections.course_number', 'like', '01%')
                                                  ->where('ps_sections.grade_level', '!=', '')
                                                  ->first(['email_addr', 'first_name', 'last_name']);
                if(!empty($engData))
                {
                    $engTeacherData[$termid] = $engData;
                }
                else
                {
                    $engData = DB::table("ps_cc")->join('ps_sections', 'ps_sections.dcid', 'ps_cc.sectionid')
                                                  ->join('ps_schoolstaff', 'ps_schoolstaff.dcid', 'ps_sections.teacher')
                                                  ->join('ps_users', 'ps_users.dcid', 'ps_schoolstaff.users_dcid')
                                                  ->where("ps_cc.studentid", $student->student_id)
                                                  ->where("ps_cc.termid", 'like', $termid.'%')
                                                  ->where('ps_sections.course_number', 'like', '01%')
                                                  ->first(['email_addr', 'first_name', 'last_name']);
                    if(!empty($engData))
                    {
                        $engTeacherData[$termid] = $engData;
                    }
                }
            }
        }
        $data['student'] = $student;

      //  dd($homeroomData);

        return view('StudentSearch::data', compact('data', 'engTeacherData', 'mathTeacherData', 'homeroomData', 'termIds'))->with('module_url', $this->module_url);
    }

    public function updateData(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'current_grade' => 'required',
            'birthday' => 'required|date',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'race' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return 'false';
        }
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'current_grade' => $request->current_grade,
            'birthday' => date('Y-m-d', strtotime($request->birthday)),
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'race' => $request->race,
        ];
        $id = $request->id;
        $update = Student::where('stateID', $id)->update($data);
        return $update ? 'true' : 'false';
    }        
}
