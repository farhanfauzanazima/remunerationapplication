<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        $response = $this->api->get('/notification-settings');

        return view('notification-settings.index', [
            'setting' => $response['success'] ? $response['data']['setting'] : [],
            'placeholders' => $response['success'] ? $response['data']['available_placeholders'] : [],
        ]);
    }

    public function update(Request $request)
    {
        $response = $this->api->put('/notification-settings', $request->only('whatsapp_template'));

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}