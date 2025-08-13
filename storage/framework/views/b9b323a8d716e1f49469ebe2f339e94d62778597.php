<?php $__env->startSection('title'); ?> Subject Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .collapsible-link::before {
        content: '';
        width: 14px;
        height: 2px;
        background: #333;
        position: absolute;
        top: calc(50% - 1px);
        right: 1rem;
        display: block;
        transition: all 0.3s;
    }

    /* Vertical line */
    .collapsible-link::after {
        content: '';
        width: 2px;
        height: 14px;
        background: #333;
        position: absolute;
        top: calc(50% - 7px);
        right: calc(1rem + 6px);
        display: block;
        transition: all 0.3s;
    }

    .collapsible-link[aria-expanded='true']::after {
        transform: rotate(90deg) translateX(-1px);
    }

    .collapsible-link[aria-expanded='true']::before {
        transform: rotate(180deg);
    }
    
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Subject Management</div>
            <div class="">
                <a href="<?php echo e(url('admin/Eligibility')); ?>" class="btn btn-sm btn-secondary" title="">Back</a>
               
            </div>
        </div>
    </div>
    <form action="<?php echo e($module_url); ?>/updateSubjectManagement" method="POST" id="subject_management" name="subject_management">
        <?php echo e(csrf_field()); ?>

        <div class="card shadow">
            <div class="card-body">
                <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="">
                    <div class="form-group">
                        <label class="control-label">Select Application : </label>
                        <div class="">
                            <select class="form-control custom-select selectApplication" name="application_id">
                                <option value="">Choose Option</option>
                                <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value->id); ?>" <?php if($value->id == $id): ?> selected <?php endif; ?>><?php echo e($value->application_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if(isset($data) && !empty($data)): ?>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 mx-auto">
                                    <div id="accordionExample" class="">
                                        <!-- Accordion item 2 -->
                                        <?php if(isset($data['grades']) && !empty($data['grades'])): ?>
                                        <?php $__empty_1 = true; $__currentLoopData = $data['grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="card" style="width: 100%">
                                                <div id="headingTwo" class="card-header bg-white shadow-sm border-0">
                                                    <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse" data-target="#<?php echo e($grade->name); ?>" aria-expanded="false" aria-controls="<?php echo e($grade->name); ?>" class="d-block position-relative collapsed text-dark text-uppercase collapsible-link py-2"><?php echo e($grade->name); ?></a></h6>
                                                </div>
                                                <input type="hidden" name="application_id" value="<?php echo e($id); ?>">
                                                <div id="<?php echo e($grade->name); ?>" aria-labelledby="heading<?php echo e($grade->name); ?>" data-parent="#accordionExample" class="collapse">
                                                    <div class="card-body p-5 mt-20">
                                                        <div class="row pl-10">
                                                            <?php if(isset($data['subjects']) && !empty($data['subjects'])): ?>
                                                            <?php $__empty_2 = true; $__currentLoopData = $data['subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                                <div class="col-md-3 mb-20">
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <?php echo e($subject); ?>

                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <?php $subject=strtolower(str_replace(' ','_',$subject)) ?>
                                                                    
                                                                            <input type="hidden" name="gradeSubject[<?php echo e($grade->name); ?>][<?php echo e($subject); ?>]" value="N">
                                                                            <input type="checkbox" value="Y" name="gradeSubject[<?php echo e($grade->name); ?>][<?php echo e($subject); ?>]" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" <?php if(!isset($data['subjectManagement'][$grade->name][$subject])||$data['subjectManagement'][$grade->name][$subject]=='Y'): ?> checked <?php endif; ?> />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                            <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <?php if($id > 0): ?>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                   
                    
                    <button type="submit" class="btn btn-warning btn-xs" value="save" name="submit"><i class="fa fa-save"></i> Save </button>
                   <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="<?php echo e(url('/admin/Eligibility')); ?>"><i class="fa fa-times"></i> Cancel</a>
                   
                </div>
            </div>
        </div>
        <?php endif; ?>
    </form>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    
    <script type="text/javascript">
        
        $(document).on("change",".selectApplication",function(event)
        {
            let app_id = $(document).find(".selectApplication").val();
            var link = "<?php echo e(url('admin/Eligibility/subjectManagement')); ?>"+"/"+app_id;
            document.location.href = link;
        });
    </script>
<?php $__env->stopSection(); ?>

       
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>