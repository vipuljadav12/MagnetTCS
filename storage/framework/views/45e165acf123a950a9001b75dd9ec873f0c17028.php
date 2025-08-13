    
<?php $__env->startSection('title'); ?>
  Edit Submission
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style type="text/css">
  .error {
        color: red;
    }
</style>
  <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Submission</div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="<?php echo e(url('admin/Submissions')); ?>" title="Go Back">Go Back</a></div>
        </div>
    </div>
    <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php 
        $acdTerms = [];
        $conduct_display = false;
    ?>
    
    <?php echo e(csrf_field()); ?>                    
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="grades0-tab" data-toggle="tab" href="#grades0" role="tab" aria-controls="grades0" aria-selected="false">General</a></li>
            <?php
              $available_methods = $available_methods_2 = [];
                if ($submission->first_choice != '') {
                  $available_methods = getSubmissionEligibilitiesIndividual($submission, 'first');
                }
                if ($submission->second_choice != '') {
                  $available_methods_2 = getSubmissionEligibilitiesIndividual($submission, 'second');
                }
              ?>

            <!-- For choice one -->
            <?php if($submission->first_choice != ''): ?>
                <?php 
                  $sprogram_id = getApplicationProgramId($submission->first_choice);
                  if(getProgramIBorNot($sprogram_id) == "Yes")
                  {
                    $conduct_display = true;
                  }
                ?>

                <?php $eligibilities = getEligibilitiesDynamic($submission->first_choice); ?>
                <?php $done_arr = [] ?>

              <?php $__currentLoopData = $eligibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if(in_array($value->eligibility_ype, $available_methods) && $value->eligibility_ype != "Composite Score" && !in_array($value->eligibility_type, $done_arr)): ?>

                    <li class="nav-item"><a class="nav-link" id="<?php echo e($key+1); ?>-tab" data-toggle="tab" href="#<?php echo e($key+1); ?>-tabcontent" role="tab" aria-controls="grades<?php echo e($key+1); ?>" aria-selected="false"><?php echo e(($value->alias_name != '' ? $value->alias_name : $value->eligibility_ype)); ?></a></li>
                    <?php $done_arr[] = $value->eligibility_type ?>

                    
                <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <!-- For choice two -->
            <?php if($submission->second_choice != ''): ?>
                <?php 
                  $sprogram_id = getApplicationProgramId($submission->second_choice);
                  if(getProgramIBorNot($sprogram_id) == "Yes")
                  {
                    $conduct_display = true;
                  }

                ?>
                <?php $eligibilities_2 = getEligibilitiesDynamic($submission->second_choice); ?>
                <?php $done_arr = [] ?>
              <?php $__currentLoopData = $eligibilities_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  // $etype_check = $eligibilities->where('eligibility_ype', '1');
                  $etype_check = $eligibilities->where('eligibility_ype', $value->eligibility_ype);
                  // check in availability and status in first choice
                  if ($etype_check->count() > 0) {
                    if(!in_array($value->eligibility_ype, $available_methods)) {
                      $etype_check = [];
                    }
                  }
                ?>
                <?php if(count($etype_check) <= 0): ?>
                  <?php if(in_array($value->eligibility_ype, $available_methods_2) && $value->eligibility_ype != "Composite Score" && !in_array($value->eligibility_type, $done_arr)): ?>
                      <li class="nav-item"><a class="nav-link" id="choice_2<?php echo e($key+1); ?>-tab" data-toggle="tab" href="#choice_2<?php echo e($key+1); ?>-tabcontent" role="tab" aria-controls="gradeschoice_2<?php echo e($key+1); ?>" aria-selected="false"><?php echo e(($value->alias_name != '' ? $value->alias_name : $value->eligibility_ype)); ?></a></li>
                      <?php $done_arr[] = $value->eligibility_type ?>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

             <?php if((isset($manually_updated) && $manual_processing == "Y") || (isset($offer_data->manually_updated) && $offer_data->manually_updated == "Y")): ?>
                 <li class="nav-item"><a class="nav-link" id="manual-tab" data-toggle="tab" href="#manual-tabcontent" role="tab" aria-controls="manualprocess" aria-selected="false">Manual Process</a></li>
             <?php endif; ?>
             <?php if($submission->manual_grade_change == "Y"): ?>
                 <li class="nav-item"><a class="nav-link" id="manual-grade-tab" data-toggle="tab" href="#manual-grade-tabcontent" role="tab" aria-controls="manualgradechange" aria-selected="false">Manual Grade Change</a></li>
             <?php endif; ?>

             <?php if($conduct_display): ?>
                <li class="nav-item"><a class="nav-link" id="incidence-tab" data-toggle="tab" href="#incidence-tabcontent" role="tab"  aria-selected="false">Conduct Criteria</a></li>
             <?php endif; ?>
             <li class="nav-item"><a class="nav-link" id="communication-tab" data-toggle="tab" href="#communication-tabcontent" role="tab" aria-controls="gradescomments" aria-selected="false">Custom Communication</a></li>
             <li class="nav-item"><a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments-tabcontent" role="tab" aria-controls="gradescomments" aria-selected="false">Comments</a></li>
             <li class="nav-item"><a class="nav-link" id="statuslogs-tab" data-toggle="tab" href="#statuslogs-tabcontent" role="tab">Status Logs</a></li>
             <li class="nav-item"><a class="nav-link" id="emailcommunication-tab" data-toggle="tab" href="#emailcommunication-tabcontent" role="tab">Email Communication</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="grades0" role="tabpanel" aria-labelledby="grades0-tab">
                <?php echo $__env->make("Submissions::template.submission_general", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <!-- For choice one -->
            <?php if($submission->first_choice != ''): ?>

              <?php $__currentLoopData = $eligibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                $choice_ary = [];
                $choice_ary['first'] = $value->eligibility_name;
                // check in availability and status in second choice
                if(!empty($eligibilities_2))
                {
                  $check_choice_2 = $eligibilities_2->where('eligibility_ype', $value->eligibility_ype)->first();
                }
                
                if (isset($check_choice_2)) {
                  if(in_array($value->eligibility_ype, $available_methods_2)){
                    $value_2 = $check_choice_2;
                    $choice_ary['second'] = $value_2->eligibility_name;
                  }
                }
                ?>
                <?php if(in_array($value->eligibility_ype, $available_methods)  && $value->eligibility_ype != "Composite Score"): ?>
                  <div class="tab-pane fade" id="<?php echo e($key+1); ?>-tabcontent" role="tabpanel" aria-labelledby="<?php echo e($key+1); ?>-tab">
                          <?php echo $__env->make("Submissions::template.submission_".str_replace(" ","_",strtolower($value->eligibility_ype)), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  </div>

                 
                <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <!-- For choice two -->
            <?php if($submission->second_choice != ''): ?>
              <?php $__currentLoopData = $eligibilities_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                // unset($value_2); //No need second loop
                $choice_ary = [];
                $choice_ary['second'] = $value->eligibility_name;
                // check in availability and status in first choice
                $check_choice_1 = $eligibilities->where('eligibility_ype', $value->eligibility_ype);
                if ($check_choice_1->count() > 0) {
                  if(!in_array($value->eligibility_ype, $available_methods)) {
                    $check_choice_1 = [];
                  }
                }
                ?>
                <?php if(count($check_choice_1) <= 0): ?>
                  <?php if(in_array($value->eligibility_ype, $available_methods_2)): ?>
                    <div class="tab-pane fade" id="choice_2<?php echo e($key+1); ?>-tabcontent" role="tabpanel" aria-labelledby="choice_2<?php echo e($key+1); ?>-tab">
                            <?php echo $__env->make("Submissions::template.submission_".str_replace(" ","_",strtolower($value->eligibility_ype)), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if((isset($manually_updated) && $manual_processing == "Y") || (isset($offer_data->manually_updated) && $offer_data->manually_updated == "Y")): ?>

              <div class="tab-pane fade" id="manual-tabcontent" role="tabpanel" aria-labelledby="manual-tab">
                  <?php echo $__env->make("Submissions::template.manual_process", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              </div>
            <?php endif; ?>

            <?php if($submission->manual_grade_change == "Y"): ?>
              <div class="tab-pane fade" id="manual-grade-tabcontent" role="tabpanel" aria-labelledby="manual-grade-tab">
                  <?php echo $__env->make("Submissions::template.manual_grade_change", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              </div>
            <?php endif; ?>

            <?php if($conduct_display): ?>
                  <div class="tab-pane fade" id="incidence-tabcontent" role="tabpanel" aria-labelledby="incidence-tab">
                    
                      <?php echo $__env->make("Submissions::template.submission_conduct_criteria", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  </div>
             <?php endif; ?>


            <div class="tab-pane fade" id="communication-tabcontent" role="tabpanel" aria-labelledby="communication-tab">
                <?php echo $__env->make("Submissions::template.submission_custom_communication", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class="tab-pane fade" id="comments-tabcontent" role="tabpanel" aria-labelledby="comments-tab">
                <?php echo $__env->make("Submissions::template.submission_comments", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class="tab-pane fade" id="statuslogs-tabcontent" role="tabpanel" aria-labelledby="statuslogs-tab">
                <?php echo $__env->make("Submissions::template.submission_statuslogs", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class="tab-pane fade" id="emailcommunication-tabcontent" role="tabpanel" aria-labelledby="emailcommunication-tab">
                <?php echo $__env->make("Submissions::template.submission_email_communication", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

        </div>
        
    

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Email Preview</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalContent">
            
          </div>
          <div class="modal-footer">
          </div>
        </div>
      </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" src="<?php echo e(url('/')); ?>/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo e(url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')); ?>"></script>

    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> <span id="loadmsg">API Successfully Started<br>It will take approx 1 minute to bring all student records.</span> </div></div>    
    <script>
        function showCDIDetails()
        {
            $("#cdi_details").removeClass("d-none");
        }

        $(document).on("change",".changeDate",function()
        {
            let year = $(document).find("#year").val();
            let month = $(document).find("#month").val();
            let day = $(document).find("#day").val();
            $(document).find("#birthday").val(year+"-"+month+"-"+day);
        });

        /* Grade Override Logic */

        $('#chk_grade_change').change(function() {
              var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
              if(click == "Y")
              {
                  $("#grade_change_comment").removeClass('d-none');
              }
              else
              {
                  $("#grade_change_comment").addClass('d-none');
              }
        });
        var gradeState = false;
        $('.grade_override').change(function() {
              var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
              $('#grade_override_status').val(click);
              $('#grade_override_comment').val('');
              $('#overrideAcademicGrade').modal({
                                            backdrop: 'static',
                                            keyboard: false
                                          });
        });

        function overrideAcademicGrade(){
          var comment = $('#grade_override_comment').val();
          var click = $('#grade_override_status').val();

          if(comment == ""){
            window.alert("Please enter comment.");
            $('#grade_override_comment').focus()
            return false; 
          }else{
            $.ajax({
              type: "get",
              url: '<?php echo e(url('admin/Submissions/override/grade')); ?>',
              data: {
                id:<?php echo e($submission->id); ?>,
                status:click,
                comment:comment
              },
              complete: function(data) {
                console.log('success');
                $('#overrideAcademicGrade').modal('hide');
              }
            });
          }
        }

        $(document).on('click','.overrideAcademicGradeNo',function(){
          $('#overrideAcademicGrade').modal('hide');
          if($('.grade_override').prop('checked')==true){
            $(".grade_override").trigger('click').prop('checked', false);
          }
          else{
            $(".grade_override").trigger('click').prop('checked', true);
          }

        });

        /* CDI Override Logic */
        var cdistate = false;
        $('.cdi_override').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $('#cdi_override_status').val(click);
            $('#cdi_override_comment').val('');
            $('#overrideConductDispInfo').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                      });
        });

        function overrideConductDispInfo(){
          var CDIcomment = $('#cdi_override_comment').val();
          var CDIclick = $('#cdi_override_status').val(); 
          
          if(CDIcomment == ""){
            window.alert("Please enter comment.");
            $('#cdi_override_comment').focus()
            return false; 
          }else{
            $.ajax({
              type: "get",
              url: '<?php echo e(url('admin/Submissions/override/cdi')); ?>',
              data: {
                  id:<?php echo e($submission->id); ?>,
                  status:CDIclick,
                  comment:CDIcomment
              },
              complete: function(data) {
                console.log('success');
                $('#overrideConductDispInfo').modal('hide');
              }
            });
          } 
        }

        $(document).on('click','.overrideConductDispInfoNo',function(){
          $('#overrideConductDispInfo').modal('hide');
          if($('.cdi_override').prop('checked')==true){
            $(".cdi_override").trigger('click').prop('checked', false);
          }
          else{
            $(".cdi_override").trigger('click').prop('checked', true);
          }
        });

        $("#first_choice").change(function(){
          var val = $(this).val();
          
          $('#second_choice > option').each(function(){
              if($(this).val() == val && val != "")
              {
                $(this).addClass("d-none");
                $(this).removeAttr("selected");
              }
              else
                $(this).removeClass("d-none");
          });
          var second_choice = $("#second_choice").val(); 
            if((val != '<?php echo e($submission->first_choice); ?>' || second_choice != '<?php echo e($submission->second_choice); ?>') && (val != "" || second_choice != "")){
              $('#add_comment').prop('disabled',false);
              $(document).find('#choice_comment').show();
            }else{
              $('#add_comment').prop('disabled',true);
              $(document).find('#choice_comment').hide();
            }
        })

        $("#second_choice").change(function(){
          var val = $(this).val();
          $('#first_choice > option').each(function(){
              if($(this).val() == val && val != "")
                $(this).addClass("d-none");
              else
                $(this).removeClass("d-none");
          });
           var first_choice = $("#first_choice").val();
          if((val != '<?php echo e($submission->second_choice); ?>' || first_choice != '<?php echo e($submission->first_choice); ?>') && (val != "" || first_choice != "")){
            $('#add_comment').prop('disabled',false);
            $(document).find('#choice_comment').show();
          }else{
            $('#add_comment').prop('disabled',true);
            $(document).find('#choice_comment').hide();
          }
        })

        $("#first_choice_final_status").change(function(){
          var val = $(this).val();
          
          $('#second_choice_final_status > option').each(function(){
              if($(this).val() == val && val != "")
              {
                if($(this).prop("selected") == true)
                {
                  $(this).removeAttr("selected");
                }
                $(this).addClass("d-none");
              }
              else
                $(this).removeClass("d-none");
          });
        })

        $("#second_choice_final_status").change(function(){
          var val = $(this).val();
          
          $('#first_choice_final_status > option').each(function(){
              if($(this).val() == val && val != "")
              {
                if($(this).prop("selected") == true)
                {
                  $(this).removeAttr("selected");
                }
                $(this).addClass("d-none");
              }
              else
                $(this).removeClass("d-none");
          });
        })

        $("#manual_first_choice").change(function(){
          var val = $(this).val();
          
          $('#manual_second_choice > option').each(function(){
              if($(this).val() == val && val != "")
              {
                if($(this).prop("selected") == true)
                {
                  $(this).removeAttr("selected");
                }
                $(this).addClass("d-none");
              }
              else
                $(this).removeClass("d-none");
          });
        })

        $("#manual_second_choice").change(function(){
          var val = $(this).val();
          $('#manual_first_choice > option').each(function(){
              if($(this).val() == val && val != "")
                $(this).addClass("d-none");
              else
                $(this).removeClass("d-none");
          });
        })
        ScreenOrientation.lock;


         $("#submission_status").change(function(){
                var val = $(this).val();
                $("#acpt_offer").addClass("d-none");
                if(val != '<?php echo e($submission->submission_status); ?>'){
                  $('#status_comment_box').prop('disabled',false);
                  $(document).find('#status_comment').show();

                  if(val == "Offered")
                  {
                    $("#acpt_offer").removeClass("d-none");
                    $("#changeprograms").removeClass("d-none");
                    $("#newstatus_label").html("New Program Status");
                  }
                  else if(val == "Waitlisted" || val == "Declined / Waitlist for other")
                  {
                    $("#changeprograms").removeClass("d-none");
                    $("#newstatus_label").html("Waitlisted Program");
                  }
                  else
                  {
                   $("#changeprograms").addClass("d-none"); 
                  }
                }else{
                  $('#status_comment_box').prop('disabled',true);
                  $(document).find('#status_comment').hide();
                   $("#changeprograms").addClass("d-none"); 
                }
            }
        );

         /* Fetch Availability */
         $("#newofferprogram").change(function(){
          $("#acpt_offer").addClass("d-none");
          if($(this).val() != "")
          {
              if($("#submission_status").val() == "Offered")
              {
                  $("#acpt_offer").removeClass("d-none");
                  $("#wrapperloading").show();
                  $("#loadmsg").html("Checking availability for selected program.");
                  $prg = $(this).val();
                  $.ajax({
                    type: "get",
                    url: '<?php echo e(url('/')); ?>/admin/Submissions/fetch/availability/' + $prg + "/<?php echo e($submission->next_grade); ?>",
                    success: function(response) {
                        var data = response;
                        $("#wrapperloading").hide();
                         swal({
                          title: '',
                          text: response,
                          type: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Yes',
                          cancelButtonText: 'No',
                        }).then(function(isConfirm) {
                        }, function(dismiss) {
                          $("#newofferprogram").val("");
                          // dismiss can be "cancel" | "close" | "outside"
                        });
                    }
                  });
              }

           }
         })
          
        /* submission academic grades script start */
        $('#store_grades_form').validate();

        gradeValidation();    
        function gradeValidation()
        {
            $(document).find('select[name^="academicYear"]').each(function(){
                $(this).rules('add', {
                    required: true,
                    messages: {
                        required: "Academic Year is required. "
                    }
                })
            });
            $(document).find('select[name^="academicTerm"]').each(function(){
                $(this).rules('add', {
                    required: true,
                    messages: {
                        required: "Academic Term is required. "
                    }
                })
            });
            $(document).find('input[name^="courseTypeID"]').each(function(){
                $(this).rules('add', {
                    required: true,
                    digits: true,
                    maxlength: 5,
                    messages: {
                        required: "Course Type ID is required. ",
                        maxlength: "No more than 5 digits"
                    }
                })
            });
           /* $(document).find('input[name^="courseName"]').each(function(){
                $(this).rules('add', {
                    required: true,
                    maxlength: 100,
                    messages: {
                        required: "Course Name is required. ",
                        maxlength: "No more than 100 characters."
                    }
                })
            });*/
            $(document).find('input[name^="numericGrade"]').each(function(){
                $(this).rules('add', {
                    required: true,
                    digits: true,
                    maxlength: 3,
                    messages: {
                        required: "Grade is required. ",
                        maxlength: "No more than 3 digits"
                    }
                })
            });
        }

        $('.add-grade').click(function(){
            var nextIndex = $('#grade-table').find('tbody>tr').length;
            var gradeclone = $('#grade-table').find('tbody>tr:first-child').clone();
            
            // Replacing dynamic array name value
            gradeclone.find('input,select').each(function(){
                // Removing error class from field
                $(this).removeClass('error');
                var name = $(this).attr('name');
                var name = name.match(/\w+/);
                // var name = name.replace('[', '').replace(']', '').replace(/\d+/, '');
                var newName = name+'['+nextIndex+']';
                $(this).attr('name', newName);
            });

            // removing hidden fields
            gradeclone.find('.grd_hidden').remove();
            // removing error label
            gradeclone.find('label.error').remove();
            gradeclone.find('input').val('');
            // Select default option of drop down
            gradeclone.find('select').each(function(){
                $(this).val($('option:first', this).val());
            });
            $('#grade-table').append(gradeclone);
            // Add validation
            gradeValidation();
        });

        // Delete Grade
        $('#del_grade').click(function(){
            $('#grade-table').find('tbody>tr').each(function(){
                if($(this).find('#chk_del_grade').prop('checked') == true){
                    $(this).remove();
                }
            });
        });
        /* submission academic grades script end */

        /* submission conduct disciplinary info script start */
        $('#insterview_score_form').validate({
            rules: {
                b_info: {
                    digits: true,
                    maxlength: 5
                },
                c_info: {
                    digits: true,
                    maxlength: 5
                },
                d_info: {
                    digits: true,
                    maxlength: 5
                },
                e_info: {
                    digits: true,
                    maxlength: 5
                },
                susp: {
                    digits: true,
                    maxlength: 5
                },
                susp_days: {
                    digits: true,
                    maxlength: 5
                }
            },
            messages: {
              b_info: {
                  maxlength: 'No more than 5 digits.'
              },
              c_info: {
                  maxlength: 'No more than 5 digits.'
              },
              d_info: {
                  maxlength: 'No more than 5 digits.'
              },
              e_info: {
                  maxlength: 'No more than 5 digits.'
              },
              susp: {
                  maxlength: 'No more than 5 digits.'
              },
              susp_days: {
                  maxlength: 'No more than 5 digits.'
              }
            }
        });
        /* submission conduct disciplinary info script end */

        jQuery.validator.addMethod("notEqual", function(value, element, param) {
           return this.optional(element) || value != $(param).val();
          }, "This has to be different...");

        $('#generalSubmission').validate({
          rules: {
            first_name: {
                required:true
            },
            last_name: {
                required:true
            },
            parent_first_name: {
                required:true
            },
            parent_last_name: {
                required:true
            },
            address: {
                required:true
            },
            city: {
                required:true
            },
            state: {
                required:true
            },
            phone_number: {
                required:true
            },
            current_school: {
                required:true
            },
            parent_email: {
                required:true
            },
            first_choice: {
                required:true,
                notEqual: "#second_choice" 
            },
            second_choice: {
                //required:true,
                notEqual: "#first_choice" 
            },
            submission_status: {
                required:true
            },
            zip: {
                required:true,
            },
            choice_comment: {
                required:true,
            },
            status_comment: {
                required:true,
            },
            grade_add_comment: {required: true},
            newofferprogram: {required: true},
            last_date_online_acceptance: {required: true},
            last_date_offline_acceptance: {required: true},
          },
          messages: {
            first_name: {
                required: 'Student First Name is required.'
            },
            last_name: {
                required: 'Student Last Name is required.'
            },
            parent_first_name: {
                required: 'Parent First Name is required.'
            },
            parent_last_name: {
                required: 'Parent Last Name is required.'
            },
            address: {
                required: 'Home Address is required.'
            },
            city: {
                required: 'City is required.'
            },
            state: {
                required: 'State is required.'
            },
            phone_number: {
                required: 'Phone Number is required.'
            },
            current_school: {
                required: 'School is required.'
            },
            parent_email: {
                required: 'Parent Email is required.'
            },
            first_choice: {
                required: 'First Choice is required.'
            },
            second_choice: {
                required: 'Second Choice is required.'
            },
            submission_status: {
                required: 'Submission Status is required.'
            },
            choice_comment: {
                required: 'Comment is required.'
            },
            status_comment: {
                required: 'Comment is required.'
            }
          }
        });
        
        /* submission comments script start */
        $('#submission_comment_form').validate({
            rules: {
                comment: {
                    required: true
                }
            },
            messages: {
              comment: {
                  required: 'Please write few words into comment box.'
              }
            }
        });
        /* submission comments script end */

        if($("#manual_process_form").length > 0)
          {
          document.querySelector('#manual_process_form').addEventListener('submit', function(e) {
              var form = this;

              e.preventDefault(); // <--- prevent form from submitting
              if($("#first_choice_final_status").val() == "")
              {
                alert("Please select program status");
                return false;
              }

              if($("#manual_first_choice").val() == "")
              {
                alert("Please select first choice program");
                return false;
              }

              if($("#second_choice_final_status").length > 0)
              {
                  if($("#second_choice_final_status").val() == "")
                  {
                    alert("Please select program status");
                    return false;
                  }

                  if($("#manual_first_choice").val() == $("#manual_second_choice").val())
                  {
                     alert("First Choice and Second Choice program should be different");
                     return false;
                  }

                  if($("#manual_second_choice").val() == "")
                  {
                    alert("Please select second choice program");
                    return false;
                  }


              }





              if($("#last_date_online_acceptance").val() == "")
              {
                  alert("Select last online acceptance date");
                  return false;
              }
              if($("#last_date_offline_acceptance").val() == "")
              {
                  alert("Select last offline acceptance date");
                  return false;
              }

              $("#wrapperloading").show();
              $("#loadmsg").html("Checking availability for selected program.");

              if($("#first_choice_final_status").val() == "Offered" || $("#second_choice_final_status").val() == "Offered")
              {
                if($("#first_choice_final_status").val() == "Offered")
                  $prg = $("#manual_first_choice").val();
                else
                  $prg = $("#manual_second_choice").val();

                $.ajax({
                  type: "get",
                  url: '<?php echo e(url('/')); ?>/admin/Submissions/fetch/availability/' + $prg + "/<?php echo e($submission->next_grade); ?>",
                  success: function(response) {
                      var data = response;
                      $("#wrapperloading").hide();

                      swal({
                          title: '',
                          text: response,
                          type: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#3085d6',
                          cancelButtonColor: '#d33',
                          confirmButtonText: 'Yes',
                          cancelButtonText: 'No',
                        }).then(function(isConfirm) {
                          form.submit();
                        })
                  }
                });


              }
              else
              {
                form.submit();
              }

              

             
            });
        }

        if($("#manual_grade_form").length > 0)
        {

        document.querySelector('#manual_grade_form').addEventListener('submit', function(e) {
            var form = this;

            e.preventDefault(); // <--- prevent form from submitting
              if($("#manual_next_grade").val() == "<?php echo e($submission->next_grade); ?>")
              {
                alert("No changes made for grade");
                return false;
              }

              <?php if($submission->submission_status == "Offered and Accepted" || $submission->submission_status == "Offered"): ?>
                      $.ajax({
                        type: "get",
                        url: '<?php echo e(url('/')); ?>/admin/Submissions/fetch/availability/<?php echo e($submission->first_choice); ?>/' + $("#manual_next_grade").val(),
                        success: function(response) {
                            var data = response;
                            $("#wrapperloading").hide();

                            swal({
                                title: '',
                                text: response,
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                              }).then(function(isConfirm) {
                                form.submit();
                              })
                        }
                    });
              <?php else: ?>
                swal({
                  title: '',
                  text: "Are you sure you want to change Grade ?",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes',
                  cancelButtonText: 'No',
                }).then(function(isConfirm) {
                  form.submit();
                })
              <?php endif; ?>
           
          });
    }
        function sendIndividualOfferEmail()
        {
          $("#wrapperloading").show();
          $("#loadmsg").html("Sending offer email to parent.");
          $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/admin/Submissions/send/offer/email/<?php echo e($submission->id); ?>',
                success: function(response) {
                    var data = response;
                    $("#wrapperloading").hide();
                    alert("Email Sent Successfully");
                    document.location.reload();
                }
            });
        }


        function sendIndividualOfferEmail1()
        {
          $("#wrapperloading").show();
          $("#loadmsg").html("Sending offer email to parent.");
          $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/admin/Submissions/send/offer/email/<?php echo e($submission->id); ?>/Grade',
                success: function(response) {
                    var data = response;
                    $("#wrapperloading").hide();
                    alert("Email Sent Successfully");
                    document.location.reload();
                }
            });
        }


        function previewIndividualOfferEmail()
        {
          $("#wrapperloading").show();
          $("#loadmsg").html("Sending offer email to parent.");
          $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/admin/Submissions/send/offer/email/<?php echo e($submission->id); ?>/preview',
                success: function(response) {
                    var data = response;
                    $("#emaildiv").html(data);
                    $("#emaildiv").removeClass('d-none');
                    $("#wrapperloading").hide();
                    $("#previewemailnow").hide();
                    $("#sendemailnow").removeClass("d-none");
//                    alert("Email Sent Successfully");
                }
            });
        }

        function previewIndividualOfferEmail1()
        {
          $("#wrapperloading").show();
          $("#loadmsg").html("Sending offer email to parent.");
          $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/admin/Submissions/send/offer/email/<?php echo e($submission->id); ?>/preview',
                success: function(response) {
                    var data = response;
                    $("#emaildiv1").html(data);
                    $("#emaildiv1").removeClass('d-none');
                    $("#wrapperloading").hide();
                    $("#previewemailnow1").hide();
                    $("#sendemailnow1").removeClass("d-none");
//                    alert("Email Sent Successfully");
                }
            });
        }
        function fetchGradeManually()
        {
            $("#wrapperloading").show();
             $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/PowerSchool/fetch_grade_single_manual.php?id=<?php echo e($submission->id); ?>',
                success: function(response) {
                    var data = response;
                    /* swal({
                        text: "Student Grades Successfully Imported",
                        type: "success",
                        confirmButtonText: "OK"
                    });*/
                    $("#wrapperloading").hide();
                    $("#grade_fetch").hide();
                    document.location.reload();
                }
            });
        }

        function fetchStandardGradeManually()
        {
            $("#wrapperloading").show();
             $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/PowerSchool/fetch_standard_grade_single_manual.php?id=<?php echo e($submission->id); ?>',
                success: function(response) {
                    var data = response;
                    /* swal({
                        text: "Student Grades Successfully Imported",
                        type: "success",
                        confirmButtonText: "OK"
                    });*/
                    $("#wrapperloading").hide();
                    $("#grade_fetch").hide();
                    document.location.reload();
                }
            });
        }

        function fetchCDIManually()
        {
            $("#wrapperloading").show();
             $.ajax({
                type: "get",
                url: '<?php echo e(url('/')); ?>/INOW/fetch_cdi_single_manual.php?id=<?php echo e($submission->student_id); ?>',
                success: function(response) {
                    var data = response;
                     /*swal({
                        text: "Student CDI Successfully Imported",
                        type: "succss",
                        confirmButtonText: "OK"
                    });*/
                    $("#wrapperloading").hide();
                    $("#cdi_fetch").hide();
                     document.location.reload();
                }
            });
        }

        function showHideMCPSInfo(val)
        {
            if(val == "Yes")
            {
                $("#employee_id_div").removeClass("d-none");
                $("#work_location_div").removeClass("d-none");
                $("#employee_first_name_div").removeClass("d-none");
                $("#employee_last_name_div").removeClass("d-none");

                //employee_id
                //work_location
                //employee_first_name
                //employee_last_name
            }
            else
            {
                $("#employee_id_div").addClass("d-none");
                $("#work_location_div").addClass("d-none");
                $("#employee_first_name_div").addClass("d-none");
                $("#employee_last_name_div").addClass("d-none");


            }
        }


         CKEDITOR.replace('letter_body',{
             toolbar : 'Basic',
            toolbarGroups: [
                    { name: 'document',    groups: [ 'mode', 'document' ] },            // Displays document group with its two subgroups.
                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },           // Group's name will be used to create voice label.
                    { name: 'basicstyles', groups: [ 'cleanup', 'basicstyles'] },
                
                    '/',                                                                // Line break - next group will be placed in new line.
                    { name: 'links' }
                ],
                on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });

         CKEDITOR.replace('mail_body',{
             toolbar : 'Basic',
            toolbarGroups: [
                    { name: 'document',    groups: [ 'mode', 'document' ] },            // Displays document group with its two subgroups.
                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },           // Group's name will be used to create voice label.
                    { name: 'basicstyles', groups: [ 'cleanup', 'basicstyles'] },
                
                    '/',                                                                // Line break - next group will be placed in new line.
                    { name: 'links' }
                ],
                on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });

         function validateCustomLetter()
         {
            var messageLength = CKEDITOR.instances['letter_body'].getData().replace(/<[^>]*>/gi, '').length;
            if( !messageLength ) {
                alert("Please enter letter body");
                return false;
            }
         }


         function validateCustomEmail()
         {
            var messageLength = CKEDITOR.instances['mail_body'].getData().replace(/<[^>]*>/gi, '').length;
            if( !messageLength ) {
                alert("Please enter email body");
                return false;
            }
            if($.trim($("#mail_subject").val()) == "")
            {
                alert("Please enter email subject");
                return false
            }
            /* $.ajax({
                type: "post",
                url: '<?php echo e(url('admin/CustomCommunication/individual/email')); ?>',
                data: {
                    "_token":"<?php echo e(csrf_token()); ?>",
                    id:<?php echo e($submission->id); ?>,
                    mail_subject: $("#mail_subject").val(),
                    mail_body: $("#mail_body").val()
                },
                complete: function(data) {
                    alert("Mail sent successfully.");
                }
            });*/
         }


        $(document).on("click",".emailpreview",function()
        {
            $(document).find("#modalContent").html("");
            var url  = "<?php echo e(url('/admin/CustomCommunication/email/individual/')); ?>/"+$(this).attr("data-id");
            $.ajax({
                url: url,
                success:function(result)
                {
                    $(document).find("#modalContent").html(result);
                }
            });
            $(document).find("#exampleModal").modal();
        });   

