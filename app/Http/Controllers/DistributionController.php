<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index(Request $request)
    {
        $periods = $this->api->get('/payroll-periods');
        $branches = $this->api->get('/branches');

        $slipData = null;
        if ($request->filled('payroll_period_id') && $request->filled('branch_id')) {
            $response = $this->api->get('/salary-slips', [
                'payroll_period_id' => $request->input('payroll_period_id'),
                'branch_id' => $request->input('branch_id'),
            ]);
            $slipData = $response['success'] ? $response['data'] : null;
        }

        $history = $this->api->get('/distribution/history');

        return view('distribution.index', [
            'periods' => $periods['success'] ? $periods['data'] : [],
            'branches' => $branches['success'] ? $branches['data'] : [],
            'selectedPeriod' => $request->input('payroll_period_id'),
            'selectedBranch' => $request->input('branch_id'),
            'slipData' => $slipData,
            'history' => $history['success'] ? $history['data'] : [],
        ]);
    }

    public function sendBulk(Request $request)
    {
        $items = [];
        foreach ($request->input('tetap_ids', []) as $id) {
            $items[] = ['type' => 'tetap', 'id' => $id];
        }
        foreach ($request->input('partime_ids', []) as $id) {
            $items[] = ['type' => 'partime', 'id' => $id];
        }

        if (empty($items)) {
            return back()->with('error', 'Pilih minimal satu karyawan untuk dikirim');
        }

        $response = $this->api->post('/distribution/send-bulk', [
            'channel' => $request->input('channel'),
            'items' => $items,
        ]);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal mengirim distribusi');
        }

        $totalSukses = collect($response['data'])->where('success', true)->count();
        $totalGagal = collect($response['data'])->where('success', false)->count();

        return back()->with('success', "Distribusi selesai: {$totalSukses} berhasil, {$totalGagal} gagal.");
    }
}