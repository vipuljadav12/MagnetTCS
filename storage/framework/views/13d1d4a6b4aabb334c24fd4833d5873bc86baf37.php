<?php $__env->startSection('content'); ?>
        <div class="mt-20 pt-50">
        <div class=""  id="printmsg">
          <div class="col-12 text-center"><h2><?php echo $confirm_title; ?></h2></div>
              <?php if($student_type=="active"): ?>
                <?php $class = "alert-success" ?>
              <?php else: ?>
                <?php $class = "alert-danger" ?>
              <?php endif; ?>
              <div class="alert <?php echo e($class); ?> text-center mt-20"><strong><?php echo $confirm_subject; ?></strong></div>

              <?php if(isset($instructions) && $instructions != ''): ?>
                   <div class="card bg-light p-20 mb-20">
                        <div class="">
                          <?php echo $instructions; ?>

                        </div>
                      </div>

              <?php endif; ?>
        <div class="card bg-light p-20">
           
            <div class="">
              

            	<?php if(isset($confirm_msg)): ?>
            		<?php
            			$confirm_msg = str_replace("###CONFIRMATION_NO###", (isset($confirmation_no) ? $confirmation_no : ""), $confirm_msg);
            			$confirm_msg = str_replace("###STARTOVER###", "<a hrer='".url('/')."' class='btn btn-primary'>START OVER</a>", $confirm_msg);

            		?>
            		<?php echo $confirm_msg; ?>

            	<?php endif; ?>
            </div>
        </div>
      </div>

         <div class="form-group d-flex flex-wrap justify-content-between pt-20">
              <label class="control-label col-12 col-md-4 col-xl-3 pl-0">
                <?php if(Session::has("from_admin")): ?>
                  <button type="button" class="btn btn-secondary" title="" value="Exit Application" onclick="document.location.href='<?php echo e(url('/phone/submission')); ?>'"><i class="fa fa-backward"></i>  Exit Application</button>
                <?php else: ?>
                  <button type="button" class="btn btn-secondary" title="" value="Exit Application" onclick="document.location.href='<?php echo e(url('/')); ?>'"><i class="fa fa-backward"></i>  Exit Application</button>
                <?php endif; ?>
              </label>
              <button onclick="document.location.href='<?php echo e(url('/print/application/'.$confirmation_no)); ?>'" class="btn btn-secondary step-2-2-btn" value="Print Application"><i class="fa fa-print"></i> Print</button>
          </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
  $(document).ready(function () {
    function disableBack() {window.history.forward()}

    window.onload = disableBack();
    window.onpageshow = function (evt) {if (evt.persisted) disableBack()}
});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>