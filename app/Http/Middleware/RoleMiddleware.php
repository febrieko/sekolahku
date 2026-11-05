<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Silakan masuk terlebih dahulu.',
                ], 401);
            }

            return redirect()->guest(route('login'))
                ->with('warning', 'Silakan masuk untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        // Akun tidak aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda dinonaktifkan.',
                ], 403);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi admin sekolah.']);
        }

        // Jika tidak ada batasan role spesifik, loloskan selama auth
        if (empty($roles)) {
            return $next($request);
        }

        // Cek otorisasi peran menggunakan hasAnyRole pada model User
        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk tindakan ini.',
                'required_roles' => $roles,
                'current_role' => $user->role,
            ], 403);
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk membuka halaman ini.');
    }
}
