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
        $payload = [
            'name' => $request->input('name'),
            'has_all_branch_access' => $request->boolean('has_all_branch_access'),
            'branch_ids' => $request->input('branch_ids', []),
        ];

        $response = $this->api->post('/hr-management', $payload);

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function update(Request $request, string $id)
    {
        $payload = [
            'name' => $request->input('name'),
            'has_all_branch_access' => $request->boolean('has_all_branch_access'),
            'branch_ids' => $request->input('branch_ids', []),
        ];

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