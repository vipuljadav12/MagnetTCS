<?php

namespace App\Modules\AddressOverride\Models;

use Illuminate\Database\Eloquent\Model;

class AddressOverride extends Model
{
	protected $table = 'address_override';
	protected $primaryKey = 'id';

	public $fillable = [
		'state_id',
		'user_id',
		'district_id',
		'zoned_school',
	];
}
