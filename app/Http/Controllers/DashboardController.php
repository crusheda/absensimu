<?php

namespace App\Http\Controllers;

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
use DB,Auth,Validator,Redirect,Response,File,Storage;

class DashboardController extends Controller
{
    function index()
    {
        $agent = new Agent();
        $now = Carbon::now();
        $month = $now->isoFormat('MM');
        $year = $now->isoFormat('YYYY');
        $hadir = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',1)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        ->whereNotNull('tgl_out')
                        ->whereNull('deleted_at')
                        ->count();
        $terlambat = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',1)
                        ->where('terlambat',1)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        ->whereNotNull('tgl_out')
                        ->whereNull('deleted_at')
                        ->count();
        $ijin = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',3)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        ->whereNull('deleted_at')
                        ->count();

        $data = [
            'agent' => $agent,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'ijin' => $ijin,
        ];

        return view('pages.dashboard.index')->with('list',$data);
    }
}
