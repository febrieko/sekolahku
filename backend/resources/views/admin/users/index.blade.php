@extends('layouts.vuexy')

@section('title', 'Manajemen Pengguna & Hak Akses (RBAC)')
@section('breadcrumb_parent', 'Sistem & Pengaturan')
@section('breadcrumb_current', 'Manajemen Pengguna')

@push('styles')
<style>
    /* Category Filter Navigation Pills */
    .user-cat-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1.25rem;
        scrollbar-width: thin;
    }
    .user-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.95rem;
        border-radius: var(--radius-pill);
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        color: var(--text-body);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        white-space: nowrap;
    }
    .user-cat-pill:hover {
        background: var(--bg-subtle);
        color: var(--text-heading);
        border-color: var(--bento-primary);
    }
    .user-cat-pill.active {
        background: var(--bento-primary);
        color: #ffffff !important;
        border-color: var(--bento-primary);
        box-shadow: 0 4px 12px rgba(70, 128, 255, 0.28);
    }
    .user-cat-pill .cat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Avatar styling */
    .user-avatar-box {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
        position: relative;
    }
    .user-avatar-box.admin { background: var(--vx-primary-light); color: var(--bento-primary); }
    .user-avatar-box.principal { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
    .user-avatar-box.staff { background: rgba(14, 165, 233, 0.15); color: #0284c7; }
    .user-avatar-box.counselor { background: rgba(245, 158, 11, 0.15); color: #d97706; }
    .user-avatar-box.waka { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .user-avatar-box.teacher { background: var(--vx-success-light); color: var(--vx-success); }
    .user-avatar-box.student { background: var(--vx-info-light); color: var(--vx-info); }
    .user-avatar-box.parent { background: var(--vx-warning-light); color: var(--vx-warning); }
    .user-avatar-box.finance, .user-avatar-box.cashier { background: var(--vx-danger-light); color: var(--vx-danger); }
    .user-avatar-box.inactive { background: var(--bg-subtle) !important; color: var(--text-muted) !important; }

    .user-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        border: 2px solid var(--bg-surface);
    }
    .user-status-dot.active { background: var(--vx-success); }
    .user-status-dot.inactive { background: var(--vx-danger); }

    /* Action Icon Button Container */
    .user-action-btn-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <div class="page-title-row">
        <div class="page-heading">
            <h1>
                <i class='bx bx-user-check' style="color: var(--bento-primary);"></i>
                Manajemen Pengguna & Hak Akses (RBAC)
                <span class="vx-badge badge-primary">Direktori Master</span>
            </h1>
            <p>Direktori master seluruh akun pengguna terdaftar, pengelompokan kategori hak akses resmi, dan penugasan satuan pendidikan.</p>
        </div>
        <div class="page-actions" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="openBulkImportModal('students')" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                <i class='bx bx-file-blank text-success'></i> Impor Siswa Excel
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="openBulkImportModal('teachers')" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                <i class='bx bx-file-blank text-primary'></i> Impor GTK Excel
            </button>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                <i class='bx bx-user-plus' style="font-size: 1.15rem;"></i> Tambah Pengguna Baru
            </a>
            <a href="{{ route('admin.users.enrollments') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                <i class='bx bx-buildings'></i> Enrol Satuan Pendidikan
            </a>
        </div>
    </div>

    <!-- RADAR KPI STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-primary">
                <i class='bx bx-group'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Total Pengguna Terdaftar</div>
                <div class="stat-value">{{ number_format($stats['total_users']) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Akun</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-primary"></span>
                    <span>{{ $stats['active_users'] }} aktif &bull; {{ $stats['inactive_users'] }} nonaktif</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-info">
                <i class='bx bx-shield-quarter'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Pimpinan & Waka GTK</div>
                <div class="stat-value">{{ number_format($stats['admin_waka']) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Akun</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-info"></span>
                    <span>Admin Sekolah & Waka Satdik</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-success">
                <i class='bx bx-briefcase'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Pendidik & Guru KBM</div>
                <div class="stat-value">{{ number_format($stats['teachers']) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Guru</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-success"></span>
                    <span>Tenaga pendidik resmi</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-warning">
                <i class='bx bx-id-card'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Siswa & Wali Murid</div>
                <div class="stat-value">{{ number_format($stats['students'] + $stats['parents']) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Akun</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-warning"></span>
                    <span>{{ $stats['students'] }} siswa &bull; {{ $stats['parents'] }} wali</span>
                </div>
            </div>
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

    <!-- CATEGORY PILLS HORIZONTAL NAVIGATION -->
    <div class="user-cat-nav">
        @foreach($categories as $catKey => $catLabel)
            @php
                $isActive = ($currentCategory === $catKey);
                $queryParam = array_merge(request()->query(), ['category' => $catKey, 'page' => 1]);
                $dotColors = [
                    'all' => '#4680ff',
                    'admin' => '#2563eb',
                    'waka' => '#8b5cf6',
                    'teacher' => '#10b981',
                    'student' => '#06b6d4',
                    'parent' => '#f59e0b',
                    'cashier' => '#ef4444',
                ];
                $dotColor = $dotColors[$catKey] ?? '#4680ff';
            @endphp
            <a href="{{ route('admin.users.index', $queryParam) }}" class="user-cat-pill {{ $isActive ? 'active' : '' }}">
                @if(!$isActive)
                    <span class="cat-dot" style="background-color: {{ $dotColor }};"></span>
                @endif
                <span>{{ $catLabel }}</span>
            </a>
        @endforeach
    </div>

    <!-- MAIN DATA TABLE CARD -->
    <div class="vx-card">
        <!-- Card Header with Title and Unified Filter Form -->
        <div class="vx-card-header">
            <div>
                <h3 class="vx-card-title">
                    <i class='bx bx-list-ul text-primary'></i> Daftar Akun Pengguna
                </h3>
                <div class="cell-sub" style="margin-top: 2px;">
                    Total <strong>{{ $users->total() }}</strong> pengguna pada kategori terpilih.
                </div>
            </div>

            <!-- Unified Filter Bar -->
            <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin: 0;">
                <input type="hidden" name="category" value="{{ $currentCategory }}">

                <!-- Search Input -->
                <div style="position: relative; width: 210px;">
                    <i class='bx bx-search' style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1rem;"></i>
                    <input type="text" name="search" class="vx-input" style="padding-left: 2rem; height: 34px; font-size: 0.8rem;" placeholder="Nama, email, telepon..." value="{{ request('search') }}">
                </div>

                <!-- School Filter (For Super Admin) -->
                @if($isSuperAdmin)
                    <select name="school_id" class="vx-input" style="width: auto; height: 34px; font-size: 0.8rem; padding-right: 1.75rem;" onchange="this.form.submit()">
                        <option value="">Semua Satuan Pendidikan</option>
                        <option value="unassigned" {{ request('school_id') === 'unassigned' ? 'selected' : '' }}>⚠️ Belum Terdaftar</option>
                        @foreach($availableSchools as $sc)
                            <option value="{{ $sc->id }}" {{ request('school_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                        @endforeach
                    </select>
                @endif

                <!-- Status Filter -->
                <select name="status" class="vx-input" style="width: auto; height: 34px; font-size: 0.8rem; padding-right: 1.75rem;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm" style="height: 34px; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-filter-alt'></i> Filter
                </button>

                @if(request('search') || request('school_id') || request('status') || request('category') !== 'all')
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm btn-icon-sm" style="height: 34px; width: 34px; display: inline-flex; align-items: center; justify-content: center;" title="Reset Filter">
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
                        <th style="width: 200px;">Peran & Hak Akses</th>
                        <th style="min-width: 180px;">Satuan Pendidikan</th>
                        <th style="width: 90px; text-align: center;">Status</th>
                        <th style="width: 130px;">Terdaftar</th>
                        <th style="width: 130px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $idx => $u)
                        @php
                            $roleMeta = $availableRoles[$u->role] ?? null;
                            $roleCategory = $roleMeta['category'] ?? 'admin';
                            $roleTitle = $roleMeta['title'] ?? ucfirst(str_replace('_', ' ', $u->role));

                            $roleBadgeClass = match(true) {
                                $u->role === 'super_admin' => 'badge-danger',
                                $u->role === 'school_admin' => 'badge-primary',
                                str_starts_with($u->role, 'waka_') => 'badge-info',
                                $u->role === 'teacher' => 'badge-success',
                                $u->role === 'student' => 'badge-info',
                                $u->role === 'parent' => 'badge-warning',
                                $u->role === 'cashier' => 'badge-danger',
                                default => 'badge-dark',
                            };

                            // Bersihkan nama dari pengulangan peran di dalam kurung
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
                                    <div class="user-avatar-box {{ $roleCategory }} {{ $u->is_active ? '' : 'inactive' }}">
                                        {{ strtoupper(substr($cleanName, 0, 1)) }}
                                        <span class="user-status-dot {{ $u->is_active ? 'active' : 'inactive' }}" title="{{ $u->is_active ? 'Akun Aktif' : 'Akun Dinonaktifkan' }}"></span>
                                    </div>
                                    <div>
                                        <div class="cell-title">
                                            {{ $cleanName }}
                                            @if(auth()->id() === $u->id)
                                                <span class="vx-badge badge-primary" style="font-size: 0.65rem; margin-left: 0.25rem;">Anda</span>
                                            @endif
                                        </div>
                                        <div class="cell-sub" style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                                            <span><i class='bx bx-envelope text-primary'></i> {{ $u->email }}</span>
                                            @if($u->phone)
                                                <span>&bull;</span>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->phone) }}" target="_blank" style="color: var(--text-muted); text-decoration: none;" title="Hubungi via WhatsApp">
                                                    <i class='bx bxl-whatsapp' style="color: var(--vx-success);"></i> {{ $u->phone }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Peran & Hak Akses (Struktur Bersih, Tanpa Redundansi) -->
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

                            <!-- Satuan Pendidikan -->
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

                            <!-- Status Akun -->
                            <td style="text-align: center;">
                                @if($u->is_active)
                                    <span class="vx-badge badge-success"><i class='bx bx-check'></i> Aktif</span>
                                @else
                                    <span class="vx-badge badge-danger"><i class='bx bx-x'></i> Nonaktif</span>
                                @endif
                            </td>

                            <!-- Tanggal Terdaftar -->
                            <td>
                                <div class="cell-title" style="font-size: 0.78rem;">
                                    {{ $u->created_at ? $u->created_at->isoFormat('D MMM Y') : '-' }}
                                </div>
                                <div class="cell-sub cell-mono">
                                    {{ $u->created_at ? $u->created_at->format('H:i') . ' WIB' : '' }}
                                </div>
                            </td>

                            <!-- Aksi: Tombol Icon Bersih Segaris (Edit, Enrol, Toggle, Hapus) -->
                            <td style="text-align: center;">
                                <div class="user-action-btn-group">
                                    <!-- 1. Tombol Edit (Buka Modal Popup Edit) -->
                                    <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Ubah Data Pengguna"
                                        onclick="openEditUserModal({{ json_encode($u) }})">
                                        <i class='bx bx-edit'></i>
                                    </button>

                                    <!-- 2. Tombol Enrol (Buka Modal Popup Enrol) -->
                                    <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Enrol ke Satuan Pendidikan"
                                        onclick="openEnrollUserModal({{ $u->id }}, '{{ addslashes($cleanName) }}', '{{ $u->school_id ?? '' }}', '{{ addslashes($u->school->name ?? '') }}')">
                                        <i class='bx bx-buildings'></i>
                                    </button>

                                    <!-- 3. Tombol Toggle Status Kunci/Buka -->
                                    @if(auth()->id() !== $u->id)
                                        <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST" style="margin: 0; display: inline;">
                                            @csrf
                                            <button type="submit" class="btn {{ $u->is_active ? 'btn-secondary' : 'btn-warning-light' }} btn-xs btn-icon-sm"
                                                title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <i class='bx {{ $u->is_active ? "bx-lock-alt" : "bx-lock-open-alt" }}'></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 4. Tombol Hapus Pengguna -->
                                    @if(auth()->id() !== $u->id)
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="margin: 0; display: inline;"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ addslashes($cleanName) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Pengguna">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class='bx bx-user-x' style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                Tidak ada akun pengguna yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: gap; gap: 1rem;">
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

<!-- ==================================================== -->
<!-- MODAL POPUP 1: UBAH DATA PENGGUNA (EDIT VUEXY MODAL) -->
<!-- ==================================================== -->
<div class="vx-modal-backdrop" id="modalEditUser">
    <div class="vx-modal modal-lg">
        <div class="vx-modal-header">
            <h3 class="vx-modal-title">
                <i class='bx bx-edit text-primary'></i> Ubah Data Pengguna
            </h3>
            <button class="vx-modal-close" onclick="closeModal('modalEditUser')">&times;</button>
        </div>
        <form id="formEditUser" method="POST">
            @csrf
            @method('PUT')
            <div class="vx-modal-body">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Nama Lengkap Pengguna *</label>
                        <input type="text" name="name" id="editName" class="vx-input" required>
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Alamat Email Resmi *</label>
                        <input type="email" name="email" id="editEmail" class="vx-input" required>
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="editPhone" class="vx-input">
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Kategori & Hak Akses (Role) *</label>
                        <select name="role" id="editRole" class="vx-input" required onchange="handleEditRoleChange()">
                            @foreach($availableRoles as $rKey => $rMeta)
                                @if(!$isSuperAdmin && $rKey === 'super_admin')
                                    @continue
                                @endif
                                <option value="{{ $rKey }}">
                                    {{ $rMeta['title'] }} [{{ $rMeta['category_label'] }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tugas Tambahan GTK (Kondisional: Hanya tampil jika role = teacher) -->
                    <div class="form-row" id="editAdditionalDutyGroup" style="display: none;">
                        <label class="vx-label">Tugas Tambahan GTK (Waka / Kepala Unit)</label>
                        <select name="additional_role" id="editAdditionalRole" class="vx-input">
                            <option value="">-- Tanpa Tugas Tambahan (Guru Reguler) --</option>
                            @foreach($availableDuties as $dKey => $dMeta)
                                <option value="{{ $dKey }}">
                                    {{ $dMeta['title'] }} (+{{ $dMeta['teaching_hours'] }} Jam)
                                </option>
                            @endforeach
                        </select>
                        <div class="cell-sub">Pilih jika guru mengemban penugasan Waka, Kepala Lab/Perpus, OSIS.</div>
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Satuan Pendidikan</label>
                        @if($isSuperAdmin)
                            <select name="school_id" id="editSchoolId" class="vx-input">
                                <option value="">-- Tanpa Unit (Unassigned) --</option>
                                @foreach($availableSchools as $sc)
                                    <option value="{{ $sc->id }}">
                                        {{ $sc->name }} (NPSN: {{ $sc->npsn ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="school_id" id="editSchoolIdHidden">
                            <input type="text" class="vx-input" value="{{ $school->name }}" readonly style="background: var(--bg-subtle);">
                            <div class="cell-sub">Terkunci ke satuan pendidikan Anda.</div>
                        @endif
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Ubah Password (Opsional)</label>
                        <input type="password" name="password" id="editPasswordInput" class="vx-input" placeholder="Kosongkan jika tidak diubah" minlength="6">
                        <div class="cell-sub">Isi minimal 6 karakter jika ingin mengganti sandi pengguna.</div>
                    </div>

                    <div class="form-row span-2">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.25rem;">
                            <input type="checkbox" name="is_active" id="editIsActive" value="1" style="width: auto; margin: 0;">
                            <span class="cell-title" style="font-size: 0.85rem;">Status Akun Aktif (Dapat Login ke Sistem)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-surface);">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalEditUser')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                    <i class='bx bx-save'></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL POPUP 2: ENROL SATUAN PENDIDIKAN (VUEXY MODAL) -->
<!-- ==================================================== -->
<div class="vx-modal-backdrop" id="modalEnrollUser">
    <div class="vx-modal" style="max-width: 520px;">
        <div class="vx-modal-header">
            <h3 class="vx-modal-title">
                <i class='bx bx-buildings text-primary'></i> Enrol Satuan Pendidikan
            </h3>
            <button class="vx-modal-close" onclick="closeModal('modalEnrollUser')">&times;</button>
        </div>
        <form id="formEnrollUser" method="POST">
            @csrf
            <div class="vx-modal-body">
                <div style="background: var(--bg-subtle); border-left: 4px solid var(--bento-primary); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.82rem;">
                    <div style="margin-bottom: 0.25rem;">Pengguna: <strong id="enrollUserName" style="color: var(--text-heading);">-</strong></div>
                    <div>Unit Saat Ini: <span id="enrollCurrentSchool" class="vx-badge badge-dark">-</span></div>
                </div>

                <div class="form-row">
                    <label class="vx-label">Pilih Satuan Pendidikan Tujuan *</label>
                    @if($isSuperAdmin)
                        <select name="school_id" id="enrollSelectSchool" class="vx-input">
                            <option value="">-- Lepaskan Enrollment (Unassign) --</option>
                            @foreach($availableSchools as $sc)
                                <option value="{{ $sc->id }}">
                                    {{ $sc->name }} (NPSN: {{ $sc->npsn ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="cell-sub">Pilih sekolah target untuk mendaftarkan akun ini ke multi-tenant resmi.</div>
                    @else
                        <input type="hidden" name="school_id" value="{{ $school->id }}">
                        <div class="vx-input" style="background: var(--bg-subtle); display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                            <i class='bx bxs-school text-primary'></i> {{ $school->name }} (NPSN: {{ $school->npsn ?? '-' }})
                        </div>
                        <div class="cell-sub">Akun ini akan didaftarkan/dikonfirmasi ke sekolah tempat Anda bertugas.</div>
                    @endif
                </div>
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-surface);">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalEnrollUser')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                    <i class='bx bx-check-shield'></i> Simpan Enrollment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL POPUP 3: UNGGAH MASAL EXCEL (SISWA & GTK) -->
<!-- ==================================================== -->
<div class="vx-modal-backdrop" id="modalBulkImport">
    <div class="vx-modal" style="max-width: 620px;">
        <div class="vx-modal-header">
            <div>
                <h3 class="vx-modal-title" id="bulkImportTitle">
                    <i class='bx bxs-file-import text-primary'></i> Impor Masal Data
                </h3>
                <div class="cell-sub" id="bulkImportSub" style="margin-top: 3px;">
                    Unggah file Excel/CSV untuk menambahkan data dan akun pengguna secara masal.
                </div>
            </div>
            <button class="vx-modal-close" onclick="closeModal('modalBulkImport')">&times;</button>
        </div>
        <form id="formBulkImport" onsubmit="submitBulkImport(event)" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="bulkImportType" value="students">
            
            <div class="vx-modal-body" style="padding: 1.25rem 1.5rem; max-height: 75vh; overflow-y: auto;">
                <!-- Step 1: Download Template Official Box -->
                <div style="background: rgba(70, 128, 255, 0.05); border: 1px solid rgba(70, 128, 255, 0.2); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div>
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.2rem;">
                            <i class='bx bxs-download text-primary'></i> Langkah 1: Unduh Format Template
                        </div>
                        <div style="font-size: 0.76rem; color: var(--text-muted);">
                            Gunakan template resmi untuk memastikan urutan kolom dan data tervalidasi dengan tepat.
                        </div>
                    </div>
                    <a href="javascript:void(0)" id="btnDownloadTemplate" class="btn btn-outline-primary btn-sm" style="white-space: nowrap; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class='bx bx-download'></i> Unduh .xlsx
                    </a>
                </div>

                <!-- Info Box Otomasi Akun -->
                <div id="bulkImportAccountNotice" style="background: var(--bg-subtle); border-left: 4px solid #10b981; padding: 0.85rem 1rem; border-radius: var(--radius-md); font-size: 0.8rem; margin-bottom: 1.25rem; line-height: 1.45;">
                    <!-- Diisi dinamis via JS -->
                </div>

                <!-- Step 2: Upload File -->
                <div class="form-row" style="margin-bottom: 1rem;">
                    <label class="vx-label">Langkah 2: Pilih File Excel / CSV Hasil Isian *</label>
                    <input type="file" id="bulkImportFileInput" name="file" class="vx-input" accept=".xlsx,.xls,.csv" required style="padding: 0.45rem; font-size: 0.82rem;">
                    <div class="cell-sub" style="font-size: 0.72rem; margin-top: 0.25rem;">Format didukung: <strong>.xlsx, .xls, .csv</strong> (Ukuran maksimal: 10 MB).</div>
                </div>

                <!-- Step 3: Opsi Penanganan Duplikasi -->
                <div class="form-row" style="margin-bottom: 1rem;">
                    <label class="vx-label">Penanganan Data yang Sudah Terdaftar</label>
                    <select name="duplicate_mode" id="bulkImportDuplicateMode" class="vx-input" style="font-size: 0.82rem;">
                        <option value="skip" selected>Lewati (Abaikan baris yang NISN / Email-nya sudah ada)</option>
                        <option value="update">Perbarui (Update data lama dengan isian Excel terbaru)</option>
                    </select>
                </div>

                <!-- Dynamic Result Alert Container -->
                <div id="bulkImportResultBox" style="display: none; margin-top: 1rem;">
                    <!-- Will show success stats & errors -->
                </div>
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-surface);">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalBulkImport')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitBulkImport" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                    <i class='bx bx-cloud-upload'></i> Unggah & Proses Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleEditRoleChange() {
        const role = document.getElementById('editRole').value;
        const dutyGroup = document.getElementById('editAdditionalDutyGroup');
        if (role === 'teacher') {
            dutyGroup.style.display = 'block';
        } else {
            dutyGroup.style.display = 'none';
            document.getElementById('editAdditionalRole').value = '';
        }
    }

    function openEditUserModal(user) {
        document.getElementById('formEditUser').action = `{{ url('admin/users') }}/${user.id}`;
        document.getElementById('editName').value = user.name || '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editPhone').value = user.phone || '';
        document.getElementById('editRole').value = user.role || 'teacher';

        // Set Tugas Tambahan jika role teacher
        const dutySelect = document.getElementById('editAdditionalRole');
        if (dutySelect) {
            dutySelect.value = user.additional_role || '';
        }
        handleEditRoleChange();

        const schoolSelect = document.getElementById('editSchoolId');
        if (schoolSelect) {
            schoolSelect.value = user.school_id || '';
        }
        const schoolHidden = document.getElementById('editSchoolIdHidden');
        if (schoolHidden) {
            schoolHidden.value = user.school_id || '';
        }

        document.getElementById('editIsActive').checked = Boolean(user.is_active);
        document.getElementById('editPasswordInput').value = '';

        openModal('modalEditUser');
    }

    function openEnrollUserModal(userId, userName, currentSchoolId, currentSchoolName) {
        document.getElementById('formEnrollUser').action = `{{ url('admin/users') }}/${userId}/enroll`;
        document.getElementById('enrollUserName').innerText = userName;
        document.getElementById('enrollCurrentSchool').innerText = currentSchoolName || 'Belum Terdaftar';

        const selectSchool = document.getElementById('enrollSelectSchool');
        if (selectSchool) {
            selectSchool.value = currentSchoolId || '';
        }

        openModal('modalEnrollUser');
    }

    function openBulkImportModal(type) {
        document.getElementById('bulkImportType').value = type;
        document.getElementById('formBulkImport').reset();
        document.getElementById('bulkImportResultBox').style.display = 'none';
        document.getElementById('bulkImportResultBox').innerHTML = '';

        const titleEl = document.getElementById('bulkImportTitle');
        const subEl = document.getElementById('bulkImportSub');
        const dlBtn = document.getElementById('btnDownloadTemplate');
        const noticeEl = document.getElementById('bulkImportAccountNotice');

        if (type === 'students') {
            titleEl.innerHTML = `<i class='bx bxs-user-detail' style="color: #10b981;"></i> Impor Masal Data Siswa (Excel)`;
            subEl.innerText = 'Unggah file Excel untuk mendaftarkan siswa baru, menempatkan ke kelas, dan otomatis membuatkan akun login portal siswa.';
            dlBtn.href = `{{ route('admin.import.template.students') }}`;
            dlBtn.innerHTML = `<i class='bx bx-download'></i> Unduh Template Siswa (.xlsx)`;
            noticeEl.style.borderLeftColor = '#10b981';
            noticeEl.innerHTML = `
                <div style="font-weight: 700; color: #047857; margin-bottom: 0.25rem;">
                    <i class='bx bx-check-shield'></i> Otomasi Akun Login Siswa:
                </div>
                <div>• Siswa dapat langsung login menggunakan <strong>NISN</strong> (10 digit) sebagai username dan kata sandi (default: <code>Siswa123!</code>).</div>
                <div>• Kolom email siswa bersifat <strong>opsional</strong>. Jika kosong, sistem otomatis meng-generate alamat email sistem berbasis NISN.</div>
            `;
        } else {
            titleEl.innerHTML = `<i class='bx bxs-briefcase-alt-2' style="color: #4680ff;"></i> Impor Masal Guru & GTK / Karyawan (Excel)`;
            subEl.innerText = 'Unggah file Excel untuk mendaftarkan tenaga pendidik (guru) dan kependidikan (staf/TU/BK/kasir) beserta akun login.';
            dlBtn.href = `{{ route('admin.import.template.teachers') }}`;
            dlBtn.innerHTML = `<i class='bx bx-download'></i> Unduh Template GTK (.xlsx)`;
            noticeEl.style.borderLeftColor = '#4680ff';
            noticeEl.innerHTML = `
                <div style="font-weight: 700; color: #1d4ed8; margin-bottom: 0.25rem;">
                    <i class='bx bx-check-shield'></i> Otomasi Akun Login GTK:
                </div>
                <div>• GTK/karyawan dapat langsung login menggunakan <strong>Email Login</strong> dan kata sandi (default: <code>Sekolahku2026!</code>).</div>
                <div>• Peran hak akses akun (Guru, Staf TU, Guru BK, Bendahara) dan Tugas Tambahan (Waka Kurikulum/Kesiswaan/Sarpras/Humas) otomatis diselaraskan.</div>
            `;
        }

        openModal('modalBulkImport');
    }

    async function submitBulkImport(e) {
        e.preventDefault();
        const type = document.getElementById('bulkImportType').value;
        const fileInput = document.getElementById('bulkImportFileInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Silakan pilih file Excel / CSV terlebih dahulu.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('file', fileInput.files[0]);
        formData.append('duplicate_mode', document.getElementById('bulkImportDuplicateMode').value);

        const btn = document.getElementById('btnSubmitBulkImport');
        const origBtnText = btn.innerHTML;
        btn.innerHTML = `<i class='bx bx-loader-alt bx-spin'></i> Memproses Data...`;
        btn.disabled = true;

        const resultBox = document.getElementById('bulkImportResultBox');
        resultBox.style.display = 'block';
        resultBox.innerHTML = `
            <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(70, 128, 255, 0.08); border: 1px solid rgba(70, 128, 255, 0.25); text-align: center; font-size: 0.85rem; color: var(--text-heading);">
                <i class='bx bx-loader-alt bx-spin' style="font-size: 1.5rem; display: block; margin-bottom: 0.35rem; color: var(--bento-primary);"></i>
                Sedang membaca dan memvalidasi file Excel... Harap tunggu beberapa saat.
            </div>
        `;

        const targetUrl = (type === 'students') 
            ? `{{ route('admin.import.students') }}` 
            : `{{ route('admin.import.teachers') }}`;

        try {
            const res = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok && data.success) {
                const stats = data.data || {};
                let errorsHtml = '';
                if (stats.errors && stats.errors.length > 0) {
                    errorsHtml = `
                        <div style="margin-top: 0.75rem; max-height: 120px; overflow-y: auto; text-align: left; font-size: 0.76rem; background: rgba(0,0,0,0.04); padding: 0.5rem; border-radius: 4px;">
                            <strong>Catatan Peringatan (${stats.errors.length}):</strong>
                            <ul style="margin: 0.25rem 0 0; padding-left: 1.25rem;">
                                ${stats.errors.map(err => `<li>${err}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                }

                resultBox.innerHTML = `
                    <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #065f46; font-size: 0.82rem;">
                        <div style="font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.35rem;">
                            <i class='bx bx-check-circle' style="font-size: 1.25rem;"></i> Impor Berhasil!
                        </div>
                        <div>${data.message}</div>
                        <div style="margin-top: 0.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <span class="vx-badge badge-success">Baru: ${stats.imported || 0}</span>
                            <span class="vx-badge badge-info">Diperbarui: ${stats.updated || 0}</span>
                            <span class="vx-badge badge-secondary">Dilewati: ${stats.skipped || 0}</span>
                        </div>
                        ${errorsHtml}
                        <div style="margin-top: 0.75rem;">
                            <button type="button" class="btn btn-primary btn-xs" onclick="location.reload()">
                                <i class='bx bx-refresh'></i> Muat Ulang Halaman
                            </button>
                        </div>
                    </div>
                `;
            } else {
                resultBox.innerHTML = `
                    <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #991b1b; font-size: 0.82rem;">
                        <div style="font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.35rem;">
                            <i class='bx bx-error-circle' style="font-size: 1.25rem;"></i> Gagal Mengimpor
                        </div>
                        <div>${data.message || 'Terjadi kesalahan saat memproses data.'}</div>
                    </div>
                `;
            }
        } catch (err) {
            resultBox.innerHTML = `
                <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #991b1b; font-size: 0.82rem;">
                    Terjadi kesalahan koneksi jaringan.
                </div>
            `;
        } finally {
            btn.innerHTML = origBtnText;
            btn.disabled = false;
        }
    }
</script>
@endpush
@endsection
