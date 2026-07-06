<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected ApiService $api)
    {
    }

    public function index(Request $request)
    {
        $response = $this->api->get('/activity-logs', $request->only(['module', 'action']));

        return view('activity-logs.index', [
            'logs' => $response['success'] ? $response['data'] : [],
            'pagination' => $response['raw']['raw']['pagination'] ?? null,
            'filters' => $request->only(['module', 'action']),
        ]);
    }

    public function show(string $id)
    {
        $response = $this->api->get("/activity-logs/{$id}");

        return view('activity-logs.show', [
            'log' => $response['success'] ? $response['data'] : null,
        ]);
    }
}