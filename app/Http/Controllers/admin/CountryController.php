<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    /**
     * get countries list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::all();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $countries]);
    }

    /**
     * create new country
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|unique:countries,name_ar',
            'name_en' => 'required|unique:countries,name_en',
            'flag' => 'required|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $data = [
            'name_ar' => $request['name_ar'],
            'name_en' => $request['name_en'],
        ];
        $country = Country::create($data);
        if ($country) {
            if ($request->hasFile('flag')) {
                $file = $request->file('flag');
                $fileName = time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('countries', $fileName, 'public');
                $country->flag = $path;
                $country->save();
            }

            return success_response($country);
        }

        return server_error_response();
    }

    /**
     * update
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        if ($country == null) {
            return not_found_response('country not found');
        }
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required',
            'name_en' => 'required',
            'flag' => 'nullable|mimes:png,jpg,jpeg',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $data = [
            'name_ar' => $request['name_ar'],
            'name_en' => $request['name_en'],
        ];
        $country->update($data);
        if ($request->hasFile('flag')) {
            if ($country->flag != null) {
                Storage::disk('public')->delete($country->flag);
            }
            $file = $request->file('flag');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('countries', $fileName, 'public');
            $country->flag = $path;
            $country->save();
        }

        return success_response($country);
    }

    /**
     * get country details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::find($id);
        if ($country == null) {
            return not_found_response('country not found');
        }

        return success_response($country);
    }
}
