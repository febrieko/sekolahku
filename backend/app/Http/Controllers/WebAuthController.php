<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->role, ['super_admin', 'school_admin', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin', 'teacher'])) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->role === 'student') {
                return redirect()->route('student.dashboard');
            }
            return redirect()->route('attendance.portal');
        }

        $school = School::where('tenant_id', 'sman1jkt')->first() ?? School::first();

        return view('auth.login', [
            'school' => $school,
        ]);
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $loginInput = trim($request->input('login', $request->input('email', '')));
        $password = $request->input('password', '');

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (empty($loginInput)) {
            return back()->withErrors([
                'login' => 'Email atau NISN wajib diisi.',
            ])->withInput();
        }

        $remember = $request->boolean('remember');

        // 1. Cek apakah input berupa format email
        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            // 2. Jika bukan format email, cari siswa berdasarkan NISN
            $student = \App\Models\Student::where('nisn', $loginInput)->first();
            if ($student && $student->user_id) {
                $user = User::find($student->user_id);
            }
            // Fallback: cari juga email dengan format nisn@siswa.sekolahku.sch.id
            if (!$user) {
                $user = User::where('email', $loginInput . '@siswa.sekolahku.sch.id')->first();
            }
        }

        if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            if (!$user->is_active) {
                return back()->withErrors([
                    'login' => 'Akun Anda dinonaktifkan. Silakan hubungi admin sekolah.',
                ])->withInput();
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            if (in_array($user->role, ['super_admin', 'school_admin', 'principal', 'staff', 'counselor', 'treasurer', 'teacher']) || str_starts_with($user->role, 'waka_')) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('login_success', 'Selamat datang kembali, ' . $user->name . ' (' . $user->getRoleTitle() . ')!');
            }

            if ($user->role === 'student') {
                return redirect()->intended(route('student.dashboard'))
                    ->with('login_success', 'Selamat datang di Portal Siswa Sekolahku, ' . $user->name . '!');
            }

            return redirect()->intended(route('attendance.portal'))
                ->with('login_success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors([
            'login' => 'Kombinasi email/NISN atau kata sandi tidak sesuai.',
        ])->withInput();
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Quick 1-click login for instant demonstration and testing.
     */
    public function quickLogin(Request $request): RedirectResponse
    {
        $role = $request->query('role', 'school_admin');
        $user = \App\Models\User::where('role', $role)->first()
             ?? \App\Models\User::where('email', 'admin@sekolahku.sch.id')->first()
             ?? \App\Models\User::first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            if ($request->query('target') === 'students') {
                return redirect()->to(route('admin.data-master') . '#tab-students')
                    ->with('login_success', 'Selamat datang! Membuka Mode Kartu Foto Siswa 4x6 (Visual Passbook).');
            }

            if ($request->query('target') === 'schedules') {
                return redirect()->to(route('admin.data-master') . '#tab-schedules')
                    ->with('login_success', 'Selamat datang! Membuka Pola Jadwal & Sesi Jam Pelajaran KBM.');
            }

            if ($request->query('target') === 'curriculums') {
                return redirect()->to(route('admin.data-master') . '#tab-curriculums')
                    ->with('login_success', 'Selamat datang! Membuka Pemetaan Kurikulum per Jenjang & Struktur Mapel.');
            }

            if ($user->role === 'student') {
                return redirect()->route('student.dashboard')
                    ->with('login_success', 'Selamat datang di Portal Siswa Sekolahku, ' . $user->name . '!');
            }

            if ($user->role === 'parent') {
                return redirect()->route('attendance.portal')
                    ->with('login_success', 'Selamat datang, ' . $user->name . '!');
            }

            return redirect()->route('admin.dashboard')
                ->with('login_success', 'Selamat datang di Sekolahku Studio, ' . $user->name . '!');
        }

        return redirect()->route('login');
    }
}
