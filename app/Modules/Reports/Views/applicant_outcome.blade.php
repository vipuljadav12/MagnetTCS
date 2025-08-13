@extends('layouts.admin.app')
@section('title')
	Applicant Outcome
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Reports</div>
        </div>
    </div>
    <div class="card shadow">
    <div class="card-body">
        <form class="">
            <div class="form-group">
                <label for="">Enrollment Year : </label>
                <div class="">
                    <select class="form-control custom-select" id="enrollment">
                        <option value="">Select Enrollment Year</option>
                        @foreach($enrollment as $key=>$value)
                            <option value="{{$value->id}}" @if($enrollment_id == $value->id) selected @endif>{{$value->school_year}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="">Report : </label>
                <div class="">
                    <select class="form-control custom-select" id="reporttype">
                        <option value="">Select Report</option>
                        <option value="offerstatus">Offer Status Report</option>
                        <option value="duplicatestudent">Student Duplicate Report</option>
                        <option value="homezoneschool">Home Zone Report - TCS Magnet</option>
                        <option value="applicant_outcome" selected>Applicant Outcome</option>                           
                        
                    </select>
                </div>
            </div>
            <div class=""><a href="javascript:void(0);" onclick="showReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
        </form>
    </div>
</div>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="datatable">
                    <thead>
                        <tr>
                            <th class="align-middle">Program</th>
                            <th class="align-middle text-center">First Choice Total Applicants</th>
                            <th class="align-middle text-center">Offered First Choice</th>
                            <th class="align-middle text-center">Offered Second Choice</th>
                            <th class="align-middle text-center">Offered & Waitlisted</th>
                            <th class="align-middle text-center">Waitlisted</th>
                            <th class="align-middle text-center">Denied</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($data['programs'])
                            @foreach($data['programs'] as $program)
                                <tr>
                                    <td class="">{{$program->name}}</td>
                                    <td class="text-center">{{($data['first_choice_total_applicants'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['first_choice_offered'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['second_choice_offered'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['offered_and_waitlisted'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['waitlisted'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['denied'][$program->id] ?? 0)}}</td>
                                </tr>
                            @endforeach
                        @endisset
                    
                   
                        <tr>
                            <th class="align-middle"></th>
                            <th class="align-middle text-center">Second Choice Total Applicants</th>
                            <th class="align-middle text-center">Offered Second Choice</th>
                            <th class="align-middle text-center">Offered First Choice</th>
                           <th class="align-middle text-center">Denied</th>
                           <th class="align-middle text-center"></th>
                           <th class="align-middle text-center"></th>
                        </tr>
                    
                   
                        @isset($data['programs'])
                            @foreach($data['programs'] as $program)
                                <tr>
                                    <td class="">{{$program->name}}</td>
                                    <td class="text-center">{{($data['second_choice_total_applicants_1'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['second_choice_offered_1'][$program->id] ?? 0)}}</td>
                                    <td class="text-center">{{($data['first_choice_offered_1'][$program->id] ?? 0)}}</td>
                                    
                                    <td class="text-center">{{($data['denied_1'][$program->id] ?? 0)}}</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
	<script type="text/javascript">
        var dtbl_submission_list = $("#datatable").DataTable({
             dom: 'Bfrtip',
             ordering: false,
             searching: false,
             buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Applicant Outcome',
                    text:'Export to Excel'
                }
            ]
        });
        function showReport()
        {
            if($("#enrollment").val() == "")
            {
                alert("Please select enrollment year");
            }
            else if($("#reporttype").val() == "")
            {
                alert("Please select report type");
            }
            else
            {
                var link = "{{url('/')}}/admin/Reports/missing/{{Session::get("enrollment_id")}}/"+$("#reporttype").val();
                document.location.href = link;
            }
        }
	</script>

@endsection