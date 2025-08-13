<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="mt-20">
            <div class="card aler alert-success p-20 pt-lg-50 pb-lg-150">
                <?php echo $msg; ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('Offers::app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>