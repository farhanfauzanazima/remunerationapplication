<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    public function index()
    {
        $role = session('user.role');

        return match($role) {
            'owner' => $this->ownerDashboard(),
            'head'  => $this->headDashboard(),
            'admin' => $this->adminDashboard(),
            default => redirect()->route('auth.login'),
        };
    }

    // ─── Dashboard Owner ───────────────────────────────────
    private function ownerDashboard()
    {
        $response = $this->api->get('/dashboard/owner');

        $data = $response['success'] ? $response['data'] : [];

        return view('dashboard.owner', compact('data'));
    }

    // ─── Dashboard Head ────────────────────────────────────
    private function headDashboard()
    {
        $response = $this->api->get('/dashboard/head');

        $data = $response['success'] ? $response['data'] : [];

        return view('dashboard.head', compact('data'));
    }

    // ─── Dashboard Admin ───────────────────────────────────
    private function adminDashboard()
    {
        $response = $this->api->get('/dashboard/admin');

        $data = $response['success'] ? $response['data'] : [];

        return view('dashboard.admin', compact('data'));
    }
}