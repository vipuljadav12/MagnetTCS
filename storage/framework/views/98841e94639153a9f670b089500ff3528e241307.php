    <?php global $hidefurther  ?>
    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($hidefurther == "Yes"): ?>
        	<?php $classdisp = "d-custom-hide" ?>
        <?php else: ?>
	        <?php $classdisp = "" ?>
        <?php endif; ?>
        <div class="row <?php echo e($classdisp); ?>">
            <div class="col-12">
                <?php echo getFieldByTypeandId($row->type,$row->id,$row->form_id, $page_id);; ?>

            </div>
        </div>
        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
