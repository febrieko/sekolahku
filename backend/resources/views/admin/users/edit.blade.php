@extends('layouts.vuexy')

@section('title', 'Ubah Data Pengguna — ' . $targetUser->name)
@section('breadcrumb_parent', 'Manajemen Pengguna')
@section('breadcrumb_current', 'Ubah Data')

@section('content')
<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <div class="page-title-row">
        <div class="page-heading">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-left-arrow-alt'></i> Kembali ke Direktori
                </a>
                <span class="vx-badge badge-primary">ID Akun: #{{ $targetUser->id }}</span>
            </div>
            <h1>
                <i class='bx bx-edit' style="color: var(--bento-primary);"></i>
                Ubah Data Pengguna — {{ $targetUser->name }}
            </h1>
            <p>Perbarui profil pengguna, penetapan peran (Role), kredensial akun, serta penugasan satuan pendidikan.</p>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if($errors->any())
        <div style="background: var(--vx-danger-light); border: 1px solid var(--vx-danger-border); color: var(--vx-danger); padding: 1rem 1.25rem; border-radius: var(--radius-lg); margin-bottom: 1.25rem;">
            <div style="font-weight: 700; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class='bx bx-error-circle' style="font-size: 1.25rem;"></i> Terjadi kesalahan validasi input:
            </div>
            <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.82rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM CARD -->
    <div style="max-width: 900px; margin: 0 auto;">
        <div class="vx-card">
            <div class="vx-card-header">
                <div>
                    <h3 class="vx-card-title">
                        <i class='bx bx-user-pin text-primary'></i> Perbarui Data Akun Pengguna
                    </h3>
                    <div class="cell-sub" style="margin-top: 2px;">
                        Pastikan data yang dimasukkan valid sesuai identitas resmi pengguna.
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.users.update', $targetUser->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <div class="vx-form-grid form-grid-2">
                        <!-- Nama Lengkap -->
                        <div class="form-row">
                            <label class="vx-label">Nama Lengkap Pengguna *</label>
                            <input type="text" name="name" class="vx-input" value="{{ old('name', $targetUser->name) }}" required>
                        </div>

                        <!-- Email Resmi -->
                        <div class="form-row">
                            <label class="vx-label">Alamat Email Resmi *</label>
                            <input type="email" name="email" class="vx-input" value="{{ old('email', $targetUser->email) }}" required>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="form-row">
                            <label class="vx-label">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" class="vx-input" value="{{ old('phone', $targetUser->phone) }}">
                        </div>

                        <!-- Peran & Hak Akses -->
                        <div class="form-row">
                            <label class="vx-label">Kategori & Hak Akses (Role) *</label>
                            <select name="role" id="editUserRoleSelect" class="vx-input" required onchange="handleEditRoleChange()">
                                @foreach($availableRoles as $rKey => $rMeta)
                                    <option value="{{ $rKey }}" {{ old('role', $targetUser->role) === $rKey ? 'selected' : '' }}>
                                        {{ $rMeta['title'] }} [{{ $rMeta['category_label'] }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tugas Tambahan GTK (Waka / Kepala Lab / Perpustakaan) -->
                        <div class="form-row" id="editAdditionalDutyGroup" style="{{ old('role', $targetUser->role) === 'teacher' ? '' : 'display: none;' }}">
                            <label class="vx-label">Tugas Tambahan GTK (Waka / Kepala Unit)</label>
                            <select name="additional_role" id="editAdditionalRole" class="vx-input">
                                <option value="">-- Tanpa Tugas Tambahan (Guru Reguler) --</option>
                                @foreach($availableDuties as $dKey => $dMeta)
                                    <option value="{{ $dKey }}" {{ old('additional_role', $targetUser->additional_role) === $dKey ? 'selected' : '' }}>
                                        {{ $dMeta['title'] }} (+{{ $dMeta['teaching_hours'] }} Jam)
                                    </option>
                                @endforeach
                            </select>
                            <div class="cell-sub">Diisi jika GTK menjabat sebagai Waka, Kepala Lab/Perpus, atau Pembina OSIS.</div>
                        </div>

                        <!-- Satuan Pendidikan -->
                        <div class="form-row">
                            <label class="vx-label">Satuan Pendidikan</label>
                            @if($isSuperAdmin)
                                <select name="school_id" class="vx-input">
                                    <option value="">-- Tanpa Unit (Unassigned) --</option>
                                    @foreach($availableSchools as $sc)
                                        <option value="{{ $sc->id }}" {{ old('school_id', $targetUser->school_id) == $sc->id ? 'selected' : '' }}>
                                            {{ $sc->name }} (NPSN: {{ $sc->npsn ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="school_id" value="{{ $school->id }}">
                                <div class="vx-input" style="background: var(--bg-subtle); display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                    <i class='bx bxs-school text-primary'></i> {{ $school->name }} (NPSN: {{ $school->npsn ?? '-' }})
                                </div>
                                <div class="cell-sub">Terkunci ke satuan pendidikan Anda.</div>
                            @endif
                        </div>

                        <!-- Kata Sandi -->
                        <div class="form-row">
                            <label class="vx-label">Ganti Password (Opsional)</label>
                            <div style="display: flex; gap: 0.35rem;">
                                <input type="password" name="password" id="editPasswordInput" class="vx-input" placeholder="Kosongkan jika tidak diubah" minlength="6" style="flex: 1;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="togglePasswordVisibility('editPasswordInput')" title="Lihat Sandi" style="width: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class='bx bx-show'></i>
                                </button>
                            </div>
                            <div class="cell-sub">Isi minimal 6 karakter hanya jika ingin mengganti sandi akun.</div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="form-row span-2">
                            <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md);">
                                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; margin: 0;">
                                    <input type="checkbox" name="is_active" id="editIsActive" value="1" {{ $targetUser->is_active ? 'checked' : '' }} style="width: auto; margin: 0;">
                                    <span class="cell-title" style="font-size: 0.85rem;">
                                        Status Akun Aktif (Pengguna dapat masuk ke portal sistem)
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem; padding-top: 1.25rem; margin-top: 1rem; border-top: 1px solid var(--border-color);">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                            <i class='bx bx-save'></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function handleEditRoleChange() {
        const role = document.getElementById('editUserRoleSelect').value;
        const group = document.getElementById('editAdditionalDutyGroup');
        if (role === 'teacher') {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
            document.getElementById('editAdditionalRole').value = '';
        }
    }

    function togglePasswordVisibility(inputId) {
        const el = document.getElementById(inputId);
        if (el) {
            el.type = el.type === 'password' ? 'text' : 'password';
        }
    }
</script>
@endpush
@endsection
