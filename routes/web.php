<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (session('api_token') && session('user')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth.login');
});

Route::get('/login',  [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth.api');

Route::middleware('auth.api')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',          [AuthController::class, 'profile'])->name('profile.index');
    Route::put('/profile',          [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

    // Kategori Gaji — Owner only
    Route::middleware('role:owner')->group(function () {
        Route::get('/categories',           [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create',    [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories',          [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}',      [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}',   [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Karyawan — Owner & Head
    Route::middleware('role:owner,head')->group(function () {
        Route::get('/employees',                      [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',               [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',                     [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}/edit',            [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{id}',                 [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}',              [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/employees/{id}/salary-history',  [EmployeeController::class, 'salaryHistory'])->name('employees.salary-history');
    });

    // Placeholder
    Route::get('/periods',       fn() => view('coming-soon'))->name('periods.index');
    Route::get('/salary-slips',  fn() => view('coming-soon'))->name('salary-slips.index');
    Route::get('/emails',        fn() => view('coming-soon'))->name('emails.index');
    Route::get('/reports',       fn() => view('coming-soon'))->name('reports.index');
    Route::get('/activity-logs', fn() => view('coming-soon'))->name('activity-logs.index');

});