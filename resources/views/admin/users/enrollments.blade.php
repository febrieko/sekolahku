@extends('layouts.vuexy')

@section('title', 'Enrol Pengguna ke Satuan Pendidikan')
@section('breadcrumb_parent', 'Manajemen Pengguna')
@section('breadcrumb_current', 'Enrol Satuan Pendidikan')

@push('styles')
<style>
    .enroll-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .user-mini-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--vx-primary-light);
        color: var(--bento-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .user-mini-avatar.admin { background: var(--vx-primary-light); color: var(--bento-primary); }
    .user-mini-avatar.principal { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
    .user-mini-avatar.staff { background: rgba(14, 165, 233, 0.15); color: #0284c7; }
    .user-mini-avatar.counselor { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .user-mini-avatar.waka { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .user-mini-avatar.teacher { background: var(--vx-success-light); color: var(--vx-success); }
    .user-mini-avatar.student { background: var(--vx-info-light); color: var(--vx-info); }
    .user-mini-avatar.parent { background: var(--vx-warning-light); color: var(--vx-warning); }
    .user-mini-avatar.finance, .user-mini-avatar.cashier { background: var(--vx-danger-light); color: var(--vx-danger); }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <div class="page-title-row">
        <div class="page-heading">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-left-arrow-alt'></i> Kembali ke Direktori
                </a>
                <span class="vx-badge badge-primary">Multi-Tenant School Enrollment</span>
            </div>
            <h1>
                <i class='bx bx-buildings' style="color: var(--bento-primary);"></i>
                Enrol Pengguna ke Satuan Pendidikan
            </h1>
            <p>Kelola penugasan dan pendaftaran unit sekolah resmi bagi seluruh pendidik (GTK), siswa, dan staf ke multi-tenant platform.</p>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="background: var(--vx-success-light); border: 1px solid var(--vx-success-border); color: var(--vx-success); padding: 0.85rem 1.25rem; border-radius: var(--radius-lg); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem; font-weight: 600; font-size: 0.85rem;">
            <i class='bx bx-check-circle' style="font-size: 1.25rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div style="background: var(--vx-danger-light); border: 1px solid var(--vx-danger-border); color: var(--vx-danger); padding: 0.85rem 1.25rem; border-radius: var(--radius-lg); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem; font-weight: 600; font-size: 0.85rem;">
            <i class='bx bx-error-circle' style="font-size: 1.25rem;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- KPI STATS -->
    <div class="enroll-stat-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-success">
                <i class='bx bxs-school'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Pengguna Ter-enrol Resmi</div>
                <div class="stat-value">{{ number_format($totalEnrolledCount) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Akun</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-success"></span>
                    <span>Telah terhubung ke satuan pendidikan</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-warning">
                <i class='bx bx-unlink'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Belum Terdaftar (Unassigned)</div>
                <div class="stat-value">{{ number_format($unassignedCount) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Akun</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-warning"></span>
                    <span>Memerlukan penugasan unit sekolah</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CARD TABLE -->
    <div class="vx-card">
        <!-- Card Header with Filters -->
        <div class="vx-card-header">
            <div>
                <h3 class="vx-card-title">
                    <i class='bx bx-git-pull-request text-primary'></i> Matriks Penugasan Satuan Pendidikan
                </h3>
                <div class="cell-sub" style="margin-top: 2px;">
                    Daftar seluruh akun pengguna beserta status unit sekolah tempat bertugas.
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.users.enrollments') }}" method="GET" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin: 0;">
                <!-- Search Input -->
                <div style="position: relative; width: 220px;">
                    <i class='bx bx-search' style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem;"></i>
                    <input type="text" name="search" class="vx-input" style="padding-left: 2rem; height: 34px; font-size: 0.8rem;" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>

                <!-- School Filter (For Super Admin) -->
                @if($isSuperAdmin)
                    <select name="school_id" class="vx-input" style="width: auto; height: 34px; font-size: 0.8rem; padding-right: 1.75rem;" onchange="this.form.submit()">
                        <option value="">Semua Satuan Pendidikan</option>
                        <option value="unassigned" {{ request('school_id') === 'unassigned' ? 'selected' : '' }}>⚠️ Belum Terdaftar (Unassigned)</option>
                        @foreach($availableSchools as $sc)
                            <option value="{{ $sc->id }}" {{ request('school_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-secondary btn-sm" style="height: 34px; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-filter-alt'></i> Filter
                </button>

                @if(request('search') || request('school_id'))
                    <a href="{{ route('admin.users.enrollments') }}" class="btn btn-secondary btn-sm btn-icon-sm" style="height: 34px; width: 34px; display: inline-flex; align-items: center; justify-content: center;" title="Reset Filter">
                        <i class='bx bx-reset'></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Clean Responsive Table (vx-table) -->
        <div class="table-wrap">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">No</th>
                        <th style="min-width: 250px;">Pengguna & Kontak</th>
                        <th style="width: 180px;">Peran / Hak Akses</th>
                        <th style="min-width: 200px;">Satuan Pendidikan Saat Ini</th>
                        <th style="width: 320px;">Ubah / Enrol ke Sekolah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $idx => $u)
                        @php
                            $roleBadgeClass = $u->getRoleBadgeClass();
                            $roleTitle = $u->getRoleTitle();
                            $roleCategory = match(true) {
                                $u->isSuperAdmin() || $u->isPrincipal() || $u->isSchoolAdmin() => 'admin',
                                $u->isWaka() => 'waka',
                                $u->isCounselor() => 'counselor',
                                $u->isStaff() => 'staff',
                                $u->isTreasurer() => 'finance',
                                $u->isTeacher() => 'teacher',
                                $u->isStudent() => 'student',
                                $u->isParent() => 'parent',
                                default => 'admin',
                            };

                            $cleanName = preg_replace('/\s*\(.*?\)\s*$/', '', $u->name);
                        @endphp
                        <tr>
                            <!-- No -->
                            <td style="text-align: center; color: var(--text-muted); font-size: 0.78rem;" class="cell-mono">
                                {{ $users->firstItem() + $idx }}
                            </td>

                            <!-- Pengguna & Kontak -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-mini-avatar {{ $roleCategory }}">
                                        {{ strtoupper(substr($cleanName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="cell-title">
                                            {{ $cleanName }}
                                            @if(auth()->id() === $u->id)
                                                <span class="vx-badge badge-primary" style="font-size: 0.65rem; margin-left: 0.25rem;">Anda</span>
                                            @endif
                                        </div>
                                        <div class="cell-sub cell-mono">
                                            <i class='bx bx-envelope text-primary'></i> {{ $u->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Peran / Hak Akses -->
                            <td>
                                @if($u->additional_role && isset($availableDuties[$u->additional_role]))
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem; align-items: flex-start;">
                                        <span class="vx-badge badge-success" style="font-size: 0.72rem;">
                                            <i class='bx bx-briefcase'></i> Guru / Pendidik
                                        </span>
                                        <span class="vx-badge {{ $availableDuties[$u->additional_role]['badge'] }}" style="font-size: 0.72rem;">
                                            <i class='bx bx-award'></i> {{ $availableDuties[$u->additional_role]['title'] }}
                                        </span>
                                    </div>
                                @else
                                    <span class="vx-badge {{ $roleBadgeClass }}">
                                        {{ $roleTitle }}
                                    </span>
                                @endif
                            </td>

                            <!-- Satuan Pendidikan Saat Ini -->
                            <td>
                                @if($u->school)
                                    <div>
                                        <div class="cell-title" style="font-size: 0.82rem; display: flex; align-items: center; gap: 0.35rem;">
                                            <i class='bx bxs-school text-primary'></i> {{ $u->school->name }}
                                        </div>
                                        <div class="cell-sub cell-mono">NPSN: {{ $u->school->npsn ?? '-' }}</div>
                                    </div>
                                @else
                                    <span class="vx-badge badge-danger">
                                        <i class='bx bx-unlink'></i> Belum Terdaftar
                                    </span>
                                @endif
                            </td>

                            <!-- Ubah / Enrol ke Sekolah -->
                            <td>
                                <form action="{{ route('admin.users.enroll', $u->id) }}" method="POST" style="display: flex; align-items: center; gap: 0.4rem; margin: 0;">
                                    @csrf
                                    @if($isSuperAdmin)
                                        <select name="school_id" class="vx-input" style="height: 32px; font-size: 0.78rem; padding: 0.2rem 1.6rem 0.2rem 0.5rem; max-width: 200px;">
                                            <option value="">-- Lepaskan (Unassign) --</option>
                                            @foreach($availableSchools as $sc)
                                                <option value="{{ $sc->id }}" {{ $u->school_id == $sc->id ? 'selected' : '' }}>
                                                    {{ $sc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm" style="height: 32px; padding: 0 0.65rem; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600; font-size: 0.78rem; white-space: nowrap;" title="Simpan Enrollment">
                                            <i class='bx bx-save'></i> Simpan
                                        </button>
                                    @else
                                        <input type="hidden" name="school_id" value="{{ $school->id }}">
                                        @if($u->school_id === $school->id)
                                            <span class="vx-badge badge-success">
                                                <i class='bx bx-check-circle'></i> Terdaftar di Unit Ini
                                            </span>
                                        @else
                                            <button type="submit" class="btn btn-primary btn-sm" style="height: 32px; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600; font-size: 0.78rem; white-space: nowrap;">
                                                <i class='bx bx-user-plus'></i> Daftarkan ke Sekolah Ini
                                            </button>
                                        @endif
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class='bx bx-buildings' style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                Tidak ada data pengguna pada filter enrollment saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="cell-sub">
                    Menampilkan <strong>{{ $users->firstItem() }}</strong> s/d <strong>{{ $users->lastItem() }}</strong> dari total <strong>{{ $users->total() }}</strong> akun
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
