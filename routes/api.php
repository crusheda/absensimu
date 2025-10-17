<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// INITIALIZE
use App\Http\Controllers\Android\Auth\LoginController;
use App\Http\Controllers\Android\DashboardController AS Dashboard;
use App\Http\Controllers\Android\JadwalDinasController AS JadwalDinas;
use App\Http\Controllers\Android\SettingController AS Setting;
use App\Http\Controllers\Android\ReminderController AS Reminder;
use App\Http\Controllers\Android\AbsensiController AS Absensi;
use App\Http\Controllers\Android\IntegrityController;
use App\Http\Controllers\Android\FcmTokenController;
use App\Http\Controllers\Android\NotificationController;

// START ROUTE REST API FLUTTER
Route::post('/login', [LoginController::class, 'login']);
Route::get('/dashboard/{user}', [Dashboard::class, 'index']);
Route::get('/jadwal/{user}/{bulan}/{tahun}', [JadwalDinas::class, 'index']);
Route::get('/jadwal2/{user}/{bulan}/{tahun}', [JadwalDinas::class, 'index2']);
Route::get('/setting/profil/{user}', [Setting::class, 'profilUser']);
// Route::get('/reminder/shift', [Reminder::class, 'reminderShift']); // TIDAK DIPAKAI
Route::get('/reminder/shift/{user}', [Reminder::class, 'reminderShift']);
Route::get('/lokasi-kantor', [Absensi::class, 'lokasiKantor']);
Route::post('/validasi', [Absensi::class, 'init']);
Route::post('/absensi', [Absensi::class, 'absensi']);
Route::get('/absensi/detail/{id}', [Absensi::class, 'detailAbsensi']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
// Route::middleware('auth:sanctum')->group(function () { });
Route::get('/faq', [Dashboard::class, 'faq']);
Route::post('/save-fcm-token', [FcmTokenController::class, 'store']);
Route::post('/remove-token', [FcmTokenController::class, 'removeToken']);
Route::post('/broadcast', [NotificationController::class, 'broadcast']);
Route::post('/sendtopegawai', [NotificationController::class, 'sendToPegawai']);
Route::post('/verify_integrity', [IntegrityController::class, 'verify_integrity']);

// END ROUTE REST API FLUTTER


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('kepegawaian/dashboard/{user}', [\App\Http\Controllers\DashboardController::class, 'show'])->name('kepegawaian.dashboard.show');

Route::post('kepegawaian/absensi/init', [\App\Http\Controllers\Absensi\AbsenController::class, 'init'])->name('kepegawaian.absensi.init');
Route::post('kepegawaian/absensi/distance', [\App\Http\Controllers\Absensi\AbsenController::class, 'getDistance'])->name('kepegawaian.absensi.getDistance');
Route::get('kepegawaian/absensi/validate/jadwal/{user}/pulang', [\App\Http\Controllers\Absensi\AbsenController::class, 'validatePulang'])->name('kepegawaian.absensi.validatePulang');
Route::get('kepegawaian/absensi/validate/jadwal/{user}/{oncall}', [\App\Http\Controllers\Absensi\AbsenController::class, 'validateJadwal'])->name('kepegawaian.absensi.validateJadwal');
Route::get('kepegawaian/absensi/validate/ijin/{user}', [\App\Http\Controllers\Absensi\AbsenController::class, 'validateIjin'])->name('kepegawaian.absensi.validateIjin');

Route::post('kepegawaian/ijin', [\App\Http\Controllers\Absensi\AbsenController::class, 'executeIjin'])->name('kepegawaian.absensi.executeIjin');
Route::post('kepegawaian/berangkat', [\App\Http\Controllers\Absensi\AbsenController::class, 'executeBerangkat'])->name('kepegawaian.absensi.executeBerangkat');
Route::post('kepegawaian/pulang', [\App\Http\Controllers\Absensi\AbsenController::class, 'executePulang'])->name('kepegawaian.absensi.executePulang');

Route::get('kepegawaian/rekap/{user}', [\App\Http\Controllers\Rekap\RekapController::class, 'showRekap'])->name('kepegawaian.rekap.showRekap');
Route::get('kepegawaian/rekap/{user}/1', [\App\Http\Controllers\Rekap\RekapController::class, 'listWeek1'])->name('kepegawaian.rekap.week1');
Route::get('kepegawaian/rekap/{user}/2', [\App\Http\Controllers\Rekap\RekapController::class, 'listWeek2'])->name('kepegawaian.rekap.week2');
Route::get('kepegawaian/rekap/{user}/3', [\App\Http\Controllers\Rekap\RekapController::class, 'listMonth1'])->name('kepegawaian.rekap.month1');
Route::get('kepegawaian/rekap/{user}/4', [\App\Http\Controllers\Rekap\RekapController::class, 'listMonth2'])->name('kepegawaian.rekap.month2');
Route::get('kepegawaian/rekap/{user}/5', [\App\Http\Controllers\Rekap\RekapController::class, 'listMonth3'])->name('kepegawaian.rekap.month3');
Route::get('kepegawaian/rekap/{user}/6', [\App\Http\Controllers\Rekap\RekapController::class, 'listThreeMonths'])->name('kepegawaian.rekap.3month');
Route::get('kepegawaian/rekap/{user}/7', [\App\Http\Controllers\Rekap\RekapController::class, 'listThisYear'])->name('kepegawaian.rekap.1year');
Route::get('kepegawaian/rekap/{user}/8', [\App\Http\Controllers\Rekap\RekapController::class, 'listDinasLuar'])->name('kepegawaian.rekap.dinasLuar');
Route::get('kepegawaian/rekap/{user}/9', [\App\Http\Controllers\Rekap\RekapController::class, 'listIjin'])->name('kepegawaian.rekap.ijin');
Route::get('kepegawaian/rekap/{user}/detail/{id}', [\App\Http\Controllers\Rekap\RekapController::class, 'showDetail'])->name('kepegawaian.rekap.detail');
Route::get('kepegawaian/detail/foto/{id}/{status}', [\App\Http\Controllers\Rekap\RekapController::class, 'showFotoDetail'])->name('kepegawaian.rekap.detailFoto');

// ENDPOINT REMOVE FOTO
Route::get('kepegawaian/absensi/dari/{tgl_dari}/sampai/{tgl_sampai}', [\App\Http\Controllers\Absensi\AbsenController::class, 'removePhoto'])->name('kepegawaian.absensi.removePhoto');

// ENDPOINT BUKTI FOTO
Route::get('/kepegawaian/detail/foto/{filename}', function ($filename) {
    $path = storage_path('app/' . $filename);

    if (!file_exists($path)) abort(404);

    return response()->file($path);
});

Route::get('kepegawaian/jadwal/{user}/{bln}/{thn}', [\App\Http\Controllers\Jadwal\JadwalController::class, 'show'])->name('kepegawaian.jadwal.show');

Route::get('kepegawaian/riwayat/{user}', [\App\Http\Controllers\Riwayat\RiwayatController::class, 'initRiwayat'])->name('kepegawaian.riwayat.initRiwayat');
Route::get('kepegawaian/riwayat/{user}/{id}', [\App\Http\Controllers\Riwayat\RiwayatController::class, 'showRiwayat'])->name('kepegawaian.riwayat.showRiwayat');
