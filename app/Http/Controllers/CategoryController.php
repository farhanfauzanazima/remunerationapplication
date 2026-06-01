<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /categories
    public function index()
    {
        $response = $this->api->get('/salary-categories');

        $categories = $response['success'] ? ($response['data'] ?? []) : [];
        $error      = !$response['success'] ? $response['message'] : null;

        return view('categories.index', compact('categories', 'error'));
    }

    // GET /categories/create
    public function create()
    {
        return view('categories.create');
    }

    // POST /categories
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:50',
            'base_salary'   => 'required|numeric|min:0',
            'allowance'     => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'late_penalty'  => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'base_salary.required'   => 'Gaji pokok wajib diisi.',
            'base_salary.numeric'    => 'Gaji pokok harus berupa angka.',
            'base_salary.min'        => 'Gaji pokok tidak boleh negatif.',
        ]);

        $response = $this->api->post('/salary-categories', [
            'category_name' => $request->category_name,
            'base_salary'   => $request->base_salary,
            'allowance'     => $request->allowance ?? 0,
            'overtime_rate' => $request->overtime_rate ?? 0,
            'late_penalty'  => $request->late_penalty ?? 0,
            'description'   => $request->description,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal menambah kategori.')
                ->withInput();
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori gaji berhasil ditambahkan.');
    }

    // GET /categories/{id}/edit
    public function edit(int $id)
    {
        $response = $this->api->get('/salary-categories/' . $id);

        if (!$response['success']) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $category = $response['data'];

        return view('categories.edit', compact('category'));
    }

    // PUT /categories/{id}
    public function update(Request $request, int $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:50',
            'base_salary'   => 'required|numeric|min:0',
            'allowance'     => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'late_penalty'  => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'is_active'     => 'nullable|boolean',
        ]);

        $response = $this->api->put('/salary-categories/' . $id, [
            'category_name' => $request->category_name,
            'base_salary'   => $request->base_salary,
            'allowance'     => $request->allowance ?? 0,
            'overtime_rate' => $request->overtime_rate ?? 0,
            'late_penalty'  => $request->late_penalty ?? 0,
            'description'   => $request->description,
            'is_active'     => $request->has('is_active') ? 1 : 0,
        ]);

        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Gagal update kategori.')
                ->withInput();
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori gaji berhasil diperbarui.');
    }

    // DELETE /categories/{id}
    public function destroy(int $id)
    {
        $response = $this->api->delete('/salary-categories/' . $id);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal menghapus kategori.');
        }

        return back()->with('success', 'Kategori gaji berhasil dihapus.');
    }
}