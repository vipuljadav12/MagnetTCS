<?php

namespace App\Modules\Priority\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model {
	protected $table = 'priorities';
	protected $primaryKey='id';
    
    public $fillable = [
    	'name', 
    	'district_id',
    	'enrollment_id',
    	'status',
    ];

}
