<?php

namespace App\Http\Controllers;

use GeoNames\Client as GeoNamesClient;
use Illuminate\Http\Request;

class GeoNameController extends Controller
{
    public function index()
    {

        return view('geonames.index');
    }



    public function fetch(Request $request)
    {
        $g = new GeoNamesClient('zair.nur');

        $country = [];
        for ($i = 0; $i < 5000; $i += 1000) {
            $searchArr = $g->search([
                'country' => $request['country-code'],
                'lang'    => 'en', // display info in Russian
                'type' => 'json',
//            'featureClass' => 'P',
                'featureCode' => 'PPL',

                'startRow' => $i,
                'maxRows' => 1000,

            ]);

            $country = array_merge($country, $searchArr);
        }

//        dd($country);


        $geoArr = [];
        $filename = '';
        foreach ($country as $city) {
            if (!isset($city->adminCode1)) continue;

            $geoArr[$city->countryCode] = [
                'code' => strtolower($city->countryCode),
                'type' => 'COUNTRY',
                'name' => $city->countryName
            ];

            $filename = $city->countryName;
        }

        foreach ($country as $city) {
            if (!isset($city->adminCode1)) continue;

            $geoArr[$city->countryCode]['region'][$city->adminCode1] = [
                'code' => strtolower($city->countryCode) . $city->adminCode1,
                'type' => 'REGION',
                'name' => $city->adminName1
            ];
        }


        foreach ($country as $city) {
            if (!isset($city->adminCode1)) continue;
            $geoArr[$city->countryCode]['region'][$city->adminCode1]['city'][] = [
                'code' => strtolower($city->countryCode) . $city->geonameId,
                'type' => 'CITY',
                'name' => $city->name,
            ];
        }

        $result = "CODE;PARENT_CODE;TYPE_CODE;NAME.EN.NAME;EXT.YAMARKET.0;EXT.ZIP.0\r\n";

        foreach ($geoArr as $country) {
            $result .= "{$country['code']};;{$country['type']};{$country['name']};;\r\n";

            foreach ($country['region'] as $region) {
                $result .= "{$region['code']};{$country['code']};{$region['type']};{$region['name']};;\r\n";

                foreach ($region['city'] as $city) {
                    $result .= "{$city['code']};{$region['code']};{$city['type']};{$city['name']};;\r\n";


                }

            }

        }


        file_put_contents($filename . '.txt', $result);



//        dump($result);
        dd($geoArr);

        return view('geonames.fetch');
    }
}
