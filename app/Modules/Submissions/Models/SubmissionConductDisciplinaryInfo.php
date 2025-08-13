<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionConductDisciplinaryInfo extends Model {

    public $timestamps = false;
    protected $table='submission_conduct_discplinary_info';
    public $traitField = "submission_id";
    public $additional = ['enrollment_id', 'application_id'];

    public $primaryKey='id';
    public $fillable=[
    	'submission_id',
    	'stateID',
    	'incidence_title',
    	'incidence_description',
    	'datetime',
    	'incidence_location',
    	'Incident_Detail_Lookup_Code_Desc',
    	'Incident_LU_Code_Type',
        'infraction_code',
        'LU_Code_State_Aggregate_Rpt_Code',
        'actionname',
        'startdate',
        'combined_data',
        'enddate'
    ];
}
