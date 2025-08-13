<?php $__env->startSection('title'); ?>Edit Eligibility <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style type="text/css">
    .error {color:red;}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Eligibility</div>
            <div class=""><a href="<?php echo e($module_url); ?>" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
     <form action="<?php echo e($module_url); ?>/update/<?php echo e($eligibility->id); ?>" method="POST" id="eligibility-edit" name="eligibility-edit">
        <?php echo e(csrf_field()); ?>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <?php if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0): ?>
                <li class="nav-item"><a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">Template 1</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link active" id="recommendation-tab" data-toggle="tab" href="#recommendation" role="tab" aria-controls="recommendation" aria-selected="true">Template 2</a></li>

            <?php endif; ?>

            <?php if($eligibility->template_id == 3): ?>
                <li class="nav-item"><a class="nav-link" id="subjects-tab" data-toggle="tab" href="#subjects" role="tab" aria-controls="subjects" aria-selected="true">Grade/Subject Selection</a></li>
            <?php endif; ?>

        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0): ?>
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="">
                        <div class="">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Select Eligibility Template : </label>
                                        <div class="">
                                            <select class="form-control custom-select template-select" name="template" disabled="">
                                                <option value="">Select Option</option>
                                                    <?php $__empty_1 = true; $__currentLoopData = $eligibilityTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$eligibilityTemplate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                        <option value="<?php echo e($eligibilityTemplate->id); ?>" <?php if(isset($eligibility->template_id) && $eligibility->template_id == $eligibilityTemplate->id): ?>  selected <?php endif; ?>><?php echo e($eligibilityTemplate->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div id="optionContent">
                                        
                                        <?php if(isset($eligibilityTemplates[$eligibility->template_id]->content_html)): ?>
                                            <?php echo $__env->make("Eligibility::templates.".$eligibilityTemplates[$eligibility->template_id]->content_html,[$eligibilityContent,$eligibility], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Template Alias</label>
                                        <div class="">
                                            <input type="text" class="form-control" value="<?php echo e($eligibility->alias_name ?? old('alias_name')); ?>" name="alias_name">
                                           
                                        </div>
                                    </div>  

                                   
                                    <div class="form-group d-flex justify-content-between pt-5 <?php if($eligibility['template_id'] != "3" && $eligibility['template_id'] != "8"): ?> d-none <?php endif; ?>" id="override">
                                        
                                        <div class="d-flex flex-wrap"><label class="control-label pr-10">Override Enabed ?</label>&nbsp;
                                           <input id="chk_acd" type="checkbox" name="override" class="js-switch js-switch-1 js-switch-xs grade_override" data-size="Small"  <?php echo e($eligibility->override=='Y'?'checked':''); ?>/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Store for : </label>
                                        <div class="">
                                            <select class="form-control custom-select" name="store_for">
                                                <option value="">Select Option</option>
                                                <option value="DO" <?php echo e(isset($eligibility->store_for) && $eligibility->store_for=='DO'?'selected':''); ?>>District Only</option>
                                                <option value="MS" <?php echo e(isset($eligibility->store_for) && $eligibility->store_for=='MS'?'selected':''); ?>>MyPick System</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="interview-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Inerview Score</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                        <div class="audition-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Audition</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                        <div class="committee-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Committee Score</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                        <div class="academic-list d-none">
                            <div class="card shadow">
                                <div class="card-header">Academic Grade Calculation</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label">Eligibility Name : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm add-header" title="">Add Header</a></div>
                                    <div class="form-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($eligibility->template_id == 3): ?>
                    <div class="tab-pane fade" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                        <?php echo $__env->make("Eligibility::subject_management", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                
            <?php endif; ?>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <?php
                        if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0)
                            $template_type = "general";
                        else
                            $template_type = "recommendation";
                    ?>
                    <input type="hidden" name="submit-from" id="submit-from-btn" value="<?php echo e($template_type); ?>">
                    <button type="submit" class="btn btn-warning btn-xs" value="save" name="submit"><i class="fa fa-save"></i> Save </button>
                   <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="<?php echo e(url('/admin/Eligibility')); ?>"><i class="fa fa-times"></i> Cancel</a>
                   
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    
    <script type="text/javascript">
        var nameUnique = true;
         $(document).on("blur", "input[name='name']", function() {
             $.ajax({    //create an ajax request 
                type: 'POST',
                url: "<?php echo e(url('admin/Eligibility/checkEligiblityName')); ?>", 
                dataType: "json",
                data:{
                    "_token": "<?php echo e(csrf_token()); ?>",
                    "name": encodeURIComponent($(this).val()),
                    "id": <?php echo e($eligibility->id); ?>

                },
                success: function(response)
                {
                    var obj = $("input[name='name']").parent();
                    if(response==false)
                    {
                        nameUnique = false;
                        $('#name-error').remove();
                        $(obj).append('<label id="name-error" class="error" for="name">Eligibility name should be unique.')
                    }
                    else
                    {
                        nameUnique = true;
                        $('#name-error').remove();
                    }
                }

            });
         });

        jQuery.validator.addMethod("unique", 
            function(value, element) {

                    return nameUnique;
            },'Eligibility name should be unique.');


        $('#eligibility-edit').validate({
                rules: {
                    name: {
                        required: true,
                        unique: true
                    }
                },
                messages: {
                    name: {
                        required: "Eligibility name is required.",
                        unique: "Eligibility name should be unique."
                    }
                }
            });

    </script>
    <?php echo $__env->make("Eligibility::js", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>