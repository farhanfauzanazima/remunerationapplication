<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalarySlipController;
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

    // Profil & Password — semua role
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
        Route::get('/employees',                     [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',              [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',                    [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{id}/edit',           [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{id}',                [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}',             [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/employees/{id}/salary-history', [EmployeeController::class, 'salaryHistory'])->name('employees.salary-history');
    });

    // Periode — Owner & Head
    Route::middleware('role:owner,head')->group(function () {
        Route::get('/periods',             [PeriodController::class, 'index'])->name('periods.index');
        Route::get('/periods/create',      [PeriodController::class, 'create'])->name('periods.create');
        Route::post('/periods',            [PeriodController::class, 'store'])->name('periods.store');
        Route::get('/periods/{id}/edit',   [PeriodController::class, 'edit'])->name('periods.edit');
        Route::put('/periods/{id}',        [PeriodController::class, 'update'])->name('periods.update');
        Route::delete('/periods/{id}',     [PeriodController::class, 'destroy'])->name('periods.destroy');
        Route::put('/periods/{id}/close',  [PeriodController::class, 'close'])->name('periods.close');
        Route::put('/periods/{id}/reopen', [PeriodController::class, 'reopen'])->name('periods.reopen');
    });

    // Slip Gaji — Semua role
    Route::middleware('role:owner,head,admin')->group(function () {
        Route::get('/salary-slips',                [SalarySlipController::class, 'index'])->name('salary-slips.index');
        Route::get('/salary-slips/create',         [SalarySlipController::class, 'create'])->name('salary-slips.create');
        Route::post('/salary-slips',               [SalarySlipController::class, 'store'])->name('salary-slips.store');
        Route::get('/salary-slips/bulk-create',    [SalarySlipController::class, 'bulkCreate'])->name('salary-slips.bulk-create');
        Route::post('/salary-slips/bulk-generate', [SalarySlipController::class, 'bulkStore'])->name('salary-slips.bulk-generate');
        Route::get('/salary-slips/{id}',           [SalarySlipController::class, 'show'])->name('salary-slips.show');
        Route::get('/salary-slips/{id}/edit',      [SalarySlipController::class, 'edit'])->name('salary-slips.edit');
        Route::put('/salary-slips/{id}',           [SalarySlipController::class, 'update'])->name('salary-slips.update');
        Route::delete('/salary-slips/{id}',        [SalarySlipController::class, 'destroy'])->name('salary-slips.destroy');

        // PDF
        Route::get('/salary-slips/{id}/preview-pdf',   [PdfController::class, 'preview'])->name('salary-slips.preview-pdf');
        Route::get('/salary-slips/{id}/download-pdf',  [PdfController::class, 'download'])->name('salary-slips.download-pdf');
        Route::post('/salary-slips/{id}/generate-pdf', [PdfController::class, 'generate'])->name('salary-slips.generate-pdf');
        Route::get('/pdf/bulk-generate', function () {
            $periods = app(\App\Services\ApiService::class)->get('/payroll-periods')['data'] ?? [];
            return view('salary-slips.bulk-pdf', compact('periods'));
        })->name('pdf.bulk-generate.page');
        Route::post('/pdf/bulk-generate', [PdfController::class, 'bulkGenerate'])->name('pdf.bulk-generate');

        // Email
        Route::get('/emails',                  [EmailController::class, 'index'])->name('emails.index');
        Route::get('/emails/send-bulk',        [EmailController::class, 'showSendBulk'])->name('emails.send-bulk');
        Route::post('/emails/send-bulk',       [EmailController::class, 'sendBulk'])->name('emails.send-bulk.post');
        Route::get('/emails/send/{slipId}',    [EmailController::class, 'showSend'])->name('emails.send');
        Route::post('/emails/send/{slipId}',   [EmailController::class, 'send'])->name('emails.send.post');
        Route::post('/emails/resend/{slipId}', [EmailController::class, 'resend'])->name('emails.resend');
        Route::get('/emails/history/{slipId}', [EmailController::class, 'slipHistory'])->name('emails.slip-history');
    });

    // Laporan — Owner & Head
    Route::middleware('role:owner,head')->group(function () {
        Route::get('/reports',               [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/salary-summary',[ReportController::class, 'salarySummary'])->name('reports.salary-summary');
        Route::get('/reports/export-pdf',    [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports/statistics',    [ReportController::class, 'statistics'])->name('reports.statistics');
        Route::get('/reports/employee/{id}', [ReportController::class, 'employeeReport'])->name('reports.employee');
    });

    // Activity Log — Owner only
    Route::middleware('role:owner')->group(function () {
        Route::get('/activity-logs',      [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

});