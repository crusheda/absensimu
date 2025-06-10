<?php

namespace App\Http\Controllers\Rekap;

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
use Intervention\Image\Facades\Image;

class RekapController extends Controller
{
    public function index()
    {
        $agent = new Agent();
        $profil_rs = profil_rs::first();

        $data = [
            'agent' => $agent,
            'profil_rs' => $profil_rs,
        ];

        return view('pages.rekap.index')->with('list',$data);
    }

    public function detail($id)
    {
        $agent = new Agent();

        $data = [
            'id' => $id,
            'agent' => $agent,
        ];

        return view('pages.rekap.detail')->with('list',$data);
    }

    function showFotoDetail($id, $status)
    {
        // 0 : pulang; 1 : berangkat
        $push = absensi::where('id', $id)->firstOrFail();

        $path = storage_path('app/' . ($status == 0 ? $push->path_out : $push->path_in));

        if (!file_exists($path)) abort(404);

        // Resize gambar ke 500x500 (atau sesuaikan)
        $image = Image::make($path)->resize(500, 600); // PAKSA 500x500
        // $image = Image::make($path)->resize(500, 500, function ($constraint) {
        //     $constraint->aspectRatio(); // Jaga proporsi
        //     $constraint->upsize();      // Hindari memperbesar gambar kecil
        // });

        // Kirim sebagai response gambar
        return $image->response(); // default JPEG, bisa pakai ->encode('png') dll
    }

    // API ARERA -----------------------------------------------------------------------------------------
    function showRekap($user)
    {
        // Ambil 3 Nama bulan Terakhir
        $now = Carbon::now();
        // $months = [];
        // for ($i = 2; $i >= 0; $i--) {
        //     $nameMonths[] = $now->copy()->subMonths($i)->translatedFormat('F'); // 'F' = Nama Bulan lengkap = ["Februari", "Maret", "April"]
        // }

            // Variabel untuk menampung bulan-bulan yang dibutuhkan
            $labels = [];
            $dataPerMonth = [];

            if ($now->isoFormat('DD') > 20) {
                $a = 2;
                $b = 0;
            } else {
                $a = 3;
                $b = 1;
            }
            // Ambil tanggal 21 bulan lalu hingga 20 bulan ini untuk setiap bulan dalam 3 bulan terakhir
            for ($i = $a; $i >= $b; $i--) {
                // Mendapatkan tanggal 21 bulan ke-i
                $startDate = $now->copy()->subMonths($i)->day(21)->startOfDay(); // Mulai dari tanggal 21
                // Mendapatkan tanggal 20 bulan ke-i berikutnya
                $endDate = $now->copy()->subMonths($i)->addMonth()->day(20)->endOfDay(); // Sampai tanggal 20 bulan berikutnya

                // Menyimpan format bulan untuk label
                $labels[] = $startDate->translatedFormat('d M y').' - '.$endDate->translatedFormat('d M y'); // Misal "Februari 2025"

                // Query untuk mengambil data di rentang tanggal tersebut
                $dataPush1 = Absensi::where('pegawai_id', $user)
                    ->where('jenis',1)
                    ->where('terlambat',0)
                    ->whereBetween('tgl_in', [$startDate, $endDate])
                    ->where('tgl_out','!=',null)
                    ->where('deleted_at',null)
                    ->count(); // Menghitung jumlah data TEPAT WAKTU berdasarkan rentang tanggal
                $dataPush2 = Absensi::where('pegawai_id', $user)
                    ->where('jenis',1)
                    ->where('terlambat',1)
                    ->whereBetween('tgl_in', [$startDate, $endDate])
                    ->where('tgl_out','!=',null)
                    ->where('deleted_at',null)
                    ->count(); // Menghitung jumlah data TERLAMBAT berdasarkan rentang tanggal
                $dataPush3 = Absensi::where('pegawai_id', $user)
                    ->where('jenis',1)
                    ->whereBetween('tgl_in', [$startDate, $endDate])
                    ->where('tgl_out',null)
                    ->where('deleted_at',null)
                    ->count(); // Menghitung jumlah data ABSEN 1X berdasarkan rentang tanggal
                $dataPush4 = Absensi::where('pegawai_id', $user)
                    ->where('jenis',3)
                    ->whereBetween('tgl_in', [$startDate, $endDate])
                    ->where('deleted_at',null)
                    ->count(); // Menghitung jumlah data IJIN berdasarkan rentang tanggal

                // Menyimpan data untuk masing-masing bulan
                $dataPerMonth[] = [
                    'bulan' => $startDate->format('Y-m'),
                    'data1' => $dataPush1,
                    'data2' => $dataPush2,
                    'data3' => $dataPush3,
                    'data4' => $dataPush4
                ];
                // print_r($startDate);
                // print_r($endDate);
                // print_r($dataPerMonth);
            }
            // die();

        $thisMonth = Absensi::where('pegawai_id', $user)->where('jenis',1)->where('deleted_at',null)->whereMonth('tgl_in', Carbon::now()->month)->whereYear('tgl_in', Carbon::now()->year)->count();
        $total = Absensi::where('pegawai_id', $user)->where('jenis',1)->whereYear('tgl_in', Carbon::now()->year)->where('deleted_at',null)->count();

        $data = [
            'labels' => $labels,
            'dataPerMonth' => $dataPerMonth,
            'thisMonth' => $thisMonth,
            'total' => $total,
        ];

        return response()->json($data, 200);
    }

