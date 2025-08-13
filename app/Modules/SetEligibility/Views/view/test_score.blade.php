<div class="card shadow">
    <form id="extraValueForm" action="{{url('admin/SetEligibility/extra_values/save')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$req['program_id']}}">
        <input type="hidden" name="eligibility_id" value="{{$req['eligibility_id']}}">
        <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
        <input type="hidden" name="application_id" value="{{$req['application_id']}}">

        <div class="card-header">{{$eligibility->name}}</div>
        <div class="card-body">
            <div class="form-group wp_container row">
                @php $box_count = json_decode($eligibility->content)->eligibility_type->box_count @endphp
                @php
                    $ts_count = !empty($extraValue['ts_scores']) ? count($extraValue['ts_scores']) : 0;
                    $tmp_ts_count = $ts_count;
                    $remain_box = $box_count - $ts_count;
                    if($ts_count < $box_count){
                        for ($i=0; $i < $remain_box; $i++) { 
                            $extraValue['ts_scores'][] = [];
                        }
                    }
                @endphp
                
                @php $count = 1 @endphp
                @foreach($extraValue['ts_scores'] as $key=>$value)
                    @php
                        // limit number of fields on change
                        if ($count > $box_count) {
                            break;
                        }
                        if($tmp_ts_count <= 0) {
                            $value = '';
                        }
                        $tmp_ts_count--;
                    @endphp
                    <div class="col-12 row wp_row">
                        <div class="col-12">Name of Test Title Score Field {{$count}}</div>
                        <div class="col-12 input-group"> 
                            <input type="text" class="form-control mb-2 col-8 mr-2" name="value[ts_scores][{{$loop->index}}]" value="{{$value}}">
                            <input type="text" class="form-control mb-2 col-4" placeholder="Short Name" name="value[ts_scores_short][{{$loop->index}}]" value="{{$extraValue['ts_scores_short'][$loop->index] ?? ''}}">
                            {{-- <div class="col-12 row ts_container mt-1">
                                <div class="col-12 row wp_row">
                                    <div class="col-12">Short Name</div>
                                    <div class="col-12"> 
                                        <input type="text" class="form-control mb-2" name="value[ts_scores_short][{{$loop->index}}]" value="{{$extraValue['ts_scores_short'][$loop->index] ?? ''}}">
                                    </div>
                                </div>
                                <div class="col-12 mb-1">Range : <a href="javascript:void(0)" class="btn btn-sm btn-success text-white ts_range_add" title="Add More">+</a></div>
                                <div class="col-12 ts_range">
                                    @php
                                        $ts_rng_cnt = isset($extraValue['range'][$loop->index]) ? count($extraValue['range'][$loop->index]) : 0;
                                        if ($ts_rng_cnt <= 1 ) {
                                            $rng_class = 'd-none';
                                        } else {
                                            $rng_class = '';
                                        }
                                        if (!isset($extraValue['range'][$loop->index])) {
                                            $tmp_rng = true;
                                            $extraValue['range'][$loop->index] = [[]];
                                        }
                                    @endphp
                                    @foreach($extraValue['range'][$loop->index] as $r_ind=>$r_val)
                                        <div class="col-12 row form-group">
                                            @php
                                                if (isset($tmp_rng)) {
                                                    $r_val = '';
                                                    $r_ind = 0;
                                                }
                                            @endphp
                                            <input type="text" class="form-control col-4 mb-2 mr-1 ts_range_field" name="value[range][{{$loop->parent->index}}][]" placeholder="marks" value="{{$r_val ?? ''}}" onkeypress="return isNumber(event)" maxlength="6">
                                            <input type="text" class="form-control col-4 mb-2 ts_point_field" name="value[range_points][{{$loop->parent->index}}][]" placeholder="points" value="{{$extraValue['range_points'][$loop->parent->index][$r_ind] ?? ''}}" onkeypress="return isNumber(event, true)" maxlength="6">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white ts_range_rmv {{$rng_class}}" title="Add More">-</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    @php $count++ @endphp
                @endforeach
            </div>
        </div>
    </form>
</div>