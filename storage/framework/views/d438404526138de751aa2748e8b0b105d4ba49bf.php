<?php $__env->startSection('title'); ?>Process Selection | <?php echo e(config('APP_NAME',env("APP_NAME"))); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style type="text/css">
    .buttons-excel{display: none !important;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Waitlist Process Selection <span class="font-16">[<?php echo e(getDateTimeFormat($version_data->created_at)); ?>]</span></div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="<?php echo e(url('/admin/Reports/process/logs')); ?>" title="Go Back">Go Back</a></div>

        </div>
    </div>
    
    <form class="">
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
                <div class="" style="height: 657px; overflow-y: auto;">
                    <div class="table-responsive " id="table-wrap">
                        <table class="table" id="tbl_population_changes">
                            <thead>
                                <tr>
                                    <th class="">Program Name</th>
                                    <th class="">Max Capacity</th>
                                    <th class="">Starting Available Slot</th>
                                    <?php if(isset($race_ary)): ?>
                                        <?php $__currentLoopData = $race_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $race=>$tmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th class=""><?php echo e($race); ?></th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <th class="">Total Offered</th>
                                    <th class="">Ending Available Slots</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($data_ary)): ?>
                                    <?php $__currentLoopData = $data_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php
                                            $available_seats = $value['available_seats'] ?? 0;
                                        ?>
                                        
                                        <td class=""><?php echo e(getProgramName($value['program_id'])); ?> - Grade <?php echo e($value['grade']); ?></td>
                                        <td class="text-center"><?php echo e($value['total_seats'] ?? 0); ?></td>
                                        <td class="text-center"><?php echo e($available_seats); ?></td>
                                        <?php if(isset($race_ary)): ?>
                                            <?php
                                                $total_offered = 0;
                                            ?>
                                            <?php $__currentLoopData = $race_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $race=>$tmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $offered_value = $value['race_count'][$race] ?? 0;
                                                    $total_offered += $offered_value;
                                                ?>
                                                <td class="text-center"><?php echo e($offered_value); ?></td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                        <td class="text-center"><?php echo e($total_offered); ?></td>
                                        <td class="text-center"><?php echo e($available_seats - $total_offered); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <div class="d-flex flex-wrap justify-content-between mt-20"><a href="javascript:void(0);" class="btn btn-secondary" title="" id="ExportReporttoExcel">Download Population Changes</a>
                       </div>
            </div>


        </div>
    </form>    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Committing submission status.<br>It will take approx 2 minutes to update all records. </div></div>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var dtbl_submission_list = $("#tbl_population_changes").DataTable({"aaSorting": [],
             dom: 'Bfrtip',
             bPaginate: false,
             bSort: false,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'PopulationChanges',
                        text:'Export to Excel'
                    }
                ]
            });

            $("#ExportReporttoExcel").on("click", function() {
                dtbl_submission_list.button( '.buttons-excel' ).trigger();
            });

            
            function updateFinalStatus()
            {
                $("#wrapperloading").show();
                $.ajax({
                    url:'<?php echo e(url('/admin/Waitlist/Accept/list')); ?>',
                    type:"post",
                    data: {"_token": "<?php echo e(csrf_token()); ?>"},
                    success:function(response){
                        alert("Status Allocation Done.");
                        $("#wrapperloading").hide();
                        document.location.reload();

                    }
                })
            }


    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>