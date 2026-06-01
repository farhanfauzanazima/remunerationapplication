<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ─── Redirect root ────────────────────────────────────────
Route::get('/', function () {
    if (session('api_token')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth.login');
});

// ─── Auth Routes ──────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

// ─── Logout ───────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth.api');

// ─── Protected Routes ─────────────────────────────────────
Route::middleware('auth.api')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/categories',    fn() => view('coming-soon'))->name('categories.index');
    Route::get('/employees',     fn() => view('coming-soon'))->name('employees.index');
    Route::get('/periods',       fn() => view('coming-soon'))->name('periods.index');
    Route::get('/salary-slips',  fn() => view('coming-soon'))->name('salary-slips.index');
    Route::get('/emails',        fn() => view('coming-soon'))->name('emails.index');
    Route::get('/reports',       fn() => view('coming-soon'))->name('reports.index');
    Route::get('/activity-logs', fn() => view('coming-soon'))->name('activity-logs.index');
    Route::get('/profile',       fn() => view('coming-soon'))->name('profile.index');

});