<?php

namespace App\Modules\SetAvailability\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model {

    protected $table = "availability";
    protected $fillable = [
    	"program_id",
    	"district_id",
        "enrollment_id",
    	"grade",
    	"available_seats",
    	"black_seats",
    	"white_seats",
    	"other_seats",
    	"not_specified_seats",
    	"total_seats",
    	"year",
        "home_zone"
    ];
}
