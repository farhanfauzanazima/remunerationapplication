<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        $response = $this->api->get('/positions');

        return view('positions.index', [
            'positions' => $response['success'] ? $response['data'] : [],
        ]);
    }

    public function store(Request $request)
    {
        $response = $this->api->post('/positions', $request->only('name'));

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api->put("/positions/{$id}", $request->only('name'));

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }

    public function destroy(string $id)
    {
        $response = $this->api->delete("/positions/{$id}");

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}