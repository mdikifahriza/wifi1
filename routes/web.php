<?php

use App\Http\Controllers\FetcHTPP;
use App\Http\Controllers\Qrgenerator;
use App\Http\Controllers\simulateSubmit;
use App\Http\Controllers\Webhookendp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HotspotController;

Route::get('/', function () {
    return view('LOG');
});

Route::post('/Generateqr', [OrderController::class, 'generate'])->name('generate.qr');

Route::get('/formsimulation',[FetcHTPP::class,'formSimulator'])->name('form.simulation'); // dapatkan form dari simulator
Route::post('/submit/midtrans/simulation',[simulateSubmit::class,'sendForm'])->name('midtrans.submit'); //submit url qr ke simulator
Route::post('/submit/midtrans/pollstatus',[Webhookendp::class,'getData'])->name('midtrans.statusNotif'); //polling untuk user
Route::get('/admin', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Grup route admin (hanya bisa diakses setelah login)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/orders', [AdminController::class, 'showOrders'])->name('admin.orders');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

Route::get('/countdown', [HotspotController::class, 'show'])->name('hotspot.countdown');

// Jalankan hotspot (dipanggil oleh countdown.js)
Route::post('/hotspot/start', [HotspotController::class, 'start'])->name('hotspot.start');

// Matikan hotspot otomatis setelah waktu habis
Route::post('/hotspot/stop', [HotspotController::class, 'stop'])->name('hotspot.stop');