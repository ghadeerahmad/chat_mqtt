<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * get list of countries
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $check = Country::first();
        if ($check) {
            $countries = Country::all();

            return success_response($countries);
        }
        $response = Http::get('https://restcountries.com/v3.1/all');

        $response = json_decode($response, true);
        // return $response;
        $data = [];
        // foreach ($response as $cn) {
        //     $code = $cn['idd'] != null ? $cn['idd']['root'] . $cn['idd']['suffixes'][0] : '00';
        //     $data[] = [
        //         'name_ar' => $cn['translations']['ara']['common'],
        //         'name_en' => $cn['name']['common'],
        //         'flag' => $cn['flags']['png'],
        //         'code' => $code
        //     ];
        // }
        // Storage::put('countries_list.txt', json_encode($data), true);
        foreach ($response as $country) {
            array_push($data, [
                'name_ar' => $country['translations']['ara']['common'],
                'name_en' => $country['name']['common'],
                'flag' => $country['flags']['png'],
            ]);
        }
        Country::upsert($data, ['name']);
        $countries = Country::all();

        return success_response($countries);
    }
}
