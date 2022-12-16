<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BannerAd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    /**
     * get list of ads
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ads = BannerAd::all();
        $now = Carbon::now();
        $data = [];
        foreach ($ads as $ad) {
            if ($ad->end_date == null) {
                array_push($data, $ad);
            } else {
                $date = Carbon::createFromFormat('Y-m-d', $ad->end_date);
                if ($now->gt($date)) {
                    $ad->delete();
                } else {
                    array_push($data, $ad);
                }
            }
        }

        return response()->json(['status' => 1, 'data' => $data]);
    }

    /**
     * create new ads
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpg,jpeg,gif,png|max:2048',
            'url' => 'nullable',
            'room_id' => 'nullable',
            'type' => 'required',
            'end_date' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
        }
        $data = ['type' => $request['type']];
        if ($request['type'] == 'URL' && $request['url'] == null) {
            return response()->json(['status' => 0, 'message' => 'url field is required'], 500);
        }
        if ($request['type'] == 'ROOM' && $request['room_id'] == null) {
            return response()->json(['status' => 0, 'message' => 'room_id field is required'], 500);
        }
        if ($request['type'] == 'URL') {
            $data['url'] = $request['url'];
        }
        if ($request['type'] == 'ROOM') {
            $data['room_id'] = $request['room_id'];
        }
        if ($request['end_date'] != null) {
            $data['end_date'] = $request['end_date'];
        }
        $ad = BannerAd::create($data);
        if ($ad) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('ads', $fileName, 'public');
                $ad->image = $path;
                $ad->save();
            }
            publish_ads($ad->toArray());

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $ad]);
        }

        return response()->json(['status' => 0, 'message' => 'server error']);
    }

    /**
     * delete ad
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $ad = BannerAd::find($id);
        if ($ad != null) {
            if ($ad->image != null) {
                Storage::disk('public')->delete($ad->image);
            }
            $ad->delete();
        }

        return success_response();
    }
}
