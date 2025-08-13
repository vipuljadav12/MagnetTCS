@php 
    /*if(isset($eligibilityContent))
    {
        $allow_spreadsheet = json_decode($eligibilityContent->content)->allow_spreadsheet ?? null;
        $content = json_decode($eligibilityContent->content)->eligibility_type;
    }*/
@endphp
<div class="form-group template-option-1">
    <label class="control-label">Name of Student Profile Eligibility : </label>
    <div class="">
    	<input type="text" class="form-control" value="{{$eligibility->name ?? old('name')}}" name="name">
    </div>
</div>

