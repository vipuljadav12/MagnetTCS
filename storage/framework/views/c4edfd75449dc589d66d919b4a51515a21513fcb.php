<?php $__env->startSection('title'); ?>
    Home Zone Report - TCS Magnet
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
.dt-buttons{position: absolute;}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Home Zone Report - TCS Magnet</div></div>
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
                        <option value="duplicatestudent">Student Duplicate Report</option>
                        <option value="homezoneschool" selected>Home Zone Report - TCS Magnet</option>
                        <option value="applicant_outcome">Applicant Outcome</option>    
                        
                    </select>
                </div>
            </div>
            <div class=""><a href="javascript:void(0);" onclick="showReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
        </form>
    </div>
</div>

<div class="">
    <div class="tab-content bordered" id="myTabContent">
        <div class="tab-pane fade show active" id="needs1" role="tabpanel" aria-labelledby="needs1-tab">
            
            <div class="tab-content" id="myTabContent1">
                <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                    <div class="">
                        <div class="card shadow">
                            <div class="card-body">
                               

                                <?php if(!empty($disp_arr)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0 w-100" id="datatable">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">School</th>
                                                <th class="align-middle">Home Zone</th>
                                                <th class="align-middle">Rising Population<br>from Home Zone</th>
                                                <th class="align-middle">Calucated<br>7% Slots</th>
                                                <th class="align-middle">Starting Population</th>
                                                <th class="align-middle">Starting %<br>(based on rising at magnet)</th>
                                                <th></th>
                                                <th class="align-middle">Offered</th>
                                                <th class="align-middle">Offered Population</th>
                                                <th class="align-middle">Offered %</th>
                                                <th></th>
                                                <th class="align-middle">Accepted</th>
                                                <th class="align-middle">Accepted Population</th>
                                                <th class="align-middle">Accepted %</th>
                                                <th></th>
                                                <th class="align-middle">Withdrawls</th>
                                                <th></th>
                                                <th class="align-middle">Final Population</th>
                                                <th class="align-middle">Final %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $disp_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($value['program_name']); ?></td>
                                                    <td><?php echo e($value['school_name']); ?></td>
                                                    <td class="text-center"><?php echo e($value['rising_population']); ?></td>
                                                    <td class="text-center"><?php echo e($value['calculated_7percent']); ?></td>
                                                    <td class="text-center"><?php echo e($value['starting_population']); ?></td>
                                                    <td class="text-center"><?php echo e($value['starting_percent']); ?>%</td>
                                                    <td></td>
                                                    <td class="text-center"><?php echo e($value['offered_count']); ?></td>
                                                    <td class="text-center"><?php echo e($value['offered_population']); ?></td>
                                                    <td class="text-center"><?php echo e($value['offered_percent']); ?>%</td>
                                                    <td></td>
                                                    <td class="text-center"><?php echo e($value['accepted_count']); ?></td>
                                                    <td class="text-center"><?php echo e($value['accepted_population']); ?></td>
                                                    <td class="text-center"><?php echo e($value['accepted_percent']); ?>%</td>
                                                    <td></td>
                                                    <td class="text-center"><?php echo e($value['withdrawn'] > 0 ? "-".$value['withdrawn'] : 0); ?></td>
                                                    <td></td>
                                                    <td class="text-center"><?php echo e($value['accepted_population']-$value['withdrawn']); ?></td>
                                                    <td class="text-center"><?php echo e(number_format(($value['accepted_population']-$value['withdrawn'])*100/$value['rising_population'], 2)); ?>%</td>
                                                    
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                    <div class="table-responsive text-center"><p>No Records found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.buttons.min.js"></script> 
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/buttons.html5.min.js"></script> 
<script type="text/javascript">
    var dtbl_submission_list = $("#datatable").DataTable({
        dom: 'Bfrtip',
        ordering: false,
        order: [],
         buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'HomeZone-TCS Magnet Elementary',
                        text:'Export to Excel'
                    }
                ]
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
            var link = "<?php echo e(url('/')); ?>/admin/Reports/missing/"+$("#enrollment").val()+"/"+$("#reporttype").val();
            document.location.href = link;
        }
    }

    function loadVersionData(value)
    {
            var link = "<?php echo e(url('/')); ?>/admin/Reports/missing/"+$("#enrollment").val()+"/offerstatus/"+value;
            document.location.href = link;

    }
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>