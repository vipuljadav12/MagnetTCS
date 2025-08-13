<?php $__env->startSection('title'); ?>
	Processing Log Report
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
            <div class="page-title mt-5 mb-5">Process Log Report</div></div>
    </div>


  <div class="card shadow" id="response">
        <div class="card-body">

                                     <div class="table-responsive">
                                        <table class="table table-striped mb-0 w-100" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Sr. No.</th>
                                                    <th class="align-middle">Date & Time</th>
                                                    <th class="align-middle">Enrollment Period</th>
                                                    <th class="align-middle notexport">Application Name</th>
                                                    <th class="align-middle">Processing Type</th>
                                                    <th class="align-middle">Offer Acceptance Deadline</th>
                                                    <th class="align-middle">Population Changes</th>
                                                    <th class="align-middle">Submission Results</th>
                                                    <th class="align-middle">Seats Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    <?php $count =1 ?>
                                                     <?php $__currentLoopData = $late_submission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo e($count); ?></td>
                                                            <td class="">
                                                                <div class=""><?php echo e(getUserName($value->generated_by)); ?></div>
                                                                <div class=""><?php echo e(getDateTimeFormat($value->created_at)); ?></div>
                                                            </td>
                                                            <td><?php echo e(getEnrollmentYear($value->enrollment_id)); ?></td>
                                                            <td><?php echo e(get_form_name($value->application_id)); ?></td>
                                                            <td>Late Submission</td>
                                                            <td><?php echo e(getDateTimeFormat($value->last_date_online_acceptance)); ?></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/LateSubmission/Population/Version/'.$value->application_id.'/'.$value->version)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/LateSubmission/Submission/Result/Version/'.$value->application_id.'/'.$value->version)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/LateSubmission/SeatsStatus/Version/'.$value->id)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                             
                                                        </tr>
                                                        <?php $count++ ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                     <?php $__currentLoopData = $waitlist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo e($count); ?></td>
                                                            <td class="">
                                                                <div class=""><?php echo e(getUserName($value->generated_by)); ?></div>
                                                                <div class=""><?php echo e(getDateTimeFormat($value->created_at)); ?></div>
                                                            </td>
                                                            <td><?php echo e(getEnrollmentYear($value->enrollment_id)); ?></td>
                                                            <td><?php echo e(findFormName($value->application_id)); ?></td>
                                                            <td>Process Waitlist</td>
                                                            <td><?php echo e(getDateTimeFormat($value->last_date_online_acceptance)); ?></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/Waitlist/Population/Version/'.$value->application_id.'/'.$value->version)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/Waitlist/Submission/Result/Version/'.$value->application_id.'/'.$value->version)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/Waitlist/SeatsStatus/Version/'.$value->id)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            
                                                        </tr>
                                                        <?php $count++ ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                    <?php $__currentLoopData = $regular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                             <td class="text-center"><?php echo e($count); ?></td>
                                                            <td class="">
                                                                <div class=""><?php echo e($value->updated_by); ?></div>
                                                                <div class=""><?php echo e(getDateTimeFormat($value->created_at)); ?></div>
                                                            </td>
                                                            <td><?php echo e(getEnrollmentYear($value->enrollment_id)); ?></td>
                                                            <td><?php echo e(findFormName($value->application_id)); ?></td>
                                                            <td>Process Selection</td>
                                                            <td><?php echo e(getDateTimeFormat($value->last_date_online_acceptance)); ?></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/Process/Selection/Population')); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/Reports/missing/'.$value->enrollment_id.'/results/'.$value->application_id)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="<?php echo e(url('/admin/Reports/missing/'.$value->enrollment_id.'/seatstatus/'.$value->application_id)); ?>" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            
                                                        </tr>
                                                        <?php $count++ ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                                
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 2 minutes to finish. </div></div>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/buttons.html5.min.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>