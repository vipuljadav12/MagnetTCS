@php
    $recommendation = getRecommendationFormData($submission->id);
    $doneArr = array();
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $rec_subjects = $eligibility_data->subjects;

@endphp
@if(isset($recommendation) && !empty($recommendation))
    @foreach($recommendation as $k=>$rec_value)
        @php 
            $doneArr[] = $rec_value->config_value; 

            // dd($rec_value);
        @endphp
        <div class="card shadow">

            @php
                $subject = explode('.', $rec_value->config_value)[0];

                $ans_content = json_decode($rec_value->answer);
                // dd($ans_content);
            @endphp
            <div class="card-header">Recommendation - {{config('variables.recommendation_subject')[$subject]}}</div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Teacher Name : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$rec_value->teacher_name}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Teacher Email : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$rec_value->teacher_email}}" disabled>
                    </div>
                </div>
                <div class="form-group row d-none">
                    <label class="control-label col-12 col-md-12">Average Score : </label>
                    <div class="col-12 col-md-12">
                        <input type="text" class="form-control" value="{{$rec_value->avg_score}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Comment : </label>
                    <div class="col-12 col-md-12">
                        <textarea class="form-control" rows="3" disabled>{{$rec_value->comment}}</textarea>
                    </div>
                </div>

                @if(isset($ans_content->answer))
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">Question-Ans : </label>
                        <div class="col-12 col-md-12">
                            @foreach($ans_content->answer as $h=>$header)

                                <div class="card">
                                    <div class="card-header">{{$header->name}}</div>
                                    <div class="card-body">
                                        @if(isset($header->answers))
                                    @foreach($header->answers as $ak=>$avalue)
                                        <div class="form-group row">
                                            <label class="control-label col-12 col-md-12">{{$ak ?? ''}} : </label>
                                            <div class="col-12 col-md-12">
                                                <select class="form-control" disabled="">
                                                @foreach($header->points as $pk=>$point)
                                                    <option>{{$avalue}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    @endforeach
                                    @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">Student LInk</label>
                    <div class="col-12 col-md-12">
                        <span style="color: blue;">{{url('/recommendation/'.$rec_value->config_value)}}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12">
                        @if(isset($eligibility_data->form_type) && $eligibility_data->form_type == "IB")
                            <a href="{{url('/admin/Submissions/ibform/pdf/'.$rec_value->id.'/ib')}}" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Recommendation Form</a>
                        @else
                            <a href="{{url('/admin/Submissions/recommendation/pdf/'.$rec_value->id.'')}}" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Recommendation Form</a>
                        @endif
                    </label>
                </div>
            </div>
        </div>

    @endforeach
@endif


@php
    $recommendationUrl = getRecommendationLinks($submission->id);    

@endphp
@if(isset($recommendationUrl) && !empty($recommendationUrl))
    <div class="card shadow">
        <div class="card-header">Pending Recommendation </div>
        <div class="card-body">
            @foreach($recommendationUrl as $key => $rec_value)
                @if(!in_array($rec_value->config_value, $doneArr))
                    @if($submission->student_id != '')
                        <form method="post" action="{{url('admin/Submissions/recommendation/send/manual')}}" onsubmit="return validateTeacherEmail(this)">
                    @else
                         <form method="post" action="{{url('admin/Submissions/recommendation/send/parent/manual')}}">
                    @endif
                    {{csrf_field()}}    
                    <input type="hidden" name="submission_id" value="{{$submission->id}}">
                    <input type="hidden" name="config_id" value="{{$rec_value->id}}">
                    <div class="form-group row pt-10">
                        <label class="control-label col-12 col-md-12">
                            @php
                                $name = "";
                                $email = "";
                                $subject_title = str_replace("recommendation_", "", $rec_value->config_name);
                                $subject_title = str_replace("_url", "", $subject_title);
                                $rsubjects = config('variables.recommendation_subject');
                                if($subject_title == "lfd_admin" && isset($eligibility_data->lfd_admin_name))
                                {
                                    $name = $eligibility_data->lfd_admin_name;
                                    $email = $eligibility_data->lfd_admin_email;
                                }
                                else
                                {
                                    if($submission->student_id != '')
                                    {
                                        $rs_student = \App\StudentData::where("stateID", $submission->student_id)->where("field_name", strtolower($subject_title)."_teacher_email")->where("enrollment_id", Session::get('enrollment_id'))->first();
                                        if(!empty($rs_student))
                                        {
                                            $email = $rs_student->field_value;
                                        }
                                        $rs_student = \App\StudentData::where("stateID", $submission->student_id)->where("field_name", strtolower($subject_title)."_teacher_name")->where("enrollment_id", Session::get('enrollment_id'))->first();
                                        if(!empty($rs_student))
                                        {
                                            $name = $rs_student->field_value;
                                        }

                                    }
                                }
                                echo "<strong>".$rsubjects[$subject_title] ." Recommendation Form"."</strong>";
                            @endphp
                        </label>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Teacher Name:</strong></label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="teacher_name" value="{{$name}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Teacher Email:</strong></label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="email" value="{{$email}}">
                        </div>
                    </div>
                     
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"><strong>Recommendation Form Link:</strong></label>
                        <div class="col-12 col-md-12">
                            <span style="color: blue;"><a href="{{url('/recommendation/'.$rec_value->config_value)}}">{{url('/recommendation/'.$rec_value->config_value)}}</a></span>
                        </div>
                    </div>
                    <div class="form-group row pb-20" style="border-bottom: 1px solid #ccc;">
                        
                        <div class="col-12 col-md-12">
                            @if($submission->student_id != '')
                                <input type="submit" class="btn btn-success" value="Send Recommendation Email Link">
                            @else
                                <input type="submit" class="btn btn-success" value="Send Recommendation Email To Parent">
                            @endif
                        </div>
                    </div>
                    </form>
                @endif
            @endforeach
        </div>
    </div>
@endif
