<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class HrManagementController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        $hrResponse = $this->api->get('/hr-management');
        $branchResponse = $this->api->get('/branches');

        return view('hr-management.index', [
            'hrUsers' => $hrResponse['success'] ? $hrResponse['data'] : [],
            'branches' => $branchResponse['success'] ? $branchResponse['data'] : [],
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'hr');

        $payload = [
            'name' => $request->input('name'),
            'type' => $type,
        ];

        // Field akses cabang HANYA relevan untuk HR biasa.
        // Super HR selalu otomatis semua cabang — jangan kirim field ini sama sekali
        // supaya tidak memicu validasi branch_ids di backend.
        if ($type !== 'super_hr') {
            $payload['has_all_branch_access'] = $request->boolean('has_all_branch_access');
            $payload['branch_ids'] = $request->input('branch_ids', []);
        }

        $response = $this->api->post('/hr-management', $payload);

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function update(Request $request, string $id)
    {
        $payload = [
            'name' => $request->input('name'),
        ];

        // Form "Edit" (nama + akses cabang) SELALU mengirim key 'has_all_branch_access'
        // karena blade sekarang punya hidden input default 0 sebelum checkbox-nya.
        // Form "Jadikan/Turunkan Super HR" TIDAK mengirim key ini sama sekali,
        // jadi kedua jenis form tetap bisa dibedakan dengan aman di sini.
        if ($request->has('has_all_branch_access')) {
            $payload['has_all_branch_access'] = $request->boolean('has_all_branch_access');
            $payload['branch_ids'] = $request->input('branch_ids', []);
        }

        if ($request->has('is_super_hr')) {
            $payload['is_super_hr'] = $request->boolean('is_super_hr');
        }

        $response = $this->api->put("/hr-management/{$id}", $payload);

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function resetPassword(string $id)
    {
        $response = $this->api->post("/hr-management/{$id}/reset-password", []);

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function destroy(string $id)
    {
        $response = $this->api->delete("/hr-management/{$id}");

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}