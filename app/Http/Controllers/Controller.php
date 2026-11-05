<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Resolve the authorized School instance for the current request.
     * Non-super_admin users (GTK, teachers, students, parents) are strictly locked to their own school_id.
     */
    protected function resolveSchool(Request $request): School
    {
        $user = $request->user();

        // 1. Jika user bukan super_admin (mis: GTK / Guru / Waka / Siswa / Staf), kunci mutlak ke user->school_id
        if ($user && ! $user->isSuperAdmin()) {
            if ($user->school_id) {
                $school = School::find($user->school_id);
                if ($school) {
                    if (session('active_school_id') !== $school->id) {
                        session(['active_school_id' => $school->id]);
                    }
                    return $school;
                }
            }
        }

        // 2. Jika Super Admin atau belum login, diperbolehkan menggunakan session atau query parameter
        $activeSchoolId = session('active_school_id') ?? $request->query('school_id') ?? $request->input('school_id');
        if ($activeSchoolId) {
            $school = School::find($activeSchoolId);
            if ($school) {
                if (session('active_school_id') !== $school->id) {
                    session(['active_school_id' => $school->id]);
                }
                return $school;
            }
        }

        // 3. Fallback default
        $defaultSchool = ($user && $user->school_id)
            ? School::find($user->school_id)
            : (School::where('level', 'SMK')->first() ?? School::first());

        if ($defaultSchool) {
            if (session('active_school_id') !== $defaultSchool->id) {
                session(['active_school_id' => $defaultSchool->id]);
            }
            return $defaultSchool;
        }

        return new School(['id' => 1, 'name' => 'Sekolahku']);
    }

    /**
     * Resolve the authorized school_id as integer.
     */
    protected function resolveSchoolId(Request $request): int
    {
        return (int) $this->resolveSchool($request)->id;
    }
}
