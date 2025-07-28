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
                "01" => "P",
                "02" => "P",
                "03" => "P",
                "04" => "P",
                "05" => "P",
                "06" => "P",
                "07" => "P",
                "08" => "P",
                "09" => "P",
                "10" => "P",
                "11" => "L",
                "12" => "CM",
                "13" => "CU",
                "14" => "C",
                "15" => "CD",
                "16" => "L",
                "17" => "P",
                "18" => "S",
                "19" => "P",
                "20" => "P",
                "21" => "P",
                "22" => "L",
                "23" => "S",
                "24" => "P",
                "25" => "P",
                "26" => "L",
                "27" => "P",
                "28" => "P",
                "29" => "P",
                "30" => "P",
                "31" => "P",
            ],
            "ref_shift" => [
                "P" => "Pagi",
                "S" => "Siang",
                "L" => "Libur",
                "C" => "Cuti Tahunan",
                "CM" => "Cuti Melahirkan",
                "CD" => "Cuti Diluar Tanggungan",
                "CU" => "Cuti Umroh",
                "CH" => "Cuti Haji",
            ],
            "icon" => [
                "P" => "check_mark_circled_solid",
                "S" => "check_mark_circled_solid",
                "L" => "check_mark_circled",
                "C" => "minus_circle_fill",
                "CM" => "minus_circle_fill",
                "CD" => "minus_circle_fill",
                "CU" => "minus_circle_fill",
                "CH" => "minus_circle_fill",
            ],
            "color" => [
                "P" => "activeGreen",
                "S" => "activeGreen",
                "L" => "systemGrey2",
                "C" => "systemRed",
                "CM" => "systemRed",
                "CD" => "systemRed",
                "CU" => "systemRed",
                "CH" => "systemRed",
            ],
            "flow" => [
                "admin" => "CONTOH",
                "tgl_dibuat" => "21 Juli 2025 21.09 WIB",
                "verif" => "CONTOH 2",
                "tgl_verif" => "25 Juli 2025 06.19 WIB",
                "valid" => "CONTOH 3",
                "tgl_valid" => "28 Juli 2025 14.01 WIB",
                "staf" => ["C1","C2","C3"],
            ],
        ]);
    }
}
