<style type="text/css">
    .disable-row{background: #f5f5f5 !important}
    .enable-row{}
    .hide-table{visibility: hidden !important}
    .show-table{}
</style>
<div class="tab-pane fade show active" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
    <form action="<?php echo e(url('/admin/Waitlist/Process/Selection/'.$application_id.'/store')); ?>" method="post" id="process_selection">
             <?php echo e(csrf_field()); ?>

             <input type="hidden" name="type" id="type" value="update">
             <input type="hidden" name="process_event" id="process_event" value="">
    <div class="table-responsive" style="height: 500px; overflow-y: auto;">
        
       <table class="table m-0" id="tbl_population_changes">
                <thead>
                    <tr>
                        <?php if($display_outcome == 0): ?>
                            <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important"><input type="checkbox" id="checkall" onchange="checkUncheckAll($(this))"><br>Check/Uncheck All</th>
                        <?php endif; ?>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important"></th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Original Seats Available</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Current <br>Enrolled<br>Student Withdrawn</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Black</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">White</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Other</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Waitlisted</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Available Slots</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Additional Seats</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Slots to Award</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($display_outcome == 1): ?>
                        <?php $__currentLoopData = $waitlist_process_logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               
                                    <tr>
                                        <td><?php echo e($value->program_name); ?></td>
                                        <td class="text-center"><?php echo e($value->total_seats); ?></td>
                                        <td class="text-center"><select class="form-control" style="width:100px;" disabled>
                                                    <option value="No" <?php if($value->withdrawn_student == "No"): ?> selected <?php endif; ?>>No</option>
                                                    <option value="Yes" <?php if($value->withdrawn_student == "Yes"): ?> selected <?php endif; ?>>Yes</option>
                                                </select>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center" style="width: 100px;" value="<?php echo e($value->black_withdrawn); ?>" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center" style="width: 100px;" value="<?php echo e($value->white_withdrawn); ?>" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-center" style="width: 100px;" value="<?php echo e($value->other_withdrawn); ?>" disabled>
                                        </td>
                                        <td class="text-center"><?php echo e($value->waitlisted); ?></td>
                                        <td class="text-center"><?php echo e($value->available_slots); ?></td>
                                        <td class="text-center"><?php echo e($value->additional_seats); ?></td>
                                        <td class="text-center"><?php echo e($value->slots_to_awards); ?></td>
                                    </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php if(isset($disp_arr)): ?>
                            <?php $count = 0 ?>
                            <?php $__currentLoopData = $disp_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="bg-info text-white"><td colspan="11"><?php echo e($key); ?></td></tr>
                                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wkey=>$wvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($class = ($wvalue['visible'] == "N" ? "disable-row" : "enable-row")) ?>
                                    <?php ($spanclass = ($wvalue['visible'] == "N" ? "hide-table" : "show-table")) ?>

                                    <tr class="<?php echo e($class); ?>" id="row<?php echo e($wvalue['application_program_id']); ?>">
                                        <td><input type="checkbox" value="<?php echo e($wvalue['application_program_id']); ?>" name="application_program_id[]" class="check_selector" onchange="showHideRow(<?php echo e($wvalue['application_program_id']); ?>, $(this))" <?php if($wvalue['visible'] == "Y"): ?> checked <?php endif; ?>></td>
                                        <td><?php echo e($wvalue['name']); ?>

                                            <input type="hidden" value="<?php echo e($wvalue['name']); ?>" name="program_name<?php echo e($wvalue['application_program_id']); ?>">
                                            <input type="hidden" value="<?php echo e($wvalue['id']); ?>" name="program_id<?php echo e($wvalue['application_program_id']); ?>">
                                            <input type="hidden" value="<?php echo e($wvalue['grade']); ?>" name="grade<?php echo e($wvalue['application_program_id']); ?>">
                                        </td>
                                        <td class="text-center"><span class="<?php echo e($spanclass); ?>"><?php echo e($wvalue['total_seats']); ?></span>
                                            <input type="hidden" value="<?php echo e($wvalue['total_seats']); ?>" name="total_seats<?php echo e($wvalue['application_program_id']); ?>">
                                        </td>
                                        <td class="text-center">
                                            <span class="<?php echo e($spanclass); ?>">
                                                <select class="form-control withdrawn_student" data-application_program_id="<?php echo e($wvalue['application_program_id']); ?>" style="width:100px;" onchange="enableDisableWithdrawn(<?php echo e($count); ?>, $(this).val())" name="withdrawn_student<?php echo e($wvalue['application_program_id']); ?>">
                                                    <option value="No" <?php if($wvalue['withdrawn_student'] == "No"): ?> selected <?php endif; ?>>No</option>
                                                    <?php if($wvalue['withdrawn_allowed'] == "Yes"): ?>
                                                        <option value="Yes" <?php if($wvalue['withdrawn_student'] == "Yes"): ?> selected <?php endif; ?>>Yes</option>
                                                    <?php endif; ?>
                                                </select>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="<?php echo e($spanclass); ?>">
                                                <input type="text" class="form-control text-center" style="width: 100px;" id="black<?php echo e($count); ?>" value="<?php echo e($wvalue['black_withdrawn']); ?>" <?php if($spanclass=="hide-table" || $wvalue['withdrawn_student'] == "No"): ?> disabled <?php endif; ?> onchange="updateAwardSlot(<?php echo e($count); ?>)" onkeypress="return onlyNumberKey(event)" name="black<?php echo e($wvalue['application_program_id']); ?>">
                                            </span>
                                        </td>
                                        <td>
                                            <span class="<?php echo e($spanclass); ?>">
                                                <input type="text" class="form-control text-center" style="width: 100px;" id="white<?php echo e($count); ?>" value="<?php echo e($wvalue['white_withdrawn']); ?>"  <?php if($spanclass=="hide-table" || $wvalue['withdrawn_student'] == "No"): ?> disabled <?php endif; ?> onchange="updateAwardSlot(<?php echo e($count); ?>)" onkeypress="return onlyNumberKey(event)" name="white<?php echo e($wvalue['application_program_id']); ?>">
                                            </span>
                                        </td>
                                        <td>
                                            <span class="<?php echo e($spanclass); ?>">
                                                <input type="text" class="form-control text-center" style="width: 100px;" id="other<?php echo e($count); ?>" value="<?php echo e($wvalue['other_withdrawn']); ?>"  <?php if($spanclass=="hide-table" || $wvalue['withdrawn_student'] == "No"): ?> disabled <?php endif; ?> onchange="updateAwardSlot(<?php echo e($count); ?>)" onkeypress="return onlyNumberKey(event)" name="other<?php echo e($wvalue['application_program_id']); ?>">
                                            </span>
                                        </td>
                                        <td class="text-center"><span class="<?php echo e($spanclass); ?>"><?php echo e($wvalue['waitlist_count']); ?></span>
                                            <input type="hidden" value="<?php echo e($wvalue['waitlist_count']); ?>" name="waitlist_count<?php echo e($wvalue['application_program_id']); ?>">
                                        </td>
                                        <td class="text-center"><span class="<?php echo e($spanclass); ?>" id="available_slot<?php echo e($count); ?>"><?php echo e($wvalue['available_count']); ?></span><input type="hidden" value="<?php echo e($wvalue['available_count']); ?>" name="available_slot<?php echo e($wvalue['application_program_id']); ?>"></td>

                                        <td class="text-center"><span class="<?php echo e($spanclass); ?>"><input type="text" value="<?php echo e($wvalue['additional_seats']); ?>" name="additional_seats<?php echo e($wvalue['application_program_id']); ?>" onchange="updateAwardSlot(<?php echo e($count); ?>)" onkeypress="return onlyNumberKey(event)" class="form-control" style="width: 100px;"  id="additional_seats<?php echo e($count); ?>"></span></td>
                                        
                                        <td><span class="<?php echo e($spanclass); ?>" id="awardslot_span<?php echo e($count); ?>"><?php echo e($wvalue['available_slot']); ?></span>
                                            <input type="hidden" value="<?php echo e($wvalue['available_slot']); ?>" id="awardslot<?php echo e($count); ?>" name="awardslot<?php echo e($wvalue['application_program_id']); ?>">
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if(!empty($wvalue['homezone_schools'])): ?>
                                        <?php
                                            $rowspan = ceil((float)(count($wvalue['homezone_schools'])/3));
                                        ?>
                                        <?php $__currentLoopData = array_chunk($wvalue['homezone_schools'], 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hzs_key=>$hzs_chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hzs_container_<?php echo e($wvalue['application_program_id']); ?>">
                                                <?php if($hzs_key == 0): ?>
                                                    <td colspan="4" rowspan="<?php echo e($rowspan); ?>"></td>
                                                <?php endif; ?>
                                                <?php $__currentLoopData = $hzs_chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hzs_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td>
                                                        <?php echo e($hzs_name); ?><br>
                                                        <input type="text" name="homezone_schools[<?php echo e($wvalue['application_program_id']); ?>][<?php echo e(getSchoolName($hzs_name)); ?>]" class="form-control text-center hzs_val" onkeypress="return onlyNumberKey(event)" style="width: 100px;" value="<?php echo e(($wvalue['homezone_val'][getSchoolName($hzs_name)] ?? 0)); ?>">
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(count($hzs_chunk) != 3): ?>
                                                    <?php for($tmp_i=0; $tmp_i<(3-count($hzs_chunk)); $tmp_i++): ?>
                                                        <td></td>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                                <?php if($hzs_key == 0): ?>
                                                    <td colspan="4" rowspan="<?php echo e($rowspan); ?>"></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php $count++ ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            
            
        
        
    </div>

<div class="text-right"><button type="button" name="value_save" value="value_save" class="btn btn-success mt-10" onclick="saveData()">Save</button></div>
    <div class="form-group mt-20">
        <label for="">Last day and time to accept ONLINE</label>
        <div class=""><input class="form-control datetimepicker" name="last_date_online_acceptance" id="last_date_online_acceptance" value="<?php echo e($last_date_online_acceptance); ?>" data-date-format="mm/dd/yyyy hh:ii"></div>
    </div>
    <div class="form-group">
        <label for="">Last day and time to accept OFFLINE</label>
        <div class=""><input class="form-control datetimepicker" name="last_date_offline_acceptance" id="last_date_offline_acceptance" value="<?php echo e($last_date_offline_acceptance); ?>" data-date-format="mm/dd/yyyy hh:ii"></div>
    </div>
    <div class="text-right"><?php if($display_outcome == 0): ?><input type="submit" class="btn btn-success" value="Process Submissions Now"> <?php else: ?> <input type="button" class="btn btn-danger disabled" value="Process Submissions Now"> <?php endif; ?></div>
    </form>
</div>