function previewCommunicationEmail(id)
{
      $(document).find("#modalContent").html("");
      var url  = "<?php echo e(url('/admin/Submissions/preview/email/')); ?>/"+id;
      $.ajax({
          url: url,
          success:function(result)
          {
              $(document).find("#modalContent").html(result);
          }
      });
      $(document).find("#exampleModal").modal();
}     

function updateGrade(id)
{
    var actual_numeric_grade = $("#actual_numeric_grade"+id).val();
    if($("#actual_numeric_grade"+id).val() == '')
      $("#actual_numeric_grade"+id).val(0);


    var advanced_course_bonus = $("#advanced_course_bonus"+id).val();
    if($("#advanced_course_bonus"+id).val() == '')
      $("#advanced_course_bonus"+id).val(0);
    $("#numericGrade"+id).val(parseInt(actual_numeric_grade) + parseInt(advanced_course_bonus));

}

 $("#last_date_online_acceptance").datetimepicker({
        numberOfMonths: 1,
        autoclose: true,
         startDate:new Date(),
        dateFormat: 'mm/dd/yy hh:ii'
    })

    $("#last_date_offline_acceptance").datetimepicker({
        numberOfMonths: 1,
        autoclose: true,
         startDate:new Date(),
        dateFormat: 'mm/dd/yy hh:ii'
    })
 $("#last_date_online_acceptance1").datetimepicker({
        numberOfMonths: 1,
        autoclose: true,
         startDate:new Date(),
        dateFormat: 'mm/dd/yy hh:ii'
    })

    $("#last_date_offline_acceptance1").datetimepicker({
        numberOfMonths: 1,
        autoclose: true,
         startDate:new Date(),
        dateFormat: 'mm/dd/yy hh:ii'
    })

    function sendWritingPromptMail(element) {
      var submission_id = element.getAttribute('data-s_id');
      var choice = element.getAttribute('data-s_choice');
      var parent_email = element.getAttribute('data-parent_email');
      $.ajax({
        url: '<?php echo e(url('admin/WritingPrompt/send/linkmail')); ?>',
        type: 'POST',
        data: {
          '_token': "<?php echo e(csrf_token()); ?>",
          submission_id: submission_id,
          choice: choice,
          parent_email: parent_email
        },
        success: function (response){
          // alert(response);
          if (response.match(/true/)) {
            alert('Email sent.');
          } else{
            alert('Something went wrong , Please try again.');
          }
        }
      });
    }

    function clearWritingPrompt(element) {
      var submission_id = element.getAttribute('data-s_id');
      var program_id = element.getAttribute('data-p_id');
      
      $.ajax({
        url: '<?php echo e(url('admin/WritingPrompt/clear')); ?>',
        type: 'POST',
        data: {
          '_token': "<?php echo e(csrf_token()); ?>",
          submission_id: submission_id,
          program_id: program_id
        },
        success: function (response){
          // alert(response);
          if (response.match(/true/)) {
            location.reload();
          } else{
            alert('Something went wrong , Please try again.');
          }
        }
      });
    }

    // function printWritingPrompt(element) {
    //   var submission_id = element.getAttribute('data-s_id');
    //   var program_id = element.getAttribute('data-p_id');
      
    //   $.ajax({
    //     url: '<?php echo e(url('admin/WritingPrompt/print')); ?>',
    //     type: 'POST',
    //     data: {
    //       '_token': "<?php echo e(csrf_token()); ?>",
    //       submission_id: submission_id,
    //       program_id: program_id
    //     },
    //     success: function (response){
    //       // alert(response);
    //       if (response.match(/true/)) {
    //         location.reload();
    //       } else{
    //         alert('Something went wrong , Please try again.');
    //       }
    //     }
    //   });
    // }

    function validateTeacherEmail(form)
    {
      var emailval = "";
      $(form).find("input").each(
        function(i){  
            if($(this).attr('type') == "text")
            {
              $(this).val($.trim($(this).val()));
              emailval = $(this).val();
            }
        }
      );

      if(emailval == "")
      {
          alert("Please enter email");
          return false;   
      }
      if(!isEmail(emailval))
      {
          alert("Please enter valid email");
          return false;
      }

    }



    function isEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    }



    </script>
    <!-- Script for Submission Test Score Tab -->
    <?php echo $__env->yieldContent('submission_test_score_script'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>