<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DemoDataController;
use App\Http\Controllers\DriverApplicationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\TransportRequestController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::redirect('/overview', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [OverviewController::class, 'index'])->name('dashboard');

    Route::get('/requests', [TransportRequestController::class, 'index'])->name('requests.index');
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('/applications', [DriverApplicationController::class, 'index'])->name('applications.index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    Route::get('/help', [HelpController::class, 'index'])->name('help');
    Route::get('/account', [AccountController::class, 'edit'])->name('account');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');

    Route::post('/demo/reset', [DemoDataController::class, 'reset'])->name('demo.reset');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
