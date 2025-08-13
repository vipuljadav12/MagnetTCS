<?php $__env->startSection('content'); ?>
    <?php echo $data['email_text']; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('emails.maillayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>