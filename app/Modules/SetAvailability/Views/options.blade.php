<div class="card shadow">
    <div class="card-header">{{$program->name}} for Current Enrollment</div>
    <input type="hidden" name="year" value="{{$enrollment->school_year ?? (date("Y")-1)."-".date("Y")}}">
	@php
		$grades = isset($program->grade_lavel) && !empty($program->grade_lavel) ? explode(',', $program->grade_lavel) : array();
        /*$schools = \App\Modules\School\Models\School::where('district_id', session('district_id'))
            ->where('status','Y');
        if (!empty($grades)) {
            $schools = $schools->whereRaw("find_in_set('3',grade_id)");
        }
        $schools = $schools->select('id','name')->get();*/
	@endphp
    <div class="card-body">
        <div class="table-responsive">
        	@forelse($grades as $g=>$grade)
                @if($loop->index !== 0) <br> @endif
                @php
                    $race_total = ($availabilities[$grade]->other_seats ?? 0) +
                        ($availabilities[$grade]->not_specified_seats ?? 0) +
                        ($availabilities[$grade]->black_seats ?? 0) +
                        ($availabilities[$grade]->white_seats ?? 0);
                    $schools = \App\Modules\School\Models\School::where('district_id', session('district_id'))
                        ->where('status','Y')
                        ->where('magnet', 'No')
                        ->whereRaw("find_in_set('".$grade."',grade_id)")
                        // ->select('id','name')
                        ->pluck('name')
                        ->toArray();
                    $fltr_schools_count = count($schools);
                    $loop_count = roundToNearesetMultiple($fltr_schools_count, 4);
                    $homezone_rowspan = ceil($fltr_schools_count / 4);
                    $stored_home_zone_data = isset($availabilities[$grade]['home_zone']) ? json_decode($availabilities[$grade]['home_zone'], 1) : [];
                    $zone_school_total = !empty($stored_home_zone_data) ? array_sum($stored_home_zone_data) : 0;
                    $field_unique_token = mt_rand().$loop->index;
                @endphp
                <table id="options_table" class="table mb-0">
                    <tbody>
                        <tr>
                            <td colspan="6" style="background-color: #eceeef;"> Rising Grade &nbsp; {{$grade}} 
                            <span id="error_{{ $field_unique_token }}" class="d-none" style="color: red;">Race & Home Zone total must be equal.</span>
                            </td>
                        </tr>
                        <!-- Race -->
                        <tr>
                            <td>Race</td>
                            <td>
                                Other <br>
                                <input type="text" class="form-control numbersOnly  race_field" data-total_field_id="{{$field_unique_token}}" data-id="{{$grade}}"  name="grades[{{$grade}}][other_seats]" value="{{$availabilities[$grade]->other_seats ?? ""}}"  @if($display_outcome > 0) disabled @endif maxlength="5">
                            </td>
                            <td>
                                Not Specified <br>
                                <input type="text" class="form-control numbersOnly race_field" data-total_field_id="{{$field_unique_token}}" data-id="{{$grade}}"  name="grades[{{$grade}}][not_specified_seats]" value="{{$availabilities[$grade]->not_specified_seats ?? ""}}"  @if($display_outcome > 0) disabled @endif maxlength="5">
                            </td>
                            <td>
                                Black <br>
                                <input type="text" class="form-control numbersOnly race_field" data-total_field_id="{{$field_unique_token}}" data-id="{{$grade}}"  name="grades[{{$grade}}][black_seats]" value="{{$availabilities[$grade]->black_seats ?? ""}}"  @if($display_outcome > 0) disabled @endif maxlength="5">
                            </td>
                            <td>
                                White <br>
                                <input type="text" class="form-control numbersOnly race_field" data-total_field_id="{{$field_unique_token}}" data-id="{{$grade}}"  name="grades[{{$grade}}][white_seats]" value="{{$availabilities[$grade]->white_seats ?? ""}}"  @if($display_outcome > 0) disabled @endif maxlength="5">
                            </td>
                            <td>
                                Total <br>
                                <span class="form-control" id="race_{{$field_unique_token}}">{{$race_total}}</span>
                            </td>
                        </tr>
                        <!-- Home Zone -->
                        @if($program->home_zone_school_needed == 'Y')
                        @for($i=0; $i<$loop_count; $i++)
                            @if($i == 0 || (($i % 4) == 0)) 
                                <tr>
                            @endif
                            @if($i == 0) 
                                <td rowspan="{{$homezone_rowspan}}">Home Zone</td>
                            @endif
                            <td>
                                @if(isset($schools[$i]))
                                    {{$schools[$i]}}
                                    <input type="text" class="form-control numbersOnly homezone_field" data-total_field_id="{{$field_unique_token}}" data-id="{{$grade}}"  name="grades[{{$grade}}][home_zone][{{getSchoolName($schools[$i])}}]" value="{{$stored_home_zone_data[getSchoolName($schools[$i])] ?? ""}}"  @if($display_outcome > 0) disabled  @endif maxlength="5">
                                @else
                                    &nbsp;
                                @endif
                            </td>
                            @if($i == 3) 
                                <td rowspan="{{$homezone_rowspan}}">Total <br> <span class="form-control" id="homezone_{{$field_unique_token}}">{{$zone_school_total}}</span></td>
                            @endif
                            @if(($i == ($loop_count-1)) || ((($i+1) % 4) == 0)) 
                                </tr> 
                            @endif
                        @endfor
                        @endif
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="5" style="background-color: #eceeef;">
                                <div class="input-group">
                                    <span class="mt-1">Grade {{$grade}} Capacity &nbsp;</span>
                                    <input type="text" class="form-control numbersOnly totalSeat"  name="grades[{{$grade}}][total_seats]" value="{{$availabilities[$grade]->total_seats ?? ""}}" data-id="{{$grade}}" @if($display_outcome > 0) disabled @endif>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @empty
                <tr>
                 	<td class="text-center">No Grades</td>
                </tr>
            @endforelse
        </div>
        <div class="text-right"> 
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs" title="Save" id="optionSubmit"><i class="fa fa-save"></i> Save </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>