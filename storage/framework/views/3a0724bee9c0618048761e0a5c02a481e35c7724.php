<?php global $hidefurther ?>
<div class="table-responsive">
	<?php $prevFormData = Session::get('form_data') ?>
    
    <?php $fieldSequence = getFieldSequence($field_id) ?>
    <?php if(!empty($fieldSequence)): ?>
        <?php $displayArr = array() ?>
        <?php $fieldArr = $prevFormData[0]['formdata'] ?>
        <?php $__currentLoopData = $fieldSequence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($fieldArr[$value->id])): ?>
                <?php $displayArr[$value->id] = $fieldArr[$value->id] ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php else: ?>
        <?php $displayArr = $prevFormData[0]['formdata'] ?>
    <?php endif; ?>

	     <table class="table table-striped table-bordered">
            <tbody>
                <?php $zoned_school_div = false ?>
            	<?php $__currentLoopData = $displayArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key1=>$value1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $fieldtype = getFormElementType($key1) ?>

                        <?php $display = true ?>
                        <?php if(is_array($value1) && $value1[0]==""): ?>
                            <?php $display = false ?>
                        <?php elseif($value1 == ""): ?>
                            <?php $display = false ?>
                        <?php endif; ?>
                        
                        <?php if(strtolower(getFormElementLabel($key1)) == "zoned school"): ?>
                            <?php $zoned_school_div = true ?>
                        <?php endif; ?>
                        
                        <?php $display = getViewEnable($key1) ?>
                        <?php if($display && $fieldtype != "termscheck"): ?>
                             <tr>
                                <td class="b-600 w-110"><?php echo e(getFormElementLabel($key1)); ?> :</td>
                                <td class="">
                                    <?php if($fieldtype == "date"): ?>
                                        <?php $tmpdate = explode("-", $value1) ?>
                                        <?php echo e(" ".date("F", mktime(0, 0, 0, $tmpdate[1], 10))." ".date("d", mktime(0, 0, 0, 0, $tmpdate[2])).", ".$tmpdate[0]); ?> 
                                    <?php else: ?>
                                        <?php echo e((is_array($value1) ? $value1[0] : $value1)); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                     <?php if(!$zoned_school_div): ?>
                            <tr id="zoned_school_div" class="d-none">
                                <td class="b-600 w-110">Zoned School :</td>
                                <td class="" id="zoned_school_val">
                                </td>
                            </tr>                        
                        <?php endif; ?>
            </tbody>
        </table>
        <?php $student_id = fetch_student_field_id($prevFormData[0]['form_id']) ?>
        <?php if(isset($prevFormData[0]['formdata'][$student_id])): ?>
            <?php $hidefurther = "Yes"; ?>
            <div class="form-group d-flex flex-wrap justify-content-between" id="correctdiv">
                    <a href="<?php echo e(url('/incorrectinfo/'.$prevFormData[0]['formdata'][$student_id])); ?>" class="btn btn-danger w-200" title="Incorrect Information"><i class="fa fa-times pr-5"></i>  Incorrect Information</a>
                     <button type="button" class="btn btn-success step-2-2-btn w-200" title="" value="Correct Information" id="correctinfo" onclick="showHideCorrect()">Correct Information  <i class="fa fa-check pl-5"></i></button>
            </div>
        <?php endif; ?>
    </div>
                            