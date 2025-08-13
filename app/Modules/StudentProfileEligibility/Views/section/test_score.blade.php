<div class="form-group">
    <div class="row pl-20 pr-20">
        <div class="col-sm-12" style="display: inline-block !important;">
            
                @if(isset($data['test_scores']) && isset($data['test_scores']['ts_scores']))
                <div class="form-group {{-- wp_container --}} row">
                    @foreach($data['test_scores']['ts_scores'] as $key=>$value)
                        <div class="col-12 col-lg-4 {{-- wp_row --}}" style="display: flex !important;">
                            <div class="card ts_main_container w-100">
                                <div class="card-header d-flex justify-content-between align-items-center form-group input-group">
                                    {{-- <div class="row form-group input-group"> --}}
                                        <div class="row col-12">
                                            <div class="">
                                                <a href="javascript:void(0)" class="btn btn-sm btn-success text-white ts_range_add" title="Add Range"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                            </div>
                                            <div class="col-6 align-self-center">
                                                {{$value}} :
                                                <input type="hidden" class="form-control col-8 mr-1 ts_range_field" name="ts_value[ts_scores][]" placeholder="marks" value="{{$value}}">
                                            </div>
                                        </div>
                                        <div class="row col-12 text-center mt-2" style="font-weight: lighter;">
                                            <div class="col-6">Min. Score</div>
                                            <div class="col-6">Point Value</div>
                                        </div>
                                    {{-- </div> --}}
                                </div>
                                <div class="card-body">
                                    <div class="row ts_container">
                                        <div class="col-12 ts_range">
                                            @php
                                                $rng_class = 'd-none';
                                                $stored_ts_idx = isset($data['test_score_range']['ts_scores']) ? array_search($value, $data['test_score_range']['ts_scores']) : false;
                                                if ($stored_ts_idx!==false) {
                                                    $ts_rng_cnt = isset($data['test_score_range']['range'][$stored_ts_idx]) ? count($data['test_score_range']['range'][$stored_ts_idx]) : 0;
                                                    if ($ts_rng_cnt > 1 ) {
                                                        $rng_class = '';
                                                    }
                                                    $tmp_rng = false;
                                                } else {
                                                    $tmp_rng = true;
                                                    $stored_ts_idx = 'new';
                                                    $data['test_score_range']['range'][$stored_ts_idx] = [[]];
                                                }
                                            @endphp
                                            @foreach($data['test_score_range']['range'][$stored_ts_idx] as $r_ind=>$r_val)
                                                <div class="form-group input-group">
                                                    @php
                                                        if ($tmp_rng) {
                                                            $r_val = '';
                                                            $r_ind = 0;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control mr-1 ts_range_field valid" name="ts_value[range][{{$loop->parent->index}}][]" placeholder="marks" value="{{$r_val ?? ''}}" onkeypress="return isNumber(event)" maxlength="6">
                                                    <input type="text" class="form-control ts_point_field valid" name="ts_value[range_points][{{$loop->parent->index}}][]" placeholder="points" value="{{$data['test_score_range']['range_points'][$stored_ts_idx][$r_ind] ?? ''}}" onkeypress="return isNumber(event, true)" maxlength="6">
                                                    <button class="form-control ml-2 btn btn-danger text-white ts_range_rmv {{$rng_class}}" title="Remove Range" style="max-width: 30px;"><i class="fa fa-trash-alt" aria-hidden="true"></i></button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                @else
                    <p class="text-center"><strong>No Test Scores setup done for this program.</strong></p>
                @endif
            
        </div>
    </div>
</div>
