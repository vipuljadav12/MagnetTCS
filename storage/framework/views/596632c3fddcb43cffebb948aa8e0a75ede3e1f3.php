<div class="">
    <form method="post" action="<?php echo e(url('admin/Submissions/update',$submission->id)); ?>" id="generalSubmission">    
    <?php echo e(csrf_field()); ?>   
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="card shadow">
                    <div class="card-header">Student Information</div>
                    <div class="card-body">
                        

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Confirmation No: </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="" value="<?php echo e($submission->confirmation_no); ?>" disabled>
                            </div>
                        </div>
                        <?php if($district->lottery_number_display == "Yes"): ?>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Lottery Number : </label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" value="<?php echo e($submission->lottery_number); ?>" disabled>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">State ID : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="student_id" value="<?php echo e($submission->student_id); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('first_name') ?? 'First Name'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="first_name" value="<?php echo e($submission->first_name); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('last_name') ?? 'Last Name'); ?><span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="last_name" value="<?php echo e($submission->last_name); ?>">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('birthday') ?? 'Date of Birth'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12 row">
                                 <?php if($submission->birthday != ''): ?>
                                    <?php $bdates = explode("-", $submission->birthday) ?>
                                 <?php else: ?>
                                    <?php $bdates = array(date("Y"), date("m"), date("d")) ?>
                                 <?php endif; ?>
                                 

                                <div class="col-4">
                                    <?php 
                                        $months = Config::get('variables.months');
                                        // print_r($months);
                                    ?>
                                    <select class="form-control changeDate" id="month">
                                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(isset($bdates[1]) && $bdates[1]==$key): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-4">
                                    <select class="form-control changeDate" id="day">
                                         <?php for($i=1; $i <= 31; $i++): ?>
                                            <?php 
                                                if($i < 10)
                                                    $day = "0".$i;
                                                else
                                                    $day = $i;

                                            ?>
                                            <option value="<?php echo e($day); ?>" <?php if(isset($bdates[2]) && $bdates[2]==$day): ?> selected="selected" <?php endif; ?>><?php echo e($i); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-control changeDate" id="year">
                                        <?php for($i=2020; $i >= 1970; $i--): ?>
                                            <option value="<?php echo e($i); ?>" <?php if($bdates[0]==$i): ?> selected="selected" <?php endif; ?>><?php echo e($i); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <input type="hidden" class="form-control" name="birthday" id="birthday" value="<?php echo e(date('Y-m-d', strtotime($submission->birthday))); ?>">
                                
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('race') ?? 'Race'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <?php $race_arr = array("African-American - Hispanic", "African-American - Non-Hispanic", "Asian - Hispanic", "Asian - Non-Hispanic", "Hispanic - Hispanic", "Hispanic - Non-Hispanic", "Caucasian - Hispanic", "Caucasian - Non-Hispanic", "American Indian - Hispanic", "American Indian - Non-Hispanic", "Pacific Islander - Hispanic", "Pacific Islander - Non-Hispanic", "White - Hispanic", "White - Non-Hispanic", "Other - Hispanic", "Other - Non-Hispanic"); ?>
                                <select class="form-control" name="race">
                                    <?php $__currentLoopData = $race_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php if($submission->race == $value): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('gender') ?? 'Gender'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <?php $gender_arr = array("Male","Female","Choose Not to Answer"); ?>
                                <?php 
                                        if($submission->gender == "F")
                                            $gender = "Female";
                                        elseif($submission->gender == "M")
                                            $gender = "Male";
                                        else
                                            $gender = $submission->gender;
                                    ?>
                                <select class="form-control" name="gender">
                                    <option value="">Select Gender</option> 
                                    <?php $__currentLoopData = $gender_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php if($gender == $value): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('address') ?? 'Address'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="address" value="<?php echo e($submission->address); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('city') ?? 'City'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="city" value="<?php echo e($submission->city); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('state') ?? 'State'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="state">
                                    <option value="">Select an Option</option> 
                                <?php $stateArray = Config::get('variables.states') ?>

                                <?php $__currentLoopData = $stateArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stkey=>$stvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($stkey); ?>" <?php if(strtolower($submission->state)==strtolower($stkey)): ?> selected <?php endif; ?>><?php echo e($stvalue); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('zip') ?? 'ZIP'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="zip" value="<?php echo e($submission->zip); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('phone_number') ?? 'Phone Number'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" maxlength="15" name="phone_number" value="<?php echo e($submission->phone_number); ?>">
                                <div class="small">Max 15 Characters</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('alternate_number') ?? 'Alternate Number'); ?> <span class="required">&nbsp;</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="alternate_number" value="<?php echo e($submission->alternate_number); ?>" maxlength="15">
                                <div class="small">Max 15 Characters</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('current_school') ?? 'Current School'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="current_school" value="<?php echo e($submission->current_school); ?>">
                                <!--<select class="form-control custom-select" name="current_school">
                                    <?php $__currentLoopData = $data['schools']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($school->id); ?>" <?php if($submission->current_school==$school->id): ?> selected="" <?php endif; ?>> <?php echo e($school->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>-->
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('zoned_school') ?? 'Zoned School'); ?> <span class="required">&nbsp;</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="zoned_school" value="<?php echo e($submission->zoned_school); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('current_grade') ?? 'Current Grade'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="current_grade" disabled>
                                    <?php $__currentLoopData = $data['grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    	<option value="<?php echo e($grade->name); ?>" <?php if($submission->current_grade==$grade->name): ?> selected="" <?php endif; ?>> <?php echo e($grade->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('next_grade') ?? 'Next Grade'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="next_grade" disabled>
                                    <?php $__currentLoopData = $data['grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    	<option value="<?php echo e($grade->name); ?>" <?php if($submission->next_grade==$grade->name): ?> selected="" <?php endif; ?>> <?php echo e($grade->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                       <div class="form-group d-flex justify-content-between pt-5 d-none">
                            <label for="" class="control-label">Allow Manual Grade Change : </label>
                            <div class="">
                                <input id="chk_grade_change" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="manual_grade_change" <?php echo e($submission->manual_grade_change=='Y' ? 'checked' : ''); ?>/>
                            </div>
                        </div>
                        <div class="form-group row d-none" id="grade_change_comment">
                            <label class="control-label col-12 col-md-12">Comment <span class="required">*</span>: </label>
                            <div class="col-12 col-md-12">
                                <textarea name="grade_change_comment" id="grade_add_comment" class="form-control"></textarea>
                            </div>
                        </div>

                       

                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card shadow">
                    <div class="card-header">Submission Information</div>
                    <div class="card-body">


                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Late Submission  : </label>
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select" disabled>
                                        <?php if($submission->late_submission == "Y"): ?>
                                            <option>Yes</option>
                                        <?php else: ?>
                                            <option>No</option>
                                        <?php endif; ?>
                                    </select>
                                    
                                </div>
                            </div>
                        

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('parent_first_name') ?? 'Parent First Name'); ?> <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="parent_first_name" value="<?php echo e($submission->parent_first_name); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('parent_last_name') ?? 'Parent Last Name'); ?><span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="parent_last_name" value="<?php echo e($submission->parent_last_name); ?>">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('parent_email') ?? "Parent's Email"); ?> : </label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="parent_email" value="<?php echo e($submission->parent_email); ?>">
                                <div class="small">changes to this field will only affect new messages that go out.</div>
                                <div class=""><a href="<?php echo e(url('/admin/Submissions/confirmation/resend/'.$submission->id)); ?>" class="btn btn-sm btn-success" title="Resend Confirmation"><i class="far fa-paper-plane"></i> Resend Confirmation</a></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('open_enrollment') ?? 'Open Enrollment'); ?> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="open_enrollment">
                                	<option value="0">Select Enrollment</option>
                                    <?php $__currentLoopData = $data['enrollments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    	<option value="<?php echo e($enrollment->id); ?>" <?php if($submission->open_enrollment==$enrollment->id): ?> selected="selected" <?php endif; ?>> <?php echo e($enrollment->school_year); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('first_choice') ?? 'First Choice'); ?> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="first_choice" id="first_choice">
                                    <option value="">Choose a First Choice</option>
                                    <?php $__currentLoopData = getProgramDropdown($submission->application_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$applicationProgram): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($submission->next_grade == $applicationProgram->grade_name): ?>
                                        <option value="<?php echo e($applicationProgram->id); ?>" <?php if($submission->first_choice==$applicationProgram->id): ?> selected="selected" <?php endif; ?>><?php echo e($applicationProgram->program_name); ?> - Grade <?php echo e($applicationProgram->grade_name); ?></option>
                                        <?php endif; ?>
                                    
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <?php if($submission->first_sibling != ''): ?>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Sibling ID : </label>
                                <div class="col-12 col-md-12">
                                    <input type="text" class="form-control" disabled="disabled" value="<?php echo e($submission->first_sibling." ".getStudentName($submission->first_sibling)); ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('second_choice') ?? 'Second Choice'); ?> : </label>
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select" name="second_choice" id="second_choice">
                                    <option value="">Choose a Second Choice</option>
                                    <?php $__currentLoopData = getProgramDropdown($submission->application_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$applicationProgram): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($submission->next_grade == $applicationProgram->grade_name): ?>
                                        <option value="<?php echo e($applicationProgram->id); ?>" <?php if($submission->second_choice==$applicationProgram->id): ?> selected="selected" <?php endif; ?> <?php if($applicationProgram->id == $submission->first_choice): ?> class="d-none" <?php endif; ?>><?php echo e($applicationProgram->program_name); ?> - Grade <?php echo e($applicationProgram->grade_name); ?></option>
                                        <?php endif; ?>
                                    
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <?php if($submission->second_sibling != ''): ?>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Sibling ID : </label>
                                <div class="col-12 col-md-12">
                                    <input type="text" disabled="disabled" class="form-control" value="<?php echo e($submission->second_sibling." ".getStudentName($submission->second_sibling)); ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group row" id="choice_comment" style="display: none;">
                            <label class="control-label col-12 col-md-12">Comment <span class="required">*</span>: </label>
                            <div class="col-12 col-md-12">
                                <textarea name="choice_comment" id="add_comment" class="form-control"></textarea>
                            </div>
                        </div>

                         <div class="form-group row d-none">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('special_accommodations') ?? 'Special Accommodations '); ?>  : </label>
                            <div class="col-12 col-md-12">
                                 <select class="form-control custom-select" name="special_accommodations" id="special_accommodations">
                                    <option vlaue="No" <?php if($submission->special_accommodations=="No"): ?> selected="selected" <?php endif; ?>>No</option>
                                    <option vlaue="Yes" <?php if($submission->special_accommodations=="Yes"): ?> selected="selected" <?php endif; ?>>Yes</option>
                                 </select>
                            </div>
                        </div>

                        <?php if($submission->form_id == 2): ?>
                         <div class="form-group row">
                                <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('gifted_student') ?? 'Gifted Status ?'); ?>  : </label>
                                <div class="col-12 col-md-12">
                                     <select class="form-control custom-select" name="gifted_student" id="gifted_student">
                                        <option vlaue="">Choose an Option</option>
                                        <option vlaue="Gifted" <?php if($submission->gifted_student=="Gifted"): ?> selected="selected" <?php endif; ?>>Gifted</option>
                                        <option vlaue="Not Gifted" <?php if($submission->gifted_student=="Not Gifted"): ?> selected="selected" <?php endif; ?>>Not Gifted</option>
                                        <option vlaue="Parent Identified as Gifted" <?php if($submission->gifted_student=="Parent Identified as Gifted"): ?> selected="selected" <?php endif; ?>>Parent Identified as Gifted</option>
                                        

                                     </select>
                                   
                                </div>
                            </div>
                        <?php endif; ?>
                             <?php if($submission->mcp_employee != "Yes"): ?> 
                                <?php $mcp_class = "d-none" ?>
                             <?php else: ?>
                                <?php $mcp_class = "" ?>
                             <?php endif; ?>
                                <div class="form-group row <?php echo e($mcp_class); ?>" id="employee_id_div">
                                    <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('employee_id') ?? 'Employee ID'); ?>  : </label>
                                    <div class="col-12 col-md-12">
                                         <input type="text" class="form-control" name="employee_id" id="employee_id" value="<?php echo e($submission->employee_id); ?>">
                                    </div>
                                </div>
                            <div class="form-group row <?php echo e($mcp_class); ?>" id="work_location_div">
                                <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('work_location') ?? 'Work Location'); ?>  : </label>
                                <div class="col-12 col-md-12">
                                     <input type="text" class="form-control" name="work_location" id="work_location" value="<?php echo e($submission->work_location); ?>">
                                </div>
                            </div>
                            <div class="form-group row <?php echo e($mcp_class); ?>" id="employee_first_name_div">
                                <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('employee_first_name') ?? 'Employee First Name'); ?>  : </label>
                                <div class="col-12 col-md-12">
                                     <input type="text" class="form-control" name="employee_first_name" id="employee_first_name" value="<?php echo e($submission->employee_first_name); ?>">
                                </div>
                            </div>
                            <div class="form-group row <?php echo e($mcp_class); ?>" id="employee_last_name_div">
                                <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('employee_last_name') ?? 'Employee Last Name'); ?>  : </label>
                                <div class="col-12 col-md-12">
                                     <input type="text" class="form-control" name="employee_last_name" id="employee_last_name" value="<?php echo e($submission->employee_last_name); ?>">
                                </div>
                            </div>
                        

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Submission Status <span class="required">*</span> : </label>
                            <div class="col-12 col-md-12">
                                
                                   <?php
                                    $onlyStatus = false;
                                    $offered_program = "";
                                    $all_options = [];
                                    if($submission->submission_status == "Active")
                                    {
                                        $all_options = array(
                                            "Offered",
                                            "Application Withdrawn",
                                            "Denied due to Ineligibility",
                                            "Waitlisted"
                                        );
                                    }
                                    elseif($submission->submission_status == "Offered" || $submission->submission_status == "Offered and Accepted")
                                    {
                                        $all_options = array(
                                            "Offered and Declined",
                                            "Application Withdrawn"
                                        );
                                    }
                                    elseif($submission->submission_status == "Pending")
                                    {
                                        $all_options = array("Active",
                                            "Denied due to Ineligibility",
                                            "Application Withdrawn",
                                        );
                                    }
                                     elseif($submission->submission_status == "Application Withdrawn")
                                    {
                                        $all_options = array("Active",
                                            "Pending"
                                        );
                                    }
                                    elseif($submission->submission_status == "Auto Decline")
                                    {
                                        $all_options = array(
                                            "Offered and Accepted",
                                        );
                                    }
                                    elseif($submission->submission_status == "Denied due to Ineligibility")
                                    {
                                        $all_options = array(
                                            "Active",
                                            "Offered",
                                            "Application Withdrawn",
                                            "Waitlisted"
                                        );
                                    }
                                    elseif($submission->submission_status == "Waitlisted" || $submission->submission_status == "Declined / Waitlist for other")
                                    {
                                        $all_options = array(
                                            "Offered",
                                            "Application Withdrawn",
                                        );
                                    }
                                    elseif($submission->submission_status == "Offered and Declined")
                                    {
                                        $all_options = array(
                                            "Offered and Accepted"
                                        );
                                    }
                                ?>
                                <select class="form-control custom-select" name="submission_status" id="submission_status">
                                    <option value="<?php echo e($submission->submission_status); ?>" selected><?php echo e($submission->submission_status); ?></option>
                                        <?php $__currentLoopData = $all_options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o=>$option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($option); ?>" <?php if(isset($submission->submission_status) && $submission->submission_status == $option): ?> selected="" <?php endif; ?>><?php echo e($option); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                            </div>
                        </div>

                        <div class="d-none" id="changeprograms">
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12" id="newstatus_label">New Offered Program : </label>
                                <div class="col-12 col-md-12">

                                    <select class="form-control custom-select" name="newofferprogram" id="newofferprogram">
                                        <option value="">Select Program</option>
                                        <option value="<?php echo e($submission->first_choice_program_id); ?>"><?php echo e(getProgramName($submission->first_choice_program_id)  . " - Grade ".$submission->next_grade); ?></option>
                                        <?php if($submission->second_choice_program_id > 0): ?>
                                            <option value="<?php echo e($submission->second_choice_program_id); ?>"><?php echo e(getProgramName($submission->second_choice_program_id)  . " - Grade ".$submission->next_grade); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>


                        </div>
                         <div class="card shadow d-none" id="acpt_offer">
                                    <div class="card-header">Acceptance Window</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-lg-6">
                                                <label class="">Last day and time to accept ONLINE</label>

                                                <div class="input-append date form_datetime">
                                                <input class="form-control datetimepicker" name="last_date_online_acceptance" id="last_date_online_acceptance1"  value="<?php echo e($last_date_online_acceptance); ?>" data-date-format="mm/dd/yyyy hh:ii">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <label class="">Last day and time to accept OFFLINE</label>
                                                <div class="input-append date form_datetime"> <input class="form-control datetimepicker" name="last_date_offline_acceptance" id="last_date_offline_acceptance1"  value="<?php echo e($last_date_offline_acceptance); ?>" data-date-format="mm/dd/yyyy hh:ii"></div>
                                            </div>
                                        </div>    
                                    </div>
                                </div>


                        <?php if($offered_program != ""): ?>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Offered Program : </label>
                                <div class="col-12 col-md-12">

                                    <select class="form-control custom-select" disabled>
                                            <option><?php echo e($offered_program . " - Grade ".$submission->next_grade); ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php elseif(($submission->submission_status == "Offered" || $submission->submission_status == "Offered and Accepted")): ?>
                        <div class="form-group row">
                                <label class="control-label col-12 col-md-12">Offered Program : </label>
                                <div class="col-12 col-md-12">

                                    <select class="form-control custom-select" disabled>
                                            <option><?php echo e($submission->awarded_school . " - Grade ".$submission->next_grade); ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>


                        <div class="form-group row" id="status_comment" style="display: none;">
                            <label class="control-label col-12 col-md-12">Comment <span class="required">*</span>: </label>
                            <div class="col-12 col-md-12">
                                <textarea name="status_comment" id="status_comment_box" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-between d-none" style="display: none !important">
                            <label for="" class="control-label">Override Student : </label>
                            <div class=""><input id="chk_99" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="override_student" <?php echo e($district->override_student=='Y'?'checked':''); ?>/></div>
                        </div>

                        <?php if(
                            (getProgramName($submission->first_choice_program_id) == 'Central High International Baccalaureate Program') ||
                            (getProgramName($submission->second_choice_program_id) == 'Central High International Baccalaureate Program')
                        ): ?>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12"><?php echo e(getFieldLabel('holistic_committee_recommendation') ?? 'Holistic Committee Recommendation '); ?>  : </label>
                            <div class="col-12 col-md-12">
                                 <select class="form-control custom-select" name="holistic_committee_recommendation" id="holistic_committee_recommendation">
                                    <option value="">Select an Option</option>
                                    <option value="recommend" <?php if($submission->holistic_committee_recommendation=="recommend"): ?> selected="selected" <?php endif; ?>>Recommend</option>
                                    <option value="do_not_recommend" <?php if($submission->holistic_committee_recommendation=="do_not_recommend"): ?> selected="selected" <?php endif; ?>>Do Not Recommend</option>
                                 </select>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">Download</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-9 col-md-9">Student Application Data Sheet : </label>
                            <div class="col-3 col-md-3 text-right">
                            <?php if($conduct_display): ?>
                                <a href="<?php echo e(url('/admin/GenerateApplicationData/generate/individual/IB/'.$submission->id)); ?>"><i class="fa fa-download  text-success"></i></a>
                            <?php else: ?>
                                <a href="<?php echo e(url('/admin/GenerateApplicationData/generate/individual/'.$submission->id)); ?>"><i class="fa fa-download  text-success"></i></a>
                            <?php endif; ?>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Priority Tab -->
                <div class="card shadow d-none">
                    <div class="card-header">Priority Information</div>
                    <div class="card-body">
                        <?php
                            $priorities = app('App\Modules\Submissions\Controllers\SubmissionsController')->priorityCalculate($submission, "first");
                        ?>
                        <?php if(count($priorities) > 0): ?>
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12"><strong>First Choice Priority</strong></label>
                            </div>
                            
                            <div class="form-group row">
                                <label class="control-label col-8 col-md-8">Priority Rank : </label>
                                <div class="col-4 col-md-4 text-right"><?php echo e(app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($submission, "first")); ?>

                                </div>
                            </div>

                            <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk=>$pv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                                

                                <div class="form-group row">
                                    <label class="control-label col-8 col-md-8"><?php echo e($pk); ?> : </label>
                                    <div class="col-4 col-md-4 text-right">
                                        <?php if($pv == "Yes"): ?>
                                            <span class="text-success">YES</span>
                                        <?php else: ?>
                                            <span class="text-danger">NO</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        <?php if($submission->second_choice != ''): ?>
                            <?php
                                $priorities = app('App\Modules\Submissions\Controllers\SubmissionsController')->priorityCalculate($submission, "second");
                            ?>
                            <?php if(count($priorities) > 0): ?>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12"><strong>Second Choice Priority</strong></label>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-8 col-md-8">Priority Rank : </label>
                                    <div class="col-4 col-md-4 text-right"><?php echo e(app('App\Modules\Reports\Controllers\ReportsController')->priorityCalculate($submission, "second")); ?>

                                    </div>
                                </div>

                                <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk=>$pv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                                    <div class="form-group row">
                                        <label class="control-label col-8 col-md-8"><?php echo e($pk); ?> : </label>
                                        <div class="col-4 col-md-4 text-right">
                                            <?php if($pv == "Yes"): ?>
                                                <span class="text-success">YES</span>
                                            <?php else: ?>
                                                <span class="text-danger">NO</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                    </div>
                </div>

                <?php if(!empty($manual_email)): ?>
                            <div class="form-group text-right">
                                 <a href="<?php echo e(url('/')); ?>/admin/Submissions/general/send/offer/email/<?php echo e($manual_email->process_type); ?>/<?php echo e($submission->id); ?>/preview" class="btn btn-success mr-10" title="Submit">Preview Offer Email</a>
                            </div>
                        
                        <?php endif; ?>

                        <?php if(!empty($offer_data) && $offer_data->offer_slug != "" && $submission->submission_status == "Offered"): ?>
                            <div class="card shadow">
                                <div class="card-header">Offered</div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="control-label col-9 col-md-9">Offered Link for TCS Admin [Offline] : </label>
                                        <div class="col-3 col-md-3 text-right"><a href="<?php echo e(url('/admin/Offers/'.$offer_data->offer_slug)); ?>" target="_blank"><i class="fa fa-link text-success"></i></a>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label col-5 col-md-5">Offered / Contract Link for Parents [Online] : </label>
                                        <div class="col-7 col-md-7 text-right"><a href="<?php echo e(url('/Offers/'.$offer_data->offer_slug)); ?>" target="_blank"><?php echo e(url('/Offers/'.$offer_data->offer_slug)); ?></a>
                                        </div>
                                    </div>

                                   

      
                                    
                                </div>
                            </div>
                       
                        <?php endif; ?>



                <?php if($submission->mcp_employee == "Yes"): ?> 
                <div class="card shadow">
                    <div class="card-header">TCS Employee Status</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-9 col-md-9">TCS Employee Verification Status : </label>
                            <div class="col-3 col-md-3 text-right">
                                <?php if($submission->mcpss_verification_status == "V"): ?>
                                    <span class="text-success">YES</span>
                                <?php else: ?>
                                    <span class="text-danger">NO</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="control-label col-9 col-md-9">TCS Magnet Program Employee Status : </label>
                            <div class="col-3 col-md-3 text-right">
                                <?php if($submission->magnet_program_employee == "Y"): ?>
                                    <span class="text-success">YES</span>
                                <?php else: ?>
                                    <span class="text-danger">NO</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php $grade_docs = getGradeUploadDocs($submission->id, 'grade') ?>

                <?php if(count($grade_docs) > 0): ?>
                 <div class="card shadow">
                                                <div class="card-header">Grade Upload Data</div>
                                                <div class="card-body">
                                                    <?php if(count($grade_docs) > 0): ?>
                                                        <div class="form-group row">
                                                            <label class="control-label col-9 col-md-9">Grade Upload : </label>
                                                            <div class="col-3 col-md-3 text-right">
                                                                <?php $__currentLoopData = $grade_docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div>
                                                                        <a href="<?php echo e(url('/resources/gradefiles/'.$grade->file_name)); ?>" title="" class="" style="color: #0000FF; text-decoration: underline;" target="_blank"><?php echo e($grade->file_name); ?></a>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                </div>
                                            </div>
                                             <?php endif; ?>

            </div>
        </div>
        <div class="text-right"> 
            
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button>
                        <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                        <a class="btn btn-danger btn-xs" href="<?php echo e(url('/admin/Submissions')); ?>" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>