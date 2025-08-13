<?php $__env->startSection('title'); ?>
    <title>Huntsville City Schools</title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!Session::has("from_admin")): ?>
        <!--include("layouts.front.common.district_header-->
    <?php endif; ?>
    <?php global $hidefurther;  ?>
    <form class="p-20 border mt-20 mb-20" method="post" action="<?php echo e(url('/nextstep')); ?>" id="programform" name="programform" onsubmit="return checkDOB(event)">
        <?php echo e(csrf_field()); ?>

        
        <input type="hidden" name="no_of_pages" value="<?php echo e(get_form_pages($data[0]->form_id)); ?>">
        <input type="hidden" name="page_id" value="<?php echo e($page_id); ?>">
        <input type="hidden" name="form_type" id="form_type" value="<?php echo e($form_type); ?>">
        <input type="hidden" id="application_id" name="application_id" value="<?php echo e($application_id); ?>">
        <input type="hidden" name="form_id" id="form_id" value="<?php echo e($data[0]->form_id); ?>">
        <input type="hidden" id="bdate_validation" value="">

        <div class="box-2" style="">
            <?php if(session::has("zonemsg")): ?>
                <div class="alert alert-danger"><?php echo e(getAlertMsg('zone_address_not_available')); ?></div>
            <?php endif; ?>
            <div class="box-2-1" style="">
                <div class="back-box hidden" style="">
                    <div class="form-group text-right">
                        <!--<label class="control-label col-12 col-md-4 col-xl-3"></label>-->
                        <div class="">
                            <a href="<?php echo e(url('/')); ?>" class="btn btn-secondary back-box-1" title="">Back</a>
                        </div>
                    </div>    
                </div>
                <div class="card" id="infodiv">
                    <div class="card-header">Step <?php echo e($page_id); ?> - <?php echo e(getFormPageTitle($data[0]->form_id, $page_id)); ?></div>
                     <div class="card-body">
                        <?php echo $__env->make("layouts.front.form_fields", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                        <?php if($hidefurther == "Yes"): ?>
                            <?php $dclass = "d-custom-hide" ?>
                        <?php else: ?>
                            <?php $dclass = "" ?>
                        <?php endif; ?>
                         <?php if(get_form_pages($data[0]->form_id) == $page_id): ?>
                            <div class="form-group d-flex flex-wrap justify-content-between <?php echo e($dclass); ?>">
                                    <!--<a href="javascript:void(0);" onclick="exitWithoutSave()" class="btn btn-danger" title="">Exit<br><sm>*Your application will not be saved</a>-->
                                        <button type="submit" name="submit" class="btn btn-secondary" title="" value="Prev Step" onclick="removeValidateSubmit()"><i class="fa fa-backward pr-5"></i>  Previous Page</button>
                                    <input type="submit" class="btn btn-success step-2-2-btn" id="np" title="" onclick="return submitForm(event)" value="Submit My Application">
                            </div>

                        <?php else: ?>
                            <div class="form-group d-flex flex-wrap justify-content-between <?php echo e($dclass); ?>">
                                    <label class="control-label col-12 col-md-4 col-xl-3 pl-0">
                                        <?php if($page_id > 1): ?>
                                            <button type="submit" name="submit" class="btn btn-secondary" title="" value="Prev Step" onclick="removeValidateSubmit()"><i class="fa fa-backward pr-5"></i>  Previous Page</button>
                                        <?php else: ?>
                                            <!--<button type="button" name="submit" class="btn btn-secondary" title="" value="Prev Step" onclick="document.location.href='<?php echo e(url('/')); ?>'"><i class="fa fa-backward"></i>  Previouse Page</button>-->
                                        <?php endif; ?>
                                    </label>
                                     <button type="submit" name="submit" class="btn btn-secondary step-2-2-btn" title="" value="Next Step" id="np" onclick="return validateAddress(event)">Next Page  <i class="fa fa-forward pl-5"></i></button>
                            </div>
                        <?php endif; ?>
                            
                     </div>
                        

                        <?php if(Config::get('variables.address_debug') == "true"): ?>
                            <div class="form-group d-flex flex-wrap justify-content-between d-none p-50" id="addressdebug">
                                                    <input type="button" name="Fetch Schools" value="Fetch Schools" onclick="fetch_zoned_school();">
                                <div class="card-body col-12" id="response">
            
                                </div>
                            </div>
                        <?php endif; ?>
                        
                </div>
                
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("scripts"); ?>

	<script type="text/javascript">
        var zonedmatch = false;
        var address_selected = true;
        if($("#address").length > 0 && $("#city").length > 0 && $("#zip").length > 0)
        {
            $("#addressdebug").removeClass("d-none");
        }



        function validateAddress(event)
        {
            <?php if(Session::has("zone_api") && Session::has("zone_api")=="N"): ?>
                return true;
            <?php endif; ?>
            if($("#mcp_employee_element").length > 0)
            {
                if($("#mcp_employee_element").val() == "Yes")
                    return true;
            }

            
            if($("#address").length > 0 && $.trim($("#address").val()) != "" && $.trim($("#city").val()) != "" && $.trim($("#zip").val()) != "" && zonedmatch == false)
            {
                $("#wrapperloading").show();
                $("#loadmsg").html("Checking address in Zoning tool.");
                event.preventDefault();
                $.ajax({
                    url:'<?php echo e(url('/admin/ZonedSchool/search1')); ?>',
                    type:"post",
                    dataType: 'text',
                    data:{_token:'<?php echo e(csrf_token()); ?>',address:$("#address").val(),grade: $("#next_grade").val(),zip: $("#zip").val(),city: $("#city").val()},
                    success:function(response){
                        $("#wrapperloading").hide();
                        if($.trim(response) == "NoMatch")
                        {
                            //response = "<div class='alert1 alert-danger'>The address you entered could not be found within the bounds of the Huntsville City Schools. <br> Please confirm your address and try again.</div>"
                            response = "";
                            //$("#address_options").html(response);
                            address_selected = false;
                            zonedmatch = false;
                            $("#address_text").removeClass("d-none");
                            $("#address_padding").removeClass("d-none");
                            $('html, body').animate({
                                    scrollTop: $("#address").offset().top
                                }, 2000);
                            //event.preventDefault();
                            //document.location.href = "<?php echo e(url('/msgs/nozone')); ?>";
                        }
                        else
                        {
                            zonedmatch = true;
                            if(response.search("form-control") > 0)
                            {
                                address_selected = false;
                                $("#address_options").html(response);
                                $("#address_text").removeClass("d-none");
                                $("#address_padding").removeClass("d-none");
                                $('html, body').animate({
                                    scrollTop: $("#address").offset().top
                                }, 2000);
                            }
                            else
                            {
                                $("#address_options").html(response);
                                if(response == "NoMatch")
                                    response = "";
                                $("#address").val(response);
                                address_selected = true;
                                $("#programform").submit();
                                      $("#np").trigger('click');

                            }
                        }
                        //$("#response").html(response);
                    }

                })
             }
            if(zonedmatch == true && address_selected == false)
            {
                 swal({
                    text: "Please select appropriate address from suggestion list",
                    type: "warning",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#d62516"
                });
                return false;
            }
            return true;
        }

        function fetch_zoned_school()
        {
            $.ajax({
                    url:'<?php echo e(url('/admin/ZonedSchool/search')); ?>',
                    type:"post",
                    data:{_token:'<?php echo e(csrf_token()); ?>',address:$("#address").val(),grade: $("#next_grade").val(),zip: $("#zip").val(),city: $("#city").val()},
                    success:function(response){
                        $("#response").html(response);
                    }

                })
        }
		$('#studentstatus_frm').validate({
                rules: {
                    student_status: {
                        required: true,                       
                    }
                },
                messages: {
                    student_status: {
                        required: "Select Student Status"
                    }
                }
            });


    $('#programform').validate({
                rules: {

                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $fieldProp = getOnlyFieldProperty($row->type,$row->id,$row->form_id, $page_id) ?>
                    'formdata[<?php echo e($row->id); ?>]': {
                        <?php if($fieldProp['required']=="yes"): ?>
                            <?php if($fieldProp['type'] == "termscheck" && Session::has("from_admin")): ?>
                            <?php else: ?>
                                required: true,
                            <?php endif; ?>                   
                        <?php endif; ?>
                        <?php if($fieldProp['type']=="email"): ?>
                            email: true,                   
                        <?php endif; ?>  
                        <?php if($fieldProp['db_field']=="confirm_email"): ?>
                            equalTo: {
                                param: '#parent_email',
                                depends: function(element) { 
                                    if($("#parent_email").length > 0)
                                    {
                                        return true;
                                        
                                    }
                                    else
                                        return false; 
                                }
                            },
                        <?php endif; ?>
                        <?php if(isset($fieldProp['validation'])): ?>
                            <?php echo e($fieldProp['validation']); ?>: true,                   
                        <?php endif; ?>    
                        },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                }, errorPlacement: function(error, element) {
                        if (element.attr("type") == "radio") {
                            $(element).parent().parent().remove( ".error" );
                            $(element).parent().parent().find(".raderror").remove();
                            $(element).parent().after( "<label class='raderror error col-12 pl-0'>"+error.text()+"</label>" );
                            //error.insertAfter(element);
                        }
                        else if (element.attr("type") == "checkbox") {
                            $(element).parent().parent().parent().find(".chkerror").remove();
                            $(element).parent().parent().after( "<label class='chkerror error col-12 pl-30'>"+error.text()+"</label>" );
                            //error.insertAfter(element);
                        } else {
                            error.insertAfter(element);
                        }
                    }


            });


    function selectAddress(val)
    {
        $("#address").val(val);
        address_selected = true;
        zonedmatch = true;
    }

        function exitWithoutSave()
        {
            var txt;
            var r = confirm("This will exit you from Application Submission process without saving data ?");
            if (r == true) {
              document.location.href = "<?php echo e(url('/')); ?>";
            } 
        }

        function showHideCorrect()
        {
            $("#wrapperloading").show();
            $("#loadmsg").html("Checking address in Zoning tool.");

            $.ajax({
                    url:'<?php echo e(url('/admin/ZonedSchool/search2')); ?>/'+$("#form_id").val(),
                    type:"get",
                    dataType: 'text',
                    success:function(response){
                        $("#wrapperloading").hide();            
                        if($.trim(response) == "NoMatch")
                        {
                            document.location.href = "<?php echo e(url('/msgs/current_nozone')); ?>";
                        }
                        else
                        {
                            $("#correctdiv").remove();
                            $(".d-custom-hide").removeClass("d-custom-hide");
                            $("#zoned_school_div").removeClass("d-none");
                            $("#zoned_school_val").html(response);
                        }
                    }

                })
            

            //correctinfo
        }
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>