<?php

namespace App\Http\Controllers\Android;

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

class ReminderController extends Controller
{
    // function reminderShift(Request $request)
    // {
    //     $pegawaiId = $request->query('id_user'); // ambil dari query string

    //     $now = Carbon::now();
    //     $datenow = $now->format('Y-m-d');
    //     $tahun = $now->format('Y'); // 2025
    //     $bulan = $now->format('m'); // 06
    //     $tgl   = $now->format('d'); // 28
    //     $hit = "tgl".$tgl;

    //     // Lakukan query shift pegawai berdasarkan tanggal hari ini
    //     $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
    //                         $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
    //                             ->whereNull('kepegawaian_jadwal.deleted_at');
    //                     })
    //                     ->select('kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.staf as bawahan','kepegawaian_jadwal.progress')
    //                     // ->whereJsonContains('kepegawaian_jadwal.staf', $request->user)
    //                     ->where('kepegawaian_jadwal_detail.pegawai_id',$pegawaiId)
    //                     ->where('kepegawaian_jadwal.bulan',$bulan)
    //                     ->where('kepegawaian_jadwal.tahun',$tahun)
    //                     ->whereIn('kepegawaian_jadwal.progress',[1,2,3])
    //                     ->whereNull('kepegawaian_jadwal_detail.deleted_at')
    //                     ->orderBy('kepegawaian_jadwal_detail.id','DESC')
    //                     ->first();

    //     if ($jadwal) {
    //         $cutiMap = [
    //             'L'  => 'Libur',
    //             'C'  => 'Cuti Tahunan',
    //             'CM' => 'Cuti Melahirkan',
    //             'CU' => 'Cuti Umroh',
    //             'CH' => 'Cuti Haji',
    //             'CD' => 'Cuti Diluar Tanggungan',
    //         ];
    //         $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
    //                     $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
    //                         ->whereNull('referensi_jadwal_users.deleted_at');
    //                 })
    //                 ->select('referensi_jadwal_shift.*')
    //                 ->whereRaw("
    //                     FIND_IN_SET(?,
    //                         REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
    //                     )
    //                 ", [$jadwal->atasan])
    //                 ->where('referensi_jadwal_shift.singkat',$jadwal->$hit)
    //                 ->where('referensi_jadwal_shift.deleted_at',null)
    //                 ->first();

    //         if ($shift) {
    //             if ($shift->berangkat > $shift->pulang) {
    //                 $nama = 'Jadwal Shift Lewat Hari';
    //             } else {
    //                 $nama = 'Jadwal Shift Reguler';
    //             }
    //             $push = Carbon::parse($shift->berangkat)->subHour()->isoFormat('HH:mm:ss');
    //             $jam = Carbon::parse($shift->berangkat)->isoFormat('HH:mm').' - '.Carbon::parse($shift->pulang)->isoFormat('HH:mm').' WIB';
    //             $shift = 'Shift '.$shift->shift;
    //         }

    //         return response()->json([
    //             'tanggal' => $datenow,
    //             'nama' => $nama,
    //             'shift' => $shift,
    //             'push' => $push,
    //             'jam' => $jam,
    //             'message' => 'Tetap Semangat!'
    //         ]);
    //     } else {
    //         return response()->json(['message' => 'Jadwal Shift tidak ditemukan'], 404);
    //     }
    // }

    function reminderShift($user)
    {
        // INIT VAL
        $now = Carbon::now();
        $tahun = $now->format('Y'); // 2025
        $bulan = $now->format('m'); // 06
        $tgl   = $now->format('j'); // 3
        $th = $now->hour;           // Jam (0–23)
        $tm = $now->minute;         // Menit (0–59)
        $ts = $now->second;         // Detik (0–59)
        $hit = "tgl".$tgl; // P atau S atau PS

        // STARTING QUERY DATA
        /////////////////////////////////// SHIFT & ABSENSI ///////////////////////////////////
        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->select('kepegawaian_jadwal_detail.pegawai_id','kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.staf as bawahan','kepegawaian_jadwal.progress')
                        // ->whereJsonContains('kepegawaian_jadwal.staf', $request->user)
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->whereIn('kepegawaian_jadwal.progress',[1,2,3])
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                    $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                        ->whereNull('referensi_jadwal_users.deleted_at');
                })
                ->select('referensi_jadwal_shift.*')
                ->whereRaw("
                    FIND_IN_SET(?,
                        REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                    )
                ", [$jadwal->atasan])
                ->where('referensi_jadwal_shift.singkat',$jadwal->$hit)
                ->where('referensi_jadwal_shift.deleted_at',null)
                ->first();

        if ($jadwal) {
            return response()->json([
                'jadwal' => [
                    'pegawai_id' => $jadwal->pegawai_id,
                    'tanggal' => date('Y-m-d'),
                    'shift' => $jadwal->$hit,
                    'jam_masuk' => $shift->berangkat, // 07:00:00
                    'jam_pulang' => $shift->pulang // 14:00:00
                ]
            ]);
        }

        return response()->json(['jadwal' => null]);
    }
}
