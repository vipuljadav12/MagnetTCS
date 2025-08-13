<div class="card shadow">
    <div class="card-header"><?php echo e($program->name); ?> for Current Enrollment</div>
    <input type="hidden" name="year" value="<?php echo e($enrollment->school_year ?? (date("Y")-1)."-".date("Y")); ?>">
	<?php
		$grades = isset($program->grade_lavel) && !empty($program->grade_lavel) ? explode(',', $program->grade_lavel) : array();
        /*$schools = \App\Modules\School\Models\School::where('district_id', session('district_id'))
            ->where('status','Y');
        if (!empty($grades)) {
            $schools = $schools->whereRaw("find_in_set('3',grade_id)");
        }
        $schools = $schools->select('id','name')->get();*/
	?>
    <div class="card-body">
        <div class="table-responsive">
        	<?php $__empty_1 = true; $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g=>$grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php if($loop->index !== 0): ?> <br> <?php endif; ?>
                <?php
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
                ?>
                <table id="options_table" class="table mb-0">
                    <tbody>
                        <tr>
                            <td colspan="6" style="background-color: #eceeef;"> Rising Grade &nbsp; <?php echo e($grade); ?> 
                            <span id="error_<?php echo e($field_unique_token); ?>" class="d-none" style="color: red;">Race & Home Zone total must be equal.</span>
                            </td>
                        </tr>
                        <!-- Race -->
                        <tr>
                            <td>Race</td>
                            <td>
                                Other <br>
                                <input type="text" class="form-control numbersOnly otherSeat race_field" data-total_field_id="<?php echo e($field_unique_token); ?>" data-id="<?php echo e($grade); ?>"  name="grades[<?php echo e($grade); ?>][other_seats]" value="<?php echo e($availabilities[$grade]->other_seats ?? ""); ?>"  <?php if($display_outcome > 0): ?> disabled <?php endif; ?> maxlength="5">
                            </td>
                            <td>
                                Not Specified <br>
                                <input type="text" class="form-control numbersOnly notSpecifiedSeat race_field" data-total_field_id="<?php echo e($field_unique_token); ?>" data-id="<?php echo e($grade); ?>"  name="grades[<?php echo e($grade); ?>][not_specified_seats]" value="<?php echo e($availabilities[$grade]->not_specified_seats ?? ""); ?>"  <?php if($display_outcome > 0): ?> disabled <?php endif; ?> maxlength="5">
                            </td>
                            <td>
                                Black <br>
                                <input type="text" class="form-control numbersOnly blackSeat race_field" data-total_field_id="<?php echo e($field_unique_token); ?>" data-id="<?php echo e($grade); ?>"  name="grades[<?php echo e($grade); ?>][black_seats]" value="<?php echo e($availabilities[$grade]->black_seats ?? ""); ?>"  <?php if($display_outcome > 0): ?> disabled <?php endif; ?> maxlength="5">
                            </td>
                            <td>
                                White <br>
                                <input type="text" class="form-control numbersOnly whiteSeat race_field" data-total_field_id="<?php echo e($field_unique_token); ?>" data-id="<?php echo e($grade); ?>"  name="grades[<?php echo e($grade); ?>][white_seats]" value="<?php echo e($availabilities[$grade]->white_seats ?? ""); ?>"  <?php if($display_outcome > 0): ?> disabled <?php endif; ?> maxlength="5">
                            </td>
                            <td>
                                Total <br>
                                <span class="form-control" id="race_<?php echo e($field_unique_token); ?>"><?php echo e($race_total); ?></span>
                            </td>
                        </tr>
                        <!-- Home Zone -->
                        <?php if($program->home_zone_school_needed == 'Y'): ?>
                        <?php for($i=0; $i<$loop_count; $i++): ?>
                            <?php if($i == 0 || (($i % 4) == 0)): ?> 
                                <tr>
                            <?php endif; ?>
                            <?php if($i == 0): ?> 
                                <td rowspan="<?php echo e($homezone_rowspan); ?>">Home Zone</td>
                            <?php endif; ?>
                            <td>
                                <?php if(isset($schools[$i])): ?>
                                    <?php echo e($schools[$i]); ?>

                                    <input type="text" class="form-control numbersOnly homezone_field" data-total_field_id="<?php echo e($field_unique_token); ?>" data-id="<?php echo e($grade); ?>"  name="grades[<?php echo e($grade); ?>][home_zone][<?php echo e(getSchoolName($schools[$i])); ?>]" value="<?php echo e($stored_home_zone_data[getSchoolName($schools[$i])] ?? ""); ?>"  <?php if($display_outcome > 0): ?> disabled  <?php endif; ?> maxlength="5">
                                <?php else: ?>
                                    &nbsp;
                                <?php endif; ?>
                            </td>
                            <?php if($i == 3): ?> 
                                <td rowspan="<?php echo e($homezone_rowspan); ?>">Total <br> <span class="form-control" id="homezone_<?php echo e($field_unique_token); ?>"><?php echo e($zone_school_total); ?></span></td>
                            <?php endif; ?>
                            <?php if(($i == ($loop_count-1)) || ((($i+1) % 4) == 0)): ?> 
                                </tr> 
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php endif; ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="5" style="background-color: #eceeef;">
                                <div class="input-group">
                                    <span class="mt-1">Grade <?php echo e($grade); ?> Capacity &nbsp;</span>
                                    <input type="text" class="form-control numbersOnly totalSeat"  name="grades[<?php echo e($grade); ?>][total_seats]" value="<?php echo e($availabilities[$grade]->total_seats ?? ""); ?>" data-id="<?php echo e($grade); ?>" <?php if($display_outcome > 0): ?> disabled <?php endif; ?>>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                 	<td class="text-center">No Grades</td>
                </tr>
            <?php endif; ?>
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