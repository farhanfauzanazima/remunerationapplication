<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\CategoricalController;
use App\Http\Controllers\HrManagementController;
use App\Http\Controllers\DistributionController;
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

    // ---------- Dashboard ----------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- Profil & Password — semua role ----------
    Route::get('/profile',          [AuthController::class, 'profile'])->name('profile.index');
    Route::put('/profile',          [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('profile.change-password');

    // ---------- Cabang — baca: owner & hr, tulis: owner only ----------
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::middleware('role:owner')->group(function () {
        Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::get('/branches/{id}/edit', [BranchController::class, 'edit'])->name('branches.edit');
        Route::put('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');
    });

    // ---------- Jabatan — owner & hr ----------
    Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
    Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
    Route::put('/positions/{id}', [PositionController::class, 'update'])->name('positions.update');
    Route::delete('/positions/{id}', [PositionController::class, 'destroy'])->name('positions.destroy');

    // ====================================================================
    // BELUM DIROMBAK — placeholder, role sudah benar (owner,hr).
    // Tampilan/form akan disesuaikan field baru di sesi terkait.
    // ====================================================================

    // ---------- Karyawan (Sesi 5) ----------
    Route::middleware('role:owner,hr')->group(function () {
        Route::get('/employees',           [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',    [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',          [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{id}',      [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}',   [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // ---------- Periode (Sesi 6) ----------
    Route::middleware('role:owner,hr')->group(function () {
        Route::get('/periods',           [PeriodController::class, 'index'])->name('periods.index');
        Route::get('/periods/create',    [PeriodController::class, 'create'])->name('periods.create');
        Route::post('/periods',          [PeriodController::class, 'store'])->name('periods.store');
        Route::get('/periods/{id}/edit', [PeriodController::class, 'edit'])->name('periods.edit');
        Route::put('/periods/{id}',      [PeriodController::class, 'update'])->name('periods.update');
        Route::delete('/periods/{id}',   [PeriodController::class, 'destroy'])->name('periods.destroy');
    });

    // ---------- Slip Gaji (Sesi 7 & 8) ----------
    Route::middleware('role:owner,hr')->group(function () {
        Route::get('/salary-slips', [SalarySlipController::class, 'index'])->name('salary-slips.index');
        Route::get('/salary-slips/bulk-create', [SalarySlipController::class, 'bulkCreate'])->name('salary-slips.bulk-create');
        Route::post('/salary-slips/bulk-generate', [SalarySlipController::class, 'bulkStore'])->name('salary-slips.bulk-generate');
        Route::get('/salary-slips/{type}/{id}', [SalarySlipController::class, 'show'])->name('salary-slips.show')->whereIn('type', ['tetap', 'partime']);
        Route::get('/salary-slips/{type}/{id}/edit', [SalarySlipController::class, 'edit'])->name('salary-slips.edit')->whereIn('type', ['tetap', 'partime']);
        Route::put('/salary-slips/{type}/{id}', [SalarySlipController::class, 'update'])->name('salary-slips.update')->whereIn('type', ['tetap', 'partime']);
        Route::delete('/salary-slips/{type}/{id}', [SalarySlipController::class, 'destroy'])->name('salary-slips.destroy')->whereIn('type', ['tetap', 'partime']);
        Route::get('/salary-slips/{type}/{id}/preview-pdf', [SalarySlipController::class, 'previewPdf'])->name('salary-slips.preview-pdf')->whereIn('type', ['tetap', 'partime']);
        Route::get('/salary-slips/{type}/{id}/download-pdf', [SalarySlipController::class, 'downloadPdf'])->name('salary-slips.download-pdf')->whereIn('type', ['tetap', 'partime']);
        Route::post('/salary-slips/{type}/{id}/generate-link', [SalarySlipController::class, 'generateLink'])->name('salary-slips.generate-link')->whereIn('type', ['tetap', 'partime']);
    });

    // ---------- Distribusi Gaji ----------
    Route::middleware('role:owner,hr')->group(function () {
        Route::get('/distribution', [DistributionController::class, 'index'])->name('distribution.index');
        Route::post('/distribution/send-bulk', [DistributionController::class, 'sendBulk'])->name('distribution.send-bulk');
    });

    // ---------- Laporan ----------
    Route::middleware('role:owner,hr')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/salary-summary', [ReportController::class, 'salarySummary'])->name('reports.salary-summary');
        Route::get('/reports/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');
        Route::get('/reports/employee/{id}', [ReportController::class, 'employeeReport'])->name('reports.employee');
    });

    // ---------- Activity Log — Owner only ----------
    Route::middleware('role:owner')->group(function () {
        Route::get('/activity-logs',      [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    Route::get('/salary-slips/create', [SalarySlipController::class, 'create'])
        ->name('salary-slips.create');

    // ---------- Kategorikal — owner & hr ----------
    Route::get('/categorical', [CategoricalController::class, 'index'])->name('categorical.index');
    Route::put('/categorical', [CategoricalController::class, 'update'])->name('categorical.update');

    // ---------- Manajemen HR — Owner only ----------
    Route::middleware('role:owner')->prefix('hr-management')->group(function () {
        Route::get('/', [HrManagementController::class, 'index'])->name('hr-management.index');
        Route::post('/', [HrManagementController::class, 'store'])->name('hr-management.store');
        Route::put('/{id}', [HrManagementController::class, 'update'])->name('hr-management.update');
        Route::post('/{id}/reset-password', [HrManagementController::class, 'resetPassword'])->name('hr-management.reset-password');
        Route::delete('/{id}', [HrManagementController::class, 'destroy'])->name('hr-management.destroy');
    });

    Route::post('/distribution/resend/{id}', [DistributionController::class, 'resend'])->name('distribution.resend');
});
