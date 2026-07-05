<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class SalarySlipController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function bulkCreate(Request $request)
    {
        $periods = $this->api->get('/payroll-periods');
        $branches = $this->api->get('/branches');

        $bulkData = null;
        if ($request->filled('payroll_period_id') && $request->filled('branch_id')) {
            $response = $this->api->get('/salary-slips/bulk-data', [
                'payroll_period_id' => $request->input('payroll_period_id'),
                'branch_id' => $request->input('branch_id'),
            ]);
            $bulkData = $response['success'] ? $response['data'] : null;
        }

        return view('salary-slips.bulk-create', [
            'periods' => $periods['success'] ? $periods['data'] : [],
            'branches' => $branches['success'] ? $branches['data'] : [],
            'selectedPeriod' => $request->input('payroll_period_id'),
            'selectedBranch' => $request->input('branch_id'),
            'bulkData' => $bulkData,
        ]);
    }

    public function bulkStore(Request $request)
    {
        $payload = [
            'payroll_period_id' => $request->input('payroll_period_id'),
            'branch_id' => $request->input('branch_id'),
            'tetap' => array_values($request->input('tetap', [])),
            'partime' => array_values($request->input('partime', [])),
        ];

        $response = $this->api->post('/salary-slips/bulk-generate', $payload);

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal menyimpan slip gaji massal');
        }

        return redirect()->route('salary-slips.bulk-create', [
            'payroll_period_id' => $payload['payroll_period_id'],
            'branch_id' => $payload['branch_id'],
        ])->with('success', $response['message']);
    }
}