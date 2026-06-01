<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /employees
    public function index(Request $request)
    {
        $params = [];
        if ($request->filled('search')) {
            $params['search'] = $request->search;
        }
        if ($request->filled('status')) {
            $params['status'] = $request->status;
        }
        if ($request->filled('category_id')) {
            $params['category_id'] = $request->category_id;
        }

        $response   = $this->api->get('/employees', $params);
        $employees  = $response['success'] ? ($response['data'] ?? []) : [];
        $error      = !$response['success'] ? $response['message'] : null;

        // Ambil kategori untuk filter dropdown
        $catResponse = $this->api->get('/salary-categories');
        $categories  = $catResponse['success'] ? ($catResponse['data'] ?? []) : [];

        return view('employees.index', compact('employees', 'categories', 'error'));
    }

    // GET /employees/create
    public function create()
    {
        $catResponse = $this->api->get('/salary-categories');
        $categories  = $catResponse['success'] ? ($catResponse['data'] ?? []) : [];

        return view('employees.create', compact('categories'));
    }

    // POST /employees
    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required',
            'full_name'     => 'required|string|max:100',
            'employee_code' => 'nullable|string|max:20',
            'email'         => 'required|email',
            'phone'         => 'required|string|max:15',
            'join_date'     => 'nullable|date',
        ], [
            'category_id.required' => 'Kategori gaji wajib dipilih.',
            'full_name.required'   => 'Nama lengkap wajib diisi.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'phone.required'       => 'Nomor telepon wajib diisi.',
        ]);

        $response = $this->api->post('/employees', [
            'category_id'   => $request->category_id,
            'full_name'     => $request->full_name,
            'employee_code' => $request->employee_code,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'join_date'     => $request->join_date,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal menambah karyawan.')
                ->withInput();
        }

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    // GET /employees/{id}/edit
    public function edit(int $id)
    {
        $response = $this->api->get('/employees/' . $id);

        if (!$response['success']) {
            return redirect()->route('employees.index')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }

        $employee    = $response['data'];
        $catResponse = $this->api->get('/salary-categories');
        $categories  = $catResponse['success'] ? ($catResponse['data'] ?? []) : [];

        return view('employees.edit', compact('employee', 'categories'));
    }

    // PUT /employees/{id}
    public function update(Request $request, int $id)
    {
        $request->validate([
            'category_id'   => 'required',
            'full_name'     => 'required|string|max:100',
            'employee_code' => 'nullable|string|max:20',
            'email'         => 'required|email',
            'phone'         => 'required|string|max:15',
            'join_date'     => 'nullable|date',
            'status'        => 'required|in:active,inactive',
        ], [
            'category_id.required' => 'Kategori gaji wajib dipilih.',
            'full_name.required'   => 'Nama lengkap wajib diisi.',
            'email.required'       => 'Email wajib diisi.',
            'phone.required'       => 'Nomor telepon wajib diisi.',
            'status.required'      => 'Status wajib dipilih.',
        ]);

        $response = $this->api->put('/employees/' . $id, [
            'category_id'   => $request->category_id,
            'full_name'     => $request->full_name,
            'employee_code' => $request->employee_code,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'join_date'     => $request->join_date,
            'status'        => $request->status,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal update karyawan.')
                ->withInput();
        }

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    // DELETE /employees/{id}
    public function destroy(int $id)
    {
        $response = $this->api->delete('/employees/' . $id);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal menghapus karyawan.');
        }

        return back()->with('success', 'Data karyawan berhasil dihapus.');
    }

    // GET /employees/{id}/salary-history
    public function salaryHistory(int $id)
    {
        $empResponse = $this->api->get('/employees/' . $id);

        if (!$empResponse['success']) {
            return redirect()->route('employees.index')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }

        $employee    = $empResponse['data'];

        $histResponse = $this->api->get('/employees/' . $id . '/salary-history');
        $history      = $histResponse['success'] ? ($histResponse['data'] ?? []) : [];

        return view('employees.salary-history', compact('employee', 'history'));
    }
}