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

Route::get('kepegawaian/absensi/{user}', [\App\Http\Controllers\Absensi\AbsenController::class, 'initAbsensi'])->name('kepegawaian.absensi.initAbsensi');
Route::post('kepegawaian/absensi/distance', [\App\Http\Controllers\Absensi\AbsenController::class, 'getDistance'])->name('kepegawaian.absensi.getDistance');
Route::get('kepegawaian/absensi/validate/jadwal/{user}/pulang', [\App\Http\Controllers\Absensi\AbsenController::class, 'validatePulang'])->name('kepegawaian.absensi.validatePulang');
Route::get('kepegawaian/absensi/validate/jadwal/{user}/{oncall}', [\App\Http\Controllers\Absensi\AbsenController::class, 'validateJadwal'])->name('kepegawaian.absensi.validateJadwal');
Route::get('kepegawaian/absensi/validate/ijin/{user}', [\App\Http\Controllers\Absensi\AbsenController::class, 'validateIjin'])->name('kepegawaian.absensi.validateIjin');

Route::post('kepegawaian/ijin', [\App\Http\Controllers\Absensi\AbsenController::class, 'executeIjin'])->name('kepegawaian.absensi.executeIjin');
Route::post('kepegawaian/berangkat', [\App\Http\Controllers\Absensi\AbsenController::class, 'executeBerangkat'])->name('kepegawaian.absensi.executeBerangkat');
Route::post('kepegawaian/pulang', [\App\Http\Controllers\Absensi\AbsenController::class, 'executePulang'])->name('kepegawaian.absensi.executePulang');

Route::get('kepegawaian/riwayat/{user}', [\App\Http\Controllers\Riwayat\RiwayatController::class, 'initRiwayat'])->name('kepegawaian.riwayat.initRiwayat');
