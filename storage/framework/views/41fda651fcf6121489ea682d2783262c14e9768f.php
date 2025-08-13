<?php $__env->startSection('title'); ?>
    <title>Tuscaloosa City Schools</title>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php if(!Session::has("from_admin")): ?>
        <?php echo $__env->make("layouts.front.common.district_header", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
    
    <?php if(!empty($application_data)): ?>
        <form class="p-20 border mt-20 mb-20" action="<?php echo e(url('/step-1')); ?>" method="post" name="studentstatus_frm" id="studentstatus_frm">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="page_id" value="1">
            <input type="hidden" name="form_id" value="<?php echo e($application_data->form_id); ?>">
            <input type="hidden" name="application_id" value="<?php echo e($application_data->id); ?>">
            <div class="box-0">
                <p class="text-center"><strong>Begin the application here:</strong></p>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-4 col-xl-3 text-right">Student Status : </label>
                    <div class="col-12 col-md-6 col-xl-6">
                        <select class="form-control custom-select student-status" name="student_status" id="student_status">
                            <option value="">Choose an Option</option>
                            
                            <option value="exist"><?php echo e((isset(getConfig()['existing_student']) ? strip_tags(getConfig()['existing_student']) : "Enrolled ".$district->short_name." Student (PreK - 11th Grade)")); ?></option>
                            <option value="new"><?php echo e((isset(getConfig()['new_student']) ? strip_tags(getConfig()['new_student']) : "New ".$district->short_name." Student")); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-4 col-xl-3"></label>
                    <div class="col-12 col-md-6 col-xl-6">
                        <button type="submit" class="btn btn-secondary submit-btn" title="">Submit</button>
                    </div>
                </div>
            </div> 
        </form>  
    <?php else: ?>
        <div class="box-0 text-center p-20 border mt-20 mb-20">
            <div class="form-group">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="b-600 font-14 mb-10 text-danger">No Application is open for submission.</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("scripts"); ?>
	<script type="text/javascript">
		$('#studentstatus_frm').validate({
                rules: {
                    student_status: {
                        required: true,                       
                    }
                },
                messages: {
                    student_status: {
                        required: "Select Student Status"
                    }
                }
            });
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>