<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /reports — halaman utama laporan
    public function index()
    {
        $periods    = $this->api->get('/payroll-periods')['data'] ?? [];
        $statistics = $this->api->get('/reports/statistics')['data'] ?? [];

        return view('reports.index', compact('periods', 'statistics'));
    }

    // GET /reports/salary-summary — rekap gaji per periode
    public function salarySummary(Request $request)
    {
        $request->validate([
            'period_id' => 'required',
        ], [
            'period_id.required' => 'Periode wajib dipilih.',
        ]);

        $response = $this->api->get('/reports/salary-summary', [
            'period_id' => $request->period_id,
        ]);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal mengambil laporan.');
        }

        $report  = $response['data'];
        $periods = $this->api->get('/payroll-periods')['data'] ?? [];

        return view('reports.salary-summary', compact('report', 'periods'));
    }

    // GET /reports/export-pdf — export laporan ke PDF
    public function exportPdf(Request $request)
    {
        $request->validate([
            'period_id' => 'required',
        ]);

        $result = $this->api->download(
            '/reports/salary-summary/export-pdf?period_id=' . $request->period_id
        );

        if (!$result) {
            return back()->with('error', 'Gagal export laporan ke PDF.');
        }

        return $result;
    }

    // GET /reports/employee/{id} — laporan per karyawan
    public function employeeReport(Request $request, int $employeeId)
    {
        $response = $this->api->get('/reports/employee/' . $employeeId, [
            'year' => $request->year,
        ]);

        if (!$response['success']) {
            return redirect()->route('reports.index')
                ->with('error', 'Gagal mengambil laporan karyawan.');
        }

        $report    = $response['data'];
        $employees = $this->api->get('/employees')['data'] ?? [];

        return view('reports.employee', compact('report', 'employees'));
    }

    // GET /reports/statistics — statistik tren gaji
    public function statistics()
    {
        $response = $this->api->get('/reports/statistics');

        $data = $response['success'] ? $response['data'] : [];

        return view('reports.statistics', compact('data'));
    }
}