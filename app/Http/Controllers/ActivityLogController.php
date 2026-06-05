<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // GET /activity-logs
    public function index(Request $request)
    {
        $params = [];
        if ($request->filled('user_id'))   $params['user_id']   = $request->user_id;
        if ($request->filled('module'))    $params['module']    = $request->module;
        if ($request->filled('action'))    $params['action']    = $request->action;
        if ($request->filled('date_from')) $params['date_from'] = $request->date_from;
        if ($request->filled('date_to'))   $params['date_to']   = $request->date_to;
        if ($request->filled('per_page'))  $params['per_page']  = $request->per_page;

        $response   = $this->api->get('/activity-logs', $params);
        $logs       = $response['success'] ? ($response['raw']['data'] ?? []) : [];
        $pagination = $response['success'] ? ($response['raw']['pagination'] ?? []) : [];
        $error      = !$response['success'] ? $response['message'] : null;

        return view('activity-logs.index', compact('logs', 'pagination', 'error'));
    }

    // GET /activity-logs/{id}
    public function show(int $id)
    {
        $response = $this->api->get('/activity-logs/' . $id);

        if (!$response['success']) {
            return redirect()->route('activity-logs.index')
                ->with('error', 'Log tidak ditemukan.');
        }

        $log = $response['data'];

        return view('activity-logs.show', compact('log'));
    }
}