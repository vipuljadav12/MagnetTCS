<?php $__env->startSection('title'); ?>
	Application Dates
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Setup Application Dates</div>
        	<div class="">
                <?php if((checkPermission(Auth::user()->role_id,'Application/create') == 1)): ?>
        		      <a href="<?php echo e(url('admin/Application/create')); ?>" class="btn btn-sm btn-secondary" title="Add">Add Application Dates</a>
                <?php endif; ?>
        		<a href="<?php echo e(url('admin/Application/trash')); ?>" class="btn btn-sm btn-danger d-none" title="Trash">Trash</a>
        	</div> 
    	</div>
    </div>
    <div class="card shadow">
        <!--<div class="card-header">Open Enrollment</div>-->
        <div class="card-body">
        <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="table-responsive">
                <table id="datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Application Name</th>
                            <th class="text-center">Parent Submission Form</th>
                            <th class="text-center">Open Enrollment</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-center"><?php echo e($application->application_name); ?></td>
                            <td class="text-center"><?php echo e($application->form_name); ?></td>
                            <td class="text-center"><?php echo e($application->school_year); ?></td>
                            <td class="text-center"><input id="<?php echo e($application->id); ?>" type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" <?php if($application->status=='Y'): ?> checked="" <?php endif; ?>></td>
                            <td class="text-center">
                                <?php if((checkPermission(Auth::user()->role_id,'Application/edit') == 1)): ?>
                            	       <a href="<?php echo e(url('admin/Application/edit',$application->id)); ?>" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if(checkApplicationStatus($application->id)): ?>
                                       <a href="javascript:void(0)" onclick="deletefunction(<?php echo e($application->id); ?>)" class="font-18 ml-5 mr-5 text-danger" title="Delete"><i class="far fa-trash-alt"></i></a>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
	<script type="text/javascript">
		$(".alert").delay(2000).fadeOut(1000);
		$('#datatable').DataTable({
                'columnDefs': [ {
                'targets': [2,3], // column index (start from 0)
                'orderable': false, // set orderable false for selected columns
            }]
        });

        $('.status').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $.ajax({
                type: "get",
                url: '<?php echo e(url('admin/Application/status')); ?>',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });
        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Application to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '<?php echo e(url('/')); ?>/admin/Application/delete/'+id;
            });
        };
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>