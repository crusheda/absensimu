<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/demo', function () {
    return view('pages.index');
});

Auth::routes(['register' => false]);
// Auth::routes();

Route::get('/signin', [App\Http\Controllers\Auth\AuthController::class, 'index'])->name('auth.signin');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', [App\Http\Controllers\Absensi\AbsenController::class, 'index'])->name('absensi.index');
    Route::get('/riwayat', [App\Http\Controllers\Riwayat\RiwayatController::class, 'index'])->name('riwayat.index');

    Route::get('/test', function () {
        return view('pages.absensi.index_def');
    });
});

Route::get('/manifest.json', [App\Http\Controllers\HomeController::class, 'manifest']);
