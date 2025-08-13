<?php $__env->startSection('title'); ?>
    Import Test Scores
<?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Import Test Scores</div>
        </div>
    </div>
    <div class="tab-content bordered" id="myTabContent">
        <div class="content-wrapper-in" id="importmissinggrade">
            <?php echo $__env->make('layouts.admin.common.alerts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card shadow">
                <div class="card-body">
                    <div class="">Before uploading data (Import Test Scores), please ensure that there is consistency with the naming of column fields in your "XLSX" file:<br></div>
                    <div class="pt-10">
                        <a href="<?php echo e(url('admin/import/test_scores/sample')); ?>" class="btn btn-secondary">Download Template</a>
                    </div>
                </div>
            </div>
            <form id="frm_import" method="post" action="<?php echo e(url('admin/import/test_scores/save')); ?>" enctype="multipart/form-data" novalidate="novalidate">
                <?php echo e(csrf_field()); ?>   
                <div class="card shadow">
                    <div class="card-header">Upload</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 mb-15">
                                <input type="file" id="upload_csv" name="file" class="form-control font-12" value="" required="">
                                
                            </div>
                            <div class="col-lg-12 pt-5 mt-5">
                                <button class="btn btn-success btn-xs" id="file_submit" type="submit" name="save" value="save"><i class="fa fa-save ml-5 mr-5"></i>Upload</button>
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
    const file_limit = 10; //in mb
    /*-- form validation start --*/    
    $.validator.addMethod("file_validation", function(value, element) {
        return fileValidation(element);
    }, "Please upload a file less than "+file_limit+" MB.");

    $('#frm_import').validate({
        rules:{
            file: {
                required: true,
                extension: 'xls|xlsx',
                file_validation: true
            }
        },messages:{
            file:{
                required:"File is required.",
                extension:'The file must be a file of type: xls|xlsx.'
            }         
        }
    });
    function fileValidation(element) {
        const fi = element;
        if (fi.files.length > 0) {
            const max_limit = (file_limit * 1024); // in Bytes
            const fsize = fi.files.item(0).size;
            const file = Math.round((fsize / 1024));
            if (file > max_limit) {
                return false;
            }
        }
        return true;
    }
    /*-- form validation end --*/
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>