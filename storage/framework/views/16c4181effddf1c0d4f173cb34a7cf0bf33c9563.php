<?php $__env->startSection('title', "Enrollment Periods"); ?>

<?php $__env->startSection('styles'); ?>
    
    <style type="text/css">
        .error{
            color: red;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">New Enrollment Period</div>
            <div class="">
                <?php if((checkPermission(Auth::user()->role_id,'Enrollment/create') == 1)): ?>
                    <?php if(Session::get('district_id') != 0): ?>
                        <a href="<?php echo e(url('admin/Enrollment/create')); ?>" class="btn btn-sm btn-secondary" title="Add">Add Enrollment</a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo e(url('admin/Enrollment/trash')); ?>" class="btn btn-sm btn-danger d-none" title="Trash">Trash</a>
            </div> 
        </div>
    </div>
    <?php echo $__env->make('layouts.admin.common.alerts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="card shadow">
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">School Year</th>
                            <th class="text-center">Confirmation Style</th>
                            <th class="text-center">Beginning Date</th>
                            <th class="text-center">Ending Date</th>
                            <th class="text-center w-120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($enrollments)): ?>
                            <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e($enrollment->school_year); ?></td>
                                    <td class="text-center"><?php echo e($enrollment->confirmation_style); ?></td>
                                    <td class="text-center"><?php echo e(getDateFormat($enrollment->begning_date)); ?></td>
                                    <td class="text-center"><?php echo e(getDateFormat($enrollment->ending_date)); ?></td>
                                    <td class="text-center">
                                        <?php if((checkPermission(Auth::user()->role_id,'Enrollment/edit') == 1)): ?>
                                            <a href="<?php echo e(url('admin/Enrollment/edit/'.$enrollment->id)); ?>" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript">
        
        $('.table').DataTable({
            // 'order': [],
            'columnDefs': [{
                'targets': [2,3],
                'orderable': false
            }]
        });

        $(document).on('change', '#status', function(){
            $.ajax({
                type: 'post',
                url: '<?php echo e(url('admin/Enrollment/update_status')); ?>',
                data: {
                    '_token': "<?php echo e(csrf_token()); ?>",
                    'id': $(this).val(),
                    'status': $(this).prop('checked')==true?'Y':'N'
                }
            });
        });

        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Enrollment Period to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '<?php echo e(url('/')); ?>/admin/Enrollment/move_to_trash/'+id;
            });
        };

    </script>
<?php $__env->stopSection(); ?>   
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>