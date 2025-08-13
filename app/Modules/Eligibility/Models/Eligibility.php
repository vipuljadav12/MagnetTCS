<?php

namespace App\Modules\Eligibility\Models;

use Illuminate\Database\Eloquent\Model;

class Eligibility extends Model
{
  protected $table = 'eligibiility';
  public $primaryKey = 'id';
  public $fillable = [
    'enrollment_id',
    'template_id',
    'name',
    'type',
    'alias_name',
    'district_id',
    'store_for',
    'override',
    'status'
    // 'created_at',
    // 'updated_at',
  ];
}
