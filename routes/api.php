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
Route::get('kepegawaian/absensi/validate/jadwal/{user}', [\App\Http\Controllers\Absensi\AbsenController::class, 'validateJadwal'])->name('kepegawaian.absensi.validateJadwal');
Route::post('kepegawaian/absensi', [\App\Http\Controllers\Absensi\AbsenController::class, 'executeAbsensi'])->name('kepegawaian.absensi.executeAbsensi');
