<?php $__env->startSection('title'); ?>Edit Eligibility Value  | <?php echo e(config('APP_NAME',env("APP_NAME"))); ?>  <?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
    <script type="text/javascript">var BASE_URL = '<?php echo e(url('/')); ?>';</script>
    <style>
        input[type="checkbox"].styled-checkbox + label.label-xs {padding-left: 1.5rem;}
        .tooltip1 {
            position: relative;
            display: inline-block;
        }
        .tooltip1 .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 5px 0;
            border-radius: 6px;
            position: absolute;
            z-index: 1;
            margin-left: -60px;
            bottom: 115%;
            left: 50%;
        }
        .tooltip1:hover .tooltiptext {
            visibility: visible;
        }
        .tooltip1 .tooltiptext::after {
            content: " ";
            position: absolute;
            top: 100%; /* At the bottom of the tooltip */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }
        .tooltip1 .tooltiptext-btm {top: 115%; bottom: auto;}
        .tooltip1 .tooltiptext-btm::after {
            content: "";
            position: absolute;
            top: -9px;
            bottom: auto;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent black transparent;
        }
        .error {
            color: red;
        }
        /** test score range styles */
        .ts_range_rmv {
            height: 35px;
            margin-left: 5px;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Eligibility Value</div>
            <div class=""><a href="<?php echo e(url('admin/SetEligibility')); ?>" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div>
        </div>
    </div>
    <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <form action="<?php echo e(url('admin/SetEligibility/update',$program->id)); ?>" method="post" name="" enctype= "multipart/form-data">
        <?php echo e(csrf_field()); ?>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a></li>
            <li class="nav-item"><a class="nav-link active" id="eligibility-tab" data-toggle="tab" href="#eligibility" role="tab" aria-controls="eligibility" aria-selected="false">Eligibility</a></li>
            <!--<li class="nav-item"><a class="nav-link" id="config-tab" data-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="false">Configurations</a></li>-->
            <li class="nav-item"><a class="nav-link" id="process-tab" data-toggle="tab" href="#process" role="tab" aria-controls="process" aria-selected="false">Selection</a></li>
            <!--<li class="nav-item"><a class="nav-link" id="recommendation-tab" data-toggle="tab" href="#recommendation" role="tab" aria-controls="recommendation" aria-selected="true">Add Recommendation</a></li>-->
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="">
                    <div class="card shadow">
                        <div class="card-header">Program Set Up</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label">Program Name : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="name" disabled="" value="<?php echo e($program->name); ?>">
                                        </div>
                                        <?php if($errors->first('name')): ?>
                                            <div class="mb-1 text-danger">
                                                <?php echo e($errors->first('name')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Applicant Group Filter 1 : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="applicant_filter1" disabled="" value="<?php echo e($program->applicant_filter1); ?>">
                                        </div>
                                        <?php if($errors->first('applicant_filter1')): ?>
                                            <div class="mb-1 text-danger">
                                                <?php echo e($errors->first('applicant_filter1')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Applicant Group Filter 2 : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="applicant_filter2" disabled="" value="<?php echo e($program->applicant_filter2); ?>">
                                        </div>
                                        <?php if($errors->first('applicant_filter2')): ?>
                                            <div class="mb-1 text-danger">
                                                <?php echo e($errors->first('applicant_filter2')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Applicant Group Filter 3 : </label>
                                        <div class="">
                                            <input type="text" class="form-control" name="applicant_filter3" disabled="" value="<?php echo e($program->applicant_filter3); ?>">
                                        </div>
                                        <?php if($errors->first('applicant_filter3')): ?>
                                            <div class="mb-1 text-danger">
                                                <?php echo e($errors->first('applicant_filter3')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>
                                        <div class="row flex-wrap program_grade">
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" disabled="" id="table25" name="grade_lavel[]" value="PreK" <?php echo e(in_array('PreK',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table25" class="custom-control-label">PreK</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="K" class="custom-control-input" disabled="" id="table06" <?php echo e(in_array('K',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table06" class="custom-control-label">K</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="1" class="custom-control-input" disabled="" id="table07" <?php echo e(in_array('1',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table07" class="custom-control-label">1</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="2" class="custom-control-input" disabled="" id="table08" <?php echo e(in_array('2',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table08" class="custom-control-label">2</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="3" class="custom-control-input" disabled="" id="table09" <?php echo e(in_array('3',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table09" class="custom-control-label">3</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="4" class="custom-control-input" disabled="" id="table10" <?php echo e(in_array('4',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table10" class="custom-control-label">4</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="5" class="custom-control-input" disabled="" id="table11" <?php echo e(in_array('5',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table11" class="custom-control-label">5</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="6" class="custom-control-input" disabled="" id="table12" <?php echo e(in_array('6',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table12" class="custom-control-label">6</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="7" class="custom-control-input" disabled="" id="table13" <?php echo e(in_array('7',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table13" class="custom-control-label">7</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="8" class="custom-control-input" disabled="" id="table14" <?php echo e(in_array('8',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table14" class="custom-control-label">8</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="9" class="custom-control-input" disabled="" id="table15" <?php echo e(in_array('9',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table15" class="custom-control-label">9</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="10" class="custom-control-input" disabled="" id="table16" <?php echo e(in_array('10',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table16" class="custom-control-label">10</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]" value="11" class="custom-control-input" disabled="" id="table17" <?php echo e(in_array('11',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table17" class="custom-control-label">11</label></div>
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-2">
                                                <div class="custom-control custom-checkbox"><input type="checkbox" name="grade_lavel[]"value="12" class="custom-control-input" disabled="" id="table18" <?php echo e(in_array('12',explode(',',$program->grade_lavel))?'checked':''); ?>> 
                                                    <label for="table18" class="custom-control-label">12</label></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label"><strong>Parent Submission Form :</strong> </label>
                                        <div class="">
                                            <select class="form-control custom-select" disabled="">
                                                <option><?php echo e(findFormName($program->parent_submission_form)); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                     <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Sibling Enabled : </label>
                                        <div class=""><input id="chk_100" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="sibling_enabled"  <?php echo e($program->sibling_enabled=='Y'?'checked':''); ?>  disabled /></div>
                                    </div>
                                    <div class="form-group d-flex justify-content-between <?php if($program->sibling_enabled == "N"): ?> d-none <?php endif; ?>" id="sibling_check">
                                        <label for="" class="control-label">Sibling Program Check : </label>
                                        <div class=""><input id="chk_03" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="silbling_check"  <?php echo e($program->silbling_check=='Y'?'checked':''); ?> disabled /></div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <label for="" class="control-label">Existing Program Check : </label>
                                        <div class=""><input id="chk_03" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" name="existing_magnet_program_alert"  <?php echo e($program->existing_magnet_program_alert=='Y'?'checked':''); ?> disabled /></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label"><strong>Select School :</strong> </label>
                                        <div class="">
                                            <select class="form-control custom-select" disabled>
                                                <option><?php echo e($program->magnet_school); ?></option>
                                            </select>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow">
                        <div class="card-header">Priority Set Up</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Select Priority :</strong> </label>
                                        <div class="d-flex flex-wrap">
                                            <div class="mr-20 w-90">
                                                <div class="custom-control custom-radio"><input type="radio" name="priority[]" value="none" class="custom-control-input" disabled="" id="table28" <?php echo e(in_array('none',explode(',',$program->priority))?'checked':''); ?>> 
                                                    <label for="table28" class="custom-control-label">None</label></div>
                                            </div>
                                            <?php $__empty_1 = true; $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p=>$priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <div class="mr-20 w-90">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="priority[]" value="<?php echo e($priority->id); ?>" class="custom-control-input" disabled="" id="priority<?php echo e($p); ?>" <?php echo e(in_array($priority->id,explode(',',$program->priority))?'checked':''); ?>> 
                                                        <label for="priority<?php echo e($p); ?>" class="custom-control-label"><?php echo e($priority->name); ?></label></div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <?php endif; ?>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show active" id="eligibility" role="tabpanel" aria-labelledby="eligibility-tab">
                <?php echo $__env->make("SetEligibility::eligibility_edit", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <div class="tab-pane fade" id="process" role="tabpanel" aria-labelledby="process-tab">
                <?php echo $__env->make("SetEligibility::selection", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
           
        </div>

        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                   
                    <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
                   <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                   <a class="btn btn-danger btn-xs" href="<?php echo e(url('/admin/SetEligibility')); ?>"><i class="fa fa-times"></i> Cancel</a>
                    
                </div>
            </div>
        </div>
        

            <?php echo $__env->make("Program::Template.eligibility_modal_grade", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php $__empty_1 = true; $__currentLoopData = $eligibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$eligibility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if($eligibility['name']=='Recommendation Form111'): ?>
                <div class="modal fade" id="modal_4" tabindex="-1" role="dialog" aria-labelledby="modal_4Label" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="modal-title" id="modal_4Label">Edit Eligibility - Recommendation Form 1</div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                            </div>
                            <div class="modal-body">
                                <div class="card shadow mb-20 d-none">
                                    <div class="card-header">Used in Determination Method</div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap">
                                            <div class="d-flex mb-10 mr-30">
                                                <div class="mr-10">Basic Method Only Active : </div>
                                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                                            </div>
                                            <div class="d-flex mb-10 mr-30">
                                                <div class="mr-10">Combined Scoring Active : </div>
                                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                                            </div>
                                            <div class="d-flex mb-10">
                                                <div class="mr-10">Final Scoring Active : </div>
                                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="">
                                            <div class="form-group">
                                                <label class="control-label">Select Prior Developed Recommendation Form: : </label>
                                                <div class="">
                                                    <select class="form-control custom-select" name="eligibility_grade_lavel[<?php echo e($eligibility['id']); ?>][]">
                                                        <option value="HCS STEM Teacher Recommendation">TCS STEM Teacher Recommendation</option>
                                                        <option value="HCS Principal Recommendation">TCS Principal Recommendation</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary " data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success " data-dismiss="modal">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
        

    </form>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
      <div class="modal-dialog   modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Set Values</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalContent">
            
          </div>
          <div class="modal-footer">
            <button type="button" id="extraValueFormBtn"  class="btn btn-success extraValueFormBtn">Save</button>
            <!--<button type="button" class="btn btn-secondary modalDismiss" data-dismiss="modal">Close</button>-->
          </div>
        </div>
      </div>
    </div>

    <!-- Large Modal -->
    <div class="modal  fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
      <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel2">Set Configuration</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalContent2">
            
          </div>
          <div class="modal-footer">
            
            <!--<button type="button" class="btn btn-secondary modalDismiss" data-dismiss="modal">Close</button>-->
          </div>
        </div>
      </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>



    
    
    <script type="text/javascript" src="<?php echo e(asset('resources/assets/admin/plugins/jquery-ui/jquery-ui.min.js')); ?>"></script>
    <script type="text/javascript">

        //44 for seteligibility script start
        $(document).on("change",".valueRequiredSelect",function()
        {
            let selected = $(this).val();
            // alert(selected);
            if(selected == "Y")
            {
                $(this).parent().parent().find(".editPopBtn").removeClass("d-none");
            }
            if(selected == "N" || selected == "X")
            {
                $(this).parent().parent().find(".editPopBtn").addClass("d-none");
            }
            $(this).parent().parent().find(".MinimumEligibility.ForSelected"+selected).removeClass("d-none");
            $(this).parent().parent().find(".MinimumEligibility.ForSelected"+selected).find("select").attr('disabled',false);
            $(this).parent().parent().find(".MinimumEligibility.ForSelected"+selected).siblings().addClass("d-none");
            $(this).parent().parent().find(".MinimumEligibility.ForSelected"+selected).siblings().find("select").attr('disabled',true);
        });

        $(document).on("click",".openPopUpForData",function()
        {
            setDataForPopUp($(this));           
        });

        $(document).on("click",".openPopUpForSetting",function()
        {
            setDataForPopUpSetting($(this));           
        });


        $(document).on("click",".openPopUpForData_ls",function()
        {
            // For late submission
            setDataForPopUp($(this), 1);           
        });

        function setDataForPopUp(element, extra_val=0) {
            $(document).find("#modalContent").html("");
            var eligibility_type  = element.attr("data-id");
            var program_id  = element.attr("data-program-id");
            var eligibility_id  = element.attr("data-eligibility-id");

            let data = $.param({
                eligibility_type:eligibility_type,
                program_id:program_id,
                eligibility_id:eligibility_id,
                late_submission:extra_val,
                application_id: $("#application_id").val()
            });
            let url = "<?php echo e(url('admin/SetEligibility/extra_values/session?')); ?>"+data;
            window.location = url;

            /*$.ajax({
                url:"<?php echo e(url('admin/SetEligibility/extra_values')); ?>",
                data:{
                    eligibility_type:eligibility_type,
                    program_id:program_id,
                    eligibility_id:eligibility_id,
                    late_submission:extra_val,
                    application_id: $("#application_id").val()
                },
                success:function(result)
                {
                    $(document).find("#modalContent").html(result);
                }
            });
            $(document).find("#exampleModal").modal();*/
        }

        function setDataForPopUpSetting(element, extra_val=0) {
            $(document).find("#modalContent2").html("");
            var eligibility_type  = element.attr("data-id");
            var program_id  = element.attr("data-program-id");
            var eligibility_id  = element.attr("data-eligibility-id");
            
            let data = $.param({
                eligibility_type:eligibility_type,
                program_id:program_id,
                eligibility_id:eligibility_id,
                late_submission:extra_val,
                application_id: $("#application_id").val()
            });
            let url = "<?php echo e(url('admin/SetEligibility/configurations/session?')); ?>"+data;
            window.location = url;

            /*$.ajax({
                url:"<?php echo e(url('admin/SetEligibility/configurations')); ?>",
                data:{
                    eligibility_type:eligibility_type,
                    program_id:program_id,
                    eligibility_id:eligibility_id,
                    late_submission:extra_val,
                    application_id: $("#application_id").val()
                },
                success:function(result)
                {

                    $(document).find("#modalContent2").html(result);
                }
            });
            $(document).find("#exampleModal2").modal();*/
        }

        $("#exampleModal2").on("hidden.bs.modal", function () {
            // put your default event here
             //$(document).find("#modalContent2").html("");

        });

        $(document).on("click","#extraValueFormBtn",function()
        {   
            if ($('#extraValueForm').valid()) {
                var formData = $('#extraValueForm').serialize();
                // alert();
                $.ajax({
                    url:"<?php echo e(url('admin/SetEligibility/extra_values/save')); ?>",
                    data:formData,
                    method:"post",
                    success:function(result)
                    {
                        $('#exampleModal').modal('hide');
                    }
                });
            }
            
            // $(document).find("#extraValueForm").submit();
        });



        //44 for seteligibility script End
        $(".alert").delay(2000).fadeOut(1000);
        $(document).on("click",".add-option-list-custome",function(){
            var i = $(this).parent().siblings(".option-list-custome").children(".form-group").length + 1;
            var a = '<div class="form-group">'+
                '<label class="control-label">Option '+i+' : </label>'+
                '<div class=""><input type="text" class="form-control" value=""></div>'+
                '</div>';
            $(this).parent().siblings(".option-list-custome").append(a);
        });
        $(".chk_6").on("change", function(){
            if($(this).is(":checked")) {
                $(".custom-field-list").show();
            }
            else {
                $(".custom-field-list").hide();
            }
        })
        $(".chk_7").on("change", function(){
            if($(this).is(":checked")) {
                $(".option-list-outer").show();
            }
            else {
                $(".option-list-outer").hide();
            }
        })
        $(document).on("click", ".add-new" , function(){
            var cc = $("#first").clone().addClass('list').removeAttr("id");
            $("#inowtable tbody").append(cc);
        });
        function del(id){
            $(id).parents(".list").remove();
        }
        //$(document).ready(function(){
        //$('#cp2').colorpicker({
        //
        //});

        $(function () {
            $('#cp2').colorpicker().on('changeColor', function (e) {
                $('#chgcolor')[0].style.backgroundColor = e.color.toHex();
            });
        });
        $("#chk_03").on("change",function(){
            if($("#chk_03").is(":checked")) {
                $("#zone").show();
            }
            else {
                $("#zone").hide();
            }
        });

        function changeApplication(value)
        {
            document.location.href = "<?php echo e(url('/admin/SetEligibility/edit/'.$program->id)); ?>/"+value;
        }
    </script>
    <script src="<?php echo e(asset('resources/assets/admin/js/program_eligibility.js?'.rand())); ?>"></script>
    <script type="text/javascript">
        $(".table-striped").find("tr").each(function() {
            changeIcon($(this), 1);    
        })
        
        /** Writing prompt question script start **/
        /*// Dynamic field validation
        function dynamicFieldsValidation(){
            $(document).find('input[name^="value[wp_question]"]').each(function(){
                $(this).rules('add', {
                    required: true,
                    messages: {
                        required: 'Question is required.'
                    }
                })
            });
        }
        // add new element
        $(document).on('click', '.wp_add', function(){
            var wp_row_clone = $('.wp_row:last').clone();
            wp_row_clone.find('input').each(function(){
                var input_name = $(this).attr('name');
                var next_count = parseInt(input_name.match(/\d+/)) + 1;
                $(this).val('');
                $(this).attr('name', 'value[wp_question]['+next_count+']');
                $(this).removeClass('error');
            });
            wp_row_clone.find('label.error').remove();
            $('.wp_container').append(wp_row_clone);
            wpRowConf();
            dynamicFieldsValidation();
        }); 
        // remove element
        $(document).on('click', '.wp_remove', function(){
            $(this).closest('.wp_row').remove(); 
            wpRowConf();
        }); 
        // check for single element
        function wpRowConf(){
            if ($('.wp_row').length <= 1) {
                $('.wp_remove').addClass('d-none');
            }else{
                $('.wp_remove').removeClass('d-none');
            }
        }*/
        /** Writing prompt question script end **/

        /** Test Score range scripts start */
        /*$(document).on('click', '.ts_range_add',  function() {
            let ts_range_field = $(this).closest('.ts_container').find('.ts_range > div').last();
            let ts_range_field_cln = ts_range_field.clone();
            let range_name = ts_range_field.find('.ts_range_field').attr('name');
            range_name = range_name.match(/\[(.*?)\]/g);
            let ts_vise_ind = range_name[(range_name.length - 2)].replace('[', '').replace(']', '');
            // let next_ind = parseInt(range_name[(range_name.length - 1)].replace('[', '').replace(']', '')) + 1;
            ts_range_field_cln.find('.ts_range_field').attr('name', 'value[range]['+ts_vise_ind+'][]');
            ts_range_field_cln.find('.ts_point_field').attr('name', 'value[range_points]['+ts_vise_ind+'][]');
            ts_range_field_cln.find('.ts_point_field').val('');
            ts_range_field_cln.find('.ts_range_field').val('');
            ts_range_field_cln.find('.ts_range_rmv').removeClass('d-none');
            ts_range_field.parent().append(ts_range_field_cln);
            // maintain remove button hide-show
            let curr_rng = $(this).closest('.ts_container').find('.ts_range');
            tsRangeRemoveMaintain(curr_rng);
        });
        $(document).on('click', '.ts_range_rmv', function() {
            let adj_rng = $(this).closest('.ts_range');
            $(this).parent().remove();
            tsRangeRemoveMaintain(adj_rng);
        });
        function tsRangeRemoveMaintain(e) {
            let rng_count = ($(e).find('div').length);
            let rmv_obj = $(e).find('div').first().find('.ts_range_rmv');
            if (rng_count < 2) {
                rmv_obj.addClass('d-none');
            } else {
                rmv_obj.removeClass('d-none');
            }
        }
        function isNumber(evt, isDot=false) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (isDot && (charCode == 46)) {
                return true; // For dot
            }
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }*/
        /** Test Score range scripts end */
        /** Student Profile start */
        /*$('.sp_eligibility').click(function() {
            let dataset = $(this).data();
            $.jquery({
                url: "<?php echo e(url('admin/SetEligibility/edit/student_profile')); ?>",
                type: 'POST',
                data: {
                    '_token' => "<?php echo e(csrf_token()); ?>",
                    dataset: dataset
                }
            });
            console.log(dataset);            
        });*/
        /** Student Profile end */
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>