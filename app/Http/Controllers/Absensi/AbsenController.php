<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AbsenController extends Controller
{
    public function index()
    {
        $agent = new Agent();

        $data = [
            'agent' => $agent,
        ];

        return view('pages.absensi.index')->with('list',$data);
    }

    function getDistance(Request $request)
    {
        $lokasi = explode(",",$request->lokasi);
        $lat2 = $lokasi[0];
        $lon2 = $lokasi[1];

        //  Kantor Kelurahan Bakung : -7.733879364254091, 110.55628417878309
        $callDistance = $this->distance("-7.63783189686527", "110.86775211807864", $lat2, $lon2);
        $distance = round($callDistance["meters"]);

        return response()->json($distance, 200);
    }

    // FUNCTION HITUNG
    function distance($lat1, $lon1, $lat2, $lon2) // Menghitung Jarak
    {
        // lat1 = latitude kantor
        // lon1 = longitude kantor
        // lat2 = latitude user
        // lon2 = longitude user

        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
