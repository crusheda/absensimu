<?php

namespace App\Http\Controllers\Jadwal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\profil_rs;
use App\Models\users;
use App\Models\absensi;
use App\Models\jadwal;
use App\Models\jadwal_detail;
use App\Models\ref_shift;
use App\Models\ref_users;
use App\Models\ref_jabatan;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use DB,Auth,Validator,Redirect,Response,File,Storage;

class JadwalController extends Controller
{
    function index()
    {
        $agent = new Agent();
        $profil_rs = profil_rs::first();
        $user_id = Auth::user()->id;
        // print_r(Auth::user()->id);
        // die();

        $data = [
            'agent' => $agent,
            'profil_rs' => $profil_rs,
            'user_id' => $user_id,
        ];

        return view('pages.jadwal.index')->with('list',$data);
    }

    function show($user,$bln,$thn)
    {
        // $staf = ref_users::whereJsonContains('staf', $user)->value('pegawai_id');
        // $pegawaiIndukIds = $ref->pegawai_id;
        $show = jadwal::join('users', 'users.id', '=', 'kepegawaian_jadwal.pegawai_id')
            ->select('kepegawaian_jadwal.*', 'users.nama as nama_pegawai')
            ->where('kepegawaian_jadwal.bulan', $bln)
            ->where('kepegawaian_jadwal.tahun', $thn)
            ->whereJsonContains('kepegawaian_jadwal.staf', $user)
            ->whereNull('kepegawaian_jadwal.deleted_at')
            ->orderByDesc('kepegawaian_jadwal.updated_at')
            ->first();

        if ($show) {
            $show->unit = ref_users::whereJsonContains('staf', $user)->value('unit');
        }

        if ($show) {
            $detail = jadwal_detail::leftJoin('referensi_jadwal_users_jabatan','referensi_jadwal_users_jabatan.id_staf','=','kepegawaian_jadwal_detail.pegawai_id')
                    ->select('kepegawaian_jadwal_detail.*','referensi_jadwal_users_jabatan.urutan','referensi_jadwal_users_jabatan.jabatan','referensi_jadwal_users_jabatan.color')
                    ->where('kepegawaian_jadwal_detail.id_jadwal',$show->id)
                    ->where('referensi_jadwal_users_jabatan.deleted_at',null)
                    ->orderBy('referensi_jadwal_users_jabatan.urutan','ASC')
                    ->get();
            $shift  = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                        $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                            ->whereNull('referensi_jadwal_users.deleted_at');
                    })
                    ->select('referensi_jadwal_shift.*')
                    ->whereJsonContains('referensi_jadwal_users.staf', $user)
                    ->where('referensi_jadwal_shift.deleted_at',null)
                    ->get();
            $jabatan = ref_jabatan::leftJoin('referensi_jadwal_users', function($join) {
                        $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_users_jabatan.pegawai_id')
                            ->whereNull('referensi_jadwal_users.deleted_at');
                    })
                    ->select('referensi_jadwal_users_jabatan.*')
                    ->whereJsonContains('referensi_jadwal_users.staf', $user)
                    ->whereNull('referensi_jadwal_users_jabatan.deleted_at')
                    ->get();
                    // print_r($shift);
                    // die();
            $totalDay = Carbon::create($thn, $bln)->format('t');
            for($i = 1; $i <= $totalDay; $i++)
            {
                $dataArray[] = Carbon::create($thn, $bln, $i)->dayName;
            }
            $getBulan = ['','Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            foreach ($getBulan as $key => $value) {
                if ($key == $show->bulan) {
                    $bulan = $value;
                }
            }

            $data = [
                'code' => 200,
                'show' => $show,
                'bulan' => $bulan,
                'detail' => $detail,
                'shift' => $shift,
                'jabatan' => $jabatan,
                'totalDay' => $totalDay,
                'dataArray' => $dataArray,
                'message' => "Jadwal Berhasil Ditemukan",
            ];
        } else {
            $data = [
                'code' => 400,
                'message' => "Jadwal Tidak Ditemukan",
            ];
        }

        return response()->json($data, 200);
    }
}
