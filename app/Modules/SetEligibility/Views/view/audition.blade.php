<div class="card shadow">
    <form id="extraValueForm" action="{{url('admin/SetEligibility/extra_values/save')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$req['program_id']}}">
        <input type="hidden" name="eligibil`ity_id" value="{{$req['eligibility_id']}}">
        <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
        <input type="hidden" name="application_id" value="{{$req['application_id']}}">
        <div class="card-header">{{$eligibility->name}}</div>
        <div class="card-body">
            <div class="form-group custom-none">
    {{--            <label class="control-label">{{$eligibility->name}}</label>--}}
                <div class="">
                    @php
                        $selected = $extraValue['eligibility_type']['type'] ?? '';
                    @endphp
                    <select class="form-control custom-select template-type" name="value[eligibility_type][type]">
                    {{-- <select class="form-control custom-select template-type" name="extra[eligibility_type][type]"> --}}
                        <option value="">Select Option</option>
                        @if(json_decode($eligibility->content)->eligibility_type->type=='YN')
                            @forelse(json_decode($eligibility->content)->eligibility_type->YN as $yn)
                                <option @if($selected == $yn) selected @endif>{{$yn}}</option>
                            @empty
                            @endforelse
                        @else
                            @forelse(json_decode($eligibility->content)->eligibility_type->NR as $nr)
                                <option @if($selected == $nr) selected @endif>{{$nr}}</option>
                            @empty
                            @endforelse
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- {{dd($extraValue['eligibility_type']['type'])}} --}}