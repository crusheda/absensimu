<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\profil_rs;
use App\Models\users;
use App\Models\users_foto;
use App\Models\users_status;
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
        $foto_profil = users_foto::where('user_id',Auth::user()->id)->whereNull('deleted_at')->first();
        $statuspgw = users_status::join('referensi','referensi.id','=','users_status.ref_id')
                                ->select('referensi.deskripsi AS nama_status')
                                ->where('users_status.pegawai_id',Auth::user()->id)
                                ->where('users_status.status',1)
                                ->whereNull('users_status.deleted_at')
                                ->where('referensi.status',1)
                                ->whereNull('referensi.deleted_at')
                                ->first();
        $hadir = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',1)
                        ->where('terlambat',0)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        ->whereNotNull('tgl_out')
                        ->whereNull('deleted_at')
                        ->count();
        $absenOne = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',1)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        ->whereNull('tgl_out')
                        ->whereNull('deleted_at')
                        ->count();
        $terlambat = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',1)
                        ->where('terlambat',1)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        // ->whereNotNull('tgl_out')
                        ->whereNull('deleted_at')
                        ->count();
        $ijin = DB::table('kepegawaian_absensi')
                        ->where('pegawai_id',Auth::user()->id)
                        ->where('jenis',3)
                        ->whereMonth('tgl_in',$month)
                        ->whereYear('tgl_in',$year)
                        ->whereNull('deleted_at')
                        ->count();
        $getJadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->select('kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.progress')
                        ->where('kepegawaian_jadwal_detail.pegawai_id',Auth::user()->id)
                        ->whereIn('kepegawaian_jadwal.progress',[2,3])
                        ->where('kepegawaian_jadwal.bulan',$month)
                        ->where('kepegawaian_jadwal.tahun',$year)
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.updated_at','DESC')
                        ->first();

        $shift = null;
        $nama_shift = null;
        // print_r($getJadwal->atasan);
        // die();
        if ($getJadwal) {
            if ($getJadwal->progress == 2) {
                $nama_shift = null;
                $shift = 'Dalam Proses Validasi';
            } else {
                $xshift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                                $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                                    ->whereNull('referensi_jadwal_users.deleted_at');
                            })
                            ->whereRaw("
                                FIND_IN_SET(?,
                                    REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                                )
                            ", [$getJadwal->atasan])
                            ->where('referensi_jadwal_shift.singkat', $getJadwal->$hit)
                            ->whereNull('referensi_jadwal_shift.deleted_at')
                            ->first();

                if ($xshift) {
                    $nama_shift = $xshift->shift;
                    if ($xshift->berangkat == '00:00:00' && $xshift->pulang == '00:00:00') {
                        $shift = null;
                    } else {
                        $shift = Carbon::parse($xshift->berangkat)->isoFormat('HH.mm').' - '.Carbon::parse($xshift->pulang)->isoFormat('HH.mm').' WIB';
                    }
                } else {
                    $shift = null;
                }

                if ($getJadwal->$hit == 'L') {
                    $shift = 'Libur/Tidak Masuk';
                } else {
                    if ($getJadwal->$hit == 'C') {
                        $shift = 'Cuti/Tidak Masuk';
                    }
                }
            }
        } else {
            $nama_shift = null;
            $shift = null;
        }

        $data = [
            'agent' => $agent,
            'foto_profil' => $foto_profil,
            'statuspgw' => $statuspgw,
            'hadir' => $hadir,
            'absenOne' => $absenOne,
            'terlambat' => $terlambat,
            'ijin' => $ijin,
            'nama_shift' => $nama_shift,
            'shift' => $shift,
            'tgl' => $tgl,
            'bulan' => $now->isoFormat('MMM'),
        ];

        return view('pages.dashboard.index')->with('list',$data);
    }

    // API
    function show($user)
    {
        $now = Carbon::now();
        $bln = $now->isoFormat('MM');
        $thn = $now->isoFormat('YYYY');
        $nama_bulan = $now->isoFormat('MMMM');
        $staf = ref_users::whereJsonContains('staf', $user)->select('pegawai_id')->first();

        $show = jadwal::join('users as pegawai', 'pegawai.id', '=', 'kepegawaian_jadwal.pegawai_id') // Join untuk pegawai_id
                        ->leftJoin('users_foto as foto_user', 'foto_user.user_id', '=', 'pegawai.id') // Join untuk foto atasan
                        ->join('users as verif_user', 'verif_user.id', '=', 'kepegawaian_jadwal.verif') // Join untuk verif
                        ->join('users as valid_user', 'valid_user.id', '=', 'kepegawaian_jadwal.valid') // Join untuk valid
                        ->join('referensi_jadwal_users', 'referensi_jadwal_users.pegawai_id', '=', 'kepegawaian_jadwal.pegawai_id')
                        ->select(
                            'kepegawaian_jadwal.*',
                            'foto_user.filename as foto_pegawai',
                            'referensi_jadwal_users.unit',
                            'pegawai.nama as nama_pegawai', // Nama dari pegawai_id
                            'verif_user.nama as nama_verif', // Nama dari verif
                            'valid_user.nama as nama_valid' // Nama dari valid
                        )
                        ->whereNull('foto_user.deleted_at')
                        ->where('kepegawaian_jadwal.bulan', $bln)
                        ->where('kepegawaian_jadwal.tahun', $thn)
                        ->where('kepegawaian_jadwal.pegawai_id', $staf->pegawai_id)
                        ->whereNull('kepegawaian_jadwal.deleted_at')
                        ->orderBy('kepegawaian_jadwal.updated_at', 'DESC')
                        ->first();

        $data = [
            'bulan' => $nama_bulan,
            'tahun' => $thn,
            'show' => $show,
        ];

        return response()->json($data, 200);
    }
}
