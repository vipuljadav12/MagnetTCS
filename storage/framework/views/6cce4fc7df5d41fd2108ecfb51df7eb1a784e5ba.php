<?php $__env->startSection('title'); ?>
	Selection Report Master
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
.dt-buttons {position: absolute !important;}
.w-50{width: 50px !important}
.content-wrapper.active {z-index: 9999 !important}
</style>
<input type="hidden" id="application_id" value="<?php echo e($application_id); ?>">
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection</div>
            <?php if($display_outcome == 2): ?>
                <div class=""><a class=" btn btn-secondary btn-sm" href="<?php echo e(url('/admin/Reports/process/logs')); ?>" title="Go Back">Go Back</a></div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="">
            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <?php if(isset($type) && $type == "update"): ?>
                                        <div class="d-flex flex-wrap justify-content-between mt-20 mb-20"><?php if($display_outcome == 0): ?> <a href="javascript:void(0);" class="btn btn-success" title="" onclick="updateFinalStatus()">Accept Outcome and Commit Result</a> <?php else: ?> <a href="javascript:void(0);" class="btn btn-danger d-none" title="" onclick="alert('Already Outcome Commited')">Accept Outcome and Commit Result</a>  <?php endif; ?>
           </div>
           <?php endif; ?>
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="preview02-tab" data-toggle="tab" href="#preview02" role="tab" aria-controls="preview02" aria-selected="true">Magnet Programs</a></li>

            <li class="nav-item"><a class="nav-link" id="preview05-tab" data-toggle="tab" href="#preview05" role="tab" aria-controls="preview05" aria-selected="true">Magnet Table</a></li>

            <li class="nav-item"><a class="nav-link" id="preview03-tab" data-toggle="tab" href="#preview03" role="tab" aria-controls="preview03" aria-selected="true">Central IB Programs</a></li>


            <li class="nav-item"><a class="nav-link" id="preview04-tab" data-toggle="tab" href="#preview04" role="tab" aria-controls="preview04" aria-selected="true">TASPA & TFA  Programs</a></li>

        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                <?php echo $__env->make('ProcessSelection::Template.display_data_magnet', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class="tab-pane fade show" id="preview05" role="tabpanel" aria-labelledby="preview05-tab">
                <div class="">
                    <div class="">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <?php echo $popHTML; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
                <?php echo $__env->make('ProcessSelection::Template.display_data_ib', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class="tab-pane fade show" id="preview04" role="tabpanel" aria-labelledby="preview04-tab">
                <?php echo $__env->make('ProcessSelection::Template.display_data_audition', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        </div>

        <?php if(isset($type) && $type == "update"): ?>
        <div class="d-flex flex-wrap justify-content-between mt-20 d-none"><?php if($display_outcome == 0): ?> <a href="javascript:void(0);" class="btn btn-success" title="" onclick="updateFinalStatus()">Accept Outcome and Commit Result</a> <?php else: ?> <a href="javascript:void(0);" class="btn btn-danger" title="" onclick="alert('Already Outcome Commited')">Accept Outcome and Commit Result</a>  <?php endif; ?>
           </div>
           <?php endif; ?>
        
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
		//$("#datatable").DataTable({"aaSorting": []});
        var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
            "bSort" : false,
             "dom": 'Bfrtip',
             "autoWidth": true,
             "iDisplayLength": 50,
            // "scrollX": true,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reports',
                        text:'Export to Excel',
                        //Columns to export
                        exportOptions: {
                                columns: "thead th:not(.d-none)"
                        }
                    }
                ]
            });

        var dtbl_submission_list1 = $("#datatable1").DataTable({"aaSorting": [],
            "bSort" : false,
             "dom": 'Bfrtip',
             "autoWidth": true,
             "iDisplayLength": 50,
            // "scrollX": true,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reports',
                        text:'Export to Excel',
                        //Columns to export
                        exportOptions: {
                                columns: "thead th:not(.d-none)"
                        }
                    }
                ]
            });

var dtbl_submission_list2 = $("#datatable2").DataTable({"aaSorting": [],
            "bSort" : false,
             "dom": 'Bfrtip',
             "autoWidth": true,
             "iDisplayLength": 50,
            // "scrollX": true,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reports',
                        text:'Export to Excel',
                        //Columns to export
                        exportOptions: {
                                columns: "thead th:not(.d-none)"
                        }
                    }
                ]
            });


$(document).ready(function()
{
    var dtbl_submission_list5 = $("#datatable4").DataTable({"aaSorting": [],
            "bSort" : false,
             "dom": 'Bfrtip',
             "autoWidth": true,
             "iDisplayLength": 50,
            // "scrollX": true,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reports',
                        text:'Export to Excel',
                        //Columns to export
                        exportOptions: {
                                columns: "thead th:not(.d-none)"
                        }
                    }
                ]
            });
})

        function updateFinalStatus()
            {
                $("#wrapperloading").show();
                $.ajax({
                    url:'<?php echo e(url('/admin/Waitlist/Accept/list/'.$application_id)); ?>',
                    type:"post",
                    data: {"_token": "<?php echo e(csrf_token()); ?>", "application_id": $("#application_id").val()},
                    success:function(response){
                        alert("Status Allocation Done.");
                        $("#wrapperloading").hide();
                        document.location.href = "<?php echo e(url('/admin/Waitlist/Population/Version/'.$application_id.'/'.$actual_version)); ?>";

                    }
                })
            }


	</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>