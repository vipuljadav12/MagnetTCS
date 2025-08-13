<?php

namespace App\Modules\Eligibility\Models;

use Illuminate\Database\Eloquent\Model;

class EligibilityTemplate extends Model 
{
  protected $table='eligibility_template';
  protected $primaryKey='id';
  protected $fillable=[
  	'name',
  	'type',
    'max_count',
  	'district_id',
    'max_count',
  	'status'
  ];
}