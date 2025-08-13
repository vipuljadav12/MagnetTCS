<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="mt-20">
            <div class="card bg-light p-20">
                <div class="">
                  <form method="post" action="<?php echo e(url('/Waitlist/Offers/store')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <input type="hidden" name="submission_id" value="<?php echo e($submission->submission_id); ?>">
                    <input type="hidden" name="version" value="<?php echo e($version); ?>">
                    <?php echo $msg; ?>

                    <div class="row <?php if($second_program == ""): ?> justify-content-center <?php endif; ?>">
                        <div class="col-12 col-lg-4 mb-10"><button type="submit" name="accept_btn" value="<?php echo e($approve_program_id); ?>" class="h-100 pt-10 pb-10 d-flex align-items-center justify-content-center btn-block text-center btn btn-success">Accept Magnet Application Offer</button></div>
                        <div class="col-12 col-lg-4 mb-10"><button type="submit" name="decline_btn" value="<?php echo e($approve_program_id); ?>" title="" class="h-100 pt-10 pb-10 d-flex align-items-center justify-content-center btn-block text-center btn btn-danger">Decline Magnet Application Offer</button></div>
                        <?php if($second_program != ""): ?>
                        <div class="col-12 col-lg-4 mb-10"><button type="submit" name="decline_waitlist" value="<?php echo e($approve_program_id); ?>" class="h-100 pt-10 pb-10 d-flex align-items-center justify-content-center btn-block text-center btn btn-warning">Decline Offer/Choose to be Waitlisted for <?php echo e($second_program); ?> - Grade <?php echo e($submission->next_grade); ?></button></div>
                        <?php endif; ?>
                    </div>
                    <div class="">IMPORTANT : If you do not accept or decline online by <?php echo e(getDateTimeFormat($last_online_date)); ?> or by calling <strong>Tuscaloosa City Schools</strong> by <?php echo e(getDateTimeFormat($last_offline_date)); ?>, your Magnet offer will automatically be DECLINED.</div>
                  </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('Offers::app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>