@if(isset($data['student']))
    @php
        $grades = [ 'PreK', 'K', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12' ];
    @endphp
    <form method="post" id="frm_student_search" action="{{url($module_url)}}/update">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$data['student']->stateID}}">
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">First Name : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="first_name" value="{{$data['student']->first_name}}"></div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Last Name : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="last_name" value="{{$data['student']->last_name}}"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Current Grade : </label>
                <div class="">
                    <select class="form-control" name="current_grade">
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option @if($data['student']->current_grade == $grade) selected @endif>{{$grade}}</option>
                        @endforeach
                    </select>
                    {{-- <input type="text" class="form-control" maxlength="10" name="current_grade" value="{{$data['student']->current_grade}}"> --}}
                </div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Birth Day : </label>
                <div class=""><input type="text" class="form-control" id="birthday" maxlength="20" name="birthday" value="{{getDateFormat($data['student']->birthday)}}"></div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Address : </label>
                <div class="">
                    <textarea class="form-control" maxlength="255" name="address">{{$data['student']->address}}</textarea>
                    {{-- <input type="text" class="form-control" maxlength="255" name="address" value="{{$data['student']->address}}"> --}}
                </div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">City : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="city" value="{{$data['student']->city}}"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Zip : </label>
                <div class=""><input type="text" class="form-control" maxlength="20" name="zip" value="{{$data['student']->zip}}"></div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Race : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="race" value="{{$data['student']->race}}"></div>
            </div>
        </div>
    </form>
    <div class="" align="right">
        <button class="btn btn-success s_save">Save <div class="spnr spinner-border spinner-border-sm d-none"></button>
    </div>

    <div class="row">
        @foreach($termIds as $termid)
            <div class="col-12 col-lg-12 mb-20">
                <div class="card shadow h-100 mb-0">
                    <div class="card-header">{{(1990+$termid) . "-".(1991+$termid)}}</div>
                        <div class="card-body">
                            @if(isset($homeroomData[$termid]))
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12 text-info" style="font-size:14px; font-weight: bold;">Home Room Teacher</label>
                                    <div class="col-12 col-md-12">
                                        <strong>Email: </strong>{{$homeroomData[$termid]->email_addr}}              
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <strong>Name: </strong>{{$homeroomData[$termid]->first_name}} {{$homeroomData[$termid]->last_name}}              
                                    </div>
                                </div>
                            @endif
                            @if(isset($engTeacherData[$termid]))
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12 text-info" style="font-size:14px; font-weight: bold;">English Teacher</label>
                                    <div class="col-12 col-md-12">
                                        <strong>Email: </strong>{{$engTeacherData[$termid]->email_addr}}              
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <strong>Name: </strong>{{$engTeacherData[$termid]->first_name}} {{$engTeacherData[$termid]->last_name}}              
                                    </div>
                                </div>
                            @endif
                            @if(isset($mathTeacherData[$termid]))
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12 text-info" style="font-size:14px; font-weight: bold;">Math Teacher Teacher</label>
                                    <div class="col-12 col-md-12">
                                        <strong>Email: </strong>{{$mathTeacherData[$termid]->email_addr}}              
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <strong>Name: </strong>{{$mathTeacherData[$termid]->first_name}} {{$mathTeacherData[$termid]->last_name}}              
                                    </div>
                                </div>
                            @endif
                        </div>
                </div>
            </div>
        @endforeach
    </div>

@else
    <div class="" align="center">Data not found..</div>
@endif