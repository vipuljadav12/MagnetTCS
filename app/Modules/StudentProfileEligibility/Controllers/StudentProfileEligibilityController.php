<?php

namespace App\Modules\StudentProfileEligibility\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Program\Models\Program;
use App\Modules\StudentProfileEligibility\Models\StudentProfileEligibility;
use DB;
use Session;
use App\Modules\Eligibility\Models\Eligibility;
use App\Modules\Eligibility\Models\EligibilityContent;

class StudentProfileEligibilityController extends Controller
{
    protected $module_url = 'admin/StudentProfileEligibility';

    public function __construct(){
        // 
    }

    public function setData(Request $request) {
        $data['program_id'] = $request['program_id'];
        $data['eligibility_id'] = $request['eligibility_id'];
        $data['application_id'] = $request['application_id'];
        $data['template_id'] = $request['template_id'];
        Session::put('sp_eligibility', $data);
        return redirect($this->module_url);
    }

    public function index()
    {  
        if (!session('sp_eligibility')) {
            abort(404);
        }
        $data = session('sp_eligibility');
        $data['program'] = Program::where('id', $data['program_id'])->first();
        $data['sp_eligibilities'] = StudentProfileEligibility::where('program_id', $data['program_id'])
            ->where('application_id', $data['application_id'])
            ->where('eligibility_id', $data['eligibility_id'])
            ->get();
        return view('StudentProfileEligibility::index', compact('data'))->with('module_url', $this->module_url);
    }

    public function createEdit($id=0)
    {
        $data = session('sp_eligibility');
        $data += $this->getTestScores($data['program_id'], $data['application_id']);
        $data['id'] = $id;
        $data['program'] = Program::where('id', $data['program_id'])->first();
        if ($id !== 0) {
            $data['eligibility'] = StudentProfileEligibility::where('id', $id)->first();
        }
        // Test Score
        // $data['extraValue'] = json_decode($data['extraValue'], 1);
        $data['test_score_range'] = isset($data['eligibility']->test_scores_data) ? json_decode($data['eligibility']->test_scores_data, 1) : [];

        $ag_tmp_id = getEligibilityTemplateID('Academic Grades');

        $ag_eligibility = Eligibility::where('template_id', $ag_tmp_id)
            ->join("program_eligibility", "program_eligibility.eligibility_type", "eligibiility.template_id")
            ->where("program_eligibility.program_id", $data['program_id'])
            ->where('district_id', session('district_id'))
            ->where('enrollment_id', session('enrollment_id'))
            ->select("eligibiility.*")
            ->first();

            
        if (isset($ag_eligibility)) {
            $ag_eligibility_content = json_decode(EligibilityContent::where('eligibility_id', $ag_eligibility->id)->first()->content, 1);
                        // print_r($ag_eligibility_content);

        }
        $data['eligibility_subjects'] = $ag_eligibility_content['subjects'] ?? [];
        /*$selected_subjects = $ag_eligibility_content['subjects'] ?? [];
        $data['ag_avl_eligibility_subjects'] = [];
        $subjects_ary = config('variables.ag_eligibility_subjects');
        foreach ($selected_subjects as $subject_short_value) {
            array_push($data['ag_avl_eligibility_subjects'], $subjects_ary[$subject_short_value]);
        }*/

        return view('StudentProfileEligibility::createEdit', compact('data'))->with('module_url', $this->module_url);
    }

    /*public function testScore(Request $request, $id=0) {
        $data = $this->getTestScores($request->program_id, $request->application_id);
        $data['extraValue'] = json_decode($data['extraValue'], 1);
        $stored_eligibility = StudentProfileEligibility::where('id', $id)->first();
        $data['storedExtraValue'] = isset($stored_eligibility->test_scores_data) ? json_decode($stored_eligibility->test_scores_data, 1) : [];
        return view('StudentProfileEligibility::section.test_score', compact('data'));
    }*/

    public function getTestScores($program_id, $application_id) {

        $eligibility = getEligibilitiesDynamicNew($program_id, $application_id, 'Test Score')[0] ?? [];
        $extra_values = DB::table('seteligibility_extravalue')->where('program_id', $program_id)->where('application_id', $application_id)->where('eligibility_type', $eligibility->eligibility_type)->first()->extra_values ?? '';
        $data['test_scores'] = json_decode($extra_values, 1);
        return $data;
    }

    public function store(Request $request, $id=0)
    {
        $rules['grade_lavel'] = ($id == 0) ? 'required' : '';
        $messages = [
            'grade_lavel.required' => 'Grade Level is required.'
        ];
        $this->validate($request, $rules, $messages);
        $sp_data = session('sp_eligibility');
        $data['name'] = $request->name;
        $data['recommendation_form'] = $request->has('recommendation_form') ? 'Y' : 'N';
        $data['test_scores'] = $request->has('test_scores') ? 'Y' : 'N';
        $data['academic_grades'] = $request->has('academic_grades') ? 'Y' : 'N';
        $data['conduct_discpline_criteria'] = $request->has('conduct_discpline_criteria') ? 'Y' : 'N';
        $data['test_scores_data'] = ($data['test_scores'] == 'Y') ? json_encode($request->ts_value) : NULL;
        $data['academic_grades_data'] = ($data['academic_grades'] == 'Y') ? json_encode($request->academic_grades_data) : NULL;
        $data['incident_consider'] = (($data['conduct_discpline_criteria'] == 'Y') && isset($request['conduct_discpline']['incident_consider'])) ? $request['conduct_discpline']['incident_consider'] : NULL;
        if ($id == 0) {
            $grades = $request->grade_lavel;
            $key_data = [
                'program_id' => $sp_data['program_id'],
                'application_id' => $sp_data['application_id'],
                'eligibility_id' => $sp_data['eligibility_id'],
                // 'grade' => $data['grades'],
            ];
            foreach ($grades as $grdidx => $grade) {
                $key_data['grade'] = $grade;
                StudentProfileEligibility::updateOrCreate($key_data, $data);
            }
        } else {
            $key_data['id'] = $id;
            StudentProfileEligibility::where('id', $id)->update($data);
        }
        // $eligibility = StudentProfileEligibility::updateOrCreate($key_data, $data);
        session()->flash('success', 'Student Profile Eligibility saved successfully.');
        if ($request->has('save_exit')) {
            return redirect($this->module_url);
        }
        if ($id != 0) {
            return redirect($this->module_url.'/edit/'.$id);
        }
        return redirect($this->module_url.'/create');
    }

    public function delete($id)
    {   
        StudentProfileEligibility::where('id', $id)->delete();
        session()->flash('success', "Student Profile Eligibility deleted successfully."); 
        return redirect($this->module_url);
    }

    /*public function validateGrades(Request $request) {
        $sp_data = session('sp_eligibility');
        $data['sp_eligibilities'] = StudentProfileEligibility::where('program_id', $sp_data['program_id'])
            ->where('application_id', $sp_data['application_id'])
            ->where('eligibility_id', $sp_data['eligibility_id'])
            ->where('grades', $request->selected_grades)
            ->first();
        if (isset($data['sp_eligibilities'])) {
            return json_encode(false);
        }
        return json_encode(true);
    }*/
}
