@php
    if ($data['id'] == 0) {
        $is_new = true;
    } else {
        $is_new = false;
    }
    $recommendation_form = (!$is_new && ($data['eligibility']->recommendation_form == 'Y')) ? true : false;
    $test_scores = (!$is_new && ($data['eligibility']->test_scores == 'Y')) ? true : false;
    $academic_grades = (!$is_new && ($data['eligibility']->academic_grades == 'Y')) ? true : false;
    $conduct_discpline_criteria = (!$is_new && ($data['eligibility']->conduct_discpline_criteria == 'Y')) ? true : false;
@endphp

@extends('layouts.admin.app')

@section('title') Student Profile Eligibility @stop

@section('styles')
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">@if($is_new) Create @else Edit @endif Student Profile Eligibility [{{ $data['program']->name }}]</div>
        <div class="">
            <a href="{{url($module_url)}}" class="btn btn-sm btn-secondary" title="">Back</a>
        </div>
    </div>
</div>
<form method="post" id="frm_eligibility" action="{{url($module_url)}}/store/{{$data['id']}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")

            <div class="row col-12">
                <div class="form-group col-12">
                    <label class="control-label"><strong>Eligibility Name :</strong> </label>
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{$data['eligibility']->name ?? ''}}"> 
                </div>
            </div>

            <div class="row col-12">
                <div class="form-group col-12">
                    <label class="control-label"><strong>@if($is_new) Available @endif Grade Level :</strong> </label>
                    <div class="row flex-wrap program_grade pl-20 pr-20">
                        
                        @php
                            $grades = ($data['program']->grade_lavel != '') ? explode(',', $data['program']->grade_lavel) : [];
                            $selected_grades = isset($data['eligibility']->grade) ? explode(',', $data['eligibility']->grade) : [];
                        @endphp
                        @foreach($grades as $grade)
                            @php
                                $checked = in_array($grade, $selected_grades);
                                if (!$is_new && !$checked)
                                    continue;
                            @endphp
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input grd_lvl_chk" id="grd_{{$grade}}" name="grade_lavel[]" value="{{$grade}}" @if($checked) checked @endif @if(!$is_new) disabled @endif> 
                                    <label for="grd_{{$grade}}" class="custom-control-label">{{$grade}}</label>
                                </div>
                            </div>
                        @endforeach
                        @if($errors->first('grade_lavel'))
                            <div class="col-12 ml-1 pl-10 pr-20 text-danger">{{ $errors->first('grade_lavel') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group col-12">
                <label class="control-label"><strong>Select Section to include in Profile Calculation :</strong> </label>
            </div>
            <div class="pl-20 pr-20">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="recommendation_form" value="recommendation" name="recommendation_form" @if($recommendation_form) checked @endif>
                        <label for="recommendation_form" class="custom-control-label">Recommendation Form [Learner Profile Screening Device (LPSD) Criteria]</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="test_scores" value="test_scores" name="test_scores" @if($test_scores) checked @endif>
                        <label for="test_scores" class="custom-control-label">Test Scores [Universal Screener Criteria]</label>
                    </div>
                    <div id="test_score_options" class="mt-1 ml-1">
                        @include('StudentProfileEligibility::section.test_score')
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="academic_grades" value="academic_grades" name="academic_grades" @if($academic_grades) checked @endif>
                        <label for="academic_grades" class="custom-control-label">Academic Grades [Student Perfomance Criteria]</label>
                    </div>
                    <div id="academic_grades_options" class="d-none mt-2 ml-1">
                        @include('StudentProfileEligibility::section.academic_grades')
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="conduct_discpline" value="conduct_discpline" name="conduct_discpline_criteria" @if($conduct_discpline_criteria) checked @endif>
                        <label for="conduct_discpline" class="custom-control-label">Conduct Discpline Criteria </label>
                    </div>
                    <div id="conduct_criteria" class="d-none mt-2 ml-1">
                        @include("StudentProfileEligibility::section.conduct_discpline_criteria")
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
               <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
               <a class="btn btn-danger btn-xs" href="{{url($module_url)}}"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function() {
            // Triggers
            $("#academic_grades").trigger('change');
            $("#conduct_discpline").trigger('change');
            $("#test_scores").trigger('change');
            $("#range_selection_1").trigger('change');
            $("#range_selection_2").trigger('change');
        });
        // Academic Grades hide/show
        $("#academic_grades").change(function(){
            hideShowContainer($(this), 'academic_grades_options');
        });
        // CDI
        $("#conduct_discpline").change(function(){
            hideShowContainer($(this), 'conduct_criteria');
        });
        // Test Scores
        $("#test_scores").change(function(){
            hideShowContainer($(this), 'test_score_options');
        });
        // Range Selection hide/show
        $("#range_selection_1").change(function(){
            hideShowContainerForAG($(this), 1);
        });
        $("#range_selection_2").change(function(){
            hideShowContainerForAG($(this), 2);
        });
        function hideShowContainer(e, container_id='') {
            if(e.prop("checked"))
                $("#"+container_id).removeClass("d-none");
            else 
                $("#"+container_id).addClass("d-none");
        }
        function hideShowContainerForAG(e, part='') {
            if(e.val() == "abc") {
                $("#abc_criteria_"+part).removeClass("d-none");
                $("#3s2s_criteria_"+part).addClass("d-none");
                $(".abc_criteria_"+part).attr('disabled', false);
                $(".3s2s_criteria_"+part).attr('disabled', true);
            } else if(e.val() == "3s2s") {
                $("#abc_criteria_"+part).addClass("d-none");
                $("#3s2s_criteria_"+part).removeClass("d-none");
                $(".abc_criteria_"+part).attr('disabled', true);
                $(".3s2s_criteria_"+part).attr('disabled', false);
            } else {
                $("#abc_criteria_"+part).addClass("d-none");
                $("#3s2s_criteria_"+part).addClass("d-none");
                $(".abc_criteria_"+part).attr('disabled', true);
                $(".3s2s_criteria_"+part).attr('disabled', true);
            }
        }
        // Show test scores data
        /*$('#test_scores').change(function() {
            if ($(this).prop("checked")) {
                $.ajax({
                    url: "{{url($module_url)}}/test_score/{{$data['id']}}",
                    type: 'POST',
                    data: {
                        '_token': "{{csrf_token()}}",
                        'program_id': "{{$data['program_id']}}",
                        'application_id': "{{$data['application_id']}}"
                    },
                    success: function(response) {
                        $('#test_score_options').html(response);
                    } 
                });
            } else {
                $('#test_score_options').html('');
            }
        });*/

        /** Test Score range scripts start */
        $(document).on('click', '.ts_range_add',  function() {
            let ts_range = $(this).closest('.ts_main_container').find('.ts_container').find('.ts_range');
            let ts_range_field = ts_range.find('div').last();
            let ts_range_field_cln = ts_range_field.clone();
            let range_name = ts_range_field.find('.ts_range_field').attr('name');
            range_name = range_name.match(/\[(.*?)\]/g);
            let ts_vise_ind = range_name[(range_name.length - 2)].replace('[', '').replace(']', '');
            ts_range_field_cln.find('.ts_range_field').attr('name', 'ts_value[range]['+ts_vise_ind+'][]');
            ts_range_field_cln.find('.ts_point_field').attr('name', 'ts_value[range_points]['+ts_vise_ind+'][]');
            ts_range_field_cln.find('.ts_point_field').val('');
            ts_range_field_cln.find('.ts_range_field').val('');
            ts_range_field_cln.find('.ts_range_rmv').removeClass('d-none');
            ts_range_field.parent().append(ts_range_field_cln);
            // maintain remove button hide-show
            let curr_rng = ts_range;
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
        }
        /** Test Score range scripts end */

        // For validation
        $("form#frm_eligibility").validate({
            rules:{
                'grade_lavel[]':{
                    required:true,
                    /*remote:{
                        url: "{{url($module_url)}}/validate/grades",
                        type: "GET",
                        data: {
                            selected_grades: function () {
                                return selectedGrades();
                            }
                        },
                    }*/
                }
            },
            messages:{
                'grade_lavel[]':{
                    required:'Grade Level is required..',
                    // remote: 'This combination of grades is already present.'
                }
            },
            errorPlacement: function(error, element)
            {
                error.addClass('ml-2 pl-10 pr-20')
                error.appendTo( element.parents('.form-group'));
            }
        });

        // Grade level validate
        $('.grd_lvl_chk').change(function() {
            $("form#frm_eligibility").valid();
        });
        function selectedGrades() {
            var selcted_grades = new Array();
            $(".grd_lvl_chk:checked").each(function () {
                selcted_grades.push(this.value);
            });
            return selcted_grades;
        }

        // Incident
        $(".datepicker").datetimepicker({
            todayHighlight: true,
            toggleActive: true
        });
    </script>
@stop