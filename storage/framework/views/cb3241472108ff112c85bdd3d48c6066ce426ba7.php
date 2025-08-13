<?php $__env->startSection('title'); ?>
	Missing Writing Prompt Report
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
.dt-buttons {position: absolute !important; padding-top: 5px !important;}

</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Missing Writing Prompt Report</div></div>
    </div>
    <div class="card shadow">
        <?php echo $__env->make("Reports::display_report_options", ["selection"=>$selection, "enrollment"=>$enrollment, "enrollment_id"=>$enrollment_id], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>

    <div class="">
        <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                        <div class="">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class=" mb-10">
                                        <?php if(count($programs) > 0): ?>
                                            <div class="form-group">
                                                 <label for="">Select Program to Download Report : </label> 
                                                <div class="">
                                                    <select class="form-control custom-select" id="wp_program">
                                                        <option value="" selected>Select Program</option>
                                                        <option value="all">All Programs</option>
                                                        <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <option value="<?php echo e($program->id); ?>"><?php echo e($program->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-center">No submission(s) found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    
    
	<script type="text/javascript">

        $(document).on('change', '#wp_program', function() {
            if ($(this).val() != '') {
                extra = '?req_program='+$(this).val();
                var link = "<?php echo e(url('/')); ?>/admin/Reports/missing/"+$("#enrollment_option").val()+"/writing_prompt"+extra;
                document.location.href = link;
            }
        }); 

	</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>