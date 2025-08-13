<?php

namespace App\Modules\Import\Controllers;

use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Import\Rule\ExcelRule;
use App\Modules\Import\ImportFiles\GiftedStudentsImport;
use App\Modules\Import\ImportFiles\AgtNewCenturyImport;
use Maatwebsite\Excel\HeadingRowImport;
use App\StudentData;
use App\Modules\Program\Models\Program;
use App\Modules\Import\Models\AgtToNch;
use App\Modules\Import\ExportFiles\TestScoreSample;
use App\Modules\Import\ExportFiles\ImportTestScoreErrorExport;
use App\Modules\Import\ImportFiles\TestScore;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    use AuditTrail;

    public function importGiftedStudents()
    {
        return view('Import::import_gifted_students');
    }

    public function saveGiftedStudents(Request $request)
    {
        $rules = [
            'upload_csv' => ['required', new ExcelRule($request->file('upload_csv'))],
        ];
        $message = [
            'upload_csv.required' => 'File is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', 'Please select proper file');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $rs = StudentData::where("field_name", "like", "%gifted%")->delete();

            $file = $request->file('upload_csv');
            $headings = (new HeadingRowImport)->toArray($file);
            $excelHeader = $headings[0][0];
            $fixheader = ['currentenrollmentstatus', 'stateidnumber', 'lastname', 'firstname', 'gr', 'school', 'primaryexceptionality', 'casemanager', 'specialeducationstatus', 'enrichmentstudent', ''];
            $fixheader1 = ['currentenrollmentstatus', 'stateidnumber', 'lastname', 'firstname', 'gr', 'school', 'primaryexceptionality', 'casemanager', 'specialeducationstatus', 'enrichmentstudent'];

            if (!(CheckExcelHeader($excelHeader, $fixheader)) && !(CheckExcelHeader($excelHeader, $fixheader1))) {
                Session::flash('error', 'Please select proper file | File header is improper');
                return redirect()->back();
            }

            $import = new GiftedStudentsImport;
            $import->import($file);
            Session::flash('success', 'Gifted Students Imported successfully');
        }
        return  redirect()->back();
    }

    public function importAGTNewCentury()
    {
        $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        return view('Import::import_agt_nch', compact("programs"));
    }

    public function storeImportAGTNewCentury(Request $request)
    {
        $rules = [
            'program_name' => ['required'],
            'upload_agt_nch' => ['required', new ExcelRule($request->file('upload_agt_nch'))],
        ];
        $message = [
            'program_name.required' => 'Program is required',
            'upload_agt_nch.required' => 'File is required',
        ];
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            Session::flash('error', 'Something wrong. Please check all fields.');
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $file = $request->file('upload_agt_nch');
            $headings = (new HeadingRowImport)->toArray($file);
            $excelHeader = array_filter($headings[0][0]);

            $fixheader = ['student_id', 'grade_level', 'name'];

            if (!(CheckExcelHeader($excelHeader, $fixheader))) {
                Session::flash('error', 'Please select proper file | File header is improper');
                return redirect()->back();
            }

            $import = new AgtNewCenturyImport;
            $import->program_name = $request->program_name;
            $import->import($file);
            Session::flash('success', 'AGT priority to New Century Imported successfully');
        }
        return  redirect()->back();
    }

    public function testScores()
    {
        $programs = Program::where('status', '!=', 'T')->where('district_id', Session::get('district_id'))->where('enrollment_id', Session::get('enrollment_id'))->get();
        return view('Import::test_scores', compact("programs"));
    }

    public function sampleTestScores()
    {
        $rs = Program::join("seteligibility_extravalue", "seteligibility_extravalue.program_id", "program.id")->where("program.enrollment_id", Session::get("enrollment_id"))->where("seteligibility_extravalue.eligibility_type", 12)->get();
        $columns = [];
        $columns[] = "SSID";
        $columns[] = "First Name";
        $columns[] = "Last Name";
        $columns[] = "Grade";

        $tmp = [];
        foreach ($rs as $k => $v) {

            if (!empty($v->extra_values)) {
                $content = json_decode($v->extra_values);

                if (isset($content->ts_scores)) {
                    foreach ($content->ts_scores as $k1 => $v1) {
                        if (!in_array($v1, $tmp)) {
                            $columns[] = $v1;
                            $tmp[] = $v1;
                        }
                    }
                }
            }
        }
        //dd($columns);
        return Excel::download(new TestScoreSample(collect(['data' => collect($columns)])), 'TestScoreSample.xlsx');
    }

    public function storeTestScores(Request $request)
    {
        Validator::extend('validate_file', function ($attribute, $value, $parameters, $validator) use ($request) {
            return in_array($request->file($attribute)->getClientOriginalExtension(), $parameters);
        });
        $max_mb = 10; // max file limit
        $max_limit = ($max_mb * 1024); // in Bytes
        $rules = [
            'file' =>  [
                'required',
                'validate_file:xlsx,xls',
                'max:' . $max_limit
            ]
        ];
        $messages = [
            'file.required' => 'File is required.',
            'file.max' => 'File may not be greater than 10 MB.',
            'file.validate_file' => 'The file must be a file of type: xls, xlsx.'
        ];
        $this->validate($request, $rules, $messages);
        $import = new TestScore();
        $import->import(request()->file('file'));
        //dd($import->errors());
        if (!empty($import->errors())) {
            $data['data'] = collect($import->errors());
            return Excel::download(new ImportTestScoreErrorExport($data), 'TestScores_Import_Error.xlsx');
        }
        Session::flash('success', 'Data imported successfully.');
        return redirect('admin/import/test_scores');
    }
}
