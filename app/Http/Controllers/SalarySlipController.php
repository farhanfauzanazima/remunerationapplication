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
        $selectedPeriodData = null;

        if ($request->filled('payroll_period_id') && $request->filled('branch_id')) {
            $response = $this->api->get('/salary-slips/bulk-data', [
                'payroll_period_id' => $request->input('payroll_period_id'),
                'branch_id' => $request->input('branch_id'),
            ]);
            $bulkData = $response['success'] ? $response['data'] : null;

            // Cari data periode yang dipilih (butuh month & year untuk kalkulasi live di JS)
            if ($periods['success']) {
                $selectedPeriodData = collect($periods['data'])
                    ->firstWhere('id', (int) $request->input('payroll_period_id'));
            }
        }

        return view('salary-slips.bulk-create', [
            'periods' => $periods['success'] ? $periods['data'] : [],
            'branches' => $branches['success'] ? $branches['data'] : [],
            'selectedPeriod' => $request->input('payroll_period_id'),
            'selectedBranch' => $request->input('branch_id'),
            'bulkData' => $bulkData,
            'selectedPeriodData' => $selectedPeriodData,
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

    public function index(Request $request)
    {
        $filters = $request->only(['payroll_period_id', 'employee_search', 'status', 'branch_id', 'tenure']);

        $response = $this->api->get('/salary-slips', array_merge($filters, [
            'page_tetap' => $request->input('page_tetap', 1),
            'page_partime' => $request->input('page_partime', 1),
        ]));

        $periods = $this->api->get('/payroll-periods');
        $branches = $this->api->get('/branches');

        return view('salary-slips.index', [
            'tetap' => $response['success'] ? ($response['data']['tetap'] ?? []) : [],
            'partime' => $response['success'] ? ($response['data']['partime'] ?? []) : [],
            'pagination' => $response['raw']['raw']['pagination'] ?? null, // <-- diperbaiki, sebelumnya $response['raw']['pagination']
            'periods' => $periods['success'] ? $periods['data'] : [],
            'branches' => $branches['success'] ? $branches['data'] : [],
            'filters' => $filters,
        ]);
    }
    public function show(string $type, string $id)
    {
        $response = $this->api->get("/salary-slips/{$type}/{$id}");

        if (!$response['success']) {
            return redirect()->route('salary-slips.index')->with('error', 'Slip gaji tidak ditemukan');
        }

        return view('salary-slips.show', ['slip' => $response['data'], 'type' => $type]);
    }

    public function edit(string $type, string $id)
    {
        $response = $this->api->get("/salary-slips/{$type}/{$id}");

        if (!$response['success']) {
            return redirect()->route('salary-slips.index')->with('error', 'Slip gaji tidak ditemukan');
        }

        return view('salary-slips.edit', ['slip' => $response['data'], 'type' => $type]);
    }

    public function update(Request $request, string $type, string $id)
    {
        $fields = $type === 'tetap'
            ? [
                'hari_kerja',
                'alfa',
                'izin',
                'sakit',
                'off',
                'hari_shift',
                'hari_full',
                'hari_parsial',
                'nominal_shift',
                'nominal_full',
                'nominal_parsial',
                'jam_lembur',
                'telat',
                'tunjangan_jabatan',
                'tunjangan_bpjs',
                'bonus_omset',
                'bonus_kinerja',
                'cashbond',
                'tabungan'
            ]
            : ['hari_kerja', 'full', 'shift', 'reguler', 'sakit', 'off', 'tunjangan', 'bonus'];

        $response = $this->api->put("/salary-slips/{$type}/{$id}", $request->only($fields));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal memperbarui slip gaji');
        }

        return redirect()->route('salary-slips.show', ['type' => $type, 'id' => $id])->with('success', 'Slip gaji berhasil diperbarui');
    }

    public function destroy(string $type, string $id)
    {
        $response = $this->api->delete("/salary-slips/{$type}/{$id}");

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function previewPdf(string $type, string $id)
    {
        return $this->api->stream("/salary-slips/{$type}/{$id}/preview-pdf");
    }

    public function downloadPdf(string $type, string $id)
    {
        return $this->api->download("/salary-slips/{$type}/{$id}/download-pdf");
    }

    public function generateLink(string $type, string $id)
    {
        $response = $this->api->post("/salary-slips/{$type}/{$id}/generate-link", []);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal membuat link publik');
        }

        return back()->with('success', 'Link publik: ' . $response['data']['url']);
    }
}
