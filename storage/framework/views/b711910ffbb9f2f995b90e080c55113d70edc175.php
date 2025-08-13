<?php $academic_calc = array() ?>
<?php $__currentLoopData = $eligibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekey=>$evalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(strtolower($evalue->eligibility_ype) == "academic grades"): ?>
        <?php $academic_calc = getEligibilityContent1($evalue->assigned_eigibility_name) ?>
    <?php endif; ?>
    <?php if(strtolower($evalue->eligibility_ype) == "academic grade calculation"): ?>
        <?php 
            $academic_grade_calc = getEligibilityContent1($evalue->assigned_eigibility_name)
        ?>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php if(empty($academic_calc)): ?>
    <?php $__currentLoopData = $eligibilities_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekey=>$evalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(strtolower($evalue->eligibility_ype) == "academic grades"): ?>
            <?php $academic_calc = getEligibilityContent1($evalue->assigned_eigibility_name) ?>
        <?php endif; ?>
        <?php if(strtolower($evalue->eligibility_ype) == "academic grade calculation"): ?>
            <?php 
                $academic_grade_calc = getEligibilityContent1($evalue->assigned_eigibility_name)
            ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php $content = $academic_calc ?? null ?>
        <?php $scoring = $academic_calc->scoring ?? null ?>
        <?php echo $__env->make("Submissions::template.submission_academic_grades_with_calc", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>