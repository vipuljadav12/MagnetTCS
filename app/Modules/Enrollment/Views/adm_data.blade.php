<div class="">
    <div class="card shadow">
        <div class="card-header">Home Zone Schools Enrollment Data for Current Enrollment</div>
        <div class="card-body">
            <div class="table-responsive">
                @foreach($school_data as $school)
                    <div class="card shadow">
                        <div class="card-header">
                            <div class="">{{$school['name']}}<input name="school[]" type="hidden" value="{{$school['id']}}" class="form-control">
                            </div>
                        </div>
                        <div class="card-body"> 
                            <div class="row margin"> 
                                @foreach($school['grade_data'] as $gk=>$gval)
                                    <div class="col-2">
                                        <div>Rising Grade {{$gval['grade']}}<input name="grade[{{$school['id']}}][]" type="hidden" class="form-control" value="{{$gval['grade']}}"></div>
                                        <div><input name="total[{{$school['id']}}][{{$gval['grade']}}]" type="text" class="form-control" value="{{$gval['total']}}"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>