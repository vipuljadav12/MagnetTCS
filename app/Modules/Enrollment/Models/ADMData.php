<?php

namespace App\Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Model;

class ADMData extends Model {

    protected $table='adm_data';
    protected $primaryKey='id';

    public $fillable = [
    	'enrollment_id', 
    	'school_id',
    	'black',
    	'white',
    	'other'
    ];

}
