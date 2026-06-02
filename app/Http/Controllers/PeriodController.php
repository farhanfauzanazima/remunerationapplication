<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /periods
    public function index(Request $request)
    {
        $params = [];
        if ($request->filled('status')) {
            $params['status'] = $request->status;
        }
        if ($request->filled('search')) {
            $params['search'] = $request->search;
        }

        $response = $this->api->get('/payroll-periods', $params);
        $periods  = $response['success'] ? ($response['data'] ?? []) : [];
        $error    = !$response['success'] ? $response['message'] : null;

        return view('periods.index', compact('periods', 'error'));
    }

    // GET /periods/create
    public function create()
    {
        return view('periods.create');
    }

    // POST /periods
    public function store(Request $request)
    {
        $request->validate([
            'period_name' => 'required|string|max:50',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'notes'       => 'nullable|string',
        ], [
            'period_name.required'    => 'Nama periode wajib diisi.',
            'start_date.required'     => 'Tanggal mulai wajib diisi.',
            'end_date.required'       => 'Tanggal akhir wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        ]);

        $response = $this->api->post('/payroll-periods', [
            'period_name' => $request->period_name,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'notes'       => $request->notes,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal menambah periode.')
                ->withInput();
        }

        return redirect()->route('periods.index')
            ->with('success', 'Periode penggajian berhasil ditambahkan.');
    }

    // GET /periods/{id}/edit
    public function edit(int $id)
    {
        $response = $this->api->get('/payroll-periods/' . $id);

        if (!$response['success']) {
            return redirect()->route('periods.index')
                ->with('error', 'Periode tidak ditemukan.');
        }

        $period = $response['data'];

        return view('periods.edit', compact('period'));
    }

    // PUT /periods/{id}
    public function update(Request $request, int $id)
    {
        $request->validate([
            'period_name' => 'required|string|max:50',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'notes'       => 'nullable|string',
        ], [
            'period_name.required'    => 'Nama periode wajib diisi.',
            'start_date.required'     => 'Tanggal mulai wajib diisi.',
            'end_date.required'       => 'Tanggal akhir wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        ]);

        $response = $this->api->put('/payroll-periods/' . $id, [
            'period_name' => $request->period_name,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'notes'       => $request->notes,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal update periode.')
                ->withInput();
        }

        return redirect()->route('periods.index')
            ->with('success', 'Periode penggajian berhasil diperbarui.');
    }

    // DELETE /periods/{id}
    public function destroy(int $id)
    {
        $response = $this->api->delete('/payroll-periods/' . $id);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal menghapus periode.');
        }

        return back()->with('success', 'Periode penggajian berhasil dihapus.');
    }

    // PUT /periods/{id}/close
    public function close(int $id)
    {
        $response = $this->api->put('/payroll-periods/' . $id . '/close');

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal menutup periode.');
        }

        return back()->with('success', 'Periode penggajian berhasil ditutup.');
    }

    // PUT /periods/{id}/reopen
    public function reopen(int $id)
    {
        $response = $this->api->put('/payroll-periods/' . $id . '/reopen');

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal membuka kembali periode.');
        }

        return back()->with('success', 'Periode penggajian berhasil dibuka kembali.');
    }
}