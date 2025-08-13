<?php

namespace App\Modules\StudentProfileEligibility\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfileEligibility extends Model {
	protected $table = 'student_profile_eligibility';
	protected $primaryKey='id';
    
    public $fillable = [
    	'name',
    	'program_id', 
    	'application_id',
    	'eligibility_id',
    	'grade',
    	'recommendation_form',
    	'test_scores',
    	'academic_grades',
    	'conduct_discpline_criteria',
    	'recommendation_form_data',
    	'test_scores_data',
    	'academic_grades_data',
    	'conduct_discpline_criteria_data',
    ];

}
