<?php

namespace App\Http\Controllers\Absensi;

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

class AbsenController extends Controller
{
    public function index()
    {
        $agent = new Agent();
        $profil_rs = profil_rs::first();

        $data = [
            'agent' => $agent,
            'profil_rs' => $profil_rs,
        ];

        return view('pages.absensi.index')->with('list',$data);
    }

    // API FUNCTION ----------------------------------------------------------------------------------------------------
    function init(Request $request)
    {
        $profil_rs = profil_rs::first();
        $lokasi = explode(",",$request->lokasi);
        $lat2 = $lokasi[0];
        $lon2 = $lokasi[1];

        // Kantor Kelurahan Bakung : -7.733879364254091, 110.55628417878309
        // Kilat Photo Studio : -7.63783189686527, 110.86775211807864
        // RS PKU Muhammadiyah Sukoharjo : -7.677851238136329, 110.83968584828327
        // $callDistance = $this->distance("-7.733137923668563", "110.55927671462696", $lat2, $lon2);

        $callDistance = $this->distance($profil_rs->coord_lat, $profil_rs->coord_long, $lat2, $lon2);
        $distance = round($callDistance["meters"]);

        // ---------------------------------------------------------------
        // $users  = users::where('nik','!=',null)->where('nama','!=',null)->orderBy('nama', 'asc')->get();
        $datenow = Carbon::now()->isoFormat('YYYY-MM-DD');
        $tahun = Carbon::now()->isoFormat('YYYY');
        $bulan = Carbon::now()->isoFormat('MM');
        $tgl = Carbon::now()->isoFormat('D');
        $hit = "tgl".$tgl;

        $jadwal = jadwal_detail::leftJoin('kepegawaian_jadwal', function($join) {
                            $join->on('kepegawaian_jadwal.id', '=', 'kepegawaian_jadwal_detail.id_jadwal')
                                ->whereNull('kepegawaian_jadwal.deleted_at');
                        })
                        ->select('kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.staf as bawahan','kepegawaian_jadwal.progress')
                        // ->whereJsonContains('kepegawaian_jadwal.staf', $request->user)
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$request->user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->whereIn('kepegawaian_jadwal.progress',[2,3])
                        ->whereNull('kepegawaian_jadwal_detail.deleted_at')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        if (!empty($jadwal) || $jadwal != null) {
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
        } else {
            $shift = null;
        }

        $show = absensi::where('pegawai_id',$request->user)
                        ->whereDate("tgl_in","=",$datenow)
                        ->where("jenis",'1') // SHIFT
                        ->orderBy("tgl_in","DESC")
                        ->first();
        $showMalam = absensi::where('pegawai_id',$request->user)
                        ->whereDate("ref_jam_pulang","=",$datenow)
                        ->where("tgl_out",null)
                        ->where("lewat_hari",'1')
                        ->where("jenis",'1')
                        ->orderBy("ref_jam_pulang","DESC")
                        ->first();
        $oncall = absensi::where('pegawai_id',$request->user)
                        ->whereDate("tgl_in","=",$datenow)
                        ->where("jenis",'4') // ONCALL
                        ->orderBy("tgl_in","DESC")
                        ->first();
        $ijin = absensi::where('pegawai_id',$request->user)
                        ->whereDate("tgl_in","=",$datenow)
                        ->where("jenis",'3') // IJIN SAKIT
                        ->orderBy("tgl_in","DESC")
                        ->first();

        // print_r($shift->shift);
        // die();
        $data = [
            'distance' => $distance,
            'jadwal' => $jadwal,
            'shift' => $shift,
            'showMalam' => $showMalam,
            'show' => $show,
            'oncall' => $oncall,
            'ijin' => $ijin,
        ];

        // print_r($data);
        // die();

        return response()->json($data, 200);
    }

    function validateJadwal($user,$oncall) // KHUSUS MASUK SHIFT
    {
        $time = Carbon::now()->isoFormat('HH:mm:ss'); // 24 hour
        $today = Carbon::now()->isoFormat('YYYY-MM-DD');
        $tommorow = Carbon::now()->addDays(1)->isoFormat('YYYY-MM-DD');
        $tahun = Carbon::now()->isoFormat('YYYY');
        $bulan = Carbon::now()->isoFormat('MM');
        $tgl = Carbon::now()->isoFormat('D');
        $hit = "tgl".$tgl;

        // $jadwal = jadwal_detail::join('kepegawaian_jadwal','kepegawaian_jadwal.id','=','kepegawaian_jadwal_detail.id_jadwal')
        //                         ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
        //                         ->where('kepegawaian_jadwal.bulan',$bulan)
        //                         ->where('kepegawaian_jadwal.tahun',$tahun)
        //                         ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
        //                         ->where('kepegawaian_jadwal_detail.deleted_at',null)
        //                         ->orderBy('kepegawaian_jadwal_detail.updated_at','DESC')
        //                         ->first();

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
            // $shift = ref_shift::where('singkat',$callShift)->where('pegawai_id',$jadwal->id_atasan)->orderBy('updated_at','DESC')->first();

            // print_r($shift);
            // die();
            if ($shift) {
                $jam_masuk = Carbon::parse($shift->berangkat);
                $jam_pulang = Carbon::parse($shift->pulang);

                if ($jam_pulang->greaterThan($jam_masuk)) {
                    $lewat_hari = 0; // TIDAK LEWAT HARI
                } else {
                    $lewat_hari = 1; // LEWAT HARI / MALAM
                }

                // VALIDATING JAM MASUK
                if ($jam_pulang->greaterThan($jam_masuk)) { // KECUALI MALAM ATAU LEWAT HARI
                    if ($time >= Carbon::parse($shift->berangkat)->subHour(2)->isoFormat('HH:mm:ss') && $time <= Carbon::parse($shift->pulang)->isoFormat('HH:mm:ss')) { // DALAM JAM KERJA (MIN 2 JAM SEBELUM JAM MASUK)
                        return Response::json(array(
                            'message' => 'Anda berada di Waktu Masuk Kerja!',
                            'lewat_hari' => $lewat_hari,
                            'kd_shift' => $shift->singkat,
                            'nm_shift' => $shift->shift,
                            // 'berangkat' => Carbon::parse($today.' '.$shift->berangkat)->toDateTimeString(),
                            // 'pulang' => Carbon::parse($today.' '.$shift->pulang)->toDateTimeString(),
                            'berangkat' => Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'pulang' => Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'code' => 200,
                        ));
                    } else {
                        return Response::json(array(
                            'message' => 'Absen Masuk belum tersedia!',
                            'code' => 400,
                        ));
                    }
                } else { // KHUSUS JAGA LEWAT HARI (SHIFT MALAM)
                    $now = Carbon::now();
                    $today = Carbon::now()->isoFormat('YYYY-MM-DD');
                    $tomorow = Carbon::now()->addDay(1)->isoFormat('YYYY-MM-DD');
                    $convBerangkat = Carbon::parse($today.' '.$shift->berangkat)->subHour(2); // MULAI ABSENSI MINIMAL 2 JAM SEBELUM JAM MASUK
                    $convPulang = Carbon::parse($tomorow.' '.$shift->pulang);
                    if ($now >= $convBerangkat && $now <= $convPulang) { // DALAM JAM KERJA
                        return Response::json(array(
                            'message' => 'Anda berada di Waktu Masuk Kerja!',
                            'lewat_hari' => $lewat_hari,
                            'kd_shift' => $shift->singkat,
                            'nm_shift' => $shift->shift,
                            'berangkat' => Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'pulang' => Carbon::createFromFormat('Y-m-d H:i:s', $tommorow . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'code' => 200,
                        ));
                    } else {
                        return Response::json(array(
                            'message' => 'Absen Masuk belum tersedia!',
                            'code' => 400,
                        ));
                    }
                }
            } else {
                return Response::json(array(
                    'message' => 'Shfit tidak valid. Silakan menghubung Admin Jadwal untuk memastikan Nama Referensi Shift sudah benar dan Valid!',
                    'code' => 400,
                ));
            }
        } else {
            return Response::json(array(
                'message' => 'Jadwal tidak valid. Konfirmasi dengan Administrator!',
                'code' => 400,
            ));
        }
    }

    function validateIjin($user) // KHUSUS MASUK SHIFT
    {
        $time = Carbon::now()->isoFormat('HH:mm:ss'); // 24 hour
        $today = Carbon::now()->isoFormat('YYYY-MM-DD');
        $tommorow = Carbon::now()->addDays(1)->isoFormat('YYYY-MM-DD');
        $tahun = Carbon::now()->isoFormat('YYYY');
        $bulan = Carbon::now()->isoFormat('MM');
        $tgl = Carbon::now()->isoFormat('D');
        $hit = "tgl".$tgl;

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
        // $shift = ref_shift::where('singkat',$callShift)->where('pegawai_id',$jadwal->id_atasan)->orderBy('updated_at','DESC')->first();

        // VALIDATING
        return Response::json(array(
            'message' => 'Anda berada di Waktu Masuk Kerja!',
            'lewat_hari' => 0,
            'kd_shift' => $shift->singkat,
            'nm_shift' => $shift->shift,
            'berangkat' => Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->berangkat, 'Asia/Jakarta')->format('Y-m-d H:i:s'),
            'pulang' => Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $shift->pulang, 'Asia/Jakarta')->format('Y-m-d H:i:s'),
            'code' => 200,
        ));
    }

    function validatePulang($user) // KHUSUS MASUK SHIFT
    {
        $now = Carbon::now();
        $datenow = $now->isoFormat('YYYY-MM-DD');
        $show = absensi::where('pegawai_id',$user)->where('jenis','1')->whereDate("ref_jam_pulang","=",$datenow)->orderBy("tgl_in","DESC")->first();

        $pulangmin = Carbon::parse($show->ref_jam_pulang);
        $pulangmax = Carbon::parse($show->ref_jam_pulang)->addHour(2);

        if ($now >= $pulangmin && $now <= $pulangmax) {
            return Response::json(array(
                'message' => 'Anda dapat melanjutkan proses Absen Pulang!',
                'code' => 200,
            ));
        } else {
            if ($now > $pulangmax) {
                return Response::json(array(
                    'message' => 'Batas Absen Pulang sudah terlewati! Absen Pulang Gagal!',
                    'code' => 400,
                ));
            } else {
                $harusnyaPulang = new Carbon($show->ref_jam_pulang); // ->isoFormat('YYYY-MM-DD H:mm:ss')
                $pulang = new Carbon();
                $diff = $pulang->diff($harusnyaPulang); // ->format('%H:%I:%S')
                return Response::json(array(
                    'message' => 'Anda belum dapat melakukan Absen Pulang sebelum jam pulang yang ditentukan!<br><b>'.$diff->h.' jam '.$diff->i.' menit '.$diff->s.' detik</b><br>menuju Jam Pulang',
                    'code' => 400,
                ));
            }
        }
    }

    function executeBerangkat(Request $request)
    {
        // JIKA TOLERANSI KETERLAMBATAN = 10 MENIT DIHITUNG DARI JAM MULAI MASUK
        // $toleransi = Carbon::parse('00:10:00')->isoFormat('HH:mm:ss');

        // Deteksi user-agent mencurigakan
        $userAgent = $request->header('User-Agent');

        if (!preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            return Response::json(array(
                'message' => 'Absensi hanya diperbolehkan menggunakan perangkat mobile (Android/IOS) !!',
                'code' => 403,
            ));
        }
        if (preg_match('/Genymotion|Xposed|Magisk/i', $userAgent)) {
            return Response::json(array(
                'message' => 'Perangkat tidak valid untuk absensi',
                'code' => 403,
            ));
        }

        $img = $request->image;
        if ($img) {
            $title = uniqid() . '.png';
            $folderPath = "public/files/kepegawaian/absensi/masuk/";
            // IMAGE CONVERSION
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $path = $folderPath . $title;
            Storage::put($path, $image_base64);

            // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI KETERLAMBATAN
            $initBerangkat = Carbon::parse($request->berangkat)->addMinutes(10);
            $harusnyaBerangkat = new Carbon($initBerangkat); // ->isoFormat('YYYY-MM-DD H:mm:ss')
            $berangkat = new Carbon();
            if ($berangkat > $harusnyaBerangkat) {
                $diff = $berangkat->diff($harusnyaBerangkat)->format('%H:%I:%S');
                $terlambat = 1; // TERLAMBAT
            } else {
                $diff = Carbon::parse('00:00:00')->isoFormat('HH:mm:ss');
                $terlambat = 0; // DISIPLIN
            }

            $ValJamMasuk = Carbon::createFromFormat('Y-m-d H:i:s', $request->berangkat, 'Asia/Jakarta');
            $ValJamPulang = Carbon::createFromFormat('Y-m-d H:i:s', $request->pulang, 'Asia/Jakarta');
            $jamMasuk = $ValJamMasuk->format('Y-m-d H:i:s');
            $jamPulang = $ValJamPulang->format('Y-m-d H:i:s');
            // $DateJamMasuk = $ValJamMasuk->format('Y-m-d');
            // $DateNow = Carbon::now()->format('Y-m-d');

            if ($ValJamMasuk->isToday()) {
                $validasi = absensi::where('pegawai_id',$request->pegawai)
                                ->where('jenis',1)
                                ->where('kd_shift',$request->kd_shift)
                                ->where('ref_jam_masuk',$jamMasuk)
                                ->where('ref_jam_pulang',$jamPulang)
                                ->delete();

                $data = new absensi;
                $data->jenis = 1;
                $data->ip_in = $this->getClientIp();
                $data->user_agent = $userAgent;
                $data->pegawai_id = $request->pegawai;
                $data->kd_shift = $request->kd_shift;
                $data->nm_shift = $request->nm_shift;
                $data->ref_jam_masuk = $jamMasuk;
                $data->ref_jam_pulang = $jamPulang;
                $data->keterlambatan = $diff;
                $data->tgl_in = Carbon::now('Asia/Jakarta');
                $data->foto_in = $title;
                // $data->title_in = $path;
                $data->path_in = $path;
                $data->lokasi_in = $request->lokasi;
                $data->terlambat = $terlambat;
                $data->lewat_hari = $request->lewat_hari;
                $data->save();

                return Response::json(array(
                    'message' => 'Absen masuk berhasil, selamat beraktifitas',
                    'code' => 200,
                ));
            } else {
                return Response::json(array(
                    'message' => 'Jam Berangkat yang terkirim ke Sistem TIDAK VALID, bisa jadi dikarenakan banyaknya Cache yang menumpuk pada Device Anda. Silakan Refresh halaman Absensi ini dan lakukan sekali lagi. Terima Kasih.',
                    'code' => 400,
                ));
            }
        } else {
            return Response::json(array(
                'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                'code' => 400,
            ));
        }
    }

    function executePulang(Request $request)
    {
        // Deteksi user-agent mencurigakan
        $userAgent = $request->header('User-Agent');

        if (!preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            return Response::json(array(
                'message' => 'Absensi hanya diperbolehkan menggunakan perangkat mobile (Android/IOS) !!',
                'code' => 403,
            ));
        }
        // if (preg_match('/Genymotion|Xposed|Magisk/i', $userAgent)) {
        //     return Response::json(array(
        //         'message' => 'Perangkat tidak valid untuk absensi',
        //         'code' => 403,
        //     ));
        // }

        $now = Carbon::now('Asia/Jakarta');
        // $datenow = $now->isoFormat('YYYY-MM-DD');
        $datenow = $now->toDateString(); // '2025-06-23'

        $img = $request->image;
        if ($img) {
            $title = uniqid() . '.png';
            $folderPath = "public/files/kepegawaian/absensi/pulang/";
            // IMAGE CONVERSION
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1] ?? 'png';
            $image_base64 = base64_decode($image_parts[1]);
            $path = $folderPath . $title;
            Storage::put($path, $image_base64);

            // VALIDASI DUPLIKASI DATA
            $validate = absensi::where('pegawai_id',$request->pegawai)
                        ->whereDate("ref_jam_pulang","=",$datenow)
                        ->whereNull('deleted_at')
                        ->orderBy('updated_at','DESC')
                        ->get();

            if ($validate->count() > 1) {
                // Ambil data pertama dan buang sisanya
                $validate->skip(1)->each->delete();
                // $validate->slice(1)->each->delete(); // Lebih Berat dari skip()
            }

            // GET DATA TO UPDATE
            // Ambil data pertama dari hasil yang sudah diambil
            $data = $validate->first();
            if (!$data) {
                return Response::json([
                    'message' => 'Data absensi tidak ditemukan.',
                    'code' => 404,
                ]);
            }

            // PERHITUNGAN LEMBUR JAM PULANG
            $jam_pulang_seharusnya = new Carbon($data->ref_jam_pulang); // ->isoFormat('YYYY-MM-DD H:mm:ss')
            $jam_pulang_sekarang = $now;
            $diffLembur = $jam_pulang_seharusnya->diff($jam_pulang_sekarang)->format('%H:%I:%S');

            // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI PULANG
            $jam_berangkat = new Carbon($data->tgl_in); // ->isoFormat('YYYY-MM-DD H:mm:ss')
            $jam_pulang = $now;
            $diffKerja = $jam_berangkat->lessThan($jam_pulang)
                        ? $jam_berangkat->diff($jam_pulang)->format('%H:%I:%S')
                        : '00:00:00';

            $data->ip_out = $this->getClientIp();
            $data->tgl_out = $now;
            $data->lembur = $diffLembur;
            $data->selisih_jam = $diffKerja;
            $data->foto_out = $title;
            $data->path_out = $path;
            $data->lokasi_out = $request->lokasi;
            $data->save();

            return Response::json(array(
                'message' => 'Absen pulang berhasil, hati-hati di jalan',
                'code' => 200,
            ));
        } else {
            return Response::json(array(
                'message' => 'Hasil selfi kamera tidak ditemukan, pastikan kamera Anda dalam kondisi Normal. Apabila masih belum dapat melakukan Absensi, silakan menghubungi Admin. Terima Kasih.',
                'code' => 400,
            ));
        }
    }

    function executeIjin(Request $request)
    {
        // JIKA TOLERANSI KETERLAMBATAN = 10 MENIT DIHITUNG DARI JAM MULAI MASUK
        // $toleransi = Carbon::parse('00:10:00')->isoFormat('HH:mm:ss');

        // Deteksi user-agent mencurigakan
        $userAgent = $request->header('User-Agent');

        if (!preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            return Response::json(array(
                'message' => 'Absensi hanya diperbolehkan menggunakan perangkat mobile (Android/IOS) !!',
                'code' => 403,
            ));
        }
        // if (preg_match('/Genymotion|Xposed|Magisk/i', $userAgent)) {
        //     return Response::json(array(
        //         'message' => 'Perangkat tidak valid untuk absensi',
        //         'code' => 403,
        //     ));
        // }

        $img = $request->image;
        $title = uniqid() . '.png';
        $folderPath = "public/files/kepegawaian/absensi/ijin/";
        // IMAGE CONVERSION
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $path = $folderPath . $title;
        Storage::put($path, $image_base64);

            $jamMasuk = Carbon::parse($request->berangkat)->format('Y-m-d H:i:s');
            $jamPulang = Carbon::parse($request->pulang)->format('Y-m-d H:i:s');

        $data = new absensi;
        $data->jenis = 3;
        $data->ip_in = $this->getClientIp();
        $data->user_agent = $userAgent;
        $data->pegawai_id = $request->pegawai;
        $data->kd_shift = $request->kd_shift;
        $data->nm_shift = $request->nm_shift;
        $data->ref_jam_masuk = $jamMasuk;
        $data->ref_jam_pulang = $jamPulang;
        $data->keterlambatan = null;
        $data->lembur = null;
        $data->tgl_in = Carbon::now();
        $data->tgl_out = null;
        $data->selisih_jam = null;
        $data->foto_in = $title;
        $data->path_in = $path;
        $data->foto_out = null;
        $data->path_out = null;
        $data->lokasi_in = $request->lokasi;
        $data->lokasi_out = null;
        $data->terlambat = null;
        $data->keterangan = $request->keterangan;
        $data->lewat_hari = $request->lewat_hari;
        $data->save();

        return Response::json(array(
            'message' => 'Surat Ijin berhasil dikirimkan',
            'code' => 200,
        ));
    }

    function getDistance(Request $request)
    {
        $profil_rs = profil_rs::first();
        $lokasi = explode(",",$request->lokasi);
        $lat2 = $lokasi[0];
        $lon2 = $lokasi[1];

        // Kantor Kelurahan Bakung : -7.733879364254091, 110.55628417878309
        // Kilat Photo Studio : -7.63783189686527, 110.86775211807864
        // RS PKU Muhammadiyah Sukoharjo : -7.677851238136329, 110.83968584828327
        // $callDistance = $this->distance("-7.733137923668563", "110.55927671462696", $lat2, $lon2);

        $callDistance = $this->distance($profil_rs->coord_lat, $profil_rs->coord_long, $lat2, $lon2);
        $distance = round($callDistance["meters"]);

        return response()->json($distance, 200);
    }

    // FUNCTION HITUNG ------------------------------------------------------------------------------------------------
    function distance($lat1, $lon1, $lat2, $lon2) // Menghitung Jarak
    {
        // lat1 = latitude kantor
        // lon1 = longitude kantor
        // lat2 = latitude user
        // lon2 = longitude user

        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    function getClientIp()
    {
        foreach ([
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ] as $key) {
            if ($ip = request()->server($key)) {
                foreach (explode(',', $ip) as $part) {
                    $part = trim($part);
                    if (filter_var($part, FILTER_VALIDATE_IP)) {
                        return $part;
                    }
                }
            }
        }
        return request()->ip(); // fallback
}

}
