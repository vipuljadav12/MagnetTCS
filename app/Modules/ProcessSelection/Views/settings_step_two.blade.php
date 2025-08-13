@extends('layouts.admin.app')
@section('title')Program Max Percent Swing | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('content')
    @include("layouts.admin.common.alerts")
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Program Max Percent Swing</div><div class=""><a href="{{url('admin/Process/Selection/settings')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a></div> 
        </div>
    </div>
    
    <div class="tab-pane fade show" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
        @include('ProcessSelection::Template.program_max')
    </div>

@endsection