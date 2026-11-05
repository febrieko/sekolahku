<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request to enforce strict multi-tenant school isolation for GTK, teachers, students, and staff.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika belum login atau adalah Super Admin, berikan akses penuh lintas sekolah
        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        // Pengguna non-Super Admin (GTK/Guru/Waka/Staff/Siswa/Ortu) WAJIB terkunci ke school_id miliknya
        $assignedSchoolId = (int) $user->school_id;

        if ($assignedSchoolId > 0) {
            // 1. Selaraskan session active_school_id dengan unit resmi user
            if (session('active_school_id') !== $assignedSchoolId) {
                session(['active_school_id' => $assignedSchoolId]);
            }

            // 2. Jika request mencoba mengirimkan parameter school_id yang berbeda dengan unitnya
            if ($request->has('school_id') && (int) $request->input('school_id') !== $assignedSchoolId) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses ditolak: Anda hanya berwenang mengakses data pada unit pendidikan tempat Anda bertugas.',
                    ], 403);
                }

                // Paksa parameter school_id di URL/Request agar selalu sesuai dengan unit resmi pengguna
                $request->merge(['school_id' => $assignedSchoolId]);
            }
        }

        return $next($request);
    }
}

