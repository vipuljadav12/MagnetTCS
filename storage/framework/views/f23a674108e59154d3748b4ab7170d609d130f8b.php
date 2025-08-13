<div class="">
    <div class="card shadow">
        <div class="card-header">Home Zone Schools Enrollment Data for Current Enrollment</div>
        <div class="card-body">
            <div class="table-responsive">
                <?php $__currentLoopData = $school_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card shadow">
                        <div class="card-header">
                            <div class=""><?php echo e($school['name']); ?><input name="school[]" type="hidden" value="<?php echo e($school['id']); ?>" class="form-control">
                            </div>
                        </div>
                        <div class="card-body"> 
                            <div class="row margin"> 
                                <?php $__currentLoopData = $school['grade_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gk=>$gval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-2">
                                        <div>Rising Grade <?php echo e($gval['grade']); ?><input name="grade[<?php echo e($school['id']); ?>][]" type="hidden" class="form-control" value="<?php echo e($gval['grade']); ?>"></div>
                                        <div><input name="total[<?php echo e($school['id']); ?>][<?php echo e($gval['grade']); ?>]" type="text" class="form-control" value="<?php echo e($gval['total']); ?>"></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>