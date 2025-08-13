@extends('layouts.admin.app')

@section('title')Student Profile Eligibility @stop

@section('styles')
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Student Profile Eligibility [{{ $data['program']->name }}]</div>
        <div class="">
            <a href="{{url($module_url)}}/create" class="btn btn-sm btn-success" title="">Add</a>
            <a href="{{url('admin/SetEligibility/edit/'.$data['program_id'])}}" class="btn btn-sm btn-secondary" title="">Back</a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        @include("layouts.admin.common.alerts")
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="tbl_data">
                <thead>
                    <tr>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Grade Level</th>
                        {{-- <th class="align-middle text-center w-120">Status</th> --}}
                        <th class="align-middle text-center w-120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['sp_eligibilities'] as $value)
                        <tr>
                            <td class="">{{$value->name}}</td>
                            <td class="">{{$value->grade}}</td>
                            <td class="text-center">
                                <a href="{{url($module_url)}}/edit/{{$value->id}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a>
                                <a href="javascript:void(0)" onclick="deletefunction({{$value->id}})" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">No data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $("#tbl_data").DataTable({
                'order': [],
                'columnDefs': [{
                    'targets': [1, 2],
                    'orderable': false
                }]
            });
        });
        //delete confermation
        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to delete this Student Profile Eligibility?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url($module_url)}}/delete/'+id;
            });
        };
    </script>
@stop