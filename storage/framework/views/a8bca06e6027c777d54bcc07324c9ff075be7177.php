<?php if(Session::has("success")): ?>
	<div class="alert alert-success"> <?php echo e(Session::get("success")); ?></div>
<?php endif; ?>
<?php if(Session::has("warning")): ?>
	<div class="alert alert-warning"> <?php echo e(Session::get("warning")); ?></div>
<?php endif; ?>
<?php if(Session::has("error")): ?>
	<div class="alert alert-danger"> <?php echo e(Session::get("error")); ?></div>
<?php endif; ?>
