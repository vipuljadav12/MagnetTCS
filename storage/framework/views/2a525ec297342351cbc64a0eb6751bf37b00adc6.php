<?php $__env->startSection('title'); ?>Student Profile Eligibility <?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Student Profile Eligibility [<?php echo e($data['program']->name); ?>]</div>
        <div class="">
            <a href="<?php echo e(url($module_url)); ?>/create" class="btn btn-sm btn-success" title="">Add</a>
            <a href="<?php echo e(url('admin/SetEligibility/edit/'.$data['program_id'])); ?>" class="btn btn-sm btn-secondary" title="">Back</a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="tbl_data">
                <thead>
                    <tr>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Grade Level</th>
                        
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $data['sp_eligibilities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class=""><?php echo e($value->name); ?></td>
                            <td class=""><?php echo e($value->grade); ?></td>
                            <td class="text-center">
                                <a href="<?php echo e(url($module_url)); ?>/edit/<?php echo e($value->id); ?>" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                <a href="javascript:void(0)" onclick="deletefunction(<?php echo e($value->id); ?>)" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="text-center">No data found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#tbl_data").DataTable({
                'order': [],
                'columnDefs': [{
                    'targets': [1, 2],
                    'orderable': false
                }]
            });
        });
        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to delete this Student Profile Eligibility?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '<?php echo e(url($module_url)); ?>/delete/'+id;
            });
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>