<?php

namespace App\Http\Controllers\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\profil_rs;
use App\Models\users;
use App\Models\absensi;
use App\Models\jadwal;
use App\Models\jadwal_detail;
use App\Models\ref_shift;
use App\Models\ref_users;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Auth,Validator,Redirect,Response,File,Storage;

class RekapController extends Controller
{
    public function index()
    {
        $agent = new Agent();
        $profil_rs = profil_rs::first();

        $data = [
            'agent' => $agent,
            'profil_rs' => $profil_rs,
        ];

        return view('pages.rekap.index')->with('list',$data);
    }

    // public function show($id)
    // {
    //     $absensi = absensi::first();

    //     $data = [
    //         'absensi' => $absensi,
    //     ];

    //     return view('pages.rekap.detail')->with('list',$data);
    // }

    // API ARERA -----------------------------------------------------------------------------------------
    function list($user)
    {
        $show = absensi::where('pegawai_id',$user)->limit(10)->orderBy('tgl_in','DESC')->get();

        $data = [
            'show' => $show,
        ];

        return response()->json($data, 200);
    }

    function detail($user,$id)
    {
        $show = absensi::where('id',$id)->where('pegawai_id',$user)->first();

        $data = [
            'show' => $show,
        ];

        return response()->json($data, 200);
    }
}
