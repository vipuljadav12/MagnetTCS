@php
    if (isset($data['eligibility']->conduct_discpline_criteria_data)) {
        $conduct_discpline_criteria = json_decode($data['eligibility']->conduct_discpline_criteria_data, 1);
    }
    // $incident_count = ($conduct_discpline_criteria['incident_count'] ?? 0);
@endphp
<div class="form-group">
    <div class="row pl-20 pr-20 col-12">
        <div class="col-12" style="display: inline-block !important;">
            <div class="card">
                <div class="card-header">How many Incidents we need to consider ?</div>
                <div class="card-body">
                    <div class="custom-control">
                        <select class="form-control" name="conduct_discpline[incident_consider]" id="incident">
                            @for($i=1; $i<8; $i++)
                                <option @if(isset($data['eligibility']) && ($data['eligibility']['incident_consider'] == $i)) selected @endif>{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>