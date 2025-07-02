<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JadwalDinasController extends Controller
{
    function index($user, $bulan, $tahun)
    {
        return response()->json([
            "jadwal" => [
                "01" => "H",
                "02" => "H",
                "03" => "H",
                "04" => "H",
                "05" => "H",
                "06" => "H",
                "07" => "H",
                "08" => "H",
                "09" => "H",
                "10" => "H",
                "11" => "L",
                "12" => "CM",
                "13" => "CU",
                "14" => "C",
                "15" => "CD",
                "16" => "L",
                "17" => "H",
                "18" => "A",
                "19" => "H",
                "20" => "H",
                "21" => "H",
                "22" => "L",
                "23" => "A",
                "24" => "H",
                "25" => "H",
                "26" => "L",
                "27" => "H",
                "28" => "H",
                "29" => "H",
                "30" => "H",
                "31" => "H",
            ]
        ]);
    }
}
