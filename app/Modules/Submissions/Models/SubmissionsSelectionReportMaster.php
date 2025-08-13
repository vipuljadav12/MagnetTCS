<?php

namespace App\Modules\Submissions\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionsSelectionReportMaster extends Model {

    //
    protected $table='submissions_select_master_report';
    public $primaryKey='id';
    public $fillable=[
        'application_id',
    	'program_name',
        'school_home_zone',
        'enrollment_id',
        'type',
        'version',
        'rising_population_homezone',
    	'calculated_target_slot',
    	'starting_population',
    	'starting_percent',
    	'offered',
        'offered_percent',
        'type'
    ];

}
