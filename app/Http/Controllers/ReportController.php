<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        return view('reports.index');
    }

    public function salarySummary(Request $request)
    {
        $periods = $this->api->get('/payroll-periods');
        $branches = $this->api->get('/branches');

        $summary = null;
        if ($request->filled('payroll_period_id')) {
            $response = $this->api->get('/reports/salary-summary', $request->only('payroll_period_id', 'branch_id'));
            $summary = $response['success'] ? $response['data'] : null;
        }

        return view('reports.salary-summary', [
            'periods' => $periods['success'] ? $periods['data'] : [],
            'branches' => $branches['success'] ? $branches['data'] : [],
            'summary' => $summary,
            'filters' => $request->only('payroll_period_id', 'branch_id'),
        ]);
    }

    public function statistics()
    {
        $response = $this->api->get('/reports/statistics');

        return view('reports.statistics', [
            'trend' => $response['success'] ? ($response['data']['overall'] ?? []) : [],
        ]);
    }
    public function employeeReport(string $id)
    {
        $response = $this->api->get("/reports/employee/{$id}");

        return view('reports.employee', [
            'report' => $response['success'] ? $response['data'] : null,
        ]);
    }

    public function financeSummary(Request $request)
    {
        $periods = $this->api->get('/payroll-periods');
        $branches = $this->api->get('/branches');

        $report = null;
        if ($request->filled('payroll_period_id') && $request->filled('branch_id')) {
            $response = $this->api->get('/reports/finance-summary', $request->only('payroll_period_id', 'branch_id'));
            $report = $response['success'] ? $response['data'] : null;
        }

        return view('reports.finance-summary', [
            'periods' => $periods['success'] ? $periods['data'] : [],
            'branches' => $branches['success'] ? $branches['data'] : [],
            'report' => $report,
            'filters' => $request->only('payroll_period_id', 'branch_id'),
        ]);
    }

    public function financeSummaryPreviewPdf(Request $request)
    {
        return $this->api->stream('/reports/finance-summary/preview-pdf?' . http_build_query($request->only('payroll_period_id', 'branch_id')));
    }

    public function financeSummaryDownloadPdf(Request $request)
    {
        return $this->api->download('/reports/finance-summary/download-pdf?' . http_build_query($request->only('payroll_period_id', 'branch_id')));
    }
}