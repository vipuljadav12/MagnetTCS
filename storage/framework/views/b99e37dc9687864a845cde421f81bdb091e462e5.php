<?php $__env->startSection('title'); ?>
    Duplicate Student Report
<?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
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
    .custom-select2{
    margin: 5px !important;
}
.dt-buttons {position: absolute !important; padding-top: 5px !important;}

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5"> Student Duplicate Report</div>
        <div class=""><a class=" btn btn-secondary btn-sm" href="<?php echo e(url('/admin/Reports/missing')); ?>" title="Go Back">Go Back</a></div>

    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <form class="">
            <div class="form-group">
                <label for="">Enrollment Year : </label>
                <div class="">
                    <select class="form-control custom-select" id="enrollment">
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
                    <select class="form-control custom-select" id="reporttype">
                        <option value="">Select Report</option>
                        <option value="offerstatus">Offer Status Report</option>
                        <option value="student" selected>Student Duplicate Report</option>
                        <option value="homezoneschool">Home Zone Report - TCS Magnet</option>
                        <option value="applicant_outcome">Applicant Outcome</option>
                    </select>
                </div>
            </div>
            <div class=""><a href="javascript:void(0);" onclick="showReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
        </form>
    </div>
</div>
<div class="card shadow">
        <div class="card-body">
            <div class="row col-md-12 pull-left pb-10">
                <select class="form-control custom-select" onchange="reloadData(this.value)"> 
                    <option value="0" <?php if($type==0): ?> selected <?php endif; ?>>Submissions</option>
                    <option value="1" <?php if($type==1): ?> selected <?php endif; ?>>Late Submissions</option>
                </select>
            </div>

            <div class="row col-md-12 pull-left" id="submission_filters"></div>

            <?php if(!empty($dispData)): ?>
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="datatable">
                    <thead>
                        <tr>
                            <th class="align-middle">#</th>
                            <th class="align-middle">Submission ID</th>
                            <th class="align-middle">State ID</th>
                            <th class="align-middle">First Name</th>
                            <th class="align-middle">Last Name</th>
                            <th class="align-middle">Next Grade</th>
                            <th class="align-middle">Current School</th>
                            <th class="align-middle">Program Choice 1</th>
                            <th class="align-middle">Program Choice 2</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        <?php $__currentLoopData = $dispData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $count = 0 ?>
                            <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skey=>$svalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    
                                        <td class="text-center" style="vertical-align: middle;" rowspan="<?php echo e(count($value)); ?>"><?php echo e($key+1); ?></td>
                                    
                                    <td class="text-center"><a href="<?php echo e(url('/')); ?>/admin/Submissions/edit/<?php echo e($svalue['submission_id']); ?>"><?php echo e($svalue['submission_id']); ?></a></td>
                                    <td class="text-center"><?php echo e($svalue['student_id']); ?></td>
                                    <td class="text-center"><?php echo e($svalue['first_name']); ?></td>
                                    <td class="text-center"><?php echo e($svalue['last_name']); ?></td>
                                    <td class="text-center"><?php echo e($svalue['next_grade']); ?></td>
                                    <td class="text-center"><?php echo e($svalue['current_school']); ?></td>
                                    <td class="text-center"><?php echo e($svalue['first_program']); ?></td>
                                    <td class="text-center"><?php echo e($svalue['second_program']); ?></td>
                                    <td class="text-center">
                                        <?php if($svalue['submission_status']  == "Active" || $svalue['submission_status'] == "Offered and Accepted"): ?>
                                            <?php $class = "alert-success" ?>
                                        <?php elseif($svalue['submission_status'] == "Auto Decline"): ?>
                                            <?php $class = "alert-secondary" ?>
                                        <?php elseif($svalue['submission_status'] == "Application Withdrawn" || $svalue['submission_status'] == "Offered and Declined" || $svalue['submission_status'] == "Denied due to Ineligibility"): ?>
                                            <?php $class = "alert-danger" ?>
                                        <?php elseif($svalue['submission_status'] == "Denied due to Incomplete Records"): ?>
                                            <?php $class = "alert-info" ?>
                                        <?php else: ?>
                                            <?php $class = "alert-warning" ?>
                                        <?php endif; ?>
                                        <div class="alert1 <?php echo e($class); ?> p-10 text-center d-block"><?php echo e($svalue['submission_status']); ?></div>
                                    </td>
                                    <td class="text-center"><?php echo e($svalue['created_at']); ?></td>
                                </tr>
                                <?php $count++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="table-responsive text-center"><p>No Records found.</div>
            <?php endif; ?>
        </div>
    </div>
    

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
 <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/buttons.html5.min.js"></script>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.rowsGroup.js"></script>

<script type="text/javascript">

    var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
        dom: 'Bfrtip',
        // bPaginate: false,
         bSort: false,
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'DuplicateSudents',
                text:'Export to Excel'
            }
        ],
        "rowsGroup":[0]
    }); 
    

    function showReport()
    {
        if($("#enrollment").val() == "")
        {
            alert("Please select enrollment year");
        }
        else if($("#reporttype").val() == "")
        {
            alert("Please select report type");
        }
        else
        {
            var link = "<?php echo e(url('/')); ?>/admin/Reports/missing/<?php echo e(Session::get("enrollment_id")); ?>/"+$("#reporttype").val();
            document.location.href = link;
        }
    }

    function reloadData(val)
    {
        var link = "<?php echo e(url('/')); ?>/admin/Reports/missing/"+$("#enrollment").val()+"/duplicatestudent/"+val;
        document.location.href = link;
    }
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>