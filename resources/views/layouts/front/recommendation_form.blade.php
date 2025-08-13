@extends('layouts.front.app')

@section('title')
    <title>{{getProgramName($program_id)}}</title>
@endsection
@section('content')
<style type="text/css">
    input[type="checkbox"]:after {
    width: 17px;
    height: 17px;
    margin-top: -2px;
    font-size: 14px;
    line-height: 1.2;
}
input[type="checkbox"]:checked:after {
    font-family: 'Font Awesome 5 Free';
    color: #00346b;
    font-weight: 900;
    width: 17px;
    height: 17px;
}
</style>
    {{-- @include("layouts.front.common.district_header") --}}
    <div class="mt-20">
      <div class="card bg-light p-20">
        <div class="row">
          <div class="col-sm-6 col-xs-12">
            <div class="text-left font-20 b-600">{{getProgramName($program_id)}}</div>
          </div>
        </div>
      </div>
    </div>
    <form action="{{url('/answer/save')}}" method="POST" id="recommendationForm">
    {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$program_id}}">
        <input type="hidden" name="eligibility_id" value="{{$eligibility_id}}">

        
        <input type="hidden" name="submission_id" value="{{$submission->id}}">
        <input type="hidden" name="subject" value="{{$subject}}">
        <div class="mt-20">
          <div class="card bg-light p-20">
            <div class="row">
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Confirmation No: <span>{{$submission->confirmation_no}}</span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">Student: <span>{{$submission->first_name. ' ' . $submission->last_name}}</span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600">School: <span>{{$submission->current_school}}</span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600 d-flex">Title: <span>{{config('variables.recommendation_subject')[$subject]}}</span></div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600 d-flex">Teacher: 
                    <span class="d-inline-block ml-10">
                        <input type="text" class="form-control max-250" name="teacher_name" @if($teacher_name != "") value="{{$teacher_name}}" @endif placeholder="Teacher Name">
                    </span>
                </div>
              </div>
              <div class="col-sm-12 col-xs-12 mb-10">
                <div class="text-left font-16 b-600 d-flex">Email: <span class="d-inline-block ml-10">
                  <input type="text" class="form-control max-250" name="teacher_email" placeholder="Email ID" @if($teacher_email != "") value="{{$teacher_email}}" @endif>
                  </span></div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="p-20 border mt-20 mb-20">
            @if($header_text != "")
                {!! str_replace("{program_name}", getProgramName($program_id), $header_text) !!}
            @else
                <div class="h6 mb-10">Dear Staff:</div>
                <div class="h6 mb-20">Your recommendation is an important consideration in the decision process of the screening committee for acceptance into the {{getProgramName($program_id)}}.</div>
            @endif
            @if($program_id != 28)
            <div style="float: left; margin: 0 auto; width: 100%;">
                <table style="border: 1px solid #000">
                    <tbody>
                        <tr>
                            <td colspan="2" align="center"><strong>Numerical Rating Description</strong></td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">7</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">In this category, the child is among the <u>very highest in frequency</u>, intensity, and/or quality of the behavior in comparison to the reference group
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">6</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">Behavior is <u>significantly more frequent</u>, etc.</td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">5</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">Behavior is <u>somewhat more frequent</u>, etc.</td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">4</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">Behavior is <u>typical or commonly observed</u> in the reference group</td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">3</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">Behavior is <u>somewhat less frequent</u>, etc.</td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">2</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">Behavior is <u>significantly less frequent</u>, etc.</td>
                        </tr>
                        <tr>
                            <td valign="top" width="66" align="center">1</td>
                            <td valign="top" style="padding-right:  10px; padding-left: 10px;">In this category the child is among the <u>very lowest in frequency</u>, intensity and/or quality of the behavior in comparison to the reference group</td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <div class="col-sm-12 col-xs-12 mb-10 mt-10" style="padding-left: 0 !important; float: left; width100%">
                    <p><strong>DEFINITIONS</strong></p>
                    <ul>
                        <li><strong>Frequency:</strong> Refers to the number of times the behavior is demonstrated in proportion to opportunities.</li>
                        <li><strong>Intensity:</strong> Refers to the amount of intellectual, emotional, or physical energy that the child invests in the behavior or activity.</li>
                        <li><strong>Quality:</strong> Implies a unique caliber of performance, implies some standard of excellence, and relates more to the academically/socially desirable categories of behavior.</li>
                    </ul>
                </div>
                @endif
                
            @if(isset($content->header) && !empty($content->header))
            @foreach($content->header as $key=>$header)
                <div class="h4 mb-20">{{$header->name}}</div>
                <div class="box-0">
                    <input type="hidden" name="extra[answer][{{$key}}][name]" value="{{$header->name}}">
                    @if(isset($header->questions))
                        @foreach($header->questions as $q=>$question)
                            <div class="form-group row" style="width:100% !important">
                                <label class="control-label col-12 col-md-3 col-xl-3 b-600 text-right mt-1">{{$question}}</label>
                                @if(isset($header->options))
                                <div class="col-12 col-md-8 col-xl-8">
                                    <select class="form-control custom-select recommQuestion" name="extra[answer][{{$key}}][answers][{{$question}}]">
                                        <option value="0">Choose an option</option>
                                        @foreach($header->options as $o => $option)
                                            @if($option != '')
                                                <option value="{{ $header->points->{$o} ?? $option }}">{{$header->points->{$o} . ($header->points->{$o} != "" ? '. ' : "") . $option}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    {{-- {{dd($header, $question)}} --}}
                    @if(isset($header->options))
                        @foreach($header->options as $ko => $option)
                            @if($option != '')
                                <input type="hidden" name="extra[answer][{{$key}}][options][]" value="{{$option}}">
                                <input type="hidden" name="extra[answer][{{$key}}][points][]" value="{{$header->points->{$ko} }}">
                            @endif
                        @endforeach
                    @endif
                </div>
            @endforeach
            @endif
            <div class="form-group row">
                <label class="control-label col-12 col-md-3 col-xl-3 b-600 text-right mt-1" for="qry02">Additional comment</label>
                <div class="col-12 col-md-8 col-xl-8">
                    <textarea class="form-control" name="comment" rows=8></textarea>    
                </div>
            </div>
            <div class="form-group row mb-0 d-none">
                <div class="col-12 col-md-11 col-xl-11 text-right">
                    <label class="mr-10">Average Score</label>
                    <span class="d-inline-block">
                        <input type="hidden" class="average_score" name="avg_score">
                        <input class="form-control max-250 average_score" id="average_score" value="0.00" disabled>
                    </span> 
                </div>
            </div>
        </div>
        @if(isset($content->description) && $content->description[0] != ''))
        <div class="mt-20">
            <div class="card bg-light p-20">
                <div class="row">
                    @foreach($content->description as $k=>$value)
                        @if(trim($value))
                            <div class="col-sm-12 col-xs-12 mb-10">
                                <div class="text-left font-16 b-600"><input type="checkbox" class="" name="extra[description][]" value="{{$value}}"> <span>{{$value}}</span></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <div class="mt-20">
            <div class="p-20 pb-0">
                <div class="text-center font-20 b-600 mb-10">This Recommendation is confidential</div>
                <div class="col-12 col-md-6 col-xl-12 text-center mb-30"> 
                    <input type="submit" class="btn btn-secondary btn-xxl" value="Submit Recommendation">
                    {{-- <a href="javascript:void(0);" class="btn btn-secondary btn-xxl" title="">Submit Recommendation</a> --}}
                </div>
                <div class="col-12 col-md-6 col-xl-12 text-center">
                  <p>This electronic form must be completed by {{getDateTimeFormat($recommendation_due_date)}}.</p>
                </div>
            </div>
        </div>

        @if(trim($footer_text) != '')
            <div class="mt-0 mb-20">
                <div class="card bg-light p-20">
                    <div class="text-center">
                        <p class="m-0">{!! $footer_text !!}</p>
                    </div>
                </div>
            </div>
        @endif

    </form>


    
@endsection
@section('scripts')

<script type="text/javascript">
    $('#recommendationForm').validate({
        rules: {
            teacher_name: {
                required: true,                       
            },
            teacher_email: {
                required: true,                       
            }
        },
        messages: {
            teacher_name: {
                required: "Teacher Name is required."
            },
            teacher_email: {
                required: "Teacher Email Address is required."
            }
        },
        submitHandler: function (form, e) {
            errorCheck();

            var count = $(document).find('.error').length;

            if(count == 0){
                form.submit();
            }
        }
    });

    $(document).on('change', '.recommQuestion', function(){

        var value = $(this).val();

            
        if(value == 0 && value != ''){
            $(this).addClass('error').css('border-color','red');
        }else{
            $(this).removeClass('error').css('border-color','');
        }

        averageScore();
    });

    function averageScore(){
        var total = 0;
        var score = 0;
        var avg = 0;

        $(document).find('.recommQuestion').each(function(){
            var value = $(this).val();
            total++;
            score = parseInt(score)  + parseInt(value);
        }); 
        
        avg = score/total;
        $(document).find('.average_score').val(avg.toFixed(2));                
    }

    function errorCheck(){
        $(document).find('.recommQuestion').each(function(){
            var value = $(this).val();
            
            if(value == 0 && value != ''){
                $(this).addClass('error').css('border-color','red');
            }else{
                $(this).removeClass('error').css('border-color','');
            }
        });
    }
</script>

@endsection