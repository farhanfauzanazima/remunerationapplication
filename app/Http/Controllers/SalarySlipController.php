<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class SalarySlipController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /salary-slips
    public function index(Request $request)
    {
        $params = [];
        if ($request->filled('period_id'))   $params['period_id']   = $request->period_id;
        if ($request->filled('status'))      $params['status']      = $request->status;
        if ($request->filled('employee_id')) $params['employee_id'] = $request->employee_id;

        $response  = $this->api->get('/salary-slips', $params);
        $slips     = $response['success'] ? ($response['data'] ?? []) : [];
        $error     = !$response['success'] ? $response['message'] : null;

        // Data untuk filter dropdown
        $periods    = $this->api->get('/payroll-periods')['data'] ?? [];
        $employees  = $this->api->get('/employees')['data'] ?? [];

        return view('salary-slips.index', compact('slips', 'periods', 'employees', 'error'));
    }

    // GET /salary-slips/create
    public function create(Request $request)
    {
        $periods   = $this->api->get('/payroll-periods', ['status' => 'open'])['data'] ?? [];
        $employees = $this->api->get('/employees', ['status' => 'active'])['data'] ?? [];
        $categories = $this->api->get('/salary-categories')['data'] ?? [];

        $selectedPeriod = $request->period_id;

        return view('salary-slips.create', compact(
            'periods', 'employees', 'categories', 'selectedPeriod'
        ));
    }

    // POST /salary-slips
    public function store(Request $request)
    {
        $request->validate([
            'period_id'            => 'required',
            'employee_id'          => 'required',
            'category_id'          => 'required',
            'total_working_days'   => 'required|integer|min:0|max:31',
            'late_count'           => 'nullable|integer|min:0',
            'bonus'                => 'nullable|numeric|min:0',
            'additional_deduction' => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string',
        ], [
            'period_id.required'          => 'Periode penggajian wajib dipilih.',
            'employee_id.required'        => 'Karyawan wajib dipilih.',
            'category_id.required'        => 'Kategori gaji wajib dipilih.',
            'total_working_days.required' => 'Total hari kerja wajib diisi.',
            'total_working_days.max'      => 'Total hari kerja maksimal 31 hari.',
        ]);

        $response = $this->api->post('/salary-slips', [
            'period_id'            => $request->period_id,
            'employee_id'          => $request->employee_id,
            'category_id'          => $request->category_id,
            'total_working_days'   => $request->total_working_days,
            'late_count'           => $request->late_count ?? 0,
            'bonus'                => $request->bonus ?? 0,
            'additional_deduction' => $request->additional_deduction ?? 0,
            'notes'                => $request->notes,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal membuat slip gaji.')
                ->withInput();
        }

        return redirect()->route('salary-slips.index',
                ['period_id' => $request->period_id])
            ->with('success', 'Slip gaji berhasil dibuat.');
    }

    // GET /salary-slips/{id}
    public function show(int $id)
    {
        $response = $this->api->get('/salary-slips/' . $id);

        if (!$response['success']) {
            return redirect()->route('salary-slips.index')
                ->with('error', 'Slip gaji tidak ditemukan.');
        }

        $slip = $response['data'];

        return view('salary-slips.show', compact('slip'));
    }

    // GET /salary-slips/{id}/edit
    public function edit(int $id)
    {
        $response = $this->api->get('/salary-slips/' . $id);

        if (!$response['success']) {
            return redirect()->route('salary-slips.index')
                ->with('error', 'Slip gaji tidak ditemukan.');
        }

        $slip       = $response['data'];
        $categories = $this->api->get('/salary-categories')['data'] ?? [];

        return view('salary-slips.edit', compact('slip', 'categories'));
    }

    // PUT /salary-slips/{id}
    public function update(Request $request, int $id)
    {
        $request->validate([
            'category_id'          => 'required',
            'total_working_days'   => 'required|integer|min:0|max:31',
            'late_count'           => 'nullable|integer|min:0',
            'bonus'                => 'nullable|numeric|min:0',
            'additional_deduction' => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string',
        ]);

        $response = $this->api->put('/salary-slips/' . $id, [
            'category_id'          => $request->category_id,
            'total_working_days'   => $request->total_working_days,
            'late_count'           => $request->late_count ?? 0,
            'bonus'                => $request->bonus ?? 0,
            'additional_deduction' => $request->additional_deduction ?? 0,
            'notes'                => $request->notes,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal update slip gaji.')
                ->withInput();
        }

        return redirect()->route('salary-slips.show', $id)
            ->with('success', 'Slip gaji berhasil diperbarui.');
    }

    // DELETE /salary-slips/{id}
    public function destroy(int $id)
    {
        $response = $this->api->delete('/salary-slips/' . $id);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal menghapus slip gaji.');
        }

        return redirect()->route('salary-slips.index')
            ->with('success', 'Slip gaji berhasil dihapus.');
    }

    // GET /salary-slips/bulk-create
    public function bulkCreate(Request $request)
    {
        $periods    = $this->api->get('/payroll-periods', ['status' => 'open'])['data'] ?? [];
        $employees  = $this->api->get('/employees', ['status' => 'active'])['data'] ?? [];
        $categories = $this->api->get('/salary-categories')['data'] ?? [];

        $selectedPeriod = $request->period_id;

        return view('salary-slips.bulk-create', compact(
            'periods', 'employees', 'categories', 'selectedPeriod'
        ));
    }

    // POST /salary-slips/bulk-generate
    public function bulkStore(Request $request)
    {
        $request->validate([
            'period_id'              => 'required',
            'employees'              => 'required|array|min:1',
            'employees.*.employee_id'=> 'required',
            'employees.*.category_id'=> 'required',
            'employees.*.total_working_days' => 'required|integer|min:0|max:31',
        ], [
            'period_id.required'   => 'Periode penggajian wajib dipilih.',
            'employees.required'   => 'Minimal satu karyawan harus diinput.',
            'employees.min'        => 'Minimal satu karyawan harus diinput.',
        ]);

        $response = $this->api->post('/salary-slips/bulk-generate', [
            'period_id' => $request->period_id,
            'employees' => $request->employees,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal generate slip gaji.')
                ->withInput();
        }

        $summary = $response['data']['summary'] ?? [];
        $message = ($summary['success'] ?? 0) . ' slip berhasil dibuat';
        if (($summary['failed'] ?? 0) > 0) {
            $message .= ', ' . $summary['failed'] . ' gagal';
        }

        return redirect()->route('salary-slips.index',
                ['period_id' => $request->period_id])
            ->with('success', $message . '.');
    }
}