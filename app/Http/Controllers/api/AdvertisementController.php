<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BannerAd;
use Carbon\Carbon;

class AdvertisementController extends Controller
{
    /**
     * get advertisements
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ads = BannerAd::all();
        $data = [];
        foreach ($ads as $ad) {
            if ($ad->end_date == null) {
                array_push($data, $ad);
            } else {
                $date1 = Carbon::createFromFormat('Y-m-d', $ad->end_date);
                $date2 = Carbon::now();
                $result = $date1->gt($date2);
                if ($result) {
                    array_push($data, $ad);
                }
            }
        }

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $data]);
    }
}
