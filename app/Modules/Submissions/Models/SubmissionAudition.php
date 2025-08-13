<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionAudition extends Model {

    //
    protected $table='submissionaudition';
    public $traitField = "submission_id";
    public $additional = ['enrollment_id', 'application_id'];
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'data'
    ];

}
