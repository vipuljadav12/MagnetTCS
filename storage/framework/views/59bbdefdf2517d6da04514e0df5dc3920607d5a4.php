<?php
    $recommendation = getRecommendationFormData($submission->id);
    $doneArr = array();
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $rec_subjects = $eligibility_data->subjects;

?>
<?php if(isset($recommendation) && !empty($recommendation)): ?>
    <?php $__currentLoopData = $recommendation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$rec_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php 
            $doneArr[] = $rec_value->config_value; 

            // dd($rec_value);
        ?>
        <div class="card shadow">

            <?php
                $subject = explode('.', $rec_value->config_value)[0];

                $ans_content = json_decode($rec_value->answer);
                // dd($ans_content);
            ?>
            <div class="card-header">Recommendation - <?php echo e(config('variables.recommendation_subject')[$subject]); ?></div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Teacher Name : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="<?php echo e($rec_value->teacher_name); ?>" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Teacher Email : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="<?php echo e($rec_value->teacher_email); ?>" disabled>
                    </div>
                </div>
                <div class="form-group row d-none">
                    <label class="control-label col-12 col-md-12">Average Score : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="<?php echo e($rec_value->avg_score); ?>" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Comment : </label>
                    <div class="col-12 col-md-12">
                        <textarea class="form-control" rows="3" disabled><?php echo e($rec_value->comment); ?></textarea>
                    </div>
                </div>

                <?php if(isset($ans_content->answer)): ?>
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">Question-Ans : </label>
                        <div class="col-12 col-md-12">
                            <?php $__currentLoopData = $ans_content->answer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h=>$header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <div class="card">
                                    <div class="card-header"><?php echo e($header->name); ?></div>
                                    <div class="card-body">
                                        <?php if(isset($header->answers)): ?>
                                    <?php $__currentLoopData = $header->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ak=>$avalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12"><?php echo e($ak ?? ''); ?> : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control" disabled="">
                                                <?php $__currentLoopData = $header->points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk=>$point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option><?php echo e($avalue); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>  
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Student LInk</label>
                    <div class="col-12 col-md-12">
                        <span style="color: blue;"><?php echo e(url('/recommendation/'.$rec_value->config_value)); ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">
                        <?php if(isset($eligibility_data->form_type) && $eligibility_data->form_type == "IB"): ?>
                            <a href="<?php echo e(url('/admin/Submissions/ibform/pdf/'.$rec_value->id.'/ib')); ?>" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Recommendation Form</a>
                        <?php else: ?>
                            <a href="<?php echo e(url('/admin/Submissions/recommendation/pdf/'.$rec_value->id.'')); ?>" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Recommendation Form</a>
                        <?php endif; ?>
                    </label>
                </div>
            </div>
        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>


<?php
    $recommendationUrl = getRecommendationLinks($submission->id);    

?>
<?php if(isset($recommendationUrl) && !empty($recommendationUrl)): ?>
    <div class="card shadow">
        <div class="card-header">Pending Recommendation </div>
        <div class="card-body">
            <?php $__currentLoopData = $recommendationUrl; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rec_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!in_array($rec_value->config_value, $doneArr)): ?>
                    <?php if($submission->student_id != ''): ?>
                        <form method="post" action="<?php echo e(url('admin/Submissions/recommendation/send/manual')); ?>" onsubmit="return validateTeacherEmail(this)">
                    <?php else: ?>
                         <form method="post" action="<?php echo e(url('admin/Submissions/recommendation/send/parent/manual')); ?>">
                    <?php endif; ?>
                    <?php echo e(csrf_field()); ?>    
                    <input type="hidden" name="submission_id" value="<?php echo e($submission->id); ?>">
                    <input type="hidden" name="config_id" value="<?php echo e($rec_value->id); ?>">
                    <div class="form-group row pt-10">
                        <label class="control-label col-12 col-md-12">
                            <?php
                                $name = "";
                                $email = "";
                                $subject_title = str_replace("recommendation_", "", $rec_value->config_name);
                                $subject_title = str_replace("_url", "", $subject_title);
                                $rsubjects = config('variables.recommendation_subject');
                                if($subject_title == "lfd_admin" && isset($eligibility_data->lfd_admin_name))
                                {
                                    $name = $eligibility_data->lfd_admin_name;
                                    $email = $eligibility_data->lfd_admin_email;
                                }
                                else
                                {
                                    if($submission->student_id != '')
                                    {
                                        $rs_student = \App\StudentData::where("stateID", $submission->student_id)->where("field_name", strtolower($subject_title)."_teacher_email")->where("enrollment_id", Session::get('enrollment_id'))->first();
                                        if(!empty($rs_student))
                                        {
                                            $email = $rs_student->field_value;
                                        }
                                        $rs_student = \App\StudentData::where("stateID", $submission->student_id)->where("field_name", strtolower($subject_title)."_teacher_name")->where("enrollment_id", Session::get('enrollment_id'))->first();
                                        if(!empty($rs_student))
                                        {
                                            $name = $rs_student->field_value;
                                        }

                                    }
                                }
                                echo "<strong>".$rsubjects[$subject_title] ." Recommendation Form"."</strong>";
                            ?>
                        </label>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Teacher Name:</strong></label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="teacher_name" value="<?php echo e($name); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Teacher Email:</strong></label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="email" value="<?php echo e($email); ?>">
                        </div>
                    </div>
                     
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Recommendation Form Link:</strong></label>
                        <div class="col-12 col-md-12">
                            <span style="color: blue;"><a href="<?php echo e(url('/recommendation/'.$rec_value->config_value)); ?>"><?php echo e(url('/recommendation/'.$rec_value->config_value)); ?></a></span>
                        </div>
                    </div>
                    <div class="form-group row pb-20" style="border-bottom: 1px solid #ccc;">
                        
                        <div class="col-12 col-md-12">
                            <?php if($submission->student_id != ''): ?>
                                <input type="submit" class="btn btn-success" value="Send Recommendation Email Link">
                            <?php else: ?>
                                <input type="submit" class="btn btn-success" value="Send Recommendation Email To Parent">
                            <?php endif; ?>
                        </div>
                    </div>
                    </form>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?>
