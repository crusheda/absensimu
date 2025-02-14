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
        $tgl = $now->isoFormat('D');
        $hit = "tgl".$tgl;
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
        $getJadwal = jadwal_detail::join('kepegawaian_jadwal','kepegawaian_jadwal_detail.id_jadwal','=','kepegawaian_jadwal.id')
                        ->select('kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan')
                        ->where('kepegawaian_jadwal_detail.pegawai_id',Auth::user()->id)
                        ->where('kepegawaian_jadwal.progress',3)
                        ->where('kepegawaian_jadwal.bulan',$month)
                        ->where('kepegawaian_jadwal.tahun',$year)
                        ->orderBy('kepegawaian_jadwal_detail.updated_at','DESC')
                        ->first();
        if ($getJadwal) {
            $xshift = ref_shift::where('pegawai_id',$getJadwal->atasan)->where('singkat',$getJadwal->$hit)->first();
            $nama_shift = $xshift->shift;
            if ($xshift->berangkat == '00:00:00' && $xshift->pulang == '00:00:00') {
                $shift = null;
            } else {
                $shift = Carbon::parse($xshift->berangkat)->isoFormat('HH.mm').' - '.Carbon::parse($xshift->pulang)->isoFormat('HH.mm').' WIB';
            }
        } else {
            $nama_shift = null;
            $shift = null;
        }

        $data = [
            'agent' => $agent,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'ijin' => $ijin,
            'nama_shift' => $nama_shift,
            'shift' => $shift,
        ];

        return view('pages.dashboard.index')->with('list',$data);
    }
}
