<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * User login for API.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $user = User::with(['school', 'student.schoolClass'])
            ->where('email', $validated['email'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kombinasi email dan password tidak sesuai.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun pengguna ini dinonaktifkan.',
            ], 403);
        }

        $tokenName = $validated['device_name'] ?? 'web-app';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'school_id' => $user->school_id,
                    'school' => $user->school ? [
                        'id' => $user->school->id,
                        'name' => $user->school->name,
                        'tenant_id' => $user->school->tenant_id,
                        'level' => $user->school->level,
                    ] : null,
                    'student' => $user->student ? [
                        'id' => $user->student->id,
                        'nis' => $user->student->nis,
                        'full_name' => $user->student->full_name,
                        'class_id' => $user->student->class_id,
                        'class_name' => $user->student->schoolClass?->name,
                    ] : null,
                ],
            ],
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['school', 'student.schoolClass']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * User logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}
