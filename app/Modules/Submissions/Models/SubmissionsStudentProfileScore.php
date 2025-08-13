<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsStudentProfileScore extends Model {

    //
    protected $table='submissions_student_profile_score';
    public $traitField = "submission_id";
    public $additional = [];
    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'student_profile_score'
    ];

}