    function listWeek1($user)
    {
        $show = Absensi::where('pegawai_id', $user)
                        ->whereBetween('tgl_in', [Carbon::now()->subWeek(), Carbon::now()])
                        // ->where('jenis',1)
                        ->orderBy('tgl_in', 'DESC')
                        ->get();
        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [Carbon::now()->subWeek(), Carbon::now()])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',0)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [Carbon::now()->subWeek(), Carbon::now()])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',1)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [Carbon::now()->subWeek(), Carbon::now()])
            ->where('jenis',1)
            ->where('tgl_out',null)
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $data = [
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    function listWeek2($user)
    {
        $show = Absensi::where('pegawai_id', $user)
                        ->whereBetween('tgl_in', [Carbon::now()->subDays(14), Carbon::now()])
                        // ->where('jenis',1)
                        ->orderBy('tgl_in', 'DESC')
                        ->get();
        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [Carbon::now()->subDays(14), Carbon::now()])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',0)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [Carbon::now()->subDays(14), Carbon::now()])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',1)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [Carbon::now()->subDays(14), Carbon::now()])
            ->where('jenis',1)
            ->where('tgl_out',null)
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $data = [
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    function listMonth1($user)
    {
        // Batas awal: 21 dua bulan lalu (April)
        $startDate = Carbon::now()->subMonths(2)->day(21)->startOfDay();

        // Batas akhir: 20 satu bulan lalu (Mei)
        $endDate = Carbon::now()->subMonth()->day(20)->endOfDay();

        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->whereNotNull('tgl_out')
            ->where('terlambat',0)
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->whereNotNull('tgl_out')
            ->where('terlambat',1)
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->whereNull('tgl_out')
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $show = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->orderBy('tgl_in', 'DESC')
            ->get();

        $data = [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    function listMonth2($user)
    {
        // Batas awal: tanggal 21 bulan lalu
        $startDate = Carbon::now()->subMonth()->day(21)->startOfDay();
        // Batas akhir: tanggal 20 bulan ini
        $endDate = Carbon::now()->day(20)->endOfDay();

        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',0)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',1)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->where('tgl_out',null)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $show = Absensi::where('pegawai_id', $user)
                        ->whereBetween('tgl_in', [$startDate, $endDate])
                        ->orderBy('tgl_in', 'DESC')
                        ->get();

        $data = [
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    function listMonth3($user)
    {
        // tanggal 20 bulan ini sampai dengan saat ini
        $startDate = Carbon::now()->day(20)->endOfDay();
        $endDate = Carbon::now();

        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',0)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->where('tgl_out','!=',null)
            ->where('terlambat',1)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis',1)
            ->where('tgl_out',null)
            ->orderBy('tgl_in', 'DESC')
            ->count();
        $show = Absensi::where('pegawai_id', $user)
                        ->whereBetween('tgl_in', [$startDate, $endDate])
                        ->orderBy('tgl_in', 'DESC')
                        ->get();

        $data = [
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    function listThreeMonths($user)
    {
        $now = Carbon::now();

        if ($now->day < 21) {
            // Kalau hari ini sebelum tanggal 21, akhir periode adalah tanggal 20 bulan ini
            $endDate = $now->copy()->day(20);
        } else {
            // Kalau hari ini tanggal 21 ke atas, akhir periode adalah tanggal 20 bulan depan
            $endDate = $now->copy()->addMonth()->day(20);
        }

        // Tanggal awal = 3 bulan sebelum tanggal akhir, tapi dimulai dari tanggal 21
        $startDate = $endDate->copy()->subMonths(3)->addDay(); // dari 21 tiga bulan lalu

        // Query seperti biasa:
        $show = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->orderBy('tgl_in', 'DESC')
            ->get();

        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis', 1)
            ->whereNotNull('tgl_out')
            ->where('terlambat', 0)
            ->count();

        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis', 1)
            ->whereNotNull('tgl_out')
            ->where('terlambat', 1)
            ->count();

        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis', 1)
            ->whereNull('tgl_out')
            ->count();

        $data = [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    // function listThreeMonths($user)
    // {
    //     $startDate = Carbon::now()->subMonths(3);
    //     $endDate = Carbon::now();

    //     $show = Absensi::where('pegawai_id', $user)
    //                     ->whereBetween('tgl_in', [$startDate, $endDate])
    //                     ->orderBy('tgl_in', 'DESC')
    //                     ->get();

    //     $tepatWaktu = Absensi::where('pegawai_id', $user)
    //         ->whereBetween('tgl_in', [$startDate, $endDate])
    //         ->where('jenis', 1)
    //         ->whereNotNull('tgl_out')
    //         ->where('terlambat', 0)
    //         ->orderBy('tgl_in', 'DESC')
    //         ->count();

    //     $terlambat = Absensi::where('pegawai_id', $user)
    //         ->whereBetween('tgl_in', [$startDate, $endDate])
    //         ->where('jenis', 1)
    //         ->whereNotNull('tgl_out')
    //         ->where('terlambat', 1)
    //         ->orderBy('tgl_in', 'DESC')
    //         ->count();

    //     $absenOne = Absensi::where('pegawai_id', $user)
    //         ->whereBetween('tgl_in', [$startDate, $endDate])
    //         ->where('jenis', 1)
    //         ->whereNull('tgl_out')
    //         ->orderBy('tgl_in', 'DESC')
    //         ->count();

    //     $data = [
    //         'show' => $show,
    //         'tepatWaktu' => $tepatWaktu,
    //         'terlambat' => $terlambat,
    //         'absenOne' => $absenOne,
    //     ];

    //     return response()->json($data, 200);
    // }

    function listThisYear($user)
    {
        $startDate = Carbon::now()->startOfYear(); // 1 Januari tahun ini
        $endDate = Carbon::now()->endOfYear();     // 31 Desember tahun ini

        $show = Absensi::where('pegawai_id', $user)
                        ->whereBetween('tgl_in', [$startDate, $endDate])
                        ->orderBy('tgl_in', 'DESC')
                        ->get();

        $tepatWaktu = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis', 1)
            ->whereNotNull('tgl_out')
            ->where('terlambat', 0)
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $terlambat = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis', 1)
            ->whereNotNull('tgl_out')
            ->where('terlambat', 1)
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $absenOne = Absensi::where('pegawai_id', $user)
            ->whereBetween('tgl_in', [$startDate, $endDate])
            ->where('jenis', 1)
            ->whereNull('tgl_out')
            ->orderBy('tgl_in', 'DESC')
            ->count();

        $data = [
            'show' => $show,
            'tepatWaktu' => $tepatWaktu,
            'terlambat' => $terlambat,
            'absenOne' => $absenOne,
        ];

        return response()->json($data, 200);
    }

    function showDetail($user,$id)
    {
        $show = absensi::where('id',$id)
                        // ->where('pegawai_id',$user)
                        ->first();

        $data = [
            'show' => $show,
        ];

        return response()->json($data, 200);
    }
}
