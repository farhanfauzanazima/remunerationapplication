<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    protected array $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function __construct(protected ApiService $api) {}

    public function index(Request $request)
    {
        $response = $this->api->get('/payroll-periods', $request->only(['search', 'year']));

        return view('periods.index', [
            'periods' => $response['success'] ? $response['data'] : [],
            'bulanIndo' => $this->bulanIndo,
            'filters' => $request->only(['search', 'year']),
        ]);
    }

    public function create()
    {
        return view('periods.create', ['bulanIndo' => $this->bulanIndo]);
    }

    public function store(Request $request)
    {
        $response = $this->api->post('/payroll-periods', $request->only('name', 'month', 'year', 'notes'));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal menambahkan periode');
        }

        return redirect()->route('periods.index')->with('success', 'Periode penggajian berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $response = $this->api->get("/payroll-periods/{$id}");

        if (!$response['success']) {
            return redirect()->route('periods.index')->with('error', 'Periode tidak ditemukan');
        }

        return view('periods.edit', [
            'period' => $response['data'],
            'bulanIndo' => $this->bulanIndo,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api->put("/payroll-periods/{$id}", $request->only('name', 'month', 'year', 'notes'));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal memperbarui periode');
        }

        return redirect()->route('periods.index')->with('success', 'Periode penggajian berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $response = $this->api->delete("/payroll-periods/{$id}");

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}