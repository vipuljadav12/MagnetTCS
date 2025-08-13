<?php $__env->startSection("title"); ?>
	Set Availability | <?php echo e(config('APP_NAME',env("APP_NAME"))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
<style type="text/css">
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
<div class="content-wrapper-in">
	<!-- InstanceBeginEditable name="Content-Part" -->
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Set Availability</div>
            <!--<div class=""><a href="add-district.html" class="btn btn-sm btn-secondary" title="">Add District</a></div>-->
        </div>
    </div>
    <form class="" action="<?php echo e(url("admin/Availability/store")); ?>" method="post">
        <?php echo csrf_field(); ?>

        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview01" role="tabpanel" aria-labelledby="preview01-tab">
                <div class="">
                    <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <label class="control-label"><strong>Select Program Group</strong></label>
                    <div class="form-group">
                        <select class="form-control custom-select" id="slt_af_program">
                            <option value="">Select Application Filter</option>
                            <?php $__empty_1 = true; $__currentLoopData = $af_programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <option value="<?php echo e($program); ?>"><?php echo e($program ?? ""); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <label class="control-label"><strong>Select Program for Seats</strong></label>
                    <div class="form-group">
                        <select class="form-control custom-select selectProgram" id="programs" name="program_id">
                            <option value="">Select Program</option>
                            
                        </select>
                    </div>
                    <div class="AjaxContent">
	                    
                    </div>
                    

                </div>
            </div>
        </div>
    </form>    
	<!-- InstanceEndEditable --> 
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection("scripts"); ?>
<script type="text/javascript">
    $(function()
    {
        generateContent();
        var lastSelected = $(document).find(".selectProgram option:selected");
    });
    $(document).on("click",".selectProgram",function(event)
    {
        lastSelected = $(document).find(".selectProgram option:selected");
    });
	$(document).on("change",".selectProgram",function(event)
	{
        event.preventDefault();
        let checkChanged = $(document).find(".changed").length;
        if(checkChanged == 0)
        {
            generateContent();
        }
        else
        {
            event.preventDefault();
            lastSelected.prop("selected",true);
            swal("Please save current changes");
        }
	});
    function generateContent()
    {
        $(document).find(".AjaxContent").html('');
        let selected = $(document).find(".selectProgram").val();

        if (selected != '') {
            $.ajax(
            {
                url:"<?php echo e(url('admin/Availability/getOptionsByProgram')); ?>"+"/"+selected,
                success:function(result)
                {
                    $(document).find(".AjaxContent").html(result);
                    matchWithTotal();
                }
            });
            // console.log(selected);
        }
    };
    function matchWithTotal()
    {
        $(document).find(".whiteSeat").each(function()
        {
            var grade = $(this).attr("data-id");
            var whiteSeat = $(this).val();
            var blackSeat = $(document).find("input[name='grades["+grade+"][black_seats]']").val();
            var otherSeat = $(document).find("input[name='grades["+grade+"][other_seats]']").val();
            var notSpecifiedSeat = $(document).find("input[name='grades["+grade+"][not_specified_seats]']").val();

            var value = parseInt(whiteSeat || 0) + parseInt(blackSeat || 0) + parseInt(otherSeat || 0) + parseInt(notSpecifiedSeat || 0);
            var total = $(document).find(".totalSeat[data-id="+grade+"]").val();


            if(parseInt(total || 0) == 0){ 
                /*console.log(parseInt(total || 0));
                $(document).find(".blackSeat[data-id='"+grade+"']").attr('disabled','disabled');
                $(document).find(".whiteSeat[data-id='"+grade+"']").attr('disabled','disabled');
                $(document).find(".otherSeat[data-id='"+grade+"']").attr('disabled','disabled');
                $(document).find(".notSpecifiedSeat[data-id='"+grade+"']").attr('disabled','disabled');*/
            }

            matchIndividualTotal($(this));
            /*else{
                $(document).find(".blackSeat[data-id='"+grade+"']").removeAttr('disabled');
                $(document).find(".whiteSeat[data-id='"+grade+"']").removeAttr('disabled');
                $(document).find(".otherSeat[data-id='"+grade+"']").removeAttr('disabled');
                $(document).find(".notSpecifiedSeat[data-id='"+grade+"']").removeAttr('disabled');
            }

            if(parseInt(value) > parseInt(total))
            {
                $(this).parent().parent().find("label").removeClass("d-none");
                $(this).addClass("notAllowed");
            }
            else
            {
                $(this).parent().parent().find("label").addClass("d-none");
                $(this).removeClass("notAllowed");
            }*/
        });
        // $(document).find(".notAllowed:first").focus();
    }
    $(document).on("change input",".whiteSeat, .blackSeat, .otherSeat, .notSpecifiedSeat, .totalSeat",function()
    {
        matchWithTotal();
        $(this).addClass("changed");
    });
    $(document).on("click","#optionSubmit",function(event)
    {
        let checkNotAllowed = $(document).find(".notAllowed").length;
        let checkIndividualTotalError = $(document).find(".has-error").length;
        // alert(checkNotAllowed);
        if(checkNotAllowed > 0)
        {
            swal("Please review all errors");
            $(document).find(".notAllowed:first").focus();

            event.preventDefault();
            return false;
        }

        if(checkIndividualTotalError > 0){
            swal("Please review all errors");
            event.preventDefault();
            return false;
        }
        $(document).find(".notAllowed:first").focus();
    });

    $(document).ready(function() {
        $('#slt_af_program').trigger('change');
    });
    // Get program on change application filter
    $('#slt_af_program').on('change', function() {

        var application_filter = $(this).val();
        var selected_program = $('#programs').val();
        $('#programs option:not(:first)').remove();

        // if (application_filter == '') {
        //     $('.avg_seats').addClass('d-none'); 
        // } else {
        //     $('.avg_seats').removeClass('d-none');
        // }

        $.ajax({
            url: "<?php echo e(url('')); ?>/admin/Availability/get_programs",
            data: {
                'application_filter': application_filter
            },
            success: function(response) {
                var p_flag = true;
                var response = JSON.parse(response);
                $.each(response.data, function(key, program) {
                    if (selected_program == program.id) {
                        p_flag = false;
                        $('#programs').append(new Option(program.name, program.id, true, true));
                    } else {
                        $('#programs').append(new Option(program.name, program.id)); 
                    }
                });
                if (p_flag) {
                    // after application filter dropdown change
                    // check program dropdown for selected program
                    // or remove  availability content
                    $(document).find(".AjaxContent").html('');
                }

                /*if (application_filter == '') {
                    $('.avg_seats').addClass('d-none');
                } else {
                    var row_1 = '';
                    var row_2 = '';
                    
                    $.each(response.avg_data.data, function(key, value) {
                        var value = parseInt(value);
                        var avg = 0;
                        if ($.isNumeric(value) && value>0) {
                            avg = ((value*100)/response.avg_data.total).toFixed(2);    
                        }
                        
                        row_1 = row_1 + '<th>'+key+'</th>';
                        row_2 = row_2 + '<td>'+avg+'% (Total '+value+')</td>';
                    });
                    $('.avg_seats').find('thead').html('<tr>'+row_1+'</tr>');
                    $('.avg_seats').find('tbody').html('<tr>'+row_2+'</tr>');
                    // show average data
                    $('.avg_seats').removeClass('d-none');
                }*/
            }
        });
    });
    /* Home Zone start */
    $(document).on('keyup', '.homezone_field', function() {
        generateFieldTotal($(this), 'homezone');
    });
    $(document).on('keyup', '.race_field', function() {
        generateFieldTotal($(this), 'race');
    });
    function generateFieldTotal(e, filed_txt='') {
        let total_id = e.data('total_field_id');
        let total = 0;
        $('.'+filed_txt+'_field[data-total_field_id='+total_id+']').each(function() {
            if ($.isNumeric($(this).val())) {
                let fld_val = parseInt($(this).val());
                total += fld_val;
            }
        });
        $(document).find('#'+filed_txt+'_'+total_id).text(total);

        matchIndividualTotal(e);
    }

    function matchIndividualTotal(e) {
        let total_id = e.data('total_field_id');  
        let homezone_count = $(document).find(".homezone_field").length

        if(homezone_count > 0){

            let race_total = parseInt($('#race_'+total_id).text());
            let homezone_total = parseInt($('#homezone_'+total_id).text());

            if(race_total != homezone_total){
                $(document).find('#error_'+total_id).removeClass('d-none').addClass('has-error');
            }else{
                $(document).find('#error_'+total_id).removeClass('has-error').addClass('d-none');
            }
        }
    }
    /* Home Zone end */
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layouts.admin.app", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>