<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected ApiService $api
    ) {}

    // ─── Tampilkan halaman login ───────────────────────────
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (session('api_token') && session('user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // ─── Proses login ──────────────────────────────────────
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

        // Kirim request ke backend API
        $response = $this->api->post('/auth/login', [
            'email'    => $request->email,
            'password' => $request->password,
        ], false); // false = tanpa auth token

        // Cek apakah response sukses
        if (!$response['success']) {
            return back()
                ->with('error', $response['message'] ?? 'Email atau password salah.')
                ->withInput($request->only('email'));
        }

        // Ambil data dari response
        $data = $response['data'];

        // Validasi struktur data
        if (!$data || !isset($data['token'], $data['user'])) {
            return back()
                ->with('error', 'Response server tidak valid. Coba lagi.')
                ->withInput($request->only('email'));
        }

        // Simpan token dan data user ke session
        session([
            'api_token'  => $data['token'],
            'user'       => $data['user'],
            'user.id'    => $data['user']['id'],
            'user.name'  => $data['user']['name'],
            'user.email' => $data['user']['email'],
            'user.role'  => $data['user']['role'],
            'user.phone' => $data['user']['phone'] ?? null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang, ' . $data['user']['name'] . '!');
    }

    // ─── Logout ────────────────────────────────────────────
    public function logout(Request $request)
    {
        // Kirim logout ke backend (hapus token di server)
        if (session('api_token')) {
            $this->api->post('/auth/logout');
        }

        // Hapus semua session
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')
            ->with('success', 'Anda berhasil logout.');
    }

    // ─── Lihat & Update Profil ────────────────────────────
    public function profile()
    {
        $response = $this->api->get('/auth/profile');

        if (!$response['success']) {
            return redirect()->route('dashboard')
                ->with('error', 'Gagal mengambil data profil.');
        }

        return view('profile.index', [
            'user' => $response['data'],
        ]);
    }

    // ─── Update Profil ─────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:15',
        ]);

        $response = $this->api->put('/auth/profile', $request->only('name', 'email', 'phone'));

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal update profil.');
        }

        // Update session
        session([
            'user.name'  => $response['data']['name'],
            'user.email' => $response['data']['email'],
            'user.phone' => $response['data']['phone'] ?? null,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ─── Ganti Password ────────────────────────────────────
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:6',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'current_password.required'          => 'Password lama wajib diisi.',
            'new_password.required'              => 'Password baru wajib diisi.',
            'new_password.min'                   => 'Password baru minimal 6 karakter.',
            'new_password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'new_password_confirmation.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        $response = $this->api->post('/auth/change-password', [
            'current_password'              => $request->current_password,
            'new_password'                  => $request->new_password,
            'new_password_confirmation'     => $request->new_password_confirmation,
        ]);

        if (!$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal mengubah password.');
        }

        // Logout setelah ganti password
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')
            ->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }
}