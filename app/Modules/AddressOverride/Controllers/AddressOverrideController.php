<?php

namespace App\Modules\AddressOverride\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\Validator;
use App\Modules\AddressOverride\Models\AddressOverride;
use App\Modules\School\Models\School;

class AddressOverrideController extends Controller
{
    protected $module_url = 'admin/AddressOverride';
    public function index()
    {
        return view('AddressOverride::index')->with('module_url', $this->module_url);
    }

    public function data(Request $request)
    {
        $id = $request->id;
        $data['student'] = Student::where('stateID', $id)->first();
        $data['schools'] = School::where('district_id', session('district_id'))
            ->where('status', 'Y')
            ->get();
        $key_data = [
            'state_id' => $id,
            'district_id' => session('district_id')
        ];
        $data['address_overwrite'] = AddressOverride::where($key_data)->first();
        return view('AddressOverride::data', compact('data'))->with('module_url', $this->module_url);
    }

    public function getListing()
    {
        $data['address_overwrite'] = AddressOverride::get();
        return view('AddressOverride::listing', compact('data'))->with('module_url', $this->module_url);
    }

    public function updateData(Request $request)
    {
        $rules = [
            'zoned_school' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return 'false';
        }
        $user_id = \Auth::user()->id;
        $key_data = [
            'state_id' => $request->id,
            'district_id' => session('district_id'),
        ]; 
        $data = [
            'user_id' => $user_id,
            'zoned_school' => $request->zoned_school,
        ];
        $id = $request->id;
        $update = AddressOverride::updateOrCreate($key_data, $data);
        return $update ? 'true' : 'false';
    }        
}
