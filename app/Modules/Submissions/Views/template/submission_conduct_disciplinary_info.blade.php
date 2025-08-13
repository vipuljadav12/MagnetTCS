@php 
    $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);
    $type = $eligibility_data->scoring->method;
    $options = isset($eligibility_data->scoring->$type) ? $eligibility_data->scoring->$type : array();
    $data = array();//getConductDisciplinaryInfo($submission->id);
    $b_info = $c_info = $d_info = $e_info = $susp = $susp_days = '';

    if($submission->student_id != "")
    {
        $exist_data = getStudentConductData($submission->student_id, $submission->id);
        //print_r($exist_data);exit; 
        if(!empty($exist_data))
        {
            $b_info = $exist_data->b_info;
            $c_info = $exist_data->c_info;
            $d_info = $exist_data->d_info;
            $e_info = $exist_data->e_info;
            $susp = $exist_data->susp;
            $susp_days = $exist_data->susp_days;
        }
    } 
    else
    {
        $exist_data = getStudentConductData($submission->student_id, $submission->id);
         if(!empty($exist_data))
        {
            $b_info = $exist_data->b_info;
            $c_info = $exist_data->c_info;
            $d_info = $exist_data->d_info;
            $e_info = $exist_data->e_info;
            $susp = $exist_data->susp;
            $susp_days = $exist_data->susp_days;
        }
    }
    $application_d = DB::table("application")->where("id", $submission->application_id)->first();

    $cdi_starting_date = "2019-08-01";
    $cdi_ending_date = "2020-07-31";

    if(!empty($application_d))
    {
        $cdi_starting_date = $application_d->cdi_starting_date;
        $cdi_ending_date = $application_d->cdi_ending_date;
    }
@endphp
<form class="form" id="insterview_score_form" method="post" action="{{url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        
        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="">{{$value->eligibility_name}}</div>
                            @if($value->override == 'Y')
                                    <div class="d-flex align-items-center">
                                        <div class="mr-10">Override Student CDI : </div> 
                                        <input id="chk_acd" type="checkbox" class="js-switch js-switch-1 js-switch-xs cdi_override" data-size="Small"  {{$submission->cdi_override=='Y'?'checked':''}}/>
                                    </div>
                                @endif
                            
                        </div>
        <div class="card-body">
            @if($submission->student_id != "")
                <div class="text-right pb-10"><a href="javascript:void(0)" onclick="fetchCDIManually()" class="btn btn-success cdi_fetch" title="Fetch Manually">Run iNOW API</a>
                </div>
            @endif
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">B info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="b_info" value="{{$b_info}}" maxlength="2" max="99">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">C info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="c_info" value="{{$c_info}}" maxlength="2" max="99">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">D info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="d_info" value="{{$d_info}}" maxlength="2" max="99">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">E info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="e_info" value="{{$e_info}}" maxlength="2" max="99">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"># Susp</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="susp" value="{{$susp}}" maxlength="2" max="99">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"># Days Susp</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="susp_days" value="{{$susp_days}}" maxlength="2" max="99">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-12 d-none" align="right"> 
                    <button type="submit" form="insterview_score_form" class="btn btn-success"><i class="fa fa-save"> </i></button>
                </div>
            </div>
            @if($submission->student_id != "")
            @php $conduct_info = fetch_conduct_details($submission->student_id) @endphp
            @if(!empty($conduct_info))
                <div class="card-body text-right">
                    <a href="javascript:void(0)" class="text-info" onclick="showCDIDetails()" title="View All">View All Records</a>
                </div>
                <table class="table table-striped d-none" id="cdi_details">
                  <thead>
                    <tr>
                      <th scope="col" class="text-center">Date</th>
                      <th scope="col" class="text-center">Infraction</th>
                      <th scope="col" class="text-center">Disposition</th>
                      <th scope="col" class="text-center">Action Name</th>
                      <th scope="col" class="text-center">Suspended Days</th>
                      <th scope="col" class="text-center">Suspension Start</th>
                      <th scope="col" class="text-center">Suspension End</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($conduct_info as $ckey=>$cinfo)
                        @if($cinfo->datetime >= date("Y-m-d", strtotime($cdi_starting_date)) && $cinfo->datetime <= date("Y-m-d", strtotime($cdi_ending_date)))
                            @php $class = "table-success" @endphp
                        @else
                            @php $class = "" @endphp
                        @endif
                        <tr class="{{$class}}">
                            <td>{{getDateFormat($cinfo->datetime)}}</td>
                            <td>{{$cinfo->infraction_name}}</td>
                            <td>{{$cinfo->disposition}}</td>
                            <td>{{$cinfo->actionname}}</td>
                            <td class="text-center">{{$cinfo->suspend_days}}</td>
                            @if($cinfo->disposition_type == "Suspended/Out of School")
                                <td>{{getDateFormat($cinfo->startdate)}}</td>
                                <td>{{getDateFormat($cinfo->enddate)}}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                    
                  </tbody>
                </table>
            @endif
        @endif
        </div>

        <div class="modal fade" id="overrideConductDispInfo" tabindex="-1" role="dialog" aria-labelledby="employeependingLabel1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeependingLabel1">Alert</h5>
                        <button type="button" class="close overrideConductDispInfoNo" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                    <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label">Comment : </label>
                                <textarea class="form-control" name="cdi_override_comment" id="cdi_override_comment"></textarea>
                                <input type="hidden" name="cdi_override_status" id="cdi_override_status">
                            </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-value="" id="overrideConductDispInfoYes" onclick="overrideConductDispInfo()">Submit</button>
                        <button type="button" class="btn btn-danger overrideConductDispInfoNo">Cancel</button>
                    </div>
                </div>
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

    </div>
</form>

