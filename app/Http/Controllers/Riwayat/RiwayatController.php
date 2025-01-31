<?php

namespace App\Http\Controllers\Riwayat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use App\Models\absensi;
use App\Models\jadwal;
use App\Models\jadwal_detail;
use App\Models\ref_shift;
use App\Models\ref_users;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Auth,Validator,Redirect,Response,File,Storage;

class RiwayatController extends Controller
{
    public function index()
    {
        $agent = new Agent();

        $data = [
            'agent' => $agent,
        ];

        return view('pages.riwayat.index')->with('list',$data);
    }

    function initRiwayat($user)
    {
        $show = absensi::where('pegawai_id',$user)
                        ->where('tgl_in','!=',null)
                        ->orderBy("tgl_in","DESC")
                        ->get();

        $data = [
            'show' => $show,
        ];

        // print_r($data);
        // die();

        return response()->json($data, 200);
    }

    function showRiwayat($user,$id)
    {
        $show = absensi::where('pegawai_id',$user)
                        ->where('id',$id)
                        ->first();
        $jadwal = jadwal_detail::join('kepegawaian_jadwal','kepegawaian_jadwal_detail.id_jadwal','=','kepegawaian_jadwal.id')
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                        ->select('kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.staf as bawahan')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();
        if ($show) {
            $shift = ref_shift::where('pegawai_id',$jadwal->atasan)
                            ->where('singkat',$show->kd_shift)
                            ->first();
        } else {
            $shift = '';
        }

        $data = [
            'show' => $show,
            'shift' => $shift,
        ];

        return response()->json($data, 200);
    }

    // $data = perbaikan_ipsrs::find($id);
    // return Storage::download($data->filename_pengaduan, $data->title_pengaduan);
}
