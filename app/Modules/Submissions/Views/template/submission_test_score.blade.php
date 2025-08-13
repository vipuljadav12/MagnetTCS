@if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)

        @php
            $program_id = $submission->{$choice.'_choice_program_id'};

            if ($choice == 'first' || count($choice_ary) == 1) {
                $data = getTestScoreData($submission->id, $value, $submission->late_submission);
            } else{
                $data = getTestScoreData($submission->id, $value_2, $submission->late_submission);
                $value = $value_2;
            }

            $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
            $option = [];
            if($eligibility_data->eligibility_type->type == "NR")
            {
                $options = $eligibility_data->eligibility_type->NR;
            } elseif($eligibility_data->eligibility_type->type == "DV") {
                $system_calculated = true;
                $ts_studentprofile_data = app('\App\Modules\Submissions\Controllers\SubmissionsController')->calculateStudentProfile($submission->id, ['USC'], $choice);
            }
        @endphp

        <form class="form" id="frm_test_score_{{$choice}}" method="post" action="{{url('admin/Submissions/update/TestScore/'.$submission->id.'/'.$program_id)}}">
            {{csrf_field()}}
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    <div class="">
                        @if(!empty($data))
                            @php
                                ${$choice.'_count'} = 0;
                            @endphp
                            @foreach($data as $ckey => $cvalue)
                                <div class="form-group row">
                                    <label class="control-label col-2 col-md-2 font-weight-bold">{{$ckey or ''}}</label>
                                    <div class="col-5 col-md-5">
                                        <input type="hidden" name="test_score_name[]" value="{{$ckey}}">
                                        <input id="ts_{{ $choice.'_'.${$choice.'_count'} }}" type="text" name="test_score_value[]" class="form-control" value="{{ $cvalue['score'][$ckey] ?? ''}}">
                                    </div>
                                    <div class="col-5 col-md-5">
                                        <div class="form-group custom-none">
                                            <div class="">
                                                @if(isset($system_calculated))
                                                    @if (!empty($ts_studentprofile_data))
                                                        @php
                                                            $ts_calculated_score = ($ts_studentprofile_data['profile']['test_score']['data'][$ckey]['score'] ?? 0);
                                                            $ts_calculated_txt_ary = ($ts_studentprofile_data['profile']['test_score']['data'][$ckey]['txt'] ?? []);
                                                            $ts_calculated_txt = '';
                                                            foreach ($ts_calculated_txt_ary as $txt) {
                                                                $ts_calculated_txt .= "".$txt."";
                                                                if (next($ts_calculated_txt_ary) !== false) {
                                                                    $ts_calculated_txt .= ' <br/> ';
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="form-group custom-none input-group">
                                                            <span class="align-self-center">Scored</span>&nbsp;
                                                            <i class="fa fa-info-circle align-self-center score_info" aria-hidden="true" data-toggle="tooltip" data-html="true" title="{!!$ts_calculated_txt!!}"></i> &nbsp;
                                                            <span class="align-self-center"> :</span>
                                                            <input type="text" class="form-control mr-2 ml-1" disabled value="{{$ts_calculated_score}}">
                                                        </div>
                                                    @endif
                                                @elseif($eligibility_data->eligibility_type->type != "DD")
                                                    <select class="form-control custom-select template-type" name="test_score_rank[]">
                                                        <option value="0">Select Option</option>
                                                        @if(isset($options))
                                                            @foreach($options as $k=>$v)
                                                                <option value="{{$v}}" @if(isset($cvalue['scorerank'][$ckey]) && $cvalue['scorerank'][$ckey] == $v) selected="" @endif>{{$v}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @php
                                    ${$choice.'_count'}++;
                                @endphp
                            @endforeach
                        @endif
                    </div>
                    <div class="text-right"> 
                        <button type="submit" form="frm_test_score_{{$choice}}" class="btn btn-success">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endforeach
    
@endif

@section('submission_test_score_script')
<script type="text/javascript">
    /*$(".score_info").on('mouseover', function() {
        $(this).css('cursor', 'pointer');
    });*/
    /*$(".score_info").on('click', function() {
        let info_txt_obj = $(this).parent().find('.score_info_txt');
        info_txt_obj.toggleClass('d-none');
        // $(this).attr('title', txt);
        // console.log(txt);
    });*/
</script>
@endsection
