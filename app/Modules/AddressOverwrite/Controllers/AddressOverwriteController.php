<?php

namespace App\Modules\AddressOverwrite\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\Validator;
use App\Modules\AddressOverwrite\Models\AddressOverwrite;
use App\Modules\School\Models\School;

class AddressOverwriteController extends Controller
{
    protected $module_url = 'admin/AddressOverwrite';
    public function index()
    {
        return view('AddressOverwrite::index')->with('module_url', $this->module_url);
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
        $data['address_overwrite'] = AddressOverwrite::where($key_data)->first();
        return view('AddressOverwrite::data', compact('data'))->with('module_url', $this->module_url);
    }

    public function getListing()
    {
        $data['address_overwrite'] = AddressOverwrite::get();
        return view('AddressOverwrite::listing', compact('data'))->with('module_url', $this->module_url);
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
        $update = AddressOverwrite::updateOrCreate($key_data, $data);
        return $update ? 'true' : 'false';
    }        
}
