@extends('layouts.vuexy')

@section('title', 'Tambah Pengguna Baru — Hak Akses RBAC')
@section('breadcrumb_parent', 'Manajemen Pengguna')
@section('breadcrumb_current', 'Tambah Pengguna')

@section('content')
<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <div class="page-title-row">
        <div class="page-heading">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-left-arrow-alt'></i> Kembali ke Direktori
                </a>
                <span class="vx-badge badge-primary">Formulir Pendaftaran Akun</span>
            </div>
            <h1>
                <i class='bx bx-user-plus' style="color: var(--bento-primary);"></i>
                Tambah Pengguna Baru
            </h1>
            <p>Registrasi akun pengguna baru, penentuan peran (Role-Based Access Control), dan penetapan satuan pendidikan resmi.</p>
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
                        <i class='bx bx-id-card text-primary'></i> Informasi Biodata & Kredensial Pengguna
                    </h3>
                    <div class="cell-sub" style="margin-top: 2px;">
                        Lengkapi data berikut untuk membuat akses masuk akun ke platform Sekolahku.
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <div class="vx-form-grid form-grid-2">
                        <!-- Nama Lengkap -->
                        <div class="form-row">
                            <label class="vx-label">Nama Lengkap Pengguna *</label>
                            <input type="text" name="name" class="vx-input" placeholder="Contoh: Ahmad Fauzi, S.Pd." value="{{ old('name') }}" required>
                            <div class="cell-sub">Gunakan nama lengkap beserta gelar jika merupakan GTK / Pendidik.</div>
                        </div>

                        <!-- Email Resmi -->
                        <div class="form-row">
                            <label class="vx-label">Alamat Email Resmi *</label>
                            <input type="email" name="email" class="vx-input" placeholder="nama@sekolah.sch.id" value="{{ old('email') }}" required>
                            <div class="cell-sub">Email unik yang digunakan untuk login dan notifikasi sistem.</div>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="form-row">
                            <label class="vx-label">Nomor Telepon / WhatsApp (Opsional)</label>
                            <input type="text" name="phone" class="vx-input" placeholder="Contoh: 081234567890" value="{{ old('phone') }}">
                            <div class="cell-sub">Digunakan untuk komunikasi cepat dan verifikasi.</div>
                        </div>

                        <!-- Peran & Hak Akses -->
                        <div class="form-row">
                            <label class="vx-label">Kategori & Hak Akses (Role) *</label>
                            <select name="role" id="createRoleSelect" class="vx-input" required onchange="handleRoleChange()">
                                <option value="">-- Pilih Peran Resmi --</option>
                                @foreach($availableRoles as $rKey => $rMeta)
                                    <option value="{{ $rKey }}" {{ old('role') === $rKey ? 'selected' : '' }}>
                                        {{ $rMeta['title'] }} [{{ $rMeta['category_label'] }}]
                                    </option>
                                @endforeach
                            </select>
                            <div class="cell-sub">Menentukan hak akses menu dan otoritas pada aplikasi.</div>
                        </div>

                        <!-- Tugas Tambahan GTK (Waka / Kepala Lab / Perpustakaan) -->
                        <div class="form-row" id="additionalDutyGroup" style="{{ old('role') === 'teacher' ? '' : 'display: none;' }}">
                            <label class="vx-label">Tugas Tambahan GTK (Waka / Kepala Unit)</label>
                            <select name="additional_role" id="createAdditionalRole" class="vx-input">
                                <option value="">-- Tanpa Tugas Tambahan (Guru Reguler) --</option>
                                @foreach($availableDuties as $dKey => $dMeta)
                                    <option value="{{ $dKey }}" {{ old('additional_role') === $dKey ? 'selected' : '' }}>
                                        {{ $dMeta['title'] }} (+{{ $dMeta['teaching_hours'] }} Jam)
                                    </option>
                                @endforeach
                            </select>
                            <div class="cell-sub">Diisi jika GTK menjabat sebagai Waka, Kepala Lab/Perpus, atau Pembina OSIS.</div>
                        </div>

                        <!-- Satuan Pendidikan -->
                        <div class="form-row">
                            <label class="vx-label">Satuan Pendidikan (Enrollment)</label>
                            @if($isSuperAdmin)
                                <select name="school_id" class="vx-input">
                                    <option value="">-- Tanpa Unit (Unassigned) --</option>
                                    @foreach($availableSchools as $sc)
                                        <option value="{{ $sc->id }}" {{ old('school_id', $school->id) == $sc->id ? 'selected' : '' }}>
                                            {{ $sc->name }} (NPSN: {{ $sc->npsn ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="cell-sub">Pilih sekolah tempat pengguna bertugas atau belajar.</div>
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
                            <label class="vx-label">Kata Sandi (Password) *</label>
                            <div style="display: flex; gap: 0.35rem;">
                                <input type="password" name="password" id="createPasswordInput" class="vx-input" placeholder="Minimal 6 karakter" required minlength="6" style="flex: 1;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="togglePasswordVisibility('createPasswordInput')" title="Lihat Sandi" style="width: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class='bx bx-show'></i>
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="generateRandomPassword()" title="Generate Sandi Acak" style="white-space: nowrap; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class='bx bx-refresh'></i> Acak
                                </button>
                            </div>
                            <div class="cell-sub">Minimal 6 karakter, dapat digenerate otomatis.</div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="form-row span-2">
                            <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md);">
                                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; margin: 0;">
                                    <input type="checkbox" name="is_active" id="createIsActive" value="1" checked style="width: auto; margin: 0;">
                                    <span class="cell-title" style="font-size: 0.85rem;">
                                        Aktifkan Akun Pengguna Langsung (Pengguna dapat langsung login ke portal)
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
                            <i class='bx bx-save'></i> Simpan & Daftarkan Pengguna
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function handleRoleChange() {
        const role = document.getElementById('createRoleSelect').value;
        const group = document.getElementById('additionalDutyGroup');
        if (role === 'teacher') {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
            document.getElementById('createAdditionalRole').value = '';
        }
    }

    function togglePasswordVisibility(inputId) {
        const el = document.getElementById(inputId);
        if (el) {
            el.type = el.type === 'password' ? 'text' : 'password';
        }
    }

    function generateRandomPassword() {
        const chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$";
        let pass = "";
        for (let i = 0; i < 10; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById('createPasswordInput');
        if (input) {
            input.value = pass;
            input.type = 'text';
        }
    }
</script>
@endpush
@endsection
