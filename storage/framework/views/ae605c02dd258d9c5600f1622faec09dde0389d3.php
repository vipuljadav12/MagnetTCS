<?php $__env->startSection('title'); ?>
    Import AGT priority to New Century
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Import Student Data for Program</div>
            
        </div>
    </div>
    <div class="tab-content bordered" id="myTabContent">
        <div class="content-wrapper-in" id="importagtnch">
            <?php echo $__env->make('layouts.admin.common.alerts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card shadow">
                <div class="card-body">
                    <div class="">Before uploading data, please ensure that there is consistency with the naming of column fields in your "XLS / XLSX" file:<br></div>
                    <div class="pt-10">
                        <a href="<?php echo e(url('/resources/assets/admin/ImportAGTNewCentury.xlsx')); ?>" class="btn btn-secondary">Download Template</a>
                    </div>
                </div>
            </div>
            <form method="post" action="<?php echo e(url('admin/import/agt_nch/save')); ?>" enctype="multipart/form-data" novalidate="novalidate" id="agt_nch">
                <?php echo e(csrf_field()); ?>   
                <div class="card shadow">
                    <div class="card-header">Upload</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label"><strong>Select Program :</strong> </label>
                            <div class="">
                                <select class="form-control custom-sel2" name="program_name">
                                    <option value="">Choose an Option</option>
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($value->name != ''): ?>
                                            <option value="<?php echo e($value->name); ?>"><?php echo e($value->name); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mb-15">
                                <input type="file" id="upload_agt_nch" name="upload_agt_nch" class="form-control font-12">
                            </div>
                            <div class="col-lg-12 pt-5 mt-5">
                                <button class="btn btn-success btn-xs" type="submit"><i class="fa fa-save ml-5 mr-5"></i>Upload</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript">
        $(function() {
            $("#agt_nch").validate({
                rules: {
                    program_name: {
                        required: true,
                    },
                    upload_agt_nch: {
                        required: true,
                    },
                },
                messages: {
                    program_name:{
                        required: 'Select program.',
                    },
                    upload_agt_nch:{
                        required: 'File is required.',
                    },
                },
                errorPlacement: function(error, element)
                {
                    error.appendTo( element.parent());
                    error.css('color','red');
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        });
        
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>