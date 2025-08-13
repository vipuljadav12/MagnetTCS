<div class="card-body">
    <form class="">
        <div class="form-group">
            <label for="">Enrollment Year : </label>
            <div class="">
                <select class="form-control custom-select" id="enrollment_option">
                    <option value="">Select Enrollment Year</option>
                    <?php $__currentLoopData = $enrollment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value->id); ?>" <?php if($enrollment_id == $value->id): ?> selected <?php endif; ?>><?php echo e($value->school_year); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="">Report : </label>
            <div class="">
                <?php $report_arr = Config::get('variables.reportArr') ?>
				<select class="form-control custom-select" id="reporttype">
				    <option value="">Select Report</option>
				    <?php $__currentLoopData = $report_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rk=>$rv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				    	<option value="<?php echo e($rk); ?>" <?php if($selection == $rk): ?> selected <?php endif; ?>><?php echo e($rv); ?></option>
				    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</select>
            </div>
        </div>
        <div class=""><a href="javascript:void(0);" onclick="showMissingReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
    </form>
</div>