<style>
    .bg-yellow{background: #fff3cd !important;}
    .bg-green{background: #d4edda !important;}
    .bg-red{background: #f8d7da !important;}
</style>

<?php 

    $pending_data = array();
    $sub = array();
    $configSubject = Config::get('variables.subjects');

    $grade_year = [];
    if(isset($gradeInfo)){
        $grade_year = explode(',', $gradeInfo->year);
    }
    foreach($content->subjects as $skey=>$svalue)
    {
        if(isset($configSubject[$svalue]))
            $sub[] = $configSubject[$svalue];
    }

    $term_calc = $term_calc1 = $year_term_calc = array();
    $year_term_calc = (array)$content->terms_calc;
    // dd($year_term_calc);
    // dd($year_term_calc['2020-2021']);
    // foreach($content->terms_calc as $tkey=>$tvalue)
    // {
    //         $term_calc[] = $tvalue;
    //         $term_calc1[] = $tvalue;
    // }


    $content = $academic_calc ?? null;
    $scoring = $academic_calc->scoring ?? null;

    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name); 

    $academic_years = array();
    if(isset($eligibility_data->academic_year_calc))
    {
        $required_subjects = $eligibility_data->subjects;

        for($i=0; $i < count($eligibility_data->academic_year_calc); $i++)
        {
            //if(in_array($eligibility_data->academic_year_calc[$i], $grade_year))
                $academic_years[] = $eligibility_data->academic_year_calc[$i]; 
        }  
    }
    else
    {
        $required_subjects = ["EN", "MA", "SC", "SS", "RE"];

        for($i=1; $i <= $content->terms_pulled[0]; $i++)
        {
            if(in_array((date("Y")-$i)."-".(date("y")-($i-1)), $grade_year))
                $academic_years[] = (date("Y")-$i)."-".(date("y")-($i-1)); 
        }       
    }       
    if(isset($eligibility_data->terms_calc))
    {
        $term_calc = $term_calc_n = [];
        // for($i=0; $i < count($eligibility_data->terms_calc); $i++)
        // {
        //     $term_calc[] = $eligibility_data->terms_calc[$i]; 
        // }  
        foreach($eligibility_data->terms_calc as $key => $tvalue){

            foreach($tvalue as $k1 => $term){
                 if(isset($term_calc_n[$key]))
                        array_push($term_calc_n[$key], $term);
                    else
                        $term_calc_n[$key] = array($term);
                    $term_calc[] = $term;
                    $term_calc1[] = $term;
            }
        }
    }

?>
<?php 
        $tmpdata = getSubmissionGradeDataNew($submission->id, $term_calc, $academic_years, $required_subjects);
       
    //dd($submission->id, $term_calc, $academic_years, $required_subjects);
?>
    
<?php if(count($tmpdata) > 0): ?>
   
    <?php
       
        $tmpdata1 = $tmpdata;
        $tmpdata = array();
        $count=0;
        foreach($tmpdata1 as $ekey=>$evalue)
        {
                if($evalue['numericGrade'] != '' && $evalue['numericGrade'] != '0' && (isset($term_calc_n[$evalue['academicYear']]) && in_array(trim($evalue['GradeName']), $term_calc_n[$evalue['academicYear']])))
                {
                    if(in_array($evalue['GradeName'], $term_calc1) && in_array($evalue['courseType'], $sub))
                    {
                        $tmpdata[$count]['display'] = "red";
                    }
                    else
                    {
                        $tmpdata[$count]['display'] = "green";
                    }
                    if(!in_array($evalue['GradeName'], $term_calc))
                        $term_calc[] =  trim($evalue['GradeName']);
                    $pending_data[] = $evalue['academicYear']."-".$evalue['GradeName']."-".$evalue['courseType'];

                    $pending_data[] = $evalue['academicYear']."-".$evalue['GradeName']."-".$configSubject[$evalue['courseType']];
                    $tmpdata[$count]['stateID'] = $evalue['stateID'] ?? null;
                    $tmpdata[$count]['academicYear'] = $evalue['academicYear'] ?? null;
                    $tmpdata[$count]['sAcademicYear'] = $evalue['academicYear'] ?? null;
                    $tmpdata[$count]['academicTerm'] = trim($evalue['GradeName']) ?? null;
                    $tmpdata[$count]['courseTypeID'] = $evalue['courseTypeID'] ?? null;
                    $tmpdata[$count]['courseType'] = $evalue['courseType'] ?? null;
                    $tmpdata[$count]['courseName'] = ($evalue['standard_identifier'] != '' ? $evalue['standard_identifier'] : (($evalue['courseName'] ?? null)));
                    $tmpdata[$count]['sectionNumber'] = $evalue['sectionNumber'] ?? null;
                    $tmpdata[$count]['numericGrade'] = $evalue['numericGrade'] ?? null;

                    $tmpdata[$count]['actual_numeric_grade'] = $evalue['actual_numeric_grade'] ?? 0;
                    $tmpdata[$count]['advanced_course_bonus'] = $evalue['advanced_course_bonus'] ?? 0;

                    $tmpdata[$count]['GradeName'] = $evalue['GradeName'] ?? null;
                    $tmpdata[$count]['sequence'] = $evalue['sequence'] ?? null;
                    $tmpdata[$count]['courseFullName'] = $evalue['courseFullName'] ?? null;
                    $tmpdata[$count]['fullsection_number'] = $evalue['fullsection_number'] ?? null;
                    $count++;    
                }
             
        }
        
    ?>

<?php elseif($submission->student_id != "" && count($tmpdata) <= 0): ?>
    
    <?php 
        $tmpdata = array();
        $count = 0;
        //$academic_years = array();

        for($i=0; $i < count($academic_years); $i++)
        {
            $completed_data = array();
            //$academic_years[] = (date("Y")-$i)."-".(date("Y")-($i-1)); 
            $term = $academic_years[$i];//(date("Y")-$i)."-".(date("y")-($i-1));
            $fterm = $academic_years[$i];//(date("Y")-$i)."-".(date("Y")-($i-1));

            $data = getStudentGradeDataYearLate($submission->student_id, $term_calc, $academic_years, $content->subjects);
            // dd($content, $eligibility_data, $value, $academic_years, $term_calc, $data);

            foreach($data['data'] as $ekey=>$evalue)
            {
                if(in_array($evalue->GradeName, $term_calc1) && in_array($evalue->courseType, $sub) && $evalue->numericGrade != '')
                {
                    $tmpdata[$count]['display'] = "red";
                }
                else
                {
                    $tmpdata[$count]['display'] = "green";
                }
                if(!in_array($evalue->GradeName, $term_calc))
                        $term_calc[] =  $evalue->GradeName;
                $pending_data[] = $fterm."-".$evalue->GradeName."-".$evalue->courseType;
                
                $tmpdata[$count]['stateID'] = $evalue->stateID ?? null;
                $tmpdata[$count]['academicYear'] = $term ?? null;
                $tmpdata[$count]['sAcademicYear'] = $fterm ?? null;
                $tmpdata[$count]['academicTerm'] = $evalue->GradeName ?? null;
                $tmpdata[$count]['courseTypeID'] = $evalue->courseTypeID ?? null;
                $tmpdata[$count]['courseType'] = $evalue->courseType ?? null;
                $tmpdata[$count]['courseName'] = $evalue->courseName ?? null;
                $tmpdata[$count]['sectionNumber'] = $evalue->sectionNumber ?? null;
                $tmpdata[$count]['numericGrade'] = $evalue->numericGrade ?? null;

                $tmpdata[$count]['actual_numeric_grade'] = $evalue->actual_numeric_grade ?? 0;
                $tmpdata[$count]['advanced_course_bonus'] = $evalue->advanced_course_bonus ?? 0;

                $tmpdata[$count]['GradeName'] = $evalue->GradeName ?? null;
                $tmpdata[$count]['sequence'] = $evalue->sequence ?? null;
                $tmpdata[$count]['courseFullName'] = $evalue->courseFullName ?? null;
                $tmpdata[$count]['fullsection_number'] = $evalue->fullsection_number ?? null;
                $count++;    
                //}  
            }
        } 

    ?>


    
<?php else: ?>
    
    <?php $grade_data = array() ?>
<?php endif; ?> 


<?php $__env->startSection('styles'); ?>
<style type="text/css">
    .error {
        color: red;
    }
</style>
<?php $__env->stopSection(); ?>

<div class="card shadow">
    <form id="store_grades_form" method="post" action="<?php echo e(url('admin/Submissions/storegrades',$submission->id)); ?>">
        <?php echo e(csrf_field()); ?>

        
        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class=""><?php echo e($value->eligibility_name); ?></div>
                           
                        </div>
        
        <div class="card-body">
            <?php if($submission->student_id != '' && !in_array($submission->current_grade, ['PreK', 'K', '1', '2'])): ?>
                <div class="text-right pb-10"><a href="javascript:void(0)" onclick="fetchGradeManually()" class="btn btn-success grade_fetch" title="Fetch Manually">Run PowerSchool API</a></div>
            <?php else: ?>
                <div class="text-right pb-10"><a href="javascript:void(0)" onclick="fetchStandardGradeManually()" class="btn btn-success grade_fetch" title="Fetch Manually">Run PowerSchool API</a></div>
            <?php endif; ?>


            <div class="table-responsive">
                <table class="table table-striped mb-0" id="grade-table">
                    <thead>
                        <tr> 
                            <th class="align-middle">#</th>
                            <th class="align-middle">Academic year</th>
                            <th class="align-middle">Academic Term</th>
                            <th class="align-middle">Course Type ID</th>
                            <th class="align-middle">Course Name</th>
                            <th class="align-middle">Grade</th>
                            <th class="align-middle">Advanced Course Bonus</th>
                            <th class="align-middle">Total</th>
                            

                        </tr>
                    </thead>
                    <tbody>
                        <?php $srcount = 1 ?>
                        <?php if(!empty($tmpdata)): ?>
                            <?php $courses = Config::get('variables.courses') ?>
                            <?php $goodTerms = Config::get('variables.goodTerms') ?>
                            
                            <?php $academic_terms = array_merge($goodTerms, getAcademicTerms($tmpdata)) ?>
                            
                            <?php $__currentLoopData = $tmpdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekey=>$evalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $field = strtolower(str_replace(" ","_", $evalue['courseType'])) ?>
                                <?php if($gradeInfo->{$field} == "N"): ?>
                                    <?php $na = "N" ?>
                                <?php else: ?>
                                    <?php $na = "Y" ?>
                                <?php endif; ?>
                                <?php if($na=="N"): ?>
                                    <?php $class = "bg-yellow" ?>
                                <?php elseif($evalue['display']=="red"): ?>
                                    <?php $class = "bg-green" ?>
                                <?php else: ?>
                                    <?php $class= "" ?>
                                <?php endif; ?>
                            <tr class="<?php echo e($class); ?>">
                                <td class="text-center">
                                    <?php echo e($srcount); ?> 
                                    <div class="d-none">
                                    <?php if(isset($evalue['sectionNumber'])): ?>
                                        <input type="text" class="grd_hidden" name="sectionNumber[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['sectionNumber']); ?>" hidden>
                                    <?php endif; ?>
                                    <?php if(isset($evalue['courseType'])): ?>
                                        <input type="text" class="grd_hidden" name="courseType[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['courseType']); ?>" hidden>
                                    <?php endif; ?>
                                    <?php if(isset($evalue['stateID'])): ?>
                                        <input type="text" class="grd_hidden" name="stateID[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['stateID']); ?>" hidden>
                                    <?php endif; ?>
                                    <?php if(isset($evalue['GradeName'])): ?>
                                        <input type="text" class="grd_hidden" name="GradeName[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['GradeName']); ?>" hidden>
                                    <?php endif; ?>
                                    <?php if(isset($evalue['sequence'])): ?>
                                        <input type="text" class="grd_hidden" name="sequence[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['sequence']); ?>" hidden>
                                    <?php endif; ?>
                                    <?php if(isset($evalue['courseFullName'])): ?>
                                        <input type="text" class="grd_hidden" name="courseFullName[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['courseFullName']); ?>" hidden>
                                    <?php endif; ?>
                                    <?php if(isset($evalue['fullsection_number'])): ?> 
                                        <input type="text" class="grd_hidden" name="fullsection_number[<?php echo e($ekey); ?>]" value="<?php echo e($evalue['fullsection_number']); ?>" hidden>
                                    <?php endif; ?>

                                    <input type="checkbox" id="chk_del_grade" name="selectCheck" />
                                    <label for="chk_del_grade" class="label-xs check-secondary"></label>
                                    </div>
                                </td>
                                <td class="">
                                    <select name="academicYear[<?php echo e($ekey); ?>]" class="form-control custom-select form-control-sm">
                                        <?php $__currentLoopData = $academic_years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akey=>$avalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                            <option value="<?php echo e($avalue); ?>" <?php if($evalue['academicYear'] == $avalue): ?> selected <?php endif; ?>><?php echo e($avalue); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td class="">
                                    <select name="academicTerm[<?php echo e($ekey); ?>]"class="form-control form-control-sm">
                                         <?php $__currentLoopData = $term_calc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akey=>$avalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                             <option <?php if($evalue['academicTerm']==$avalue): ?> selected="selected" <?php endif; ?> value="<?php echo e($avalue); ?>"><?php echo e($avalue); ?></option> 
                                           <!--  <option value="<?php echo e($evalue['academicTerm']); ?>"><?php echo e($evalue['academicTerm']); ?></option> -->
                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                                    </select>
                                    
                                </td>
                                <td class="">
                                    <?php
                                $courses = config('variables.ag_eligibility_subjects');
                                    /*$courses = Config::get('variables.courseType');
                                    $subjects_ary_conf = config('variables.ag_eligibility_subjects');*/
                                ?>
                                <?php $tmpCName = "" ?>

                                <select name="courseTypeID[<?php echo e($ekey); ?>]" class="form-control custom-select form-control-sm">
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mkey=>$mvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($mkey); ?>" <?php if($evalue['courseTypeID']==$mkey): ?> selected="selected" <?php endif; ?>><?php echo e($mvalue); ?></option>
                                                <?php $tmpCName = $mvalue ?>
                                            
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                   
                                    
                                </td>
                                <td class=""><input name="courseName[<?php echo e($ekey); ?>]" maxlength="100" type="text" class="form-control form-control-sm" value="<?php echo e($evalue['courseName']); ?>"></td>
                                <td class=""><input name="actual_numeric_grade[<?php echo e($ekey); ?>]" maxlength="100" type="text" class="form-control form-control-sm" value="<?php echo e($evalue['actual_numeric_grade']); ?>" id="actual_numeric_grade<?php echo e($srcount); ?>" onblur="return updateGrade(<?php echo e($srcount); ?>)"></td>
                                <td class=""><input name="advanced_course_bonus[<?php echo e($ekey); ?>]" maxlength="100" type="text" class="form-control form-control-sm" value="<?php echo e($evalue['advanced_course_bonus']); ?>" id="advanced_course_bonus<?php echo e($srcount); ?>" onblur="return updateGrade(<?php echo e($srcount); ?>)"></td>
                                <td class="text-center">
                                    <?php if($evalue['numericGrade'] == "" && $na == "N"): ?>
                                        <?php  $evalue['numericGrade'] = 0 ?>
                                    <?php endif; ?>
                                    <input name="numericGrade[<?php echo e($ekey); ?>]" maxlength="3" type="text" class="form-control form-control-sm gradecalc_cls" max="100" value="<?php echo e($evalue['numericGrade']); ?>" id="numericGrade<?php echo e($srcount); ?>">
                                    
                                </td>
                            </tr>
                            <?php $srcount++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <!--<?php $__currentLoopData = $academic_years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ak=>$av): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $term_calc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tk=>$tv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $sub; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk=>$sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <tr>
                                            <td class="">
                                                <input type="checkbox" id="chk_del_grade" name="selectCheck" />
                                                <label for="chk_del_grade" class="label-xs check-secondary"></label>
                                            </td>
                                            <td class="">
                                                <select name="academicYear[]" class="form-control custom-select form-control-sm">
                                                    <option value="<?php echo e($av); ?>"><?php echo e($av); ?></option>
                                                </select>
                                            </td>
                                            <td class="">
                                                <select name="academicTerm[]" class="form-control form-control-sm">
                                                   <option value="<?php echo e($tv); ?>"><?php echo e($tv); ?></option>
                                                </select>
                                            </td>
                                            <td class="">
                                                <select name="courseTypeID[]" class="form-control custom-select form-control-sm">
                                                    <option value="<?php echo e($sk); ?>"><?php echo e($sv); ?></option>
                                                </select>
                                            </td>
                                            <td class=""><input name="courseName[]" maxlength="100" type="text" class="form-control form-control-sm"></td>
                                            <td class=""><input name="numericGrade[]" maxlength="3" type="text" class="form-control form-control-sm"></td>

                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                               
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->
                        <?php endif; ?>
                        <?php 

                            $termArrayDisp = [];
                            foreach($academic_years as $acy=>$acvy)
                            {
                                foreach($year_term_calc[$acvy] as $ytc=>$vtc)
                                {
                                    if(!in_array($vtc, $termArrayDisp))
                                    {
                                        $termArrayDisp[] = $vtc;
                                    }
                                }
                            }

                        ?>
                        

                         <tr class="bg-red">
                            <td class="text-center">
                                <div class="">
                                <input type="checkbox" id="chk_del_grade" name="selectCheck" />
                                <label for="chk_del_grade" class="label-xs check-secondary"></label>
                                </div>
                            </td>
                            <td class="">
                                <select name="academicYear[]" class="form-control custom-select form-control-sm">
                                     <?php $__currentLoopData = $academic_years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acy=>$acvy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($acvy); ?>"><?php echo e($acvy); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                    
                            </td>
                            <td class="">
                                <select name="academicTerm[]" class="form-control form-control-sm">
                                    <?php $__currentLoopData = $termArrayDisp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ytc=>$vtc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vtc); ?>"><?php echo e($vtc); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="">
                                <?php
                                $courses = config('variables.ag_eligibility_subjects');
                                    /*$courses = Config::get('variables.courseType');
                                    $subjects_ary_conf = config('variables.ag_eligibility_subjects');*/
                                ?>
                                <?php $tmpCName = "" ?>
                                <select name="courseTypeID[]" class="form-control custom-select form-control-sm">
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mkey=>$mvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($mkey); ?>"><?php echo e($mvalue); ?></option>
                                                <?php $tmpCName = $mvalue ?>
                                            
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                            </td>
                            <td class=""><input name="courseName[]" maxlength="100" value="" type="text" class="form-control form-control-sm"></td>
                             <td class=""><input name="actual_numeric_grade[]" maxlength="100" value="" type="text" class="form-control form-control-sm" id="actual_numeric_grade<?php echo e($srcount); ?>" onblur="return updateGrade(<?php echo e($srcount); ?>)"></td>
                              <td class=""><input name="advanced_course_bonus[]" maxlength="100" value="" type="text" class="form-control form-control-sm" id="advanced_course_bonus<?php echo e($srcount); ?>"  onblur="return updateGrade(<?php echo e($srcount); ?>)"></td>
                            <td class="text-center">
                                
                                    <input name="numericGrade[]" max="100" maxlength="3" type="text" class="form-control form-control-sm  gradecalc_cls" value="" id="numericGrade<?php echo e($srcount); ?>">
                                
                            </td>

                        </tr>

                    </tbody>
                    <tfoot>
                    <tr class="">
                        <td colspan="9" class="text-right">
                            <a href="javascript:void(0);" class="btn btn-secondary add-grade" title="">Add New</a>
                            <button type="button" id="del_grade" class="btn btn-danger">Delete</button>
                            <button type="submit" form="store_grades_form" class="btn btn-success"><i class="fa fa-save"> </i></button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="text-right"> 
            
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button>
                        <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                        <a class="btn btn-danger btn-xs" href="<?php echo e(url('/admin/Submissions')); ?>" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
