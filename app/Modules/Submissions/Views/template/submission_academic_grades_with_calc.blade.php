<style>
    .bg-yellow{background: #fff3cd !important;}
    .bg-green{background: #d4edda !important;}
    .bg-red{background: #f8d7da !important;}
</style>

@php 

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

@endphp
@php 
        $tmpdata = getSubmissionGradeDataNew($submission->id, $term_calc, $academic_years, $required_subjects);
       
    //dd($submission->id, $term_calc, $academic_years, $required_subjects);
@endphp
    
@if(count($tmpdata) > 0)
   
    @php
       
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
        
    @endphp

@elseif($submission->student_id != "" && count($tmpdata) <= 0)
    
    @php 
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

    @endphp


    
@else
    
    @php $grade_data = array() @endphp
@endif 


@section('styles')
<style type="text/css">
    .error {
        color: red;
    }
</style>
@endsection

<div class="card shadow">
    <form id="store_grades_form" method="post" action="{{ url('admin/Submissions/storegrades',$submission->id) }}">
        {{csrf_field()}}
        {{-- {{dd($value)}} --}}
        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="">{{$value->eligibility_name}}</div>
                           
                        </div>
        
        <div class="card-body">
            @if($submission->student_id != '' && !in_array($submission->current_grade, ['PreK', 'K', '1', '2']))
                <div class="text-right pb-10"><a href="javascript:void(0)" onclick="fetchGradeManually()" class="btn btn-success grade_fetch" title="Fetch Manually">Run PowerSchool API</a></div>
            @else
                <div class="text-right pb-10"><a href="javascript:void(0)" onclick="fetchStandardGradeManually()" class="btn btn-success grade_fetch" title="Fetch Manually">Run PowerSchool API</a></div>
            @endif


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
                        @php $srcount = 1 @endphp
                        @if(!empty($tmpdata))
                            @php $courses = Config::get('variables.courses') @endphp
                            @php $goodTerms = Config::get('variables.goodTerms') @endphp
                            
                            @php $academic_terms = array_merge($goodTerms, getAcademicTerms($tmpdata)) @endphp
                            
                            @foreach($tmpdata as $ekey=>$evalue)
                                @php $field = strtolower(str_replace(" ","_", $evalue['courseType'])) @endphp
                                @if($gradeInfo->{$field} == "N")
                                    @php $na = "N" @endphp
                                @else
                                    @php $na = "Y" @endphp
                                @endif
                                @if($na=="N")
                                    @php $class = "bg-yellow" @endphp
                                @elseif($evalue['display']=="red")
                                    @php $class = "bg-green" @endphp
                                @else
                                    @php $class= "" @endphp
                                @endif
                            <tr class="{{$class}}">
                                <td class="text-center">
                                    {{$srcount}} 
                                    <div class="d-none">
                                    @isset($evalue['sectionNumber'])
                                        <input type="text" class="grd_hidden" name="sectionNumber[{{$ekey}}]" value="{{$evalue['sectionNumber']}}" hidden>
                                    @endisset
                                    @isset($evalue['courseType'])
                                        <input type="text" class="grd_hidden" name="courseType[{{$ekey}}]" value="{{$evalue['courseType']}}" hidden>
                                    @endisset
                                    @isset($evalue['stateID'])
                                        <input type="text" class="grd_hidden" name="stateID[{{$ekey}}]" value="{{$evalue['stateID']}}" hidden>
                                    @endisset
                                    @isset($evalue['GradeName'])
                                        <input type="text" class="grd_hidden" name="GradeName[{{$ekey}}]" value="{{$evalue['GradeName']}}" hidden>
                                    @endisset
                                    @isset($evalue['sequence'])
                                        <input type="text" class="grd_hidden" name="sequence[{{$ekey}}]" value="{{$evalue['sequence']}}" hidden>
                                    @endisset
                                    @isset($evalue['courseFullName'])
                                        <input type="text" class="grd_hidden" name="courseFullName[{{$ekey}}]" value="{{$evalue['courseFullName']}}" hidden>
                                    @endisset
                                    @isset($evalue['fullsection_number']) 
                                        <input type="text" class="grd_hidden" name="fullsection_number[{{$ekey}}]" value="{{$evalue['fullsection_number']}}" hidden>
                                    @endisset

                                    <input type="checkbox" id="chk_del_grade" name="selectCheck" />
                                    <label for="chk_del_grade" class="label-xs check-secondary"></label>
                                    </div>
                                </td>
                                <td class="">
                                    <select name="academicYear[{{$ekey}}]" class="form-control custom-select form-control-sm">
                                        @foreach($academic_years as $akey=>$avalue) 
                                            <option value="{{$avalue}}" @if($evalue['academicYear'] == $avalue) selected @endif>{{$avalue}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="">
                                    <select name="academicTerm[{{$ekey}}]"class="form-control form-control-sm">
                                         @foreach($term_calc as $akey=>$avalue) 
                                             <option @if($evalue['academicTerm']==$avalue) selected="selected" @endif value="{{$avalue}}">{{$avalue}}</option> 
                                           <!--  <option value="{{$evalue['academicTerm']}}">{{$evalue['academicTerm']}}</option> -->
                                         @endforeach 
                                    </select>
                                    
                                </td>
                                <td class="">
                                    @php
                                $courses = config('variables.ag_eligibility_subjects');
                                    /*$courses = Config::get('variables.courseType');
                                    $subjects_ary_conf = config('variables.ag_eligibility_subjects');*/
                                @endphp
                                @php $tmpCName = "" @endphp

                                <select name="courseTypeID[{{$ekey}}]" class="form-control custom-select form-control-sm">
                                        @foreach($courses as $mkey=>$mvalue)
                                                <option value="{{$mkey}}" @if($evalue['courseTypeID']==$mkey) selected="selected" @endif>{{$mvalue}}</option>
                                                @php $tmpCName = $mvalue @endphp
                                            
                                        @endforeach
                                    </select>
                                   
                                    
                                </td>
                                <td class=""><input name="courseName[{{$ekey}}]" maxlength="100" type="text" class="form-control form-control-sm" value="{{$evalue['courseName']}}"></td>
                                <td class=""><input name="actual_numeric_grade[{{$ekey}}]" maxlength="100" type="text" class="form-control form-control-sm" value="{{$evalue['actual_numeric_grade']}}" id="actual_numeric_grade{{$srcount}}" onblur="return updateGrade({{$srcount}})"></td>
                                <td class=""><input name="advanced_course_bonus[{{$ekey}}]" maxlength="100" type="text" class="form-control form-control-sm" value="{{$evalue['advanced_course_bonus']}}" id="advanced_course_bonus{{$srcount}}" onblur="return updateGrade({{$srcount}})"></td>
                                <td class="text-center">
                                    @if($evalue['numericGrade'] == "" && $na == "N")
                                        @php  $evalue['numericGrade'] = 0 @endphp
                                    @endif
                                    <input name="numericGrade[{{$ekey}}]" maxlength="3" type="text" class="form-control form-control-sm gradecalc_cls" max="100" value="{{$evalue['numericGrade']}}" id="numericGrade{{$srcount}}">
                                    
                                </td>
                            </tr>
                            @php $srcount++ @endphp
                            @endforeach
                        @else
                            <!--@foreach($academic_years as $ak=>$av)
                                @foreach($term_calc as $tk=>$tv)
                                    @foreach($sub as $sk=>$sv)
                                         <tr>
                                            <td class="">
                                                <input type="checkbox" id="chk_del_grade" name="selectCheck" />
                                                <label for="chk_del_grade" class="label-xs check-secondary"></label>
                                            </td>
                                            <td class="">
                                                <select name="academicYear[]" class="form-control custom-select form-control-sm">
                                                    <option value="{{$av}}">{{$av}}</option>
                                                </select>
                                            </td>
                                            <td class="">
                                                <select name="academicTerm[]" class="form-control form-control-sm">
                                                   <option value="{{$tv}}">{{$tv}}</option>
                                                </select>
                                            </td>
                                            <td class="">
                                                <select name="courseTypeID[]" class="form-control custom-select form-control-sm">
                                                    <option value="{{$sk}}">{{$sv}}</option>
                                                </select>
                                            </td>
                                            <td class=""><input name="courseName[]" maxlength="100" type="text" class="form-control form-control-sm"></td>
                                            <td class=""><input name="numericGrade[]" maxlength="3" type="text" class="form-control form-control-sm"></td>

                                        </tr>
                                    @endforeach
                                @endforeach
                               
                            @endforeach-->
                        @endif
                        @php 

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

                        @endphp
                        {{-- @foreach($academic_years as $acy=>$acvy) --}}

                         <tr class="bg-red">
                            <td class="text-center">
                                <div class="">
                                <input type="checkbox" id="chk_del_grade" name="selectCheck" />
                                <label for="chk_del_grade" class="label-xs check-secondary"></label>
                                </div>
                            </td>
                            <td class="">
                                <select name="academicYear[]" class="form-control custom-select form-control-sm">
                                     @foreach($academic_years as $acy=>$acvy)
                                            <option value="{{$acvy}}">{{$acvy}}</option>
                                    @endforeach
                                </select>
                                    
                            </td>
                            <td class="">
                                <select name="academicTerm[]" class="form-control form-control-sm">
                                    @foreach($termArrayDisp as $ytc=>$vtc)
                                        <option value="{{$vtc}}">{{$vtc}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="">
                                @php
                                $courses = config('variables.ag_eligibility_subjects');
                                    /*$courses = Config::get('variables.courseType');
                                    $subjects_ary_conf = config('variables.ag_eligibility_subjects');*/
                                @endphp
                                @php $tmpCName = "" @endphp
                                <select name="courseTypeID[]" class="form-control custom-select form-control-sm">
                                        @foreach($courses as $mkey=>$mvalue)
                                                <option value="{{$mkey}}">{{$mvalue}}</option>
                                                @php $tmpCName = $mvalue @endphp
                                            
                                        @endforeach
                                    </select>
                            </td>
                            <td class=""><input name="courseName[]" maxlength="100" value="" type="text" class="form-control form-control-sm"></td>
                             <td class=""><input name="actual_numeric_grade[]" maxlength="100" value="" type="text" class="form-control form-control-sm" id="actual_numeric_grade{{$srcount}}" onblur="return updateGrade({{$srcount}})"></td>
                              <td class=""><input name="advanced_course_bonus[]" maxlength="100" value="" type="text" class="form-control form-control-sm" id="advanced_course_bonus{{$srcount}}"  onblur="return updateGrade({{$srcount}})"></td>
                            <td class="text-center">
                                
                                    <input name="numericGrade[]" max="100" maxlength="3" type="text" class="form-control form-control-sm  gradecalc_cls" value="" id="numericGrade{{$srcount}}">
                                
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
            {{-- <button class="btn btn-success">    
                <i class="fa fa-save"></i> Save
            </button> --}}
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button>
                        <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                        <a class="btn btn-danger btn-xs" href="{{url('/admin/Submissions')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
