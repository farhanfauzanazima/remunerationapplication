<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index(Request $request)
    {
        $query = $request->only(['search', 'branch_id', 'employee_type', 'status', 'tenure']);
        $response = $this->api->get('/employees', $query);

        $branchResponse = $this->api->get('/branches');

        return view('employees.index', [
            'employees' => $response['success'] ? $response['data'] : [],
            'pagination' => $response['raw']['raw']['pagination'] ?? null, // <-- diperbaiki
            'branches' => $branchResponse['success'] ? $branchResponse['data'] : [],
            'filters' => $query,
        ]);
    }
    public function create()
    {
        $branches = $this->api->get('/branches');
        $positions = $this->api->get('/positions');

        return view('employees.create', [
            'branches' => $branches['success'] ? $branches['data'] : [],
            'positions' => $positions['success'] ? $positions['data'] : [],
        ]);
    }

    public function store(Request $request)
    {
        $response = $this->api->post('/employees', $request->only([
            'name', 'code', 'position_id', 'branch_id', 'join_date', 'phone', 'email',
            'bank_account_number', 'bank_account_name', 'bank_name', 'employee_type', 'status',
        ]));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal menambahkan karyawan');
        }

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $employeeResponse = $this->api->get("/employees/{$id}");
        $branches = $this->api->get('/branches');
        $positions = $this->api->get('/positions');

        if (!$employeeResponse['success']) {
            return redirect()->route('employees.index')->with('error', 'Karyawan tidak ditemukan');
        }

        $employee = $employeeResponse['data'];
        // Format ulang ke Y-m-d supaya cocok dengan <input type="date">
        if (!empty($employee['join_date'])) {
            $employee['join_date'] = \Carbon\Carbon::parse($employee['join_date'])->format('Y-m-d');
        }

        return view('employees.edit', [
            'employee' => $employee,
            'branches' => $branches['success'] ? $branches['data'] : [],
            'positions' => $positions['success'] ? $positions['data'] : [],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api->put("/employees/{$id}", $request->only([
            'name', 'code', 'position_id', 'branch_id', 'join_date', 'phone', 'email',
            'bank_account_number', 'bank_account_name', 'bank_name', 'employee_type', 'status',
        ]));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal memperbarui karyawan');
        }

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $response = $this->api->delete("/employees/{$id}");

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}