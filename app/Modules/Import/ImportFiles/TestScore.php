<?php
namespace App\Modules\Import\ImportFiles;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Session;
use App\Modules\Submissions\Models\Submissions;
use App\Modules\Eligibility\Models\EligibilityTemplate;
use App\Modules\Submissions\Models\SubmissionTestScore;
use DB;
use App\StudentData;

class TestScore implements ToCollection,WithBatchInserts,WithHeadingRow,SkipsOnFailure
{
  use Importable, SkipsFailures;

  protected $allErrors=[];

  // public function 

  public function collection(Collection $rows)
  {
    foreach ($rows as $row) 
    {
      $errors=[];

      if($row['ssid'] != '')
      {
          $data = [];
          $data['ssid'] = $row['ssid'];
          unset($row['ssid']);
          unset($row['first_name']);
          unset($row['last_name']);
          unset($row['grade']);
          foreach ($row as $sub => $test_score_value) {
            if (($sub == '') || ($test_score_value == '')) {
              continue;
            }
            $tmp = [];
            $tmp['stateID'] = $data['ssid'];
            $tmp['field_name'] = $sub;
            $tmp['field_value'] = $test_score_value;
            $tmp['enrollment_id'] = Session::get('enrollment_id');
            $rp = StudentData::updateOrCreate(["stateID" => $tmp['stateID'], "field_name" => $tmp['field_name'], "enrollment_id"=>Session::get('enrollment_id')], $tmp);

            $submission = Submissions::where('student_id', $tmp['stateID'])->whereIn("submission_status", array('Active', 'Pending'))->first();
            if(!empty($submission))
            {
                $first_choice = $this->storeData($submission, $sub, $test_score_value, 'first');
                if (($submission->second_choice_program_id != '') && !$first_choice) {
                  $second_choice = $this->storeData($submission, $sub, $test_score_value, 'second');
                }

            }
          }


      }
      
      
    }
  }

  public function storeData($submission, $sub, $test_score_value, $choice='') {
    $ts_extra = getTestScores($submission, $choice)['fields'];
    if (isset($ts_extra)) {
      $test_score_name = '';
      // Match subject name(short_name) with excel file name
      foreach ($ts_extra as $tseKey => $tseValue) {
        if (strtolower(str_replace("_", " ", $sub)) == strtolower($tseKey)) {
          $test_score_name = $tseKey;
          break;
        } elseif (
          isset($tseValue['short_name']) && 
          (strtolower($sub) == strtolower($tseValue['short_name']))
        ) {
          $test_score_name = $tseKey;
          break;
        }
      }
      // Store subject scores
      if ($test_score_name != '') {
        $key_data = [
          'submission_id' => $submission->id,
          'program_id' => $submission->{$choice.'_choice_program_id'},
          'test_score_name' => $test_score_name
        ];
        $data = [
          'test_score_value' => $test_score_value
        ];
        SubmissionTestScore::updateOrCreate($key_data, $data);
      } else {
        return false;
      }
    }
    return true;
  } 

  public function batchSize(): int
  {
    return 1;
  }
  public function errors()
  {
      return $this->allErrors;
  }
}
