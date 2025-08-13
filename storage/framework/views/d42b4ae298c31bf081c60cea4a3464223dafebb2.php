<?php $__env->startSection('title'); ?>Eligibility Master <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Eligibility Master</div>
            <div class="">
                 <?php if(Session::get("district_id") != "0"): ?>
                    <a href="<?php echo e(url('admin/Eligibility/create')); ?>" class="btn btn-sm btn-secondary" title="">Add Eligibility</a>
                    <a href="<?php echo e(url('admin/Eligibility/subjectManagement')); ?>" class="btn btn-sm btn-info" title="">Set Grade/Subject Options</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="align-middle">Eligibility Name</th>
                        <th class="align-middle">Eligibility Type</th>
                        <th class="align-middle">Eligibility Alias NAme</th>

                        <th class="align-middle text-center w-120">Status</th>
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($eligibilities)): ?>
                       <?php $__currentLoopData = $eligibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$eligibility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <tr>
                               <td class=""><?php echo e($eligibility->name); ?></td>
                               <td class=""><?php echo e($eligibilityTemplates[$eligibility->template_id]['name'] ?? "Template 2"); ?></td>
                               <td class=""><?php echo e($eligibility->alias_name); ?></td>
                               <td class="text-center"><input id="<?php echo e($eligibility->id); ?>" type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" <?php echo e(isset($eligibility->status)&&$eligibility->status=='Y'?'checked':''); ?> /></td>
                               <td class="text-center">
                                   <a href="<?php echo e(url('admin/Eligibility/edit',$eligibility->id)); ?>" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                    
                                    <a href="<?php echo e(url('admin/Eligibility/view',$eligibility->id)); ?>" class="font-18 ml-5 mr-5" target="_blank" title=""><i class="far fa-eye"></i></a>
                                    
                                   <a href="javascript:void(0);" onclick="deletefunction(<?php echo e($eligibility->id); ?>)" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a></td>
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
    
    <script src="<?php echo e(asset('resources/assets/common/js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('resources/assets/common/js/additional-methods.min.js')); ?>"></script>
    <!-- Sweet Alert -->
    <script src="<?php echo e(url('/resources/assets/plugins/sweet-alert2/sweetalert2.min.js')); ?>"></script>
    <script src="<?php echo e(url('/resources/assets/plugins/sweet-alert2/jquery.sweet-alert.init.js')); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".alert").delay(2000).fadeOut(1000);
            $('#datatable').DataTable({
                'columnDefs': [ {
                    'targets': [2,3], // column index (start from 0)
                    'orderable': false, // set orderable false for selected columns,

                }],
                "order": []
            });
            //Buttons examples
            var table = $('#datatable-buttons').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'colvis'],
            });
            table.buttons().container()
                .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
        });
        $('.status').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $.ajax({
                type: "get",
                url: '<?php echo e(url('admin/Eligibility/status')); ?>',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this Eligibility to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '<?php echo e(url('/')); ?>/admin/Eligibility/delete/'+id;
            });
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>