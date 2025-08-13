<?php if(!empty($choice_ary)): ?>
    <?php $__currentLoopData = $choice_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $choice => $cvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php
            $program_id = $submission->{$choice.'_choice_program_id'};
            // Entery in writing prompt table
            $wp_entry = App\Modules\WritingPrompt\Models\WritingPrompt::where('submission_id', $submission->id)->where('program_id', $program_id)->first();

            if ($choice == 'first' || count($choice_ary) == 1) {
                $wp_data = getWritingPromptDetails($submission->id, $value, $submission->late_submission);
            } else{
                $wp_data = getWritingPromptDetails($submission->id, $value_2, $submission->late_submission);
                // $wp_data = getWritingPromptDetails($submission->id, $value_2, $submission->late_submission, $choice);
                $value = $value_2;
            }
            if (!empty($wp_data)) {
                $wp_data = json_decode($wp_data, true);
            }

            $submission_data = \DB::table('submission_data')
                ->where('submission_id', $submission->id)
                ->where('config_name', 'wp_'.$choice.'_choice_link')
                ->first();
            $wp_link = $submission_data->config_value ?? '';

            // $data = getSubmissionWritingPrompt($submission->id);
        ?>

        
            
            <div class="card shadow">
                <div class="card-header"><?php echo e($value->eligibility_ype); ?> <?php echo e($cvalue); ?> [<?php echo e(getProgramName($submission->{$choice.'_choice_program_id'})); ?>]</div>
                <div class="card-body">
                    <div class="">
                        <?php $__empty_1 = true; $__currentLoopData = $wp_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wp_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12 font-weight-bold"><?php echo e(isset($wp_value['writing_prompt']) ? $wp_value['writing_prompt'] : 'Writing Prompt'); ?></label>
                                <div class="col-12 col-md-12">
                                    <textarea class="form-control" disabled><?php echo e(isset($wp_value['writing_sample']) ? $wp_value['writing_sample'] : ''); ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Student Email</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" value="<?php echo e(isset($submission->parent_email) ? $submission->parent_email : ''); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Student LInk</label>
                            <div class="col-12 col-md-12">
                                <span style="color: blue;"><?php echo e(url('/WritingPrompt/').'/'.$wp_link); ?></span>
                            </div>
                        </div>
                        <div class="input-group">
                            <?php if(isset($wp_entry)): ?>
                            
                                <a href="<?php echo e(url('admin/WritingPrompt/print/'.$submission->id.'/'.$program_id)); ?>" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Writing Sample</a>
                            
                                <a href="javascript:void(0);" data-s_id="<?php echo e($submission->id); ?>" data-p_id="<?php echo e($program_id); ?>" class="btn btn-sm btn-primary" title="" onclick="clearWritingPrompt(this)"><i class="fas fa-eraser"></i> Clear Writing Prompt</a>
                            <?php else: ?>
                                <a href="javascript:void(0);" data-s_id="<?php echo e($submission->id); ?>" data-s_choice="<?php echo e($choice); ?>" data-parent_email="<?php echo e(isset($submission->parent_email) ? $submission->parent_email : ''); ?>" class="btn btn-sm btn-primary" title="" onclick="sendWritingPromptMail(this)"><i class="far fa-paper-plane"></i> Resend Email</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
