<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class CategoricalController extends Controller
{
    public function __construct(protected ApiService $api) {}

    public function index()
    {
        $response = $this->api->get('/salary-settings');

        return view('categorical.index', [
            'setting' => $response['success'] ? $response['data'] : [],
        ]);
    }

    public function update(Request $request)
    {
        $response = $this->api->put('/salary-settings', $request->only([
            'transport_tetap', 'transport_partime',
            'tenure_months_threshold', 'tenure_bonus_amount',
            'disiplin_bonus_tetap', 'rate_full', 'rate_shift', 'rate_reguler',
            'lembur_1_2_jam', 'lembur_3_4_jam', 'lembur_5_jam',
        ]));

        return back()->with($response['success'] ? 'success' : 'error', $response['message']);
    }
}