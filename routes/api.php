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
Route::get('kepegawaian/rekap/{user}/detail/{id}', [\App\Http\Controllers\Rekap\RekapController::class, 'showDetail'])->name('kepegawaian.rekap.detail');
Route::get('kepegawaian/detail/foto/{id}/{status}', [\App\Http\Controllers\Rekap\RekapController::class, 'showFotoDetail'])->name('kepegawaian.rekap.detailFoto');

// ENDPOINT BUKTI FOTO
Route::get('/kepegawaian/detail/foto/{filename}', function ($filename) {
    $path = storage_path('app/' . $filename);

    if (!file_exists($path)) abort(404);

    return response()->file($path);
});

Route::get('kepegawaian/jadwal/{user}/{bln}/{thn}', [\App\Http\Controllers\Jadwal\JadwalController::class, 'show'])->name('kepegawaian.jadwal.show');

Route::get('kepegawaian/riwayat/{user}', [\App\Http\Controllers\Riwayat\RiwayatController::class, 'initRiwayat'])->name('kepegawaian.riwayat.initRiwayat');
Route::get('kepegawaian/riwayat/{user}/{id}', [\App\Http\Controllers\Riwayat\RiwayatController::class, 'showRiwayat'])->name('kepegawaian.riwayat.showRiwayat');
