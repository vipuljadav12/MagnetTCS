@extends('layouts.admin.app')
@section('title')Process Selection | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
<form action="{{ url('admin/Process/Selection/store')}}" method="post" name="process_selection" id="process_selection">
    {{csrf_field()}}
        <input type="hidden" name="application_id" value="{{$application_id}}" id="application_id">

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Selection <span class="text-danger">{{getApplicationName($application_id)}}</span></div>
            <div class="text-right">
                <a href="{{url('/admin/Process/Selection/calculate_profile/'.$application_id)}}" class="btn btn-secondary">Calculate Profile Score</a>&nbsp;
            @if($display_outcome > 0 && Config::get("variables.rollback_process_selection") == 1) <a href="javascript:void(0)" class="btn btn-secondary" onclick="rollBackStatus();">Roll Back Status</a>&nbsp;@endif
                <a href="{{url('/admin/Process/Selection')}}" class="btn btn-primary">Change Application</a></div>
        </div>
    </div>

    <div class="card shadow">
        
        <div class="card-body">
    @include("layouts.admin.common.alerts")
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="preview02-tab" data-toggle="tab" href="#preview02" role="tab" aria-controls="preview02" aria-selected="true">Settings</a></li>
        </ul>
        <div class="tab-content bordered" id="myTabContent">
            <div class="tab-pane fade show active" id="preview02" role="tabpanel" aria-labelledby="preview02-tab">
                @if($display_outcome == 0)
                    @include('ProcessSelection::Template.acceptance_window')
                @else
                    @include('ProcessSelection::Template.acceptance_window1')
                @endif
            </div>
        </div>
        
    </div>
</div>
    </form>
@endsection
@section('scripts')
    <div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 15 minutes to finish. </div></div>

@include("ProcessSelection::common_js")
<script type="text/javascript">

    function rollBackStatus()
    {
        $("#wrapperloading").show();
        $.ajax({
            url:'{{url('/admin/Process/Selection/Revert/list')}}',
            type:"post",
            data: {"_token": "{{csrf_token()}}", "application_id": $("#application_id").val()},
            success:function(response){
                alert("All Statuses Reverted.");
                document.location.href = "{{url('/admin/Process/Selection')}}";
                $("#wrapperloading").hide();

            }
        })
    }


</script>
@endsection