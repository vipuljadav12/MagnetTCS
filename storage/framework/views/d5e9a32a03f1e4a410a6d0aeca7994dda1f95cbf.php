<?php if(!empty($choice_ary)): ?>
    <?php $__currentLoopData = $choice_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $choice => $cvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            if ($choice == 'first' || count($choice_ary) == 1) {
                $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
            } else{
                $eligibility_data = getEligibilityContent1($value_2->assigned_eigibility_name);
            }
            $submission_audition_data = getSubmissionAudition($submission->id);
            $data = !empty($submission_audition_data->data) ? json_decode($submission_audition_data->data, true) : [];
            $options = ($eligibility_data->eligibility_type->type=="NR") ? $eligibility_data->eligibility_type->NR : [];
        ?>
        <form class="form" id="audition_form_<?php echo e($choice); ?>" method="post" action="<?php echo e(url('admin/Submissions/update/audition/'.$submission->id)); ?>">  
            <?php echo e(csrf_field()); ?>

            <div class="card shadow">
                <div class="card-header"><?php echo e($value->eligibility_ype); ?> <?php echo e($cvalue); ?> [<?php echo e(getProgramName($submission->{$choice.'_choice_program_id'})); ?>]</div>
                <div class="card-body">
                    <div class="form-group custom-none">

                        <div class="">
                            <select class="form-control custom-select template-type" name="<?php echo e($choice); ?>_data">
                                <option value="">Select Option</option>
                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php if(isset($data[$choice.'_data']) && $data[$choice.'_data'] == $v): ?> selected="" <?php endif; ?>><?php echo e($v); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-right"> 
                        <button type="submit" form="audition_form_<?php echo e($choice); ?>" class="btn btn-success">    
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

