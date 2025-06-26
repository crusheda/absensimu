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

class AbsensiController extends Controller
{
    function init(Request $request)
    {
        return response()->json([
            'berangkat' => true,
            'pulang' => false,
            'ijin' => true,
            'nama' => 'Jadwal Reguler',
            'jam' => '08:00 - 15:00 WIB',
            'keterangan' => 'Pagi Kantor (sdasd)',
        ]);
    }

    function absensi(Request $request)
    {
        $now = Carbon::now();
        $waktuSekarang = new Carbon();;
        $time = Carbon::now()->isoFormat('HH:mm:ss'); // 24 hour
        $today = Carbon::now()->isoFormat('YYYY-MM-DD');
        $tommorow = Carbon::now()->addDays(1)->isoFormat('YYYY-MM-DD');
        $tahun = Carbon::now()->isoFormat('YYYY');
        $bulan = Carbon::now()->isoFormat('MM');
        $tgl = Carbon::now()->isoFormat('D');
        $hit = "tgl".$tgl;
        $user = $request->id_user;

        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
                        // ->whereJsonContains('kepegawaian_jadwal.staf', $user)
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        // EXECUTE
        $callShift = $jadwal->$hit;

        if ($callShift) {
            // FIND SHIFT
            $shift = ref_shift::leftJoin('referensi_jadwal_users', function($join) {
                        $join->on('referensi_jadwal_users.pegawai_id', '=', 'referensi_jadwal_shift.pegawai_id')
                            ->whereNull('referensi_jadwal_users.deleted_at');
                    })
                    ->select('referensi_jadwal_shift.*')
                    ->whereRaw("
                        FIND_IN_SET(?,
                            REPLACE(REPLACE(REPLACE(referensi_jadwal_users.staf, '\"', ''), '[', ''), ']', '')
                        )
                    ", [$jadwal->id_atasan])
                    ->where('referensi_jadwal_shift.singkat',$callShift)
                    ->where('referensi_jadwal_shift.deleted_at',null)
                    ->first();

            if ($shift) {
                $berangkat = null;
                $pulang = null;
                $jam_masuk = Carbon::parse($shift->berangkat);
                $jam_pulang = Carbon::parse($shift->pulang);

                if ($jam_pulang->greaterThan($jam_masuk)) {
                    $lewat_hari = 0; // TIDAK LEWAT HARI
                } else {
                    $lewat_hari = 1; // LEWAT HARI / MALAM
                }

                // VALIDATING JAM MASUK
                if ($jam_pulang->greaterThan($jam_masuk)) { // KECUALI MALAM ATAU LEWAT HARI
                    if ($time >= Carbon::parse($shift->berangkat)->subHour()->isoFormat('HH:mm:ss') && $time <= Carbon::parse($shift->pulang)->isoFormat('HH:mm:ss')) { // DALAM JAM KERJA (MIN 1 JAM SEBELUM JAM MASUK)
                        $berangkat = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                        $pulang = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                    } else {
                        return Response::json(array(
                            'message' => 'Absen Masuk belum tersedia!',
                            'code' => 401,
                        ));
                    }
                } else { // KHUSUS JAGA LEWAT HARI (SHIFT MALAM)
                    $convBerangkat = Carbon::parse($today.' '.$shift->berangkat)->subHour(); // MULAI ABSENSI MINIMAL 1 JAM SEBELUM JAM MASUK
                    $convPulang = Carbon::parse($tommorow.' '.$shift->pulang);
                    if ($now >= $convBerangkat && $now <= $convPulang) { // DALAM JAM KERJA
                        $berangkat = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                        $pulang = Carbon::createFromFormat('Y-m-d H:i:s', $tommorow . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s');
                    } else {
                        return Response::json(array(
                            'message' => 'Absen Masuk belum tersedia!',
                            'code' => 401,
                        ));
                    }
                }

                // INITIALIZE PROCESS = ABSENSI
                if ($request->hasFile('foto')) {
                    // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI KETERLAMBATAN
                    $initBerangkat = Carbon::parse($berangkat)->addMinutes(10);
                    $harusnyaBerangkat = new Carbon($initBerangkat); // ->isoFormat('YYYY-MM-DD H:mm:ss')
                    if ($waktuSekarang > $harusnyaBerangkat) {
                        $diff = $waktuSekarang->diff($harusnyaBerangkat)->format('%H:%I:%S');
                        $terlambat = 1; // TERLAMBAT
                    } else {
                        $diff = Carbon::parse('00:00:00')->isoFormat('HH:mm:ss');
                        $terlambat = 0; // DISIPLIN
                    }

                    if (Carbon::createFromFormat('Y-m-d H:i:s', $berangkat, 'Asia/Jakarta')->isToday()) {
                        $validasi = absensi::where('pegawai_id',$user)
                                        ->where('jenis',1)
                                        ->where('kd_shift',$shift->singkat)
                                        ->where('ref_jam_masuk',$berangkat)
                                        ->where('ref_jam_pulang',$pulang)
                                        ->get();

                        if ($validasi->isNotEmpty()) {
                            return Response::json(array(
                                'message' => 'Absen Masuk sudah tercatat! Silakan melanjutkan Aktifitas Bekerja Anda!',
                                'code' => 401,
                            ));
                        }

                        // SAVE FOTO
                        $file = $request->file('foto');
                        $title = uniqid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('public/files/kepegawaian/absensi/masuk', $title);

                        // SAVE DB
                        $data = new absensi;
                        $data->jenis = 1;
                        $data->pegawai_id = $user;
                        $data->kd_shift = $shift->singkat;
                        $data->nm_shift = $shift->shift;
                        $data->ref_jam_masuk = $berangkat;
                        $data->ref_jam_pulang = $pulang;
                        $data->keterlambatan = $diff;
                        $data->tgl_in = Carbon::now('Asia/Jakarta');
                        $data->foto_in = $title;
                        // $data->title_in = $path;
                        $data->path_in = $path;
                        $data->lokasi_in = $request->latitude.', '.$request->longitude;
                        $data->terlambat = $terlambat;
                        $data->lewat_hari = $lewat_hari;
                        $data->save();

                        return Response::json(array(
                            'message' => 'Absen masuk berhasil, selamat beraktifitas. Semangat!!',
                            'code' => 200,
                        ));
                    } else {
                        return Response::json(array(
                            'message' => 'Jam Berangkat yang terkirim ke Sistem TIDAK VALID, bisa jadi dikarenakan banyaknya Cache yang menumpuk pada Device Anda. Silakan Refresh halaman Absensi ini dan lakukan sekali lagi. Terima Kasih.',
                            'code' => 401,
                        ));
                    }
                } else {
                    return Response::json(array(
                        'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                        'code' => 401,
                    ));
                }
            } else {
                return Response::json(array(
                    'message' => 'Shift tidak valid. Pastikan Hari ini Anda masuk jaga Shift atau Libur. Silakan konfirmasi kepada Admin Jadwal bulan ini untuk memastikan Nama Referensi Shift sudah benar dan Valid!',
                    'code' => 401,
                ));
            }
        } else {
            return Response::json(array(
                'message' => 'Jadwal tidak valid. Konfirmasi dengan Administrator!',
                'code' => 401,
            ));
        }
    }
}
