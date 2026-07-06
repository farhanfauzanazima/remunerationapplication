<?php

namespace App\Http\Controllers;

use App\Services\ApiService;

class DashboardController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        $response = $this->api->get('/dashboard');

        return view('dashboard.index', [
            'stats' => $response['success'] ? $response['data'] : [],
        ]);
    }
}