<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /salary-slips/{id}/preview-pdf
    // Tampilkan PDF inline di browser
    public function preview(int $id)
    {
        $result = $this->api->stream('/salary-slips/' . $id . '/preview-pdf');

        if (!$result) {
            return redirect()->route('salary-slips.show', $id)
                ->with('error', 'Gagal memuat PDF. Pastikan slip sudah di-generate.');
        }

        return $result;
    }

    // GET /salary-slips/{id}/download-pdf
    // Download PDF slip gaji
    public function download(int $id)
    {
        $result = $this->api->download('/salary-slips/' . $id . '/download-pdf');

        if (!$result) {
            return redirect()->route('salary-slips.show', $id)
                ->with('error', 'Gagal mendownload PDF.');
        }

        return $result;
    }

    // POST /salary-slips/{id}/generate-pdf
    // Generate & simpan PDF di backend
    public function generate(int $id)
    {
        $response = $this->api->post('/salary-slips/' . $id . '/generate-pdf');

        if (!$response['success']) {
            return redirect()->route('salary-slips.show', $id)
                ->with('error', $response['message'] ?? 'Gagal generate PDF.');
        }

        return redirect()->route('salary-slips.show', $id)
            ->with('success', 'PDF slip gaji berhasil digenerate.');
    }

    // POST /pdf/bulk-generate
    // Generate PDF semua slip dalam satu periode
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'period_id' => 'required',
        ], [
            'period_id.required' => 'Periode wajib dipilih.',
        ]);

        $payload = ['period_id' => $request->period_id];

        if ($request->filled('slip_ids')) {
            $payload['slip_ids'] = $request->slip_ids;
        }

        $response = $this->api->post('/salary-slips/bulk-generate-pdf', $payload);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal bulk generate PDF.');
        }

        $summary = $response['data']['summary'] ?? [];
        $message = ($summary['success'] ?? 0) . ' PDF berhasil digenerate';
        if (($summary['failed'] ?? 0) > 0) {
            $message .= ', ' . $summary['failed'] . ' gagal';
        }

        return redirect()->route('salary-slips.index',
                ['period_id' => $request->period_id])
            ->with('success', $message . '.');
    }
}