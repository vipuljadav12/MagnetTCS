<?php $__env->startSection('title'); ?>Process Selection | <?php echo e(config('APP_NAME',env("APP_NAME"))); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<form action="<?php echo e(url('admin/Process/Selection/store')); ?>" method="post" name="process_selection" id="process_selection">
    <?php echo e(csrf_field()); ?>

        <input type="hidden" name="application_id" value="<?php echo e($application_id); ?>" id="application_id">

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection <span class="text-danger"><?php echo e(getApplicationName($application_id)); ?></span></div>
            <div class="text-right">
                <a href="<?php echo e(url('/admin/Process/Selection/calculate_profile/'.$application_id)); ?>" class="btn btn-secondary">Calculate Profile Score</a>&nbsp;
            <?php if($display_outcome > 0 && Config::get("variables.rollback_process_selection") == 1): ?> <a href="javascript:void(0)" class="btn btn-secondary" onclick="rollBackStatus();">Roll Back Status</a>&nbsp;<?php endif; ?>
                <a href="<?php echo e(url('/admin/Process/Selection')); ?>" class="btn btn-primary">Change Application</a></div>
        </div>
    </div>

    <div class="card shadow">
        
        <div class="card-body">
    <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="preview02-tab" data-toggle="tab" href="#preview02" role="tab" aria-controls="preview02" aria-selected="true">Settings</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                <?php if($display_outcome == 0): ?>
                    <?php echo $__env->make('ProcessSelection::Template.acceptance_window', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('ProcessSelection::Template.acceptance_window1', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>
    </form>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 15 minutes to finish. </div></div>

<?php echo $__env->make("ProcessSelection::common_js", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script type="text/javascript">

    function rollBackStatus()
    {
        $("#wrapperloading").show();
        $.ajax({
            url:'<?php echo e(url('/admin/Process/Selection/Revert/list')); ?>',
            type:"post",
            data: {"_token": "<?php echo e(csrf_token()); ?>", "application_id": $("#application_id").val()},
            success:function(response){
                alert("All Statuses Reverted.");
                document.location.href = "<?php echo e(url('/admin/Process/Selection')); ?>";
                $("#wrapperloading").hide();

            }
        })
    }


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>