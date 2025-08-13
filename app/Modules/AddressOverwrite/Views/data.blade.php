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
                <div class=""><input type="text" class="form-control" maxlength="100" disabled name="first_name" value="{{$data['student']->first_name}}"></div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Last Name : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" disabled name="last_name" value="{{$data['student']->last_name}}"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Current Grade : </label>
                <div class="">
                    <select class="form-control" name="current_grade" disabled>
                        <option value="">Select Grade</option>
                        @foreach($grades as $grade)
                            <option @if($data['student']->current_grade == $grade) selected @endif>{{$grade}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Birth Day : </label>
                <div class=""><input type="text" class="form-control" id="birthday" maxlength="20" disabled name="birthday" value="{{getDateFormat($data['student']->birthday)}}"></div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Address : </label>
                <div class="">
                    <textarea class="form-control" maxlength="255" disabled name="address">{{$data['student']->address}}</textarea>
                </div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">City : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" disabled name="city" value="{{$data['student']->city}}"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Zip : </label>
                <div class=""><input type="text" class="form-control" maxlength="20" disabled name="zip" value="{{$data['student']->zip}}"></div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Race : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" disabled name="race" value="{{$data['student']->race}}"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Zoned School : </label>
                <div class="">
                    @php
                        $zoned_school = $data['address_overwrite']->zoned_school ?? '';
                    @endphp
                    <select class="form-control" name="zoned_school" id="zoned_school">
                        <option value="">Select Zoned School</option>
                        @foreach($data['schools'] as $school)
                            <option value="{{$school->name}}" @if($zoned_school == $school->name) selected @endif>{{$school->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>
    <div class="" align="right">
        <button class="btn btn-success s_save">Save <div class="spnr spinner-border spinner-border-sm d-none"></button>
    </div>
@else
    <div class="" align="center">Data not found..</div>
@endif