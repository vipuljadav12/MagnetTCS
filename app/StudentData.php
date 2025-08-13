<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class StudentData extends Model
{
    protected $table='student_data';
    public $additional = [];
    public $date_fields = [];
    public $primaryKey='id';
    public $createField  = ['stateID', 'enrollment_id','field_name', 'field_value'];
    public $fillable=[
         "stateID",
         "enrollment_id",
         "field_name",
         "field_value"
    ];
}