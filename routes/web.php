<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ─── Redirect root ────────────────────────────────────────
Route::get('/', function () {
    if (session('api_token') && session('user')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth.login');
});

// ─── Auth Routes (tanpa perlu login) ─────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

// ─── Logout ───────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth.api');

// ─── Protected Routes (wajib login) ──────────────────────
Route::middleware('auth.api')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Profile & Password (semua role)
    Route::get('/profile',          [AuthController::class, 'profile'])->name('profile.index');
    Route::put('/profile',          [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

    // Placeholder routes (akan diisi di sesi berikutnya)
    Route::get('/categories',    fn() => view('coming-soon'))->name('categories.index');
    Route::get('/employees',     fn() => view('coming-soon'))->name('employees.index');
    Route::get('/periods',       fn() => view('coming-soon'))->name('periods.index');
    Route::get('/salary-slips',  fn() => view('coming-soon'))->name('salary-slips.index');
    Route::get('/emails',        fn() => view('coming-soon'))->name('emails.index');
    Route::get('/reports',       fn() => view('coming-soon'))->name('reports.index');
    Route::get('/activity-logs', fn() => view('coming-soon'))->name('activity-logs.index');

});