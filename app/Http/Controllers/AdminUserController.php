<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users and user categories.
     */
    public function index(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $isSuperAdmin = (bool) $request->user()?->isSuperAdmin();

        $categories = User::getRoleCategories();
        $availableRoles = User::getAvailableRoles();
        $availableDuties = User::getAvailableAdditionalDuties();

        // Daftar sekolah untuk opsi enrollment
        $availableSchools = $isSuperAdmin
            ? School::orderBy('name')->get()
            : School::where('id', $school->id)->get();

        // Base Query
        $query = User::with(['school', 'teacher']);

        // Jika bukan Super Admin, batasi hanya melihat pengguna di sekolahnya
        if (! $isSuperAdmin) {
            $query->where('school_id', $school->id);
        } else {
            // Filter Sekolah untuk Super Admin
            if ($request->filled('school_id')) {
                if ($request->input('school_id') === 'unassigned') {
                    $query->whereNull('school_id');
                } else {
                    $query->where('school_id', $request->input('school_id'));
                }
            }
        }

        // Filter Kategori Peran
        $category = $request->query('category', 'all');
        if ($category !== 'all') {
            match ($category) {
                'admin' => $query->whereIn('role', ['super_admin', 'principal', 'school_admin']),
                'teacher' => $query->where(function ($q) {
                    $q->whereIn('role', ['teacher', 'counselor'])
                        ->orWhereNotNull('additional_role')
                        ->orWhere('role', 'like', 'waka_%');
                }),
                'staff' => $query->where('role', 'staff'),
                'student' => $query->where('role', 'student'),
                'parent' => $query->where('role', 'parent'),
                'finance' => $query->whereIn('role', ['treasurer', 'cashier']),
                default => null,
            };
        }

        // Filter Peran Spesifik (Opsional)
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter Status Akun (Aktif / Nonaktif)
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter Pencarian Teks (Nama, Email, No. Telepon)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Query Stats Counter
        $baseStatQuery = User::query();
        if (! $isSuperAdmin) {
            $baseStatQuery->where('school_id', $school->id);
        }

        $stats = [
            'total_users' => (clone $baseStatQuery)->count(),
            'active_users' => (clone $baseStatQuery)->where('is_active', true)->count(),
            'inactive_users' => (clone $baseStatQuery)->where('is_active', false)->count(),
            'admin_waka' => (clone $baseStatQuery)->where(function ($q) {
                $q->whereIn('role', ['super_admin', 'principal', 'school_admin'])
                    ->orWhereNotNull('additional_role')
                    ->orWhere('role', 'like', 'waka_%');
            })->count(),
            'teachers' => (clone $baseStatQuery)->where(function ($q) {
                $q->whereIn('role', ['teacher', 'counselor'])
                    ->orWhereNotNull('additional_role')
                    ->orWhere('role', 'like', 'waka_%');
            })->count(),
            'students' => (clone $baseStatQuery)->where('role', 'student')->count(),
            'parents' => (clone $baseStatQuery)->where('role', 'parent')->count(),
            'unassigned' => $isSuperAdmin ? User::whereNull('school_id')->count() : 0,
        ];

        // Urutkan dan paginasi
        $users = $query->orderByDesc('id')->paginate(15)->appends($request->query());

        return view('admin.users.index', [
            'school' => $school,
            'isSuperAdmin' => $isSuperAdmin,
            'users' => $users,
            'categories' => $categories,
            'currentCategory' => $category,
            'availableRoles' => $availableRoles,
            'availableDuties' => $availableDuties,
            'availableSchools' => $availableSchools,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new user (Menu Tersendiri: Tambah Pengguna).
     */
    public function create(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $isSuperAdmin = (bool) $request->user()?->isSuperAdmin();

        $availableRoles = User::getAvailableRoles();
        if (! $isSuperAdmin) {
            $availableRoles = array_filter($availableRoles, fn ($k) => $k !== 'super_admin', ARRAY_FILTER_USE_KEY);
        }

        $availableDuties = User::getAvailableAdditionalDuties();

        $availableSchools = $isSuperAdmin
            ? School::orderBy('name')->get()
            : School::where('id', $school->id)->get();

        return view('admin.users.create', [
            'school' => $school,
            'isSuperAdmin' => $isSuperAdmin,
            'availableRoles' => $availableRoles,
            'availableDuties' => $availableDuties,
            'availableSchools' => $availableSchools,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $isSuperAdmin = (bool) $request->user()?->isSuperAdmin();
        $school = $this->resolveSchool($request);

        $allowedRoles = array_keys(User::getAvailableRoles());
        // Non-super admins cannot create super_admin accounts
        if (! $isSuperAdmin) {
            $allowedRoles = array_filter($allowedRoles, fn ($r) => $r !== 'super_admin');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'additional_role' => ['nullable', 'string', Rule::in(array_keys(User::getAvailableAdditionalDuties()))],
            'school_id' => ['nullable', 'exists:schools,id'],
            'password' => ['required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Tetapkan school_id
        $schoolId = $validated['school_id'] ?? null;
        if (! $isSuperAdmin) {
            $schoolId = $school->id;
        }

        // Tugas tambahan hanya berlaku bagi role teacher
        $additionalRole = ($validated['role'] === 'teacher') ? ($validated['additional_role'] ?? null) : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'additional_role' => $additionalRole,
            'school_id' => $schoolId,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} ({$user->getRoleTitle()}) berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified user (Menu Tersendiri: Ubah Data Pengguna).
     */
    public function edit(Request $request, $id): View
    {
        $user = User::with('school')->findOrFail($id);
        $currentUser = $request->user();
        $isSuperAdmin = (bool) $currentUser?->isSuperAdmin();
        $school = $this->resolveSchool($request);

        // Validasi batasan tenant
        if (! $isSuperAdmin) {
            if ($user->school_id !== $school->id || $user->isSuperAdmin()) {
                abort(403, 'Akses ditolak: Anda tidak memiliki wewenang mengedit akun ini.');
            }
        }

        $availableRoles = User::getAvailableRoles();
        if (! $isSuperAdmin) {
            $availableRoles = array_filter($availableRoles, fn ($k) => $k !== 'super_admin', ARRAY_FILTER_USE_KEY);
        }

        $availableDuties = User::getAvailableAdditionalDuties();

        $availableSchools = $isSuperAdmin
            ? School::orderBy('name')->get()
            : School::where('id', $school->id)->get();

        return view('admin.users.edit', [
            'school' => $school,
            'targetUser' => $user,
            'isSuperAdmin' => $isSuperAdmin,
            'availableRoles' => $availableRoles,
            'availableDuties' => $availableDuties,
            'availableSchools' => $availableSchools,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::with('teacher')->findOrFail($id);
        $currentUser = $request->user();
        $isSuperAdmin = (bool) $currentUser?->isSuperAdmin();
        $school = $this->resolveSchool($request);

        // Validasi batasan tenant
        if (! $isSuperAdmin) {
            if ($user->school_id !== $school->id || $user->isSuperAdmin()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Akses ditolak: Anda tidak memiliki wewenang mengedit akun ini.');
            }
        }

        $allowedRoles = array_keys(User::getAvailableRoles());
        if (! $isSuperAdmin) {
            $allowedRoles = array_filter($allowedRoles, fn ($r) => $r !== 'super_admin');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'additional_role' => ['nullable', 'string', Rule::in(array_keys(User::getAvailableAdditionalDuties()))],
            'school_id' => ['nullable', 'exists:schools,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Update atribut dasar
        $user->name = $validated['name'];
        $user->email = strtolower(trim($validated['email']));
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->additional_role = ($validated['role'] === 'teacher') ? ($validated['additional_role'] ?? null) : null;
        $user->is_active = $request->boolean('is_active', true);

        // Hanya Super Admin yang boleh memindahkan unit sekolah
        if ($isSuperAdmin) {
            $user->school_id = $validated['school_id'] ?? null;
        }

        // Update password jika diisi
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sinkronisasi tugas tambahan ke profil Teacher jika ada
        if ($user->teacher) {
            $dutiesMeta = User::getAvailableAdditionalDuties();
            $dutyTitle = $user->additional_role && isset($dutiesMeta[$user->additional_role])
                ? $dutiesMeta[$user->additional_role]['title']
                : null;
            $user->teacher->update([
                'additional_duties' => $dutyTitle,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Dedicated View for School Enrollments (Menu Tersendiri: Enrol Pengguna ke Satuan Pendidikan).
     */
    public function enrollments(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $isSuperAdmin = (bool) $request->user()?->isSuperAdmin();

        $availableSchools = $isSuperAdmin
            ? School::orderBy('name')->get()
            : School::where('id', $school->id)->get();

        $query = User::with('school');

        if (! $isSuperAdmin) {
            $query->where('school_id', $school->id);
        } else {
            if ($request->filled('school_id')) {
                if ($request->input('school_id') === 'unassigned') {
                    $query->whereNull('school_id');
                } else {
                    $query->where('school_id', $request->input('school_id'));
                }
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $unassignedCount = User::whereNull('school_id')->count();
        $totalEnrolledCount = User::whereNotNull('school_id')->count();

        $users = $query->orderByRaw('CASE WHEN school_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->query());

        $availableRoles = User::getAvailableRoles();
        $availableDuties = User::getAvailableAdditionalDuties();

        return view('admin.users.enrollments', [
            'school' => $school,
            'isSuperAdmin' => $isSuperAdmin,
            'users' => $users,
            'availableRoles' => $availableRoles,
            'availableDuties' => $availableDuties,
            'availableSchools' => $availableSchools,
            'unassignedCount' => $unassignedCount,
            'totalEnrolledCount' => $totalEnrolledCount,
        ]);
    }

    /**
     * Enrol / re-assign user to a school unit.
     */
    public function enrollSchool(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();
        $isSuperAdmin = (bool) $currentUser?->isSuperAdmin();
        $school = $this->resolveSchool($request);

        $request->validate([
            'school_id' => ['nullable', 'exists:schools,id'],
        ]);

        $targetSchoolId = $request->input('school_id');

        // Jika bukan super admin, hanya boleh enrol ke sekolahnya sendiri
        if (! $isSuperAdmin) {
            $targetSchoolId = $school->id;
            if ($user->school_id && $user->school_id !== $school->id) {
                return redirect()->back(fallback: route('admin.users.index'))
                    ->with('error', 'Akses ditolak: Pengguna ini terdaftar di unit sekolah lain.');
            }
        }

        $user->school_id = $targetSchoolId;
        $user->save();

        $targetSchool = $targetSchoolId ? School::find($targetSchoolId) : null;
        $schoolName = $targetSchool ? $targetSchool->name : 'Tanpa Unit (Unassigned)';

        return redirect()->back(fallback: route('admin.users.index'))
            ->with('success', "Pengguna {$user->name} berhasil dienrol ke satuan pendidikan: {$schoolName}.");
    }

    /**
     * Toggle active/disabled status of a user.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();

        // Mencegah menonaktifkan akun sendiri
        if ($currentUser && $currentUser->id === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.');
        }

        // Validasi tenant
        if (! $currentUser->isSuperAdmin()) {
            $school = $this->resolveSchool($request);
            if ($user->school_id !== $school->id || $user->isSuperAdmin()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Akses ditolak: Anda tidak memiliki wewenang mengubah status akun ini.');
            }
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusLabel = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$user->name} berhasil {$statusLabel}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();

        // Mencegah menghapus akun sendiri
        if ($currentUser && $currentUser->id === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Validasi tenant
        if (! $currentUser->isSuperAdmin()) {
            $school = $this->resolveSchool($request);
            if ($user->school_id !== $school->id || $user->isSuperAdmin()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Akses ditolak: Anda tidak memiliki wewenang menghapus akun ini.');
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus dari sistem.");
    }
}
