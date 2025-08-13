 <div class="">
    <div class="card shadow">
        
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="form-group">
                        <div class="mb-3">
                            
                            <select class="form-control custom-select ranking_system col-6" name="exception_choice" id="exception_choice">
                                <option value="">Select Exception Type</option>
                                <?php if($rec_form_data['status']): ?>
                                    <option value="recommendation_form" <?php if($exception_choice == 'recommendation_form'): ?> selected <?php endif; ?>>Recommendation Form</option>
                                <?php endif; ?>
                                <option value="program_choice" <?php if($exception_choice == 'program_choice'): ?> selected <?php endif; ?>>Choice Program Change</option>
                            </select>
                        </div>
                        <div class="exception_content">
                            <?php if($exception_choice != ''): ?>
                                <?php
                                    $grade_lavel = explode(',', $program->grade_lavel);
                                ?>
                                <?php if($exception_choice == 'recommendation_form'): ?>
                                    <input type="hidden" name="assigned_eligibility_id" value="<?php echo e($rec_form_data['eligibility_id']); ?>">
                                    <?php
                                        $i = 0;
                                        $j = 0;
                                        // $grade_lavel = explode(',', $program->grade_lavel);
                                        $rec_subj = config('variables.recommendation_subject');
                                    ?>
                                    <div class="form-group ifDD ">
                                        <label class="control-label">Recommendation Subjects :</label>
                                        <div class="row">
                                            <?php $__currentLoopData = $grade_lavel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-12 col-lg-3 mb-30 ay_main">
                                                    <div class="custom-outer-box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input grade_lvl ay_parent" id="grade_lvl_<?php echo e($i); ?>" value="<?php echo e($gvalue); ?>" name="extra[grade][]" <?php if(isset($rec_form_data['data'][$gvalue])): ?> checked <?php endif; ?>>
                                                            <label for="grade_lvl_<?php echo e($i); ?>" class="custom-control-label"><?php echo e($gvalue); ?></label>
                                                        </div>
                                                        <div class="custom-sub-box grade_lvl_<?php echo e($i); ?>" <?php if(!isset($rec_form_data['data'][$gvalue])): ?> style="display: none;" <?php endif; ?>>
                                                            <?php $__currentLoopData = $rec_subj; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rskey=>$rsvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="pl-20 custom-sub-child">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <?php
                                                                            $is_checked = '';
                                                                            if( isset($rec_form_data['data'][$gvalue]) && 
                                                                                !empty($rec_form_data['data'][$gvalue]) && 
                                                                                in_array($rskey, $rec_form_data['data'][$gvalue]) ) 
                                                                            {
                                                                                $is_checked = 'checked';
                                                                            } 
                                                                        ?>
                                                                        <input type="checkbox" class="custom-control-input ay_child" id="rec_subj_<?php echo e($j); ?>" value="<?php echo e($rskey); ?>" name="extra[rec_subj][<?php echo e($gvalue); ?>][]" <?php echo e($is_checked); ?>>
                                                                    <label for="rec_subj_<?php echo e($j); ?>" class="custom-control-label"><?php echo e($rsvalue); ?></label></div>
                                                                </div>
                                                                <?php $j++ ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $i++ ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php elseif($exception_choice == 'program_choice'): ?>
                                    <div class="form-group">
                                        <label class="control-label">Program Choice :</label>
                                        <?php $__currentLoopData = $grade_lavel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gkey=>$gvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(is_numeric($gvalue) && $gvalue > 1): ?>
                                                <?php $cgrade = $gvalue-1 ?>
                                            <?php elseif($gvalue == 1): ?>
                                                <?php $cgrade = "PreK" ?>
                                            <?php elseif($gvalue == "PreK"): ?>
                                                <?php $cgrade = "K" ?>
                                            <?php else: ?>
                                                <?php $cgrade = "K" ?>
                                            <?php endif; ?>
                                            <div class="row col-12 mb-3 pc_parent">
                                                <?php
                                                    $grade_check = $rec_form_data['data']->where('grade', $cgrade)->first();
                                                    $display_name = $grade_check->display_name ?? '';
                                                    // dd($grade_check);
                                                ?>
                                                <div class="mt-5 custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input pc_chkbx" id="grade_lvl_<?php echo e($gkey); ?>" name="extra[grade][]" value="<?php echo e($cgrade); ?>" <?php if(isset($grade_check)): ?> checked <?php endif; ?>>
                                                    <label for="grade_lvl_<?php echo e($gkey); ?>" class="custom-control-label"><?php echo e($cgrade); ?></label>
                                                </div>
                                                <div class="col-5 ml-4 pc_input d-none">
                                                    <input class="form-control" type="text" name="extra[name][<?php echo e($cgrade); ?>]" value="<?php echo e($display_name); ?>" maxlength="200">
                                                    <sup style="color: red;">* </sup>For next academic year, use ##NEXT_YEAR##
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            
                                <div class="" align="right">  
                                    <button form="frm_exception" class="btn btn-success" id="rec_frm_save" type="submit">Save</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('exception_script'); ?>
<script type="text/javascript">
    $(document).on('change', '#exception_choice', function() {
        var op_val = $(this).val();
        if (op_val != '') {
            window.location.href = '<?php echo e(url('/')); ?>/admin/Program/edit/'+'<?php echo e($program->id); ?>?exception_choice='+op_val;
        }
    });

    <?php if($exception_choice == 'recommendation_form'): ?>
        // In checkbox Uncheck child when parent unchecked
        $(document).on('change', '.ay_parent', function() {
            if ($(this).prop('checked') == false) {
                $(this).closest('.ay_main').find('.ay_child').prop('checked', false).trigger('change');
            }
        });
        // Hide/show
        $(document).on('click', '.grade_lvl', function(){
            var id = $(this).attr('id');

            if($(this).is(':checked')){
                $(document).find('.'+id).show();
            }else{
                $(document).find('.'+id).hide();

            }
        });
    <?php elseif($exception_choice == 'program_choice'): ?>
        $('.pc_parent').find('.pc_chkbx').each(function() {
            hideShowInput($(this));
        });
        $(document).on('change', '.pc_chkbx', function() {
            hideShowInput($(this));
        });
        function hideShowInput(e) {
            var input_obj = e.closest('.pc_parent').find('.pc_input');
            if (e.prop('checked')) {
                input_obj.removeClass('d-none');
            } else {
                input_obj.addClass('d-none');
            }
        }
    <?php endif; ?>

    <?php if($exception_choice != ''): ?>
        // Recommendation form save
        $('form#frm_exception').submit(function() {
            var content = $('.exception_content').clone();
            $('.exception_tab_content').find('#frm_exception').append(content);
        });
    <?php endif; ?>

</script>
<?php $__env->stopSection(); ?>