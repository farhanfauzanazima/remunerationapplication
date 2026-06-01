<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $response = $this->api->post('/auth/login', [
            'email'    => $request->email,
            'password' => $request->password,
        ], false);

        // ─── DEBUG SEMENTARA ──────────────────────────────────
        // Hapus setelah berhasil
        // \Log::info('Login response', $response);
        // ─────────────────────────────────────────────────────

        if ($response['success']) {
            $data = $response['data'];

            // Pastikan data tidak null
            if (!$data || !isset($data['token']) || !isset($data['user'])) {
                return back()
                    ->with('error', 'Response dari server tidak valid.')
                    ->withInput($request->only('email'));
            }

            session([
                'api_token' => $data['token'],
                'user'      => $data['user'],
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, ' . $data['user']['name'] . '!');
        }

        return back()
            ->with('error', $response['message'] ?? 'Login gagal.')
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $this->api->post('/auth/logout');
        session()->flush();

        return redirect()->route('auth.login')
            ->with('success', 'Anda berhasil logout.');
    }
}
