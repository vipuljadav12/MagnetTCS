@php
    $eligibility_name = $eligibilityTemplate->content_html;
    $stng_name = ucwords(str_replace('_', ' ', $eligibility_name));
    $eligibility_edit_url = "admin/SetEligibility/edit/".$req['program_id']."/".($req['application_id'] ?? '');
@endphp

@extends('layouts.admin.app')

@section('title')
    {{ $stng_name }} Setting
@stop

@section('styles')
    <style type="text/css">
        .error {
            color: red;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">{{$stng_name}} Setting</div>
        <div class=""><a href="{{url($eligibility_edit_url)}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
    </div>
</div>
<div class="form-list">
    @include("layouts.admin.common.alerts")
</div>
{{-- <div class="card shadow"> --}}
    {{-- <div class="card-body"> --}}
        @if(isset($eligibility->template_id) && $eligibility->template_id != 0)
            @include("SetEligibility::view.".$eligibilityTemplate->content_html."_configuration")
        @else
            @include("SetEligibility::view.template2")
        @endif
    {{-- </div> --}}
{{-- </div> --}}
<div class="box content-header-floating" id="listFoot">
    <div class="row">
        <div class="col-lg-12 text-right hidden-xs float-right">
            <button type="submit" form="extraValueForm1" class="btn btn-warning btn-xs"  form="priority-add" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
        </div>
    </div>
</div>
@stop

@section('scripts')
    @yield('editConfScripts')
@stop

