<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        $response = $this->api->get('/branches');

        return view('branches.index', [
            'branches' => $response['success'] ? $response['data'] : [],
        ]);
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $response = $this->api->post('/branches', $request->only('name', 'address', 'phone'));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal menambahkan cabang');
        }

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $response = $this->api->get("/branches/{$id}");

        if (!$response['success']) {
            return redirect()->route('branches.index')->with('error', 'Cabang tidak ditemukan');
        }

        return view('branches.edit', ['branch' => $response['data']]);
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api->put("/branches/{$id}", $request->only('name', 'address', 'phone'));

        if (!$response['success']) {
            return back()->withInput()->with('error', $response['message'] ?? 'Gagal memperbarui cabang');
        }

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $response = $this->api->delete("/branches/{$id}");

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}