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
        // $datenow = "2025-01-02";
        // print_r($datenow);
        // die();
        $jadwal = jadwal_detail::join('kepegawaian_jadwal','kepegawaian_jadwal_detail.id_jadwal','=','kepegawaian_jadwal.id')
                        ->where('kepegawaian_jadwal_detail.pegawai_id',$request->user)
                        ->where('kepegawaian_jadwal.bulan',$bulan)
                        ->where('kepegawaian_jadwal.tahun',$tahun)
                        ->where('kepegawaian_jadwal.progress',3)
                        ->select('kepegawaian_jadwal_detail.'.$hit,'kepegawaian_jadwal.pegawai_id as atasan','kepegawaian_jadwal.staf as bawahan')
                        ->orderBy('kepegawaian_jadwal_detail.id','DESC')
                        ->first();

        if (!empty($jadwal) || $jadwal != null) {
            $shift = ref_shift::where('pegawai_id',$jadwal->atasan)->where('singkat',$jadwal->$hit)->first();
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
                        ->where("lewat_hari",'1') // SHIFT
                        ->where("jenis",'1') // SHIFT
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

        $jadwal = jadwal_detail::join('kepegawaian_jadwal','kepegawaian_jadwal.id','=','kepegawaian_jadwal_detail.id_jadwal')
                                ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                                ->where('kepegawaian_jadwal.bulan',$bulan)
                                ->where('kepegawaian_jadwal.tahun',$tahun)
                                ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
                                ->first();

        // EXECUTE
        $callShift = $jadwal->$hit;

        // FIND SHIFT
        $shift = ref_shift::where('singkat',$callShift)->where('pegawai_id',$jadwal->id_atasan)->orderBy('updated_at','DESC')->first();

        $jam_masuk = Carbon::parse($shift->berangkat)->isoFormat('HH:mm:ss');
        $jam_pulang = Carbon::parse($shift->pulang)->isoFormat('HH:mm:ss');
        if ($jam_pulang > $jam_masuk) {
            $lewat_hari = 0;
        } else {
            $lewat_hari = 1;
        }

        if ($oncall) { // JIKA USER BISA ONCALL / MEMPUNYAI PERMISSION = absensi_oncall
            // VALIDATING JAM MASUK
            if ($shift->pulang > $shift->berangkat) {
                if ($time >= Carbon::parse($shift->berangkat)->subHour()->isoFormat('HH:mm:ss') && $time <= $shift->pulang) { // DALAM JAM KERJA (MIN 1 JAM SEBELUM JAM MASUK)
                    return Response::json(array(
                        'message' => 'Anda berada di Waktu Masuk Kerja!',
                        'lewat_hari' => $lewat_hari,
                        'kd_shift' => $shift->singkat,
                        'nm_shift' => $shift->shift,
                        'berangkat' => Carbon::parse($today.' '.$shift->berangkat)->isoFormat('YYYY-MM-DD HH:mm:ss'),
                        'pulang' => Carbon::parse($today.' '.$shift->pulang)->isoFormat('YYYY-MM-DD HH:mm:ss'),
                        'code' => 200,
                    ));
                } else {
                    return Response::json(array(
                        'message' => 'Absen Masuk belum tersedia!',
                        'code' => 400,
                    ));
                }
            } else {
                return Response::json(array(
                    'message' => 'Absen Masuk belum tersedia!',
                    'code' => 400,
                ));
            }
        } else { // JIKA USER TIDAK ADA PERMISSION = absensi_oncall
            // VALIDATING JAM MASUK
            if ($shift->pulang > $shift->berangkat) { // KECUALI MALAM ATAU LEWAT HARI
                if ($time >= Carbon::parse($shift->berangkat)->subHour()->isoFormat('HH:mm:ss') && $time <= $shift->pulang) { // DALAM JAM KERJA (MIN 1 JAM SEBELUM JAM MASUK)
                    return Response::json(array(
                        'message' => 'Anda berada di Waktu Masuk Kerja!',
                        'lewat_hari' => $lewat_hari,
                        'kd_shift' => $shift->singkat,
                        'nm_shift' => $shift->shift,
                        'berangkat' => Carbon::parse($today.' '.$shift->berangkat)->isoFormat('YYYY-MM-DD HH:mm:ss'),
                        'pulang' => Carbon::parse($today.' '.$shift->pulang)->isoFormat('YYYY-MM-DD HH:mm:ss'),
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
                $convBerangkat = Carbon::parse($today.' '.$shift->berangkat)->subHour(); // MULAI ABSENSI MINIMAL 1 JAM SEBELUM JAM MASUK
                $convPulang = Carbon::parse($tomorow.' '.$shift->pulang);
                if ($now >= $convBerangkat && $now <= $convPulang) { // DALAM JAM KERJA
                    return Response::json(array(
                        'message' => 'Anda berada di Waktu Masuk Kerja!',
                        'lewat_hari' => $lewat_hari,
                        'kd_shift' => $shift->singkat,
                        'nm_shift' => $shift->shift,
                        'berangkat' => Carbon::parse($today.' '.$shift->berangkat)->isoFormat('YYYY-MM-DD HH:mm:ss'),
                        'pulang' => Carbon::parse($tommorow.' '.$shift->pulang)->isoFormat('YYYY-MM-DD HH:mm:ss'),
                        'code' => 200,
                    ));
                } else {
                    return Response::json(array(
                        'message' => 'Absen Masuk belum tersedia!',
                        'code' => 400,
                    ));
                }
            }

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

        $jadwal = jadwal_detail::join('kepegawaian_jadwal','kepegawaian_jadwal.id','=','kepegawaian_jadwal_detail.id_jadwal')
                                ->where('kepegawaian_jadwal_detail.pegawai_id',$user)
                                ->where('kepegawaian_jadwal.bulan',$bulan)
                                ->where('kepegawaian_jadwal.tahun',$tahun)
                                ->select('kepegawaian_jadwal.pegawai_id as id_atasan','kepegawaian_jadwal.staf','kepegawaian_jadwal.bulan','kepegawaian_jadwal.tahun','kepegawaian_jadwal_detail.*')
                                ->first();

        // EXECUTE
        $callShift = $jadwal->$hit;

        // FIND SHIFT
        $shift = ref_shift::where('singkat',$callShift)->where('pegawai_id',$jadwal->id_atasan)->orderBy('updated_at','DESC')->first();

        // VALIDATING
        return Response::json(array(
            'message' => 'Anda berada di Waktu Masuk Kerja!',
            'lewat_hari' => 0,
            'kd_shift' => $shift->singkat,
            'nm_shift' => $shift->shift,
            'berangkat' => Carbon::parse($today.' '.$shift->berangkat)->isoFormat('YYYY-MM-DD HH:mm:ss'),
            'pulang' => Carbon::parse($today.' '.$shift->pulang)->isoFormat('YYYY-MM-DD HH:mm:ss'),
            'code' => 200,
        ));
    }

    function validatePulang($user) // KHUSUS MASUK SHIFT
    {
        $now = Carbon::now();
        $datenow = $now->isoFormat('YYYY-MM-DD');
        $show = absensi::where('pegawai_id',$user)->where('jenis','1')->whereDate("ref_jam_pulang","=",$datenow)->orderBy("tgl_in","DESC")->first();

        $pulangmin = Carbon::parse($show->ref_jam_pulang);
        $pulangmax = Carbon::parse($show->ref_jam_pulang)->addHour();

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

        // print_r($request->all());
        // die();
        $img = $request->image;
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

        $data = new absensi;
        $data->jenis = 1;
        $data->pegawai_id = $request->pegawai;
        $data->kd_shift = $request->kd_shift;
        $data->nm_shift = $request->nm_shift;
        $data->ref_jam_masuk = $request->berangkat;
        $data->ref_jam_pulang = $request->pulang;
        $data->keterlambatan = $diff;
        $data->tgl_in = Carbon::now();
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
    }

    function executeIjin(Request $request)
    {
        // JIKA TOLERANSI KETERLAMBATAN = 10 MENIT DIHITUNG DARI JAM MULAI MASUK
        // $toleransi = Carbon::parse('00:10:00')->isoFormat('HH:mm:ss');

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

        $data = new absensi;
        $data->jenis = 3;
        $data->pegawai_id = $request->pegawai;
        $data->kd_shift = $request->kd_shift;
        $data->nm_shift = $request->nm_shift;
        $data->ref_jam_masuk = $request->berangkat;
        $data->ref_jam_pulang = $request->pulang;
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
        $data->keterangan = null;
        $data->lewat_hari = $request->lewat_hari;
        $data->save();

        return Response::json(array(
            'message' => 'Surat Ijin berhasil dikirimkan',
            'code' => 200,
        ));
    }

    function executePulang(Request $request)
    {
        $now = Carbon::now();
        $datenow = $now->isoFormat('YYYY-MM-DD');

        $img = $request->image;
        $title = uniqid() . '.png';
        $folderPath = "public/files/kepegawaian/absensi/pulang/";
        // IMAGE CONVERSION
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $path = $folderPath . $title;
        Storage::put($path, $image_base64);

        // GET DATA TO UPDATE
        $data = absensi::where('pegawai_id',$request->pegawai)->whereDate("ref_jam_pulang","=",$datenow)->orderBy("ref_jam_pulang","DESC")->first();

        // PERHITUNGAN LEMBUR JAM PULANG
        $jam_pulang_seharusnya = new Carbon($data->ref_jam_pulang); // ->isoFormat('YYYY-MM-DD H:mm:ss')
        $jam_pulang_sekarang = new Carbon();
        $diffLembur = $jam_pulang_seharusnya->diff($jam_pulang_sekarang)->format('%H:%I:%S');

        // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI PULANG
        $jam_berangkat = new Carbon($data->tgl_in); // ->isoFormat('YYYY-MM-DD H:mm:ss')
        $jam_pulang = new Carbon();
        $diffKerja = $jam_berangkat->diff($jam_pulang)->format('%H:%I:%S');

        $data->tgl_out = Carbon::now();
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
}
