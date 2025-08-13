<div class="form-group" >
    <div class="card">
        <div class="card-header">
            
            <?php echo e($data['label'] ?? ""); ?>

        </div>
        <div class="card-body">
            <div class="b-600 font-14 mb-10">First Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        <?php $progam_data = getProgramDropdown() ?>
                        <?php $next_grade_field_id = getNextGradeField($field_id) ?>
                        <?php $current_grade_field_id = getCurrentGradeField($field_id) ?>
                        <?php $current_school_field_id = getCurrentSchoolField($field_id) ?>
                        
                        <?php ($next_grade_name = "") ?>
                        <?php if(Session::has('form_data')): ?>
                            <?php if(isset(Session::get('form_data')[0]['formdata'][$next_grade_field_id])): ?>
                                <?php ($next_grade_name = Session::get('form_data')[0]['formdata'][$next_grade_field_id]) ?>
                                <?php ($current_grade_name = Session::get('form_data')[0]['formdata'][$current_grade_field_id]) ?>
                                <?php ($current_school_name = Session::get('form_data')[0]['formdata'][$current_school_field_id]) ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php $school_count = 0 ?>
                        <?php $__currentLoopData = $progam_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $magnet_school = getMagnetSchool($value->program_id) ?>
                                <?php $max_grade = getMaxGrade($value->program_id) ?>
                                <?php $changed_name = checkCheckedProgram($value->program_id, $current_grade_name, $value->grade_name) ?>
                                <?php if($value->grade_name == $next_grade_name): ?>
                                    <?php if(in_array($current_school_name, $magnet_school)): ?>
                                    <?php else: ?>
                                        <?php $school_count++ ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <select class="form-control custom-select" name="first_choice" id="first_choice">

                            <option value="">Choose an option</option>
                            <?php $__currentLoopData = $progam_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $magnet_school = getMagnetSchool($value->program_id) ?>
                                <?php $max_grade = getMaxGrade($value->program_id) ?>
                                <?php $changed_name = checkCheckedProgram($value->program_id, $current_grade_name, $value->grade_name) ?>
                                <?php if($value->grade_name == $next_grade_name): ?>
                                    <?php if($max_grade <= $current_grade_name): ?>
                                        <?php if($changed_name != ""): ?>
                                            <option value="<?php echo e($value->id); ?>"><?php echo e($changed_name); ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo e($value->id); ?>"><?php echo e($value->program_name." - Grade ".$value->grade_name); ?></option>
                                        <?php endif; ?>
                                    <?php else: ?>

                                        <?php if(in_array($current_school_name, $magnet_school)): ?>
                                        <?php else: ?>
                                            <?php if($changed_name != ""): ?>
                                                <option value="<?php echo e($value->id); ?>"><?php echo e($changed_name); ?></option>
                                            <?php else: ?>
                                                <option value="<?php echo e($value->id); ?>"><?php echo e($value->program_name." - Grade ".$value->grade_name); ?></option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row d-none" id="first_sibling_part_1">
                    <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" value="Yes">
                            <label class="custom-control-label" for="customRadioInline1">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input" checked="" value="No">
                            <label class="custom-control-label" for="customRadioInline2">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row" style="display: none;" id="first_sibling">
                    <label class="col-12 col-lg-4">Sibling State ID# : </label>
                    <div class="col-12 col-lg-6">
                        <input type="text" class="form-control" name="first_sibling" id="first_sibling_field" onblur="checkSibling(this)"><span class="hidden">
                        Checking Sibing State ID <img src="<?php echo e(url('/resources/assets/front/images/loader.gif')); ?>"></span>
                        <span class="first_sibling_label"></span>
                    </div>
                     <div class="col-12 col-md-2 col-xl-2">
                        <span class="help" data-toggle="tooltip" data-html="true" title="If you do not know the sibling's 10-digit state identification number, log into the I-Now Parent Portal to obtain this information. If you need assistance with I-Now access, contact your school office.">
                            <i class="fas fa-question"></i>
                        </span>
                    </div>
                </div>
            </div>
             <?php if(((isset($data['second_display']) && $data['second_display'] == "Yes") || !isset($data['second_display'])) && $school_count > 1): ?>
                    <div class="b-600 font-14 mb-10">Second Program Choice</div>
                    <div class="border p-20 mb-20">
                        <div class="form-group row">
                            <label class="col-12 col-lg-4">Program : </label>
                            <div class="col-12 col-lg-6">
                                
                                <select class="form-control custom-select" name="second_choice" id="second_choice">
                                    <option value="">Choose an option</option>
                                     <?php $__currentLoopData = $progam_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $magnet_school = getMagnetSchool($value->program_id) ?>
                                        <?php $max_grade = getMaxGrade($value->program_id) ?>
                                        <?php $changed_name = checkCheckedProgram($value->program_id, $current_grade_name, $value->grade_name) ?>

                                        <?php if($value->grade_name == $next_grade_name): ?>
                                            <?php if($max_grade <= $current_grade_name): ?>
                                                <?php if($changed_name != ""): ?>
                                                    <option value="<?php echo e($value->id); ?>"><?php echo e($changed_name); ?></option>
                                                <?php else: ?>
                                                    <option value="<?php echo e($value->id); ?>"><?php echo e($value->program_name." - Grade ".$value->grade_name); ?></option>
                                                <?php endif; ?>
                                                <option value="<?php echo e($value->id); ?>"><?php echo e($value->program_name." - Grade ".$value->grade_name); ?></option>
                                            <?php else: ?>
                                                <?php if(in_array($current_school_name, $magnet_school)): ?>
                                                <?php else: ?>
                                                    <?php if($changed_name != ""): ?>
                                                        <option value="<?php echo e($value->id); ?>"><?php echo e($changed_name); ?></option>
                                                    <?php else: ?>
                                                        <option value="<?php echo e($value->id); ?>"><?php echo e($value->program_name." - Grade ".$value->grade_name); ?></option>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>

                            </div>
                        </div>
                        <div class="form-group row d-none" id="second_sibling_part_1">
                            <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                            <div class="col-12 col-lg-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline3" name="customRadioInline" class="custom-control-input" value="Yes">
                                    <label class="custom-control-label" for="customRadioInline3">Yes</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline4" name="customRadioInline" class="custom-control-input" checked value="No">
                                    <label class="custom-control-label" for="customRadioInline4" >No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" style="display: none;" id="second_sibling">
                            <label class="col-12 col-lg-4">Sibling State ID# : </label>
                            <div class="col-12 col-lg-6">
                                <input type="text" class="form-control" name="second_sibling" id="second_sibling_field" onblur="checkSibling(this)">
                                <span class="hidden">
                                Checking Sibing State ID <img src="<?php echo e(url('/resources/assets/front/images/loader.gif')); ?>"></span>
                                <span class="second_sibling_label"></span>

                                
                            </div>
                             <div class="col-12 col-md-2 col-xl-2">
                                <span class="help" data-toggle="tooltip" data-html="true" title="If you do not know the sibling's 10-digit state identification number, log into the I-Now Parent Portal to obtain this information. If you need assistance with I-Now access, contact your school office.">
                                    <i class="fas fa-question"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#customRadioInline3").change(function(){
            if($(this).val()=="Yes")
            {
                $("#second_sibling").show();
            }
        })
        $("#customRadioInline4").change(function(){
            if($(this).val()=="No")
            {
                $("#second_sibling").hide();
                $("#<?php echo e($field_id); ?>_second_sibling").val("");
            }
        })

        $("#customRadioInline1").change(function(){
            if($(this).val()=="Yes")
            {
                $("#first_sibling").show();
            }
        })
        $("#customRadioInline2").change(function(){
            if($(this).val()=="No")
            {
                $("#first_sibling").hide();
                $("#<?php echo e($field_id); ?>_first_sibling").val("");
            }
        })
    })
</script>