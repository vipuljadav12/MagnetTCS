<?php $__env->startSection('title', "Create Enrollment Period"); ?>

<?php $__env->startSection('styles'); ?>
   <link rel="stylesheet" href="<?php echo e(url('/resources/assets/admin/css/jquery-ui.css?rand()')); ?>">

    <style type="text/css">
        .error{
            color: red;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Create Enrollment Period</div>
            <div class=""><a href="<?php echo e(url('admin/Enrollment')); ?>" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div> 
        </div>
    </div>
    <?php echo $__env->make('layouts.admin.common.alerts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <form id="add-enrollment" method="post" action="<?php echo e(url('admin/Enrollment/store')); ?>">
        <?php echo e(csrf_field()); ?>

        <div class="card shadow">
            <div class="card-header">Open Enrollment</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">School Year*</label>
                            <div class="">
                                <input name="school_year" value="<?php echo e(old('school_year')); ?>" type="text" maxlength="10" class="form-control">
                                <?php if($errors->has('school_year')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('school_year')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Confirmation Style*</label>
                            <div class="">
                                <input name="confirmation_style" value="<?php echo e(old('confirmation_style')); ?>" type="text" maxlength="30" class="form-control">
                                <?php if($errors->has('confirmation_style')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('confirmation_style')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12" style="display:none">
                        <div class="form-group">
                            <label for="">Import Grades By (cannot be changed after Beginning Date)</label>
                            <div class="">
                                <select name="import_grades_by" class="form-control custom-select">
                                    <option value="">Select</option>
                                    <option value="submission_date" 
                                        <?php if(old('import_grades_by')=='submission_date'): ?>
                                        selected
                                        <?php endif; ?>>Submission Date
                                    </option>
                                </select>
                                <?php if($errors->has('import_grades_by')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('import_grades_by')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Beginning Date (1st day of open enrollment period)*</label>
                            <div class="">
                                <input name="begning_date" value="<?php echo e(old('begning_date')); ?>" class="form-control" id="begning_date">
                                <?php if($errors->has('begning_date')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('begning_date')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Ending Date (last day of open enrollment period ending at 11:59 PM)*</label>
                            <div class="">
                                <input name="ending_date" value="<?php echo e(old('ending_date')); ?>" class="form-control" id="ending_date">
                                <?php if($errors->has('ending_date')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('ending_date')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Late Application Beginning date (1st Day to accept late applications)*</label>
                            <div class="">
                                <input class="form-control" id="datepicker03">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Late Application Ending date (system will accept applications up until 11:59 PM on this date)*</label>
                            <div class="">
                                <input class="form-control" id="datepicker04">
                            </div>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">Application Cut offs<br>
                <small>Applications must be born on or before:</small></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">PreK Birthday Cut Off*</label>
                            <div class="">
                                <input name="perk_birthday_cut_off" value="<?php echo e(old('perk_birthday_cut_off')); ?>" class="form-control" id="perk_birthday_cut_off">
                                <small>After this date, submissions applying for Pre Kindergarten will not be accepted.</small> <br>
                                <?php if($errors->has('perk_birthday_cut_off')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('perk_birthday_cut_off')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">Kindergarten Birthday Cut Off*</label>
                            <div class="">
                                <input name="kindergarten_birthday_cut_off" value="<?php echo e(old('kindergarten_birthday_cut_off')); ?>" class="form-control" id="kindergarten_birthday_cut_off">
                                <small>After this date, submissions applying for Kindergarten will not be accepted.</small>  <br>
                                <?php if($errors->has('kindergarten_birthday_cut_off')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('kindergarten_birthday_cut_off')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">First Grade Birthday Cut Off *</label>
                            <div class="">
                                <input name="first_grade_birthday_cut_off" value="<?php echo e(old('first_grade_birthday_cut_off')); ?>" class="form-control" id="first_grade_birthday_cut_off">
                                <small>After this date, submissions applying for First grade will not be accepted.</small>  <br>
                                <?php if($errors->has('first_grade_birthday_cut_off')): ?>
                                    <span class="error">
                                        <?php echo e($errors->first('first_grade_birthday_cut_off')); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    
                    <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save" title="Save"><i class="fa fa-save"></i> Save </button>
                    <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="<?php echo e(url('/admin/Enrollment')); ?>" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                    
                    
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <!-- InstanceBeginEditable name="Footer Scripts" -->
    <script type="text/javascript" src="<?php echo e(url('resources/assets/admin/plugins/jquery-ui/jquery-ui.min.js')); ?>"></script>
    <script src="<?php echo e(asset('resources/assets/common/js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('resources/assets/common/js/additional-methods.min.js')); ?>"></script>

    <script>

        $( function() {
            // $( "#datepicker01" ).datepicker();
            // $( "#datepicker02" ).datepicker();
            // $( "#datepicker03" ).datepicker();
            // $( "#datepicker04" ).datepicker();
            // $( "#datepicker05" ).datepicker();
            // $( "#datepicker06" ).datepicker();
            // $( "#datepicker07" ).datepicker();

           
            $("#begning_date").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#ending_date").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#first_grade_birthday_cut_off").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#kindergarten_birthday_cut_off").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });
            $("#perk_birthday_cut_off").datepicker({
                numberOfMonths: 1,
                autoclose: true,
                dateFormat: 'mm/dd/yy',
            });

            // Ending Date validation  
            jQuery.validator.addMethod('afterDate', function(value, element, parameters){
                return new Date(value) >= new Date($(parameters).val());
            }, 'End Date should be greater that Begining Date');

            // Form validation
            $('#add-enrollment').validate({
                rules:{
                    school_year: {
                        required: true,
                        pattern: /(^[\d]+[\-][\d]+$)/
                    },
                    confirmation_style: {
                        required: true,
                        pattern: /[0-9a-z\s]+$/i
                    },
                    import_grades_by: {
                        required: true
                    },
                    begning_date: {
                        required: true
                    },
                    ending_date: {
                        required: true,
                        afterDate: $('input[name="begning_date"]')
                    },
                    perk_birthday_cut_off: {
                        required: true
                    },
                    kindergarten_birthday_cut_off: {
                        required: true
                    },
                    first_grade_birthday_cut_off: {
                        required: true
                    }
                },
                messages:{
                    school_year: {
                        required: 'School year is required',
                        pattern: 'Enter valid school year'
                    },
                    confirmation_style: {
                        required: 'Confirmation style is required.',
                        pattern: 'Do not enter special characters.'
                    },
                    import_grades_by: {
                        required: 'Import grades by is required.'
                    },
                    begning_date: {
                        required: 'Beginning date is required.'
                    },
                    ending_date: {
                        required: 'Ending date is required.'
                    },
                    perk_birthday_cut_off: {
                        required: 'Perk birthday cut off is required.'
                    },
                    kindergarten_birthday_cut_off: {
                        required: 'Kindergarten birthday cut off is required.'
                    },
                    first_grade_birthday_cut_off: {
                        required: 'First grade birthday cut off is required.'
                    }
                },
                errorPlacement: function(error, element){
                    error.insertAfter(element.parent());
                }
            });

        } );
    </script>         

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>