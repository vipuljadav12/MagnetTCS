<?php $__env->startSection('title'); ?>
	Missing Submission Recommendation
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style type="text/css">
    .alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    border-radius: 0.25rem;
}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Admin Review</div>
        </div>
    </div>
    <div class="card shadow">
        <?php echo $__env->make("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if(count($programs) > 0): ?>
                <div class="form-group">
                    <label for="">Programs : </label>
                    <div class="">
                        <select class="form-control custom-select" id="recomm_program">
                            <option value="All">All</option>
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e(getProgramName($value)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="">
                    <a href="javascript:void(0);" onclick="exportReport()" class="btn btn-secondary" title="Export Report">Export Missing Recommendation</a>
                </div>
            <?php else: ?>
                    <p class="text-center">No submission(s) found.</p>
            <?php endif; ?>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
	<script type="text/javascript">
        

        function exportReport(){
            var program = $('#recomm_program').val();   
            var enrollment = $(document).find(".enroll").val();
            var link = "<?php echo e(url('/')); ?>/admin/Reports/missing/"+$("#enrollment_option").val()+"/recommendation/export/"+program;
                document.location.href = link;         
        }
	</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>