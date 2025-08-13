<?php $__env->startSection('title'); ?>
    Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('resources/assets/admin/css/Chart.min.css')); ?>"> 
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Dashboard</div>
         <?php if((checkPermission(Auth::user()->role_id,'dashboard') == 1)): ?>
            <div class="">
                <select class="form-control custom-select w-200" id="switch_dashboard">
                    <option value="magnet">Super Admin - Application</option>
                    <option value="mcpss" selected>District Admin - Application</option>
                    <option value="superoffer">Super Admin - Offer</option>
                    <option value="districtoffer">District Admin - Offer</option>
                </select>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-address-card"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Applicants This Enrollment Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold"><?php echo e(isset($data['total_applicants_per_enrollment_period']) ? $data['total_applicants_per_enrollment_period'] : 0); ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-address-book"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Applicants This Application Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold"><?php echo e(isset($data['total_applicants_per_application_period']) ? $data['total_applicants_per_application_period'] : 0); ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="fas fa-user-check"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Current Students This Application Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold"><?php echo e(isset($data['total_current_students_per_application_period']) ? $data['total_current_students_per_application_period'] : 0); ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="fas fa-user-times"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Non Current Students This Application Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold"><?php echo e(isset($data['total_noncurrent_students_per_application_period']) ? $data['total_noncurrent_students_per_application_period'] : 0); ?></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-lg-6 mb-20">
        <div class="card shadow h-100 mb-0">
            <div class="card-header">Submission Status</div>
            <div class="card-body">
                <canvas id="myChart1" width="100%" height="70"></canvas>
                <div class="custom-legends" id="js-legend1"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mb-20">
        <div class="card shadow h-100 mb-0">
            <div class="card-header">Applicants by Race</div>
            <div class="card-body">
                <canvas id="myChart2" width="100%" height="70"></canvas>
                <div class="custom-legends" id="js-legend2"></div>
            </div>
        </div>
    </div>
</div>
<div class="">
    <div class="card shadow">
        <div class="card-header">Number of Applicants Per Program</div>
        <div class="card-body">
            <div class="table-responsive" style="height: 465px; overflow-y: auto;">
                <table class="table table-striped mb-0" id="tbl_applicants_each_program">
                    <thead>
                        <tr>
                            <th class="align-middle" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program Name</th>
                            <?php if(isset($data['all_grades'])): ?>
                                <?php $__currentLoopData = $data['all_grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="align-middle text-center w-90" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important"><?php echo e(isset($value->name) ? $value->name : ''); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <th class="align-middle text-center w-120" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $skip_programs = [];
                            $applicants_each_program = [];
                            if (!empty($data['first_choice_grade_count'])) {
                                $applicants_each_program[] = $data['first_choice_grade_count'];
                            }
                            if (!empty($data['second_choice_grade_count'])) {
                                $applicants_each_program[] = $data['second_choice_grade_count'];
                            }
                        ?>
                        <?php if(isset($applicants_each_program)): ?>
                            <?php $__currentLoopData = $applicants_each_program; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grades_choice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $grades_choice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(in_array($key, $skip_programs) == false): ?>
                                    <?php if($loop->parent->iteration == 1): ?>
                                        <?php $disp = 1 ?>
                                    <?php else: ?>
                                        <?php $disp = 2 ?>
                                    <?php endif; ?>
                                <tr>
                                    <td class=""><?php echo e(getProgramName($key)); ?> - Choice <?php echo e($disp); ?></td>
                                    <?php if(isset($data['all_grades'])): ?>
                                        <?php
                                            $total_value = 0;
                                        ?>
                                        <?php $__currentLoopData = $data['all_grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(array_key_exists($grade->name, $value)): ?>
                                                <?php
                                                    $total_value += $value[$grade->name];
                                                ?>
                                                <td class="text-center"><div class="text-center alert-info p-10"><?php echo e($value[$grade->name]); ?></div></td>    
                                            <?php else: ?>
                                                <td class="text-center"><div class="text-center alert-warning p-10">NA</div></td>
                                            <?php endif; ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <td class="text-center"><div class="text-center alert-success p-10"><?php echo e($total_value); ?></div></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($loop->parent->iteration == 1 && isset($applicants_each_program[1][$key])): ?>
                                <tr>
                                    <td class=""><?php echo e(getProgramName($key)); ?> - Choice 2</td>
                                    <?php if(isset($data['all_grades'])): ?>
                                        <?php
                                            $total_value = 0;
                                        ?>
                                        <?php $__currentLoopData = $data['all_grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(array_key_exists($grade->name, $applicants_each_program[1][$key])): ?>
                                                <?php
                                                    $total_value += $applicants_each_program[1][$key][$grade->name];
                                                ?>
                                                <td class="text-center"><div class="text-center  alert-info p-10"><?php echo e($applicants_each_program[1][$key][$grade->name]); ?></div>
                                                </td>
                                            <?php else: ?>
                                                <td class="text-center"><div class="text-center  alert-warning p-10">NA</div></td>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            array_push($skip_programs, $key);
                                        ?> 
                                    <?php endif; ?>                           
                                    
                                    <td class="text-center"><div class="text-center  alert-success p-10"><?php echo e($total_value); ?></div></td>
                                </tr>
                                <?php endif; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="">
    <div class="card shadow">
        <div class="card-header">Number of Applicants Per Current School</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="tbl_home_zone_school">
                    <thead>
                        <tr>
                            <th class="align-middle">School Name</th>
                            <?php if(isset($data['all_grades'])): ?>
                                <?php $__currentLoopData = $data['all_grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="align-middle text-center w-90"><?php echo e(isset($value->name) ? $value->name : ''); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <th class="align-middle text-center w-120">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($data['zoned_school'])): ?>
                        <?php $__currentLoopData = $data['zoned_school']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school_name => $grades_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $total_value = 0;
                            ?>
                            <tr>
                                <td class=""><?php echo e($school_name); ?></td>
                                <?php if(isset($data['all_grades'])): ?>
                                <?php $__currentLoopData = $data['all_grades']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(array_key_exists($grade->name, $grades_data)): ?>
                                        <?php
                                            $total_value += $grades_data[$grade->name];
                                        ?>
                                        <td class="text-center"><div class="text-center alert-info p-10"><?php echo e($grades_data[$grade->name]); ?></div></td>
                                    <?php else: ?>
                                        <td class="text-center"><div class="text-center alert-warning p-10">NA</div></td>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                <td class="text-center"><div class="text-center alert-success p-10"><?php echo e($total_value); ?></div></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" src="<?php echo e(asset('resources/assets/admin/js/Chart.min.js')); ?>"></script> 
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="<?php echo e(url('/resources/assets/admin')); ?>/js/bootstrap/buttons.html5.min.js"></script>

<script type="text/javascript">

    
    $('#tbl_updated_race_comp').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Updated-Racial',
                text:'Export to Excel',
           }
        ]

    });

     $('#tbl_applicants_each_program').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Submissions-Count',
                text:'Export to Excel',
           }
        ]

    });

    $('#tbl_home_zone_school').DataTable({
        paging: false,
        info: false,
        ordering: false,
        sDom: 'lrtip',
        initComplete: function () {
            this.api().columns(0).every( function () {
                var column = this;
                var select = $('<select class="form-control col-3" style="float:right; margin-bottom: 10px;" id="filter_zoneschool"><option>Select Current School</option></select>')
                    .prependTo( $("#tbl_home_zone_school").parent())
                    .on( 'change', function () {
                       var term = $(this).val();
 
                        column
                            .search( term ? '^'+term+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    var selected = '';
                    if (j==0) {
                        //selected = 'selected';
                    }
                    var tmptxt = d.replace('<div class="alert alert-info">','');
                     tmptxt = tmptxt.replace('</div>','');
                    select.append( '<option '+selected+' value="'+tmptxt+'">'+tmptxt+'</option>' )
                } );
            } );
            $('#filter_zoneschool').trigger('change');
        }
    });

    var dynamicColors = function() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
    }

    /* chart scripting start */
    /*Chart 1*/    
    var astatus = <?php echo json_encode($data['submission_status'] ?? []); ?>;
    var status_keys = Object.keys(astatus);
    var status_values = Object.values(astatus);

    var bg_colors_1 = [];

    var default_bg_colors_2 = ['#117899','#0d3c55','#c02e1d','#d94e1f','#f16c20','#ef8b2c','#ecaa38'];
    var total_rstatus_value = 0;
    $.each(status_values, function(key, value){
        total_rstatus_value += parseInt(value);
        if(default_bg_colors_2.length > 0) {
            bg_colors_1.push(default_bg_colors_2.shift());
        }else{
            bg_colors_1.push(dynamicColors());
        }
    });

    // for (var i=0; i<=1; i++){
    //  bg_colors_1.push(dynamicColors()); 
    // }
   var data1 = {
        labels: status_keys,
        datasets: [{
            label: '',
            data: status_values,
            backgroundColor: bg_colors_1,
            borderWidth: 2
        }]
    };   
    var ctx1 = document.getElementById('myChart1').getContext('2d');
    var myChart1 = new Chart(ctx1, {
        type: 'doughnut',
        data: data1,
        options: {
            responsive: true,
            legend : {
                display: false
            },
        }
    });
    //legends
    document.getElementById('js-legend1').innerHTML = myChart1.generateLegend(); 
    Chart.pluginService.register({
      beforeDraw: function(chart) {
        var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
        ctx1.restore();
        var fontSize = (height / 114).toFixed(2);
        ctx1.font = fontSize + "em Open Sans";
        ctx1.textBaseline = "middle";
        var text = total_rstatus_value,
            textX = Math.round((width - ctx1.measureText(text).width) / 2),
            textY = height / 2;
        ctx1.fillText(text, textX, textY);
        ctx1.save();
      }
    });      
    // center text
    

    /*Chart 2*/
    var race = <?php echo json_encode($data['race'] ?? []); ?>;
    var race_keys = Object.keys(race);
    var race_values = Object.values(race);
    var bg_colors_2 = [];
    var default_bg_colors_2 = ['#117899','#0d3c55','#c02e1d','#d94e1f','#f16c20','#ef8b2c','#ecaa38'];
    // var default_bg_colors_2 = ['#1395ba','#117899','#0f5b78','#0d3c55','#c02e1d','#d94e1f','#f16c20','#ef8b2c','#ecaa38'];
    var total_race_value = 0;
    $.each(race_values, function(key, value){
        total_race_value += parseInt(value);
        if(default_bg_colors_2.length > 0) {
            bg_colors_2.push(default_bg_colors_2.shift());
        }else{
            bg_colors_2.push(dynamicColors());
        }
    });

    var data2 = {
        labels: race_keys,
        datasets: [{
            label: '',
            data: race_values,
            backgroundColor: bg_colors_2,
            borderWidth: 2
        }]
    }    
    var ctx2 = document.getElementById('myChart2').getContext('2d');
    var myChart2 = new Chart(ctx2, {
        type: 'doughnut',
        data: data2,
        options: {
            responsive: true,
            legend : {
                display: false
            }
        }
    });    
    //legends
    document.getElementById('js-legend2').innerHTML = myChart2.generateLegend();    
    //// center text
    Chart.pluginService.register({
      beforeDraw: function(chart) {
        var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
        ctx2.restore();
        var fontSize = (height / 114).toFixed(2);
        ctx2.font = fontSize + "em Open Sans";
        ctx2.textBaseline = "middle";
        var text = total_race_value,
            textX = Math.round((width - ctx2.measureText(text).width) / 2),
            textY = height / 2;
        ctx2.fillText(text, textX, textY);
        ctx2.save();
      }
    });    
    /* chart scripting end */

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>