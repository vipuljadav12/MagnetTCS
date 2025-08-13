@if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)
        @php
            if ($choice == 'first' || count($choice_ary) == 1) {
                $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
            } else{
                $eligibility_data = getEligibilityContent1($value_2->assigned_eigibility_name);
            }
            $submission_audition_data = getSubmissionAudition($submission->id);
            $data = !empty($submission_audition_data->data) ? json_decode($submission_audition_data->data, true) : [];
            $options = ($eligibility_data->eligibility_type->type=="NR") ? $eligibility_data->eligibility_type->NR : [];
        @endphp
        <form class="form" id="audition_form_{{$choice}}" method="post" action="{{url('admin/Submissions/update/audition/'.$submission->id)}}">  
            {{csrf_field()}}
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    <div class="form-group custom-none">

                        <div class="">
                            <select class="form-control custom-select template-type" name="{{$choice}}_data">
                                <option value="">Select Option</option>
                                @foreach($options as $k=>$v)
                                    <option @if(isset($data[$choice.'_data']) && $data[$choice.'_data'] == $v) selected="" @endif>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-right"> 
                        <button type="submit" form="audition_form_{{$choice}}" class="btn btn-success">    
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
@endif

{{-- @if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)
        @php
            if ($choice == 'first' || count($choice_ary) == 1) {
                $eligibility_data = getEligibilityConfig($submission->first_choice_program_id, $value->assigned_eigibility_name, "email");
            } else{
                $eligibility_data = getEligibilityConfig($submission->second_choice_program_id, $value->assigned_eigibility_name, "email");
            }
            
        @endphp
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    {!! $eligibility_data !!}
                </div>
            </div>
    @endforeach
@endif --}}