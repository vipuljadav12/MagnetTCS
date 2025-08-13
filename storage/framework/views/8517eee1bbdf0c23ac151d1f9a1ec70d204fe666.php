<?php $__env->startSection('title'); ?>
	View/Edit Submissions
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style type="text/css">
    .alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    border-radius: 0.25rem;
}
.custom-select{
    margin: 5px !important;
}
.pagination {
    display: inline-block;
    padding-left: 0;
    margin: 20px 0;
    border-radius: 4px
}

.pagination>li {
    display: inline
}

.pagination>li>a,
.pagination>li>span {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.428571429;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd
}

.pagination>li:first-child>a,
.pagination>li:first-child>span {
    margin-left: 0 !important;
    border-bottom-left-radius: 4px !important;
    border-top-left-radius: 4px !important;
}

.pagination>li:last-child>a,
.pagination>li:last-child>span {
    border-top-right-radius: 4px !important;
    border-bottom-right-radius: 4px !important;
}

.pagination>li>a:hover,
.pagination>li>span:hover,
.pagination>li>a:focus,
.pagination>li>span:focus {
    background-color: #eee
}

.pagination>.active>a,
.pagination>.active>span,
.pagination>.active>a:hover,
.pagination>.active>span:hover,
.pagination>.active>a:focus,
.pagination>.active>span:focus {
    z-index: 2;
    color: #fff;
    cursor: default;
    background-color: #428bca !important;
    border-color: #428bca !important
}

.pagination>.disabled>span,
.pagination>.disabled>a,
.pagination>.disabled>a:hover,
.pagination>.disabled>a:focus {
    color: #999;
    cursor: not-allowed;
    background-color: #fff;
    border-color: #ddd
}

.pagination-lg>li>a,
.pagination-lg>li>span {
    padding: 10px 16px;
    font-size: 18px
}

.pagination-lg>li:first-child>a,
.pagination-lg>li:first-child>span {
    border-bottom-left-radius: 6px;
    border-top-left-radius: 6px
}

.pagination-lg>li:last-child>a,
.pagination-lg>li:last-child>span {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px
}

.pagination-sm>li>a,
.pagination-sm>li>span {
    padding: 5px 10px;
    font-size: 12px
}

.pagination-sm>li:first-child>a,
.pagination-sm>li:first-child>span {
    border-bottom-left-radius: 3px;
    border-top-left-radius: 3px
}

.pagination-sm>li:last-child>a,
.pagination-sm>li:last-child>span {
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px
}

.pager {
    padding-left: 0;
    margin: 20px 0;
    text-align: center;
    list-style: none
}

.pager:before,
.pager:after {
    display: table;
    content: " "
}

.pager:after {
    clear: both
}

.pager:before,
.pager:after {
    display: table;
    content: " "
}

.pager:after {
    clear: both
}

.pager li {
    display: inline
}

.pager li>a,
.pager li>span {
    display: inline-block;
    padding: 5px 14px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 15px
}

.pager li>a:hover,
.pager li>a:focus {
    text-decoration: none;
    background-color: #eee
}

.pager .next>a,
.pager .next>span {
    float: right
}

.pager .previous>a,
.pager .previous>span {
    float: left
}

.pager .disabled>a,
.pager .disabled>a:hover,
.pager .disabled>a:focus,
.pager .disabled>span {
    color: #999;
    cursor: not-allowed;
    background-color: #fff
}

.loader {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(255,255,255,0.7);
    z-index:9999;
    display:none;
}

@-webkit-keyframes spin {
    from {-webkit-transform:rotate(0deg);}
    to {-webkit-transform:rotate(360deg);}
}

@keyframes  spin {
    from {transform:rotate(0deg);}
    to {transform:rotate(360deg);}
}

.loader::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:#3498db;
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .8s linear infinite;
    animation: spin .8s linear infinite;
}
</style>
<link href="<?php echo e(url('/resources/assets/admin/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" />
	<div class="card shadow w-100">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">View/Edit Submissions</div>
            <div class="">
                <a href="<?php echo e(url('/')); ?>/admin/submission" class="btn btn-sm btn-secondary" title="">Add Submission</a>
            </div>
            <div class=" d-none">
                <div class="d-inline-block position-relative">
                    <a href="javascript:void(0);" onClick="custfilter();" class="d-inline-block border pt-5 pb-5 pl-10 pr-10" title=""><span class="d-inline-block mr-10">Filter</span> <i class="fas fa-caret-down"></i></a>
                    <div class="filter-box border shadow" style="display: none;">
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline01" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline01">Submission ID</label>
                            </div>
                        </div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline001" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline001">State ID</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline02" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline02">Open Enrollment</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline03" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline03">First Name</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline04" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline04">Last Name</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline05" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline05">Race</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline06" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline06">Birthday</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline07" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline07">Current School</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline08" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline08">Current Grade</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline09" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline09">Next Grade</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline10" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline10">Status</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline11" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline11">Awarded School</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline12" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline12">First Choice</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline13" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline13">Second Choice</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline14" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline14">Third Choice</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline15" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline15">Form</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline16" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline16">Student Status</label></div></div>
                        <div class="">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="customRadioInline17" name="customRadioInline00" class="custom-control-input" value="">
                                <label class="custom-control-label" for="customRadioInline17">Special Accommodations</label></div></div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
    <div class="card shadow">
        <div class="card-body">
    	<?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <div class="row col-md-12" id="submission_filters">
             <select class="form-control col-md-3 custom-select custom-select2 enrollment_yr d-none">
                <option value="">Select Enrollment Year</option>
                <?php $__currentLoopData = $all_data['enroll_year']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($yr->school_year); ?>"><?php echo e($yr->school_year); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
                <select class="form-control col-md-3 custom-select custom-select2 sel_race">
                    <option value="">Select Race</option>
                    <?php $__currentLoopData = $all_data['race']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ra->race); ?>"><?php echo e($ra->race); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 curr_school">
                    <option value="">Select Current School</option>
                    <?php $__currentLoopData = $all_data['current_school']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curr_school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($curr_school->current_school); ?>"><?php echo e($curr_school->current_school); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 curr_grade">
                    <option value="">Select Current Grade</option>
                    <?php $__currentLoopData = $all_data['current_grade']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curr_grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($curr_grade->current_grade); ?>"><?php echo e($curr_grade->current_grade); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 next_grade">
                    <option value="">Select Next Grade</option>
                    <?php $__currentLoopData = $all_data['next_grade']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $next_grades): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($next_grades->next_grade); ?>"><?php echo e($next_grades->next_grade); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 first_choice_program">
                    <option value="">Select First Choice Program</option>
                    <?php $__currentLoopData = $all_data['first_programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $first_program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($first_program->id); ?>"><?php echo e($first_program->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 second_choice_program">
                    <option value="">Select Second Choice Program</option>
                    <?php $__currentLoopData = $all_data['second_programs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $second_program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($second_program->id); ?>"><?php echo e($second_program->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 forms">
                    <option value="">Select Form</option>
                    <?php $__currentLoopData = $all_data['forms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($form->form_id); ?>"><?php echo e(findFormName($form->form_id)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 app_status">
                    <option value="">Select Application Status</option>
                     <?php $__currentLoopData = $all_data['submission_status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sub_status->submission_status); ?>"><?php echo e($sub_status->submission_status); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                </select>
                <?php $blank = false ?>
                <select class="form-control col-md-3 custom-select custom-select2 zoned_school">
                    <option value="">Select Zoned School</option>
                     <?php $__currentLoopData = $all_data['zoned_school']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoned_schools): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($zoned_schools->zoned_school != '' || !$blank): ?>
                                <option value="<?php echo e($zoned_schools->zoned_school); ?>"><?php echo e($zoned_schools->zoned_school); ?></option>
                        <?php endif; ?>
                         <?php if($zoned_schools->zoned_school == ""): ?>
                            <?php $blank = true ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 stu_type">
                    <option value="">Select Student Type</option>
                    <option value="current">Current</option>
                    <option value="new">New</option>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 awarded_school">
                    <option value="">Select Awarded Program</option>
                     <?php $__currentLoopData = $all_data['awarded_school']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $awarded_programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($awarded_programs->name != ''): ?>
                                <option value="<?php echo e($awarded_programs->name); ?>"><?php echo e($awarded_programs->name); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-control col-md-3 custom-select custom-select2 gender">
                    <option value="">Select Gender</option>
                     <?php $__currentLoopData = $all_data['gender']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($gender->gender != ''): ?>
                                <option value="<?php echo e($gender->gender); ?>"><?php echo e($gender->gender); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                 <select class="form-control col-md-3 custom-select custom-select2 late_submission">
                    <option value="">Select Submission Type</option>
                    <option value="">All</option>
                    <option value="N">Submissions</option>
                    <option value="Y">Late Submissions</option>
                </select>

        </div>
            <div class="loader" id="loader" style="display:none;"></div>

            <div class="pt-20 pb-20">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped mb-0 w-100">
                        <thead>
                            <tr>
                                <th class="align-middle">Submission ID</th> <!--0-->
                                <th class="align-middle">State ID</th><!--1-->
                                <th class="align-middle">Enrollment Year</th><!--2-->
                                <th class="align-middle">Student Name</th><!--3-->
                                <th class="align-middle">Parent Name</th><!--4-->
                                <th class="align-middle">Phone</th><!--5-->
                                <th class="align-middle">Address</th><!--6-->
                                <th class="align-middle">Parent Email</th><!--7-->
                                <th class="align-middle">Race</th><!--8-->
                                <th class="align-middle">Date of Birth</th><!--9-->
                                <th class="align-middle">Current School</th><!--10-->
                                <th class="align-middle">Current Grade</th><!--11-->
                                <th class="align-middle">Next Grade</th><!--12-->
                                <th class="align-middle">First Program Choice</th><!--13-->
                                <th class="align-middle">Second Program Choice</th><!--14-->
                                <th class="align-middle">Submitted at</th><!--15-->
                                <th class="align-middle">Form</th><!--16-->
                                <th class="align-middle">Application Status</th><!--17-->
                                <th class="align-middle">Zoned School</th><!--18-->
                                <th class="align-middle">Student Type</th><!--19-->
                                <th class="align-middle">Confirmation No</th><!--20-->
                                <th class="align-middle">Gifted Status</th><!--21-->
                                <th class="align-middle">Awarded Program</th><!--22-->
                                <th class="align-middle">Late Submission</th><!--23-->

                            </tr>
                        </thead>
                        <tbody>
                            <tr></tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.buttons.min.js"></script>
<!--<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/jszip.min.js"></script>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/pdfmake.min.js"></script>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/vfs_fonts.js"></script>-->
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/buttons.html5.min.js"></script>
	<script type="text/javascript">
        var enroll_yr=sel_race=curr_school=curr_grade=next_grade=app_status=zoned_school=stu_type=first_choice_program=second_choice_program=form=awarded_school=gender=late_submission="";
        var dtbl_submission_list = $("#datatable").DataTable({
            "aaSorting": [],
            'serverSide': true,
                'ajax': {
                    url: "<?php echo e(url('admin/Submissions/getsubmissions')); ?>",
                    "data": function ( d ) {
                        d.enroll_yr = enroll_yr;
                        d.sel_race = sel_race;
                        d.curr_school = curr_school;
                        d.curr_grade = curr_grade;
                        d.next_grade = next_grade;
                        d.first_choice_program = first_choice_program;
                        d.second_choice_program = second_choice_program;
                        d.app_status = app_status;
                        d.zoned_school = zoned_school;
                        d.gender = gender;
                        d.stu_type = stu_type;
                        d.form = form;
                        d.awarded_school = awarded_school;
                        d.late_submission = late_submission;

                    }
                    //type: 'POST'
                },
             dom: 'Bfrtip',
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Submissions',
                        text:'Export to Excel',
                        //Columns to export
                       action: newexportaction,
                   }
                ]
            });

         // Each column dropdown filter
        /*$("#datatable thead th").each( function ( i ) {
            // Disable dropdown filter for disalble_dropdown_ary (index=0)
            var disalble_dropdown_ary = [0, 1, 3, 5, 9, 14, 15, 16, 17, 18 ,19];//13
            if ($.inArray(i, disalble_dropdown_ary) == -1) {
                var column_title = $(this).text();
                if(column_title=="Status")
                    column_title = "Application Status";
                if(column_title=="New/Current")
                    column_title = "Student Status";
                if(column_title=="Awarded School")
                    column_title = "Zoned School";
                
                var select = $('<select class="form-control col-md-3 custom-select custom-select2"><option value="">Select '+column_title+'</option></select>')
                    .appendTo( $('#submission_filters') )
                    .on( 'change', function () {
                        dtbl_submission_list.column( i )
                            .search($(this).val())
                            .draw();
                    } );
         
                dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {
                    str = d.replace('<div class="alert1 alert-success p-10 text-center d-block">', "");
                    str = str.replace('<div class="alert1 alert-danger p-10 text-center d-block">', "");
                    str = str.replace('<div class="alert1 alert-warning p-10 text-center d-block">', "");
                    str = str.replace('</div>', "");
                    select.append( '<option value="'+str+'">'+str+'</option>' )
                } );
            }
        } );*/
        // Hide Columns
        dtbl_submission_list.columns([2, 4, 5, 6, 7, 8, 9, 13, 14, 16, 18, 20, 21, 22, 23]).visible(false);

        $(".enrollment_yr").change(function(){
            enroll_yr = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".sel_race").change(function(){
            sel_race = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".curr_school").change(function(){
            curr_school = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".curr_grade").change(function(){
            curr_grade = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".next_grade").change(function(){
            next_grade = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".app_status").change(function(){
            app_status = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
         $(".forms").change(function(){
            form = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
         $(".gender").change(function(){
            gender = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".zoned_school").change(function(){
            zoned_school = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".stu_type").change(function(){
            stu_type = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".first_choice_program").change(function(){
            first_choice_program = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".second_choice_program").change(function(){
            second_choice_program = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".awarded_school").change(function(){
            awarded_school = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        $(".late_submission").change(function(){
            late_submission = $(this).val();
            dtbl_submission_list.ajax.reload();
        })
        
        
function newexportaction(e, dt, button, config) {
    $('#loader').show();
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        dt.one('preDraw', function (e, settings) {
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
    
            $('#loader').hide();
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>