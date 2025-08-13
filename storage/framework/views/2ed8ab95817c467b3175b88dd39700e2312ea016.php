<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="" rel="stylesheet">
    <style type="text/css">

        /*page size start*/
        body {
            font-size: 12px;
        }
        td, th {
          padding: 5px !important;
          padding-left: 6px !important;
        }
        table {
          margin-bottom: 5px !important;
          margin-top: 0px !important;
        }
        .tbl_top_mrgn {
            margin-top: 15px !important;
        }
        /*page size end*/

        @font-face {
            font-family: 'Open Sans';
            src: url('fonts/OpenSans-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
       footer {
            bottom: 55px; 
            left: 0px; 
            right: 0px;
        }
        .header {
            top: 0px;
        }
        .footer {
            bottom: 0px;
        }
        body {padding: 10px; margin: 0; font-family: 'Open Sans', sans-serif;}
        .container {max-width: 700px; margin: 0 auto;}
        img {max-width:100%;}
        .w-50 {width:50%;}
        .w-100 {width:100%;}
        .logo-box {width:120px; margin-bottom: 20px;}
        .logo-box.text-right {margin-left: auto;}
        .text-center {text-align:center;}
        .table {width:100%; border: 1px solid black; border-collapse: collapse;}
        .table tr {padding: 0; margin: 0; border-bottom: 1px solid black;}
        .table tr th, .table tr td {padding: 10px 5px;margin: 0; border-top: 1px solid black; border-right: 1px solid black;}
        .section {margin-bottom: 0px !important;}
        .section-title {margin-top: 5px; margin-bottom: -10px !important; font-size: 12px; margin-left: -6px;}
        .section-title1 {margin-bottom: 0px !important; font-weight: 600; font-family: 'Open Sans', sans-serif;}
        .text-right {text-align: right;}
        .text-center {text-align: center;}
        .font-12{font-size: :12px;}

        .page_break {
           page-break-after: always;
        }
        .page_break:last-child {
           page-break-after: unset;
        }
    </style>
</head>
<body>
    <?php
        if(isset($sp_datasheet) && !empty($sp_datasheet))
        {
            $loop_data = $sp_datasheet;
            $extra_container_class = 'page_break';
        } else {
            $loop_data[] = $data;
            $extra_container_class = '';
        }
    ?>
    <?php $__currentLoopData = $loop_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="<?php echo e($extra_container_class); ?>">
            <div class="header"></div>
            <div class="container " style="height: 100%;">
                <div class="header1">
                    <div align="center" style="margin-bottom: 10px; text-align: center;"> 
                       <table align="center" style="text-align: center;">
              <tbody>
                  <tr>
                      <td align="center" style="text-align: center">
                         <?php $logo = (isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo()) ?>
                        <img src="<?php echo e(str_replace('https://', 'http://', $logo)); ?>" title="" alt="" style="max-width: 300px !important;"></td>
                      
                  </tr>
              </tbody>
          </table>
                    
                    </div>
                    <div style="margin-bottom: 5px; margin-top: 50px;">
                        <?php echo e(date('m/d/Y')); ?>

                    </div>
                   
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <th class="w-100 text-center" colspan="3" style="font-size: 15px;">Student Profile Page</th>
                            </tr>
                            <tr>
                                <th class="w-100 text-center" colspan="3" style="font-size: 15px;"><?php echo e($data['submission']->current_grade.' to '.$data['submission']->next_grade); ?></th>
                            </tr>
                            
                            <tr>
                                <td align="left">Student Name: <?php echo e($data['submission']->first_name.' '.$data['submission']->last_name); ?></td>
                                <td align="center">
                                    <?php if($data['submission']->student_id != ''): ?>
                                        Student ID: <?php echo e($data['submission']->student_id); ?>

                                    <?php endif; ?>
                                </td>
                                <td align="right">Submission ID: <?php echo e($data['submission']->id); ?></td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                <div class="wrapper">
                    <!-- Recommendation Data -->
                   
                    <?php if($data['profile']['recommendation']['status'] == 'Y'): ?>
                        <div class="section section-1 page">
                            <div class="section-title">
                                <table class="" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><b>Learner Profile Screening Device (LPSD) Criteria</b></td>
                                            <td align="right"><?php echo e($data['profile']['recommendation']['scored'].'/'.$data['profile']['recommendation']['total']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php if(count($data['profile']['recommendation']['data']) > 0): ?>
                                <table class="table" style="white-space: nowrap;">
                                    <tr>
                                        <?php $__empty_1 = true; $__currentLoopData = $data['profile']['recommendation']['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question => $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php
                                                $field_txt = '';
                                                foreach ($data['profile']['recommendation']['lpsd_fields'] as $field => $find_key) {
                                                    if (strpos($question, $find_key) !== false) {
                                                        $field_txt = $field;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <td><?php echo e($field_txt); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <?php $__empty_1 = true; $__currentLoopData = $data['profile']['recommendation']['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question => $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <td><?php echo e($score); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <?php endif; ?>
                                    </tr>
                                </table>
                                
                                <table class="table tbl_top_mrgn" style="white-space: nowrap;">
                                    <tr>
                                        <td style="width: 200px;">Add Top 4 Scores</td>
                                        <?php $__currentLoopData = $data['profile']['recommendation']['lpsd_points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                                            <td <?php if($loop->last): ?> style="width: 100px;" <?php endif; ?>><?php echo e($key); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <tr>
                                        <td>LPSD Points</td>
                                        <?php $__currentLoopData = $data['profile']['recommendation']['lpsd_points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>    
                                            <td><?php echo e($value); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </table>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Test Score Data -->
                    <?php if($data['profile']['test_score']['status'] == 'Y'): ?>
                        <div class="section section-2 page">
                            <div class="section-title">
                                <table class="" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><b>Universal Screener Criteria</b></td>
                                            <td align="right"><?php echo e($data['profile']['test_score']['scored'].'/'.$data['profile']['test_score']['total']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                                $ts_data_count = count($data['profile']['test_score']['data']);
                                $ts_table_count = round($ts_data_count/2);
                                $ts_itr = $ts_table_count;
                            ?>
                            <?php if($ts_data_count > 0): ?>
                                <?php for($i=0; $i<$ts_table_count; $i++): ?>
                                    <?php
                                        $ts_rmn_sub_count = count($data['profile']['test_score']['test_scores']);
                                        if ($i > 0) {
                                            $ts_tbl_class = 'tbl_top_mrgn';
                                        } else {
                                            $ts_tbl_class = '';
                                        }
                                    ?>
                                    <table class="table <?php echo e($ts_tbl_class); ?>" <?php if($ts_rmn_sub_count <= 1): ?> style="width: 50%;" <?php endif; ?>>
                                        <tr>
                                            <?php
                                                $avg_ts_total = round(($data['profile']['test_score']['total']/(count($data['profile']['test_score']['test_scores']))), 2);
                                                $flag = 2;
                                            ?>
                                            <?php $__currentLoopData = $data['profile']['test_score']['test_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject => $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    if ($flag <= 0)
                                                        break;
                                                    $flag--;
                                                    $sub_short_name = ($data['profile']['test_score']['data'][$subject]['short_name'] ?? '');
                                                    $subject_name = ($sub_short_name != '') ? $sub_short_name : $subject;
                                                    $pts_scored = ($data['profile']['test_score']['data'][$subject]['score'] ?? '0');
                                                ?>
                                                <td style="width: 50%;"><?php echo e($subject_name.' ('.$score.'%): '.$pts_scored.'/'.$avg_ts_total); ?></td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                        <?php if($data['profile']['test_score']['is_txt']): ?>
                                            <tr>
                                                <?php
                                                    $flag = 2;
                                                ?>
                                                <?php $__currentLoopData = $data['profile']['test_score']['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ts_values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        if ($flag <= 0)
                                                            break;
                                                        $flag--;
                                                    ?>
                                                    <td class="font-12-1">
                                                        <?php $__currentLoopData = $ts_values['txt']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txt_values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($loop->index !== 0): ?>
                                                        <br>
                                                        <?php endif; ?>
                                                            <?php echo e($txt_values); ?>

                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <?php
                                                        // remove printed subjects
                                                        unset($data['profile']['test_score']['test_scores'][$key]); 
                                                        unset($data['profile']['test_score']['data'][$key]); 
                                                    ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                    <?php if(($ts_itr > 1) && ($ts_table_count > 1)): ?>  <?php endif; ?>
                                    <?php $ts_itr--; ?>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Grade Data -->
                    <?php if($data['profile']['grade']['status'] == 'Y'): ?>
                        <?php
                            $ag_data = $data['student_profile']['eligibility']['academic_grades_data'] ?? '';
                            $ag_data = ($ag_data!='null') ? json_decode($ag_data, 1) : [];
                             //dd($ag_data);
                        ?>
                        <?php if(!empty($ag_data) && isset($data['profile']['grade'])): ?>
                            <div class="section section-3 page">
                                <div class="section-title">
                                    <table class="" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td><b>Student Performance Criteria</b></td>
                                                <td align="right"><?php echo e($data['profile']['grade']['scored'].'/'.$data['profile']['grade']['total']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php
                                    $year_grades = $data['profile']['grade']['year_grades'] ?? [];
                                    $subjects_ary_conf = config('variables.ag_eligibility_subjects');
                                    // dd(3);
                                ?>
                                <?php if(isset($year_grades) && !empty($year_grades)): ?>
                                    <?php if(isset($ag_data['part_1']['ts_scores']) || isset($ag_data['part_2']['ts_scores'])): ?>
                                        <table class="table">
                                            <tr>
                                                <?php for($i=1; $i<3; $i++): ?>
                                                    <?php
                                                        // $rng_method = array_key_first(($ag_data['part_'.$i]['rangeselection'] ?? []));
                                                        $ts_fields = $ag_data['part_'.$i]['ts_scores'] ?? [];
                                                        $ts_fields_count = count($ts_fields);
                                                        $clspn = $ts_fields_count * (count($year_grades));
                                                        // dd($ag_data['part_1']['ts_scores']);
                                                    ?>
                                                    <?php if(($clspn > 0) && !empty($ts_fields)): ?>
                                                        <td colspan="<?php echo e($clspn); ?>">
                                                            <?php if($ts_fields_count > 0): ?>
                                                                <?php $__currentLoopData = $ag_data['part_'.$i]['ts_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php echo e($subjects_ary_conf[$ts_field]); ?>

                                                                    <?php if(next($ag_data['part_'.$i]['ts_scores']) !== false): ?> and <?php endif; ?>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                : <?php echo e($data['profile']['grade']['data']['part_'.$i]['scored'].'/'.($data['profile']['grade']['total']/2)); ?>

                                                            <?php endif; ?>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </tr>
                                            <tr>
                                                <?php if(isset($year_grades) && !empty($year_grades)): ?>
                                                    <?php for($i=1; $i<3; $i++): ?>
                                                        <?php if(isset($ag_data['part_'.$i]['ts_scores'])): ?>
                                                            <?php $__currentLoopData = $year_grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_year_grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php $__currentLoopData = $ag_data['part_'.$i]['ts_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <td>
                                                                    <?php echo e($ts_year_grade); ?><br>
                                                                    <?php echo e($subjects_ary_conf[$ts_field]); ?>

                                                                </td>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            </tr>
                                            <tr style="font-size: 15px !important;">
                                                
                                                    <?php for($i=1; $i<3; $i++): ?>
                                                        <?php
                                                            $range_method = $data['profile']['grade']['data']['part_'.$i]['range_method'];
                                                            // dd(2);
                                                            // dump('dump=',$i);
                                                        ?>
                                                        <?php if(isset($ag_data['part_'.$i]['ts_scores'])): ?>
                                                            <?php if($range_method == 'abc'): ?>
                                                                <?php if(isset($year_grades) && !empty($year_grades)): ?>
                                                                    <?php $__currentLoopData = $year_grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_year_grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php $__currentLoopData = $ag_data['part_'.$i]['ts_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                            // dd($data['profile']['grade']);
                                                                                $score = $data['profile']['grade']['data']['part_'.$i]['score_ary'][$ts_year_grade][$ts_field]['score'] ?? '-';
                                                                                $points = $data['profile']['grade']['data']['part_'.$i]['score_ary'][$ts_year_grade][$ts_field]['points'] ?? 0;
                                                                            ?>
                                                                            <td class="font-12"><?php echo e('('.$score.')'.' '.$points); ?></td>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                            <?php if($range_method == '3s2s'): ?>
                                                                <?php if(isset($year_grades) && !empty($year_grades)): ?>
                                                                    
                                                                        <?php
                                                                            $data_3s2s = $data['profile']['grade']['data']['part_'.$i]['3s2s'];
                                                                        ?>
                                                                        <?php if(isset($ag_data['part_'.$i]['ts_scores'])): ?>
                                                                            <?php $__currentLoopData = $year_grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_year_grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <?php $__currentLoopData = $ag_data['part_'.$i]['ts_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php if(isset($data_3s2s) && !empty($data_3s2s)): ?>
                                                                                        <?php
                                                                                            $tr_data_ary = ($data_3s2s[$ts_year_grade][$ts_field] ?? []);
                                                                                        ?>
                                                                                        <td class="font-12">
                                                                                            <?php $__currentLoopData = $tr_data_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $identifier=>$tr_val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <?php echo e($identifier); ?> :
                                                                                                <?php echo e($tr_val); ?>

                                                                                                <?php if(!$loop->last): ?> <br> <?php endif; ?>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                        </td>
                                                                                    <?php endif; ?>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php endif; ?>
                                                                    
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                
                                            </tr>
                                        </table>
                                        <table class="table tbl_top_mrgn">
                                            <tr>
                                                <?php if(isset($year_grades) && !empty($year_grades)): ?>
                                                    <?php for($i=1; $i<3; $i++): ?>
                                                        <?php
                                                            // Check for subject selection
                                                            if (!isset($ag_data['part_'.$i]['ts_scores']) || empty($ag_data['part_'.$i]['ts_scores'])) {
                                                                continue;
                                                            }
                                                            if (isset($ag_data['part_'.$i]['rangeselection']['abc'])) {
                                                                $range_selection = $ag_data['part_'.$i]['rangeselection']['abc'];
                                                            } elseif (isset($ag_data['part_'.$i]['rangeselection']['3s2s'])) {
                                                                $range_selection = $ag_data['part_'.$i]['rangeselection']['3s2s'];
                                                            } else {
                                                                $range_selection = [];
                                                            }
                                                        ?>
                                                        <?php if(!empty($range_selection)): ?>
                                                            <td width="50%">
                                                                <?php if(isset($ag_data['part_'.$i]['ts_scores'])): ?>
                                                                    <?php $__currentLoopData = $ag_data['part_'.$i]['ts_scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php echo e($subjects_ary_conf[$ts_field]); ?>

                                                                        <?php if(next($ag_data['part_'.$i]['ts_scores']) !== false): ?> and <?php endif; ?>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <br>
                                                                <?php endif; ?>
                                                                <?php $__currentLoopData = $range_selection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rngkey=>$rngval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $points = ($rngval != '') ? $rngval.' Points' : '-';
                                                                    ?>
                                                                    <?php echo e($rngkey.' = '. $points); ?> <br>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            </tr>
                                        </table>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- Conduct Discpline Creiteria -->
                    <?php if(isset($data['profile']['conduct_discpline_criteria']['data'])): ?>
                        <?php
                            $incident_count = ($data['profile']['conduct_discpline_criteria']['data']['incident_count'] ?? 0);
                            $incidents = ($data['profile']['conduct_discpline_criteria']['data']['incidents'] ?? []);
                        ?>
                        <div class="section section-4 page">
                            <div class="section-title">
                                <table class="" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><b>Conduct Criteria</b></td>
                                        </tr>
                                        <tr><td>Note: Three(3) Most Recent Office Discpline Referrals (ODRs) (Fields will be blank if not applicable):</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <table class="table">
                                <?php for($i=0; $i<3; $i++): ?>
                                    <?php $itr = $i+1; ?>
                                    <tr>
                                        <td><?php echo e($itr); ?></td>
                                        <td>
                                            <?php if(isset($incidents[$i]['date']) && ($incidents[$i]['date'] != '')): ?> 
                                                <?php echo e(($incidents[$i]['date'])); ?> <br>
                                            <?php endif; ?>
                                            <?php if(isset($incidents[$i]['incident']) && ($incidents[$i]['incident'] != '')): ?> 
                                                Incident: <?php echo e(($incidents[$i]['incident'])); ?> <br>
                                            <?php endif; ?>
                                            <?php if(isset($incidents[$i]['infraction']) && ($incidents[$i]['infraction'] != '')): ?> 
                                                Infraction: <?php echo e(($incidents[$i]['infraction'])); ?> <br><br>
                                            <?php endif; ?>
                                            <?php if(isset($incidents[$i]['action']) && ($incidents[$i]['action'] != '')): ?> 
                                                Action: <?php echo e(($incidents[$i]['action'])); ?> <br>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </table>
                        </div>
                    <?php endif; ?>
                    <!-- Final Data -->
                    <table style="width: 100%; padding: 1px !important; margin-left: -6px;">
                        <tr>
                            <td style="font-weight: bold; font-size: 13px !important;">
                                <span>Total Student Profile Score <?php echo e($data['profile']['student_score']); ?> / <?php echo e($data['profile']['total']); ?></span><br>
                                <span>Student Profile Percentage Score <?php echo e($data['profile']['final_percent']); ?>%</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="wrapper">
                    <div class="section section-5 page" style="bottom: 0px; ">
                        <table style="width: 100%;">
                            <tr><td align="center">
                                <div><b>Tuscaloosa City Schools</b></div>
                                <div><b>1210 21st Avenue | Tuscaloosa, AL 36104 | (205) 759-3700</b></div>
                                <div><span style="color: #10335d;"><b>AN EQUAL OPPORTUNITY EMPLOYER</b></span></div></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>