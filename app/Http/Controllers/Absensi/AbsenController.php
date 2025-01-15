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
    function initAbsensi($user)
    {
        // $users  = users::where('nik','!=',null)->where('nama','!=',null)->orderBy('nama', 'asc')->get();
        $datenow = Carbon::now()->isoFormat('YYYY-MM-DD');
        // $datenow = "2025-01-02";
        // print_r($datenow);
        // die();

        $show = absensi::where('pegawai_id',$user)
                        ->whereDate("tgl_in","=",$datenow)
                        ->where("tgl_out",null)
                        ->orderBy("tgl_in","DESC")
                        ->first();

        $data = [
            'show' => $show,
        ];

        // print_r($data);
        // die();

        return response()->json($data, 200);
    }

    function validateJadwal($user)
    {
        $time = Carbon::now()->isoFormat('HH:mm:ss'); // 24 hour
        // print_r($time);
        // die();
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

        // VALIDATING JAM MASUK
        if ($shift->pulang > $shift->berangkat) { // KECUALI MALAM ATAU LEWAT HARI
            // print_r($time);
            // print_r($shift->berangkat);
            // print_r($shift->pulang);
            // die();
            if ($time >= $shift->berangkat && $time <= $shift->pulang) { // DALAM JAM KERJA
                return Response::json(array(
                    'message' => 'Anda berada di Waktu Masuk Kerja!',
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
            $convBerangkat = Carbon::parse($today.' '.$shift->berangkat);
            $convPulang = Carbon::parse($tomorow.' '.$shift->pulang);
            if ($now >= $convBerangkat && $now <= $convPulang) { // DALAM JAM KERJA
                return Response::json(array(
                    'message' => 'Anda berada di Waktu Masuk Kerja!',
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

    function validatePulang($user)
    {
        $now = Carbon::now();
        $datenow = $now->isoFormat('YYYY-MM-DD');
        $show = absensi::where('pegawai_id',$user)->whereDate("ref_jam_pulang","=",$datenow)->orderBy("tgl_in","DESC")->first();

        $pulangmin = Carbon::parse($show->ref_jam_pulang);
        $pulangmax = Carbon::parse($show->ref_jam_pulang)->addHour();

        if ($now <= $pulangmax && $now >= $pulangmin) {
            return Response::json(array(
                'message' => 'Anda dapat melanjutkan proses Absen Pulang!',
                'code' => 200,
            ));
        } else {
            return Response::json(array(
                'message' => 'Anda belum dapat melakukan Absen Pulang!',
                'code' => 400,
            ));
        }


        return Response::json(array(
            'message' => 'Anda berada di Waktu Masuk Kerja!',
            'kd_shift' => $shift->singkat,
            'nm_shift' => $shift->shift,
            'berangkat' => Carbon::parse($today.' '.$shift->berangkat)->isoFormat('YYYY-MM-DD HH:mm:ss'),
            'pulang' => Carbon::parse($today.' '.$shift->pulang)->isoFormat('YYYY-MM-DD HH:mm:ss'),
            'code' => 200,
        ));
    }

    function executeAbsensi(Request $request)
    {
        // JIKA TOLERANSI KETERLAMBATAN = 10 MENIT DIHITUNG DARI JAM MULAI MASUK
        // $toleransi = Carbon::parse('00:10:00')->isoFormat('HH:mm:ss');

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
        $data->lokasi_in = $request->lokasi;
        $data->terlambat = $terlambat;
        $data->save();

        return Response::json(array(
            'message' => 'Absen masuk berhasil, selamat beraktifitas',
            'code' => 200,
        ));
    }

    function executePulang(Request $request)
    {
        $now = Carbon::now();
        $datenow = $now->isoFormat('YYYY-MM-DD');

        // GET DATA TO UPDATE
        $data = absensi::where('pegawai_id',$request->pegawai)->whereDate("ref_jam_pulang","=",$datenow)->orderBy("ref_jam_pulang","DESC")->first();

        // PERHITUNGAN SELISIH JAM SAAT MASUK SAMPAI PULANG
        $jam_berangkat = new Carbon($data->tgl_in); // ->isoFormat('YYYY-MM-DD H:mm:ss')
        $jam_pulang = new Carbon();
        $diff = $jam_berangkat->diff($jam_pulang)->format('%H:%I:%S');

        $data->tgl_out = Carbon::now();
        $data->selisih_jam = $diff;
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
