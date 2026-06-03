<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /emails
    public function index(Request $request)
    {
        $params = [];
        if ($request->filled('status'))      $params['status']      = $request->status;
        if ($request->filled('period_id'))   $params['period_id']   = $request->period_id;
        if ($request->filled('employee_id')) $params['employee_id'] = $request->employee_id;

        $response  = $this->api->get('/email/history', $params);
        $histories = $response['success'] ? ($response['raw']['data'] ?? []) : [];
        $pagination = $response['success'] ? ($response['raw']['pagination'] ?? []) : [];
        $error     = !$response['success'] ? $response['message'] : null;

        // Data untuk filter
        $periods   = $this->api->get('/payroll-periods')['data'] ?? [];
        $employees = $this->api->get('/employees')['data'] ?? [];

        return view('emails.index', compact(
            'histories', 'pagination', 'periods', 'employees', 'error'
        ));
    }

    // GET /emails/send/{slipId} — form konfirmasi kirim single
    public function showSend(int $slipId)
    {
        $response = $this->api->get('/salary-slips/' . $slipId);

        if (!$response['success']) {
            return redirect()->route('salary-slips.index')
                ->with('error', 'Slip gaji tidak ditemukan.');
        }

        $slip = $response['data'];

        return view('emails.send', compact('slip'));
    }

    // POST /emails/send/{slipId} — kirim email ke satu karyawan
    public function send(int $slipId)
    {
        $response = $this->api->post('/email/send/' . $slipId);

        if (!$response['success']) {
            return redirect()->route('salary-slips.show', $slipId)
                ->with('error', $response['message'] ?? 'Gagal mengirim email.');
        }

        $data = $response['raw'];

        return redirect()->route('salary-slips.show', $slipId)
            ->with('success', 'Slip gaji berhasil dikirim ke '
                . ($data['data']['email_to'] ?? 'karyawan') . '.');
    }

    // GET /emails/send-bulk — halaman kirim bulk
    public function showSendBulk(Request $request)
    {
        $periods = $this->api->get('/payroll-periods')['data'] ?? [];

        // Jika ada period_id, ambil slip gaji yang belum terkirim
        $slips      = [];
        $selectedId = $request->period_id;

        if ($selectedId) {
            $slipsResp = $this->api->get('/salary-slips', [
                'period_id' => $selectedId,
                'status'    => 'draft',
            ]);
            $slips = $slipsResp['data'] ?? [];
        }

        return view('emails.send-bulk', compact('periods', 'slips', 'selectedId'));
    }

    // POST /emails/send-bulk — kirim email massal
    public function sendBulk(Request $request)
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

        $response = $this->api->post('/email/send-bulk', $payload);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal mengirim email massal.');
        }

        $summary = $response['raw']['data']['summary'] ?? [];
        $message = ($summary['success'] ?? 0) . ' email berhasil dikirim';
        if (($summary['failed'] ?? 0) > 0) {
            $message .= ', ' . ($summary['failed']) . ' gagal';
        }

        return redirect()->route('emails.index')
            ->with('success', $message . '.');
    }

    // POST /emails/resend/{slipId} — kirim ulang
    public function resend(int $slipId)
    {
        $response = $this->api->post('/email/resend/' . $slipId);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal mengirim ulang email.');
        }

        $data = $response['raw'];

        return back()->with('success', 'Email berhasil dikirim ulang ke '
            . ($data['data']['email_to'] ?? 'karyawan') . '.');
    }

    // GET /emails/history/{slipId} — riwayat per slip
    public function slipHistory(int $slipId)
    {
        $slipResp    = $this->api->get('/salary-slips/' . $slipId);
        $histResp    = $this->api->get('/email/history/' . $slipId);

        if (!$slipResp['success']) {
            return redirect()->route('emails.index')
                ->with('error', 'Slip tidak ditemukan.');
        }

        $slip      = $slipResp['data'];
        $histories = $histResp['success'] ? ($histResp['raw']['data']['histories'] ?? []) : [];

        return view('emails.slip-history', compact('slip', 'histories'));
    }
}