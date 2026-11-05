@extends('layouts.vuexy')

@section('title', 'Profil & Biodata GTK — ' . $teacher->formatted_name)
@section('breadcrumb_parent', 'Pendidik & GTK')
@section('breadcrumb_current', 'Profil GTK')

@push('styles')
<style>
    /* Profil Header Card */
    .gtk-header-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .gtk-header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--bento-primary), #8b5cf6, #10b981);
    }
    .gtk-header-layout {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    /* Pas Foto Resmi 4x6 (Rasio 4:6) */
    .photo-4x6-wrap {
        width: 140px;
        flex-shrink: 0;
        text-align: center;
    }
    .photo-4x6-box {
        width: 140px;
        height: 210px; /* Rasio presisi 4x6 (140 x 210) */
        border-radius: var(--radius-md);
        border: 2px solid var(--border-color);
        background: var(--bg-subtle);
        box-shadow: var(--shadow-dropdown);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }
    .photo-4x6-box:hover {
        border-color: var(--bento-primary);
    }
    .photo-4x6-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .photo-4x6-empty {
        padding: 1rem 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--text-muted);
    }
    .photo-4x6-badge {
        position: absolute;
        bottom: 6px;
        left: 6px;
        right: 6px;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(4px);
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.2rem 0;
        border-radius: var(--radius-sm);
        text-align: center;
        letter-spacing: 0.04em;
    }
    .photo-upload-btn {
        margin-top: 0.5rem;
        width: 100%;
        font-size: 0.75rem;
        padding: 0.35rem 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    /* Identity Details in Banner */
    .gtk-id-details {
        flex: 1;
        min-width: 280px;
    }
    .gtk-name-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-heading);
        line-height: 1.25;
        margin-bottom: 0.4rem;
    }
    .gtk-badge-row {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
        margin-bottom: 0.85rem;
    }

    /* Completeness Progress Bar */
    .completeness-card {
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        max-width: 320px;
        margin-top: 0.75rem;
    }
    .progress-track {
        width: 100%;
        height: 8px;
        border-radius: var(--radius-pill);
        background: var(--border-color);
        overflow: hidden;
        margin-top: 0.35rem;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #06b6d4);
        border-radius: var(--radius-pill);
        transition: width 0.5s ease-in-out;
    }

    /* Tabs Styling */
    .gtk-tabs-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.25rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        scrollbar-width: thin;
    }
    .gtk-tab-btn {
        padding: 0.65rem 1.15rem;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        white-space: nowrap;
        transition: var(--transition);
    }
    .gtk-tab-btn:hover {
        color: var(--text-heading);
        background: var(--bg-subtle);
    }
    .gtk-tab-btn.active {
        color: var(--bento-primary);
        border-bottom-color: var(--bento-primary);
        background: var(--bg-surface);
    }
    .gtk-tab-pane {
        display: none;
    }
    .gtk-tab-pane.active {
        display: block;
        animation: fadeInTab 0.25s ease;
    }
    @keyframes fadeInTab {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
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
                <span class="vx-badge badge-primary">Profil GTK Dapodik</span>
                @if($isWindowOpen)
                    <span class="vx-badge badge-success"><i class='bx bx-lock-open-alt'></i> Periode Edit Mandiri Terbuka</span>
                @else
                    <span class="vx-badge badge-danger"><i class='bx bx-lock-alt'></i> Periode Edit Mandiri Ditutup</span>
                @endif
            </div>
            <h1>
                <i class='bx bx-id-card' style="color: var(--bento-primary);"></i>
                Profil & Biodata Lengkap GTK
            </h1>
            <p>Master biodata pendidik dan tenaga kependidikan resmi sesuai instrumen data Dapodik Kemendikbudristek.</p>
        </div>

        <!-- Admin Action Buttons (Jadwal Pengeditan & GTK Selector) -->
        <div class="page-actions" style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            @if($isStaff)
                <!-- Selector GTK untuk Admin -->
                @if($allTeachers->count() > 1)
                    <form method="GET" action="{{ route('teacher.profile') }}" style="margin: 0;">
                        <select name="teacher_id" class="vx-input" style="height: 34px; font-size: 0.8rem; padding-right: 1.8rem;" onchange="this.form.submit()">
                            @foreach($allTeachers as $ot)
                                <option value="{{ $ot->id }}" {{ $ot->id === $teacher->id ? 'selected' : '' }}>
                                    {{ $ot->formatted_name }} ({{ $ot->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <!-- Tombol Atur Jadwal Edit -->
                <button type="button" class="btn btn-secondary btn-sm" onclick="openModal('modalGtkWindowSettings')" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                    <i class='bx bx-calendar-edit text-primary'></i> Atur Jadwal Durasi Edit
                </button>
            @endif
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

    @if(!$canEdit)
        <div style="background: var(--bg-subtle); border-left: 4px solid var(--vx-warning); padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.83rem; display: flex; align-items: center; gap: 0.65rem;">
            <i class='bx bx-info-circle text-warning' style="font-size: 1.35rem;"></i>
            <div>
                <strong>Mode Tinjauan (Read-Only):</strong> Periode pemutakhiran data profil mandiri saat ini sedang <strong>ditutup</strong> oleh Administrator Sekolah. Jika terdapat data krusial yang memerlukan perbaikan, silakan hubungi bagian Tata Usaha / Operator Dapodik.
            </div>
        </div>
    @endif

    <!-- MAIN FORM WRAPPER -->
    <form action="{{ route('teacher.profile.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" id="formGtkProfile">
        @csrf
        @method('PUT')

        <!-- GTK PROFILE HEADER CARD -->
        <div class="gtk-header-card">
            <div class="gtk-header-layout">
                <!-- 1. Pas Foto Resmi 4x6 (Standar Formal Dapodik) -->
                <div class="photo-4x6-wrap">
                    <div class="photo-4x6-box" id="photoPreviewContainer">
                        @if($teacher->photo_url)
                            <img src="{{ $teacher->photo_url }}" alt="Pas Foto 4x6 {{ $teacher->full_name }}" class="photo-4x6-img" id="imgPhotoPreview">
                        @else
                            <div class="photo-4x6-empty" id="emptyPhotoPlaceholder">
                                <i class='bx bxs-user-badge' style="font-size: 3.5rem; opacity: 0.4;"></i>
                                <span style="font-size: 0.7rem; font-weight: 600;">Belum Ada Foto</span>
                            </div>
                            <img src="" alt="Preview" class="photo-4x6-img" id="imgPhotoPreview" style="display: none;">
                        @endif
                        <span class="photo-4x6-badge">PAS FOTO 4x6</span>
                    </div>

                    @if($canEdit)
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="previewPhoto4x6(this)">
                        <button type="button" class="btn btn-secondary btn-sm photo-upload-btn" onclick="document.getElementById('photoInput').click()">
                            <i class='bx bx-camera'></i> Unggah Foto 4x6
                        </button>
                        <small class="cell-sub" style="display: block; margin-top: 4px; font-size: 0.68rem;">Rasio 4:6, maks 2MB (JPG/PNG)</small>
                    @endif
                </div>

                <!-- 2. Informasi Utama GTK -->
                <div class="gtk-id-details">
                    <div class="gtk-name-title">
                        {{ $teacher->formatted_name }}
                    </div>

                    <div class="gtk-badge-row">
                        <span class="vx-badge badge-primary">
                            <i class='bx bx-briefcase'></i> {{ $teacher->employment_type ?? 'Guru Mapel' }}
                        </span>
                        <span class="vx-badge badge-info">
                            <i class='bx bx-id-card'></i> {{ $teacher->employment_status ?? 'GTT' }}
                        </span>
                        @if($teacher->is_certified)
                            <span class="vx-badge badge-success">
                                <i class='bx bx-award'></i> Bersertifikasi Pendidik
                            </span>
                        @endif
                        <span class="vx-badge badge-dark">
                            <i class='bx bxs-school'></i> {{ $teacher->school->name ?? $school->name }}
                        </span>
                    </div>

                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.82rem; margin-top: 0.5rem; color: var(--text-body);">
                        <div><strong style="color: var(--text-muted);">NIP:</strong> <span class="cell-mono font-weight-600">{{ $teacher->nip ?: '— (Non-PNS)' }}</span></div>
                        <div><strong style="color: var(--text-muted);">NUPTK:</strong> <span class="cell-mono font-weight-600">{{ $teacher->nuptk ?: '—' }}</span></div>
                        <div><strong style="color: var(--text-muted);">NIK:</strong> <span class="cell-mono font-weight-600">{{ $teacher->nik ?: '—' }}</span></div>
                    </div>

                    <!-- Indikator Kelengkapan Profil -->
                    <div class="completeness-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; font-weight: 600;">
                            <span style="color: var(--text-heading);"><i class='bx bx-pie-chart-alt text-primary'></i> Kelengkapan Data Dapodik:</span>
                            <span style="color: var(--vx-success);">{{ $teacher->profile_completeness }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-bar-fill" style="width: {{ $teacher->profile_completeness }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="gtk-tabs-nav">
            <button type="button" class="gtk-tab-btn active" onclick="switchGtkTab('tab-biodata', this)">
                <i class='bx bx-user'></i> 1. Biodata & Kontak
            </button>
            <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tab-kepegawaian', this)">
                <i class='bx bx-certification'></i> 2. Kepegawaian & Penugasan
            </button>
            <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tab-pendidikan', this)">
                <i class='bx bx-graduation'></i> 3. Pendidikan & Sertifikasi
            </button>
            <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tab-tugas', this)">
                <i class='bx bx-calendar-check'></i> 4. Tugas Tambahan & KBM
            </button>
            <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tab-bank', this)">
                <i class='bx bx-credit-card'></i> 5. Rekening Bank & NPWP
            </button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: BIODATA PRIBADI & KONTAK               -->
        <!-- ============================================== -->
        <div id="tab-biodata" class="gtk-tab-pane active">
            <div class="vx-card">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-user-pin text-primary'></i> Identitas Pribadi Sesuai KTP / KK</h3>
                </div>
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <div class="vx-form-grid form-grid-3">
                        <div class="form-row">
                            <label class="vx-label">Gelar Depan</label>
                            <input type="text" name="title_prefix" class="vx-input" placeholder="Contoh: Dr., Drs., Prof." value="{{ old('title_prefix', $teacher->title_prefix) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nama Lengkap (Sesuai KTP/Akta) *</label>
                            <input type="text" name="full_name" class="vx-input" placeholder="Nama lengkap tanpa gelar" value="{{ old('full_name', $teacher->full_name) }}" required {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Gelar Belakang</label>
                            <input type="text" name="title_suffix" class="vx-input" placeholder="Contoh: S.Pd., M.M., M.Pd." value="{{ old('title_suffix', $teacher->title_suffix) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>

                    <div class="vx-form-grid form-grid-3" style="margin-top: 0.85rem;">
                        <div class="form-row">
                            <label class="vx-label">Jenis Kelamin *</label>
                            <select name="gender" class="vx-input" required {{ $canEdit ? '' : 'disabled' }}>
                                <option value="L" {{ old('gender', $teacher->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $teacher->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" class="vx-input" maxlength="16" placeholder="16 digit angka NIK" value="{{ old('nik', $teacher->nik) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nama Ibu Kandung</label>
                            <input type="text" name="mother_maiden_name" class="vx-input" placeholder="Kunci validasi verifikasi Dapodik" value="{{ old('mother_maiden_name', $teacher->mother_maiden_name) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>

                    <div class="vx-form-grid form-grid-3" style="margin-top: 0.85rem;">
                        <div class="form-row">
                            <label class="vx-label">Tempat Lahir</label>
                            <input type="text" name="birth_place" class="vx-input" placeholder="Kota/Kabupaten kelahiran" value="{{ old('birth_place', $teacher->birth_place) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="vx-input" value="{{ old('birth_date', $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : '') }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Agama / Kepercayaan</label>
                            <select name="religion" class="vx-input" {{ $canEdit ? '' : 'disabled' }}>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $rel)
                                    <option value="{{ $rel }}" {{ old('religion', $teacher->religion) === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="vx-form-grid form-grid-3" style="margin-top: 0.85rem;">
                        <div class="form-row">
                            <label class="vx-label">Status Perkawinan</label>
                            <select name="marital_status" class="vx-input" {{ $canEdit ? '' : 'disabled' }}>
                                @foreach(['Belum Kawin', 'Kawin', 'Janda/Duda'] as $ms)
                                    <option value="{{ $ms }}" {{ old('marital_status', $teacher->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nama Suami / Istri</label>
                            <input type="text" name="spouse_name" class="vx-input" placeholder="Nama pasangan (jika menikah)" value="{{ old('spouse_name', $teacher->spouse_name) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Pekerjaan Pasangan</label>
                            <input type="text" name="spouse_job" class="vx-input" placeholder="Contoh: PNS, Karyawan Swasta, Wiraswasta" value="{{ old('spouse_job', $teacher->spouse_job) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>

                    <!-- Bagian Alamat Domisili -->
                    <div style="border-top: 1px solid var(--border-color); margin: 1.5rem 0 1.25rem 0; padding-top: 1.25rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-heading); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class='bx bx-map-pin text-primary'></i> Alamat Tempat Tinggal & Kontak
                        </h4>

                        <div class="form-row">
                            <label class="vx-label">Alamat Lengkap (Jalan, Dusun, RT/RW)</label>
                            <textarea name="address" class="vx-input" rows="2" placeholder="Alamat domisili saat ini" {{ $canEdit ? '' : 'readonly' }}>{{ old('address', $teacher->address) }}</textarea>
                        </div>

                        <div class="vx-form-grid form-grid-4" style="margin-top: 0.85rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.85rem;">
                            <div class="form-row">
                                <label class="vx-label">RT</label>
                                <input type="text" name="rt" class="vx-input" placeholder="001" value="{{ old('rt', $teacher->rt) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">RW</label>
                                <input type="text" name="rw" class="vx-input" placeholder="002" value="{{ old('rw', $teacher->rw) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Kelurahan / Desa</label>
                                <input type="text" name="sub_district" class="vx-input" placeholder="Kelurahan" value="{{ old('sub_district', $teacher->sub_district) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Kecamatan</label>
                                <input type="text" name="district" class="vx-input" placeholder="Kecamatan" value="{{ old('district', $teacher->district) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                        </div>

                        <div class="vx-form-grid form-grid-4" style="margin-top: 0.85rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.85rem;">
                            <div class="form-row">
                                <label class="vx-label">Kota / Kabupaten</label>
                                <input type="text" name="city" class="vx-input" placeholder="Kab/Kota" value="{{ old('city', $teacher->city) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Provinsi</label>
                                <input type="text" name="province" class="vx-input" placeholder="Provinsi" value="{{ old('province', $teacher->province) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Nomor WhatsApp / HP</label>
                                <input type="text" name="phone" class="vx-input" placeholder="08xxxxxxxxxx" value="{{ old('phone', $teacher->phone) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Email Resmi / Pribadi</label>
                                <input type="email" name="email" class="vx-input" placeholder="guru@sekolah.sch.id" value="{{ old('email', $teacher->email) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: KEPEGAWAIAN & PENUGASAN RESMI           -->
        <!-- ============================================== -->
        <div id="tab-kepegawaian" class="gtk-tab-pane">
            <div class="vx-card">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-briefcase text-primary'></i> Status Kepegawaian & Penugasan Dapodik</h3>
                </div>
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <div class="vx-form-grid form-grid-3">
                        <div class="form-row">
                            <label class="vx-label">Status Kepegawaian *</label>
                            <select name="employment_status" class="vx-input" required {{ $canEdit ? '' : 'disabled' }}>
                                @foreach(['PNS', 'CPNS', 'PPPK', 'GTY', 'GTT', 'HONOR_SEKOLAH'] as $es)
                                    <option value="{{ $es }}" {{ old('employment_status', $teacher->employment_status) === $es ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $es) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" class="vx-input cell-mono" placeholder="18 digit NIP bagi PNS/PPPK" value="{{ old('nip', $teacher->nip) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">NUPTK</label>
                            <input type="text" name="nuptk" class="vx-input cell-mono" placeholder="16 digit NUPTK resmi" value="{{ old('nuptk', $teacher->nuptk) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>

                    <div class="vx-form-grid form-grid-3" style="margin-top: 0.85rem;">
                        <div class="form-row">
                            <label class="vx-label">Jenis PTK (Peran Fungsional) *</label>
                            <select name="employment_type" class="vx-input" required {{ $canEdit ? '' : 'disabled' }}>
                                @foreach([
                                    'GURU_MAPEL' => 'Guru Mata Pelajaran',
                                    'GURU_KELAS' => 'Guru Kelas (SD)',
                                    'GURU_BK' => 'Guru BK / Konselor',
                                    'KEPALA_SEKOLAH' => 'Kepala Sekolah',
                                    'TENAGA_ADMINISTRASI' => 'Tenaga Administrasi Sekolah (TU)',
                                    'PUSTAKAWAN' => 'Tenaga Perpustakaan',
                                    'LABORAN' => 'Tenaga Laboratorium'
                                ] as $ptkKey => $ptkLabel)
                                    <option value="{{ $ptkKey }}" {{ old('employment_type', $teacher->employment_type) === $ptkKey ? 'selected' : '' }}>
                                        {{ $ptkLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Lembaga Pengangkat</label>
                            <input type="text" name="institution_assignment" class="vx-input" placeholder="Contoh: Pemerintah Provinsi DKI Jakarta / Yayasan" value="{{ old('institution_assignment', $teacher->institution_assignment) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Sumber Gaji</label>
                            <select name="salary_source" class="vx-input" {{ $canEdit ? '' : 'disabled' }}>
                                @foreach(['APBD Provinsi', 'APBD Kab/Kota', 'APBN', 'Yayasan', 'Sekolah / BOS'] as $ss)
                                    <option value="{{ $ss }}" {{ old('salary_source', $teacher->salary_source) === $ss ? 'selected' : '' }}>{{ $ss }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="vx-form-grid form-grid-3" style="margin-top: 0.85rem;">
                        <div class="form-row">
                            <label class="vx-label">Nomor SK Pengangkatan</label>
                            <input type="text" name="sk_appointment_number" class="vx-input" placeholder="Nomor surat keputusan pengangkatan" value="{{ old('sk_appointment_number', $teacher->sk_appointment_number) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Tanggal SK Pengangkatan</label>
                            <input type="date" name="sk_appointment_date" class="vx-input" value="{{ old('sk_appointment_date', $teacher->sk_appointment_date ? $teacher->sk_appointment_date->format('Y-m-d') : '') }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">TMT Pengangkatan (Mulai Bertugas)</label>
                            <input type="date" name="tmt_appointment" class="vx-input" value="{{ old('tmt_appointment', $teacher->tmt_appointment ? $teacher->tmt_appointment->format('Y-m-d') : '') }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>

                    <div class="form-row" style="margin-top: 0.85rem;">
                        <label class="vx-label">Status Satuan Pendidikan Induk (Satminkal)</label>
                        <select name="institution_assignment_status" class="vx-input" style="max-width: 320px;" {{ $canEdit ? '' : 'disabled' }}>
                            <option value="Induk" {{ old('institution_assignment_status', $teacher->institution_assignment_status) === 'Induk' ? 'selected' : '' }}>Sekolah Induk (Satminkal Utama)</option>
                            <option value="Non-Induk" {{ old('institution_assignment_status', $teacher->institution_assignment_status) === 'Non-Induk' ? 'selected' : '' }}>Sekolah Non-Induk (Mengajar Tambahan)</option>
                        </select>
                        <div class="cell-sub">Menentukan basis penetapan penerbitan SKTP dan kelayakan tunjangan sertifikasi.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: PENDIDIKAN & SERTIFIKASI GURU          -->
        <!-- ============================================== -->
        <div id="tab-pendidikan" class="gtk-tab-pane">
            <div class="vx-card">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-graduation text-primary'></i> Riwayat Pendidikan Formal & Sertifikasi Profesi Pendidik</h3>
                </div>
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <!-- Riwayat Pendidikan -->
                    <div class="vx-form-grid form-grid-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.85rem;">
                        <div class="form-row">
                            <label class="vx-label">Jenjang Terakhir *</label>
                            <select name="last_education" class="vx-input" required {{ $canEdit ? '' : 'disabled' }}>
                                @foreach(['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'] as $edu)
                                    <option value="{{ $edu }}" {{ old('last_education', $teacher->last_education) === $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Program Studi / Jurusan</label>
                            <input type="text" name="major_study" class="vx-input" placeholder="Contoh: Pendidikan Matematika" value="{{ old('major_study', $teacher->major_study) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nama Universitas / Kampus</label>
                            <input type="text" name="university_name" class="vx-input" placeholder="Contoh: Universitas Negeri Jakarta" value="{{ old('university_name', $teacher->university_name) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Tahun Kelulusan</label>
                            <input type="text" name="graduation_year" class="vx-input cell-mono" placeholder="Tahun lulus (cth: 2015)" value="{{ old('graduation_year', $teacher->graduation_year) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>

                    <!-- Sertifikasi Guru -->
                    <div style="border-top: 1px solid var(--border-color); margin: 1.5rem 0 1.25rem 0; padding-top: 1.25rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-heading); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class='bx bx-award text-success'></i> Data Sertifikasi Pendidik (PPG / Portofolio)
                        </h4>

                        <div class="form-row">
                            <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                                <input type="checkbox" name="is_certified" value="1" {{ old('is_certified', $teacher->is_certified) ? 'checked' : '' }} style="width: auto; margin: 0;" {{ $canEdit ? '' : 'disabled' }}>
                                <span class="cell-title" style="font-size: 0.85rem;">Sudah Memiliki Sertifikat Pendidik (Gr. / Bersertifikasi TPG)</span>
                            </label>
                        </div>

                        <div class="vx-form-grid form-grid-3" style="margin-top: 0.85rem;">
                            <div class="form-row">
                                <label class="vx-label">Nomor Sertifikat Pendidik</label>
                                <input type="text" name="certification_number" class="vx-input cell-mono" placeholder="Nomor seri sertifikat pendidik" value="{{ old('certification_number', $teacher->certification_number) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Nomor Registrasi Guru (NRG)</label>
                                <input type="text" name="nrg" class="vx-input cell-mono" placeholder="Nomor registrasi NRG resmi" value="{{ old('nrg', $teacher->nrg) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Bidang Studi Sertifikasi</label>
                                <input type="text" name="certification_subject" class="vx-input" placeholder="Mata pelajaran yang disertifikasi" value="{{ old('certification_subject', $teacher->certification_subject) }}" {{ $canEdit ? '' : 'readonly' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: TUGAS TAMBAHAN & KBM                    -->
        <!-- ============================================== -->
        <div id="tab-tugas" class="gtk-tab-pane">
            <div class="vx-card">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-calendar-check text-primary'></i> Tugas Tambahan & Ekuivalensi Jam Mengajar (JJM)</h3>
                </div>
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <div class="vx-form-grid form-grid-2">
                        <div class="form-row">
                            <label class="vx-label">Jabatan Tugas Tambahan Resmi</label>
                            <input type="text" name="additional_duties" class="vx-input" placeholder="Contoh: Waka Kurikulum, Kepala Laboratorium Komputer, Pembina OSIS" value="{{ old('additional_duties', is_array($teacher->additional_duties) ? implode(', ', $teacher->additional_duties) : $teacher->additional_duties) }}" {{ $canEdit ? '' : 'readonly' }}>
                            <div class="cell-sub">Pisahkan dengan koma jika mengemban lebih dari satu tugas tambahan.</div>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Total Ekuivalensi Jam Tatap Muka (JJM per Minggu)</label>
                            <input type="number" name="teaching_hours" class="vx-input cell-mono" min="0" max="80" value="{{ old('teaching_hours', $teacher->teaching_hours ?? 24) }}" {{ $canEdit ? '' : 'readonly' }}>
                            <div class="cell-sub">Standar pemenuhan beban kerja guru minimal 24 jam tatap muka/minggu.</div>
                        </div>
                    </div>

                    <!-- Jadwal KBM Aktif yang Terhubung -->
                    <div style="border-top: 1px solid var(--border-color); margin: 1.5rem 0 1.25rem 0; padding-top: 1.25rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-heading); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class='bx bx-book-open text-primary'></i> Matriks Mengajar KBM Aktif di Sistem
                        </h4>

                        @if($teacher->schedules && $teacher->schedules->count() > 0)
                            <div class="table-wrap">
                                <table class="vx-table">
                                    <thead>
                                        <tr>
                                            <th>Hari</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Kelas</th>
                                            <th>Jam Pelajaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($teacher->schedules as $sc)
                                            <tr>
                                                <td class="font-weight-600">{{ $sc->day ?? '-' }}</td>
                                                <td><span class="vx-badge badge-primary">{{ $sc->subject->name ?? '-' }}</span></td>
                                                <td>{{ $sc->schoolClass->name ?? '-' }}</td>
                                                <td class="cell-mono">{{ $sc->start_time ?? '-' }} - {{ $sc->end_time ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div style="background: var(--bg-subtle); padding: 1.5rem; text-align: center; border-radius: var(--radius-md); color: var(--text-muted); font-size: 0.85rem;">
                                <i class='bx bx-calendar-x' style="font-size: 2rem; display: block; margin-bottom: 0.35rem; opacity: 0.5;"></i>
                                Belum ada plotting jadwal pelajaran KBM yang ditugaskan ke guru ini pada semester aktif.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 5: REKENING BANK & PERPAJAKAN             -->
        <!-- ============================================== -->
        <div id="tab-bank" class="gtk-tab-pane">
            <div class="vx-card">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-credit-card text-primary'></i> Data Kesejahteraan, Rekening Penyaluran & NPWP</h3>
                </div>
                <div class="vx-card-body" style="padding: 1.5rem;">
                    <div class="vx-form-grid form-grid-2">
                        <div class="form-row">
                            <label class="vx-label">Nomor Pokok Wajib Pajak (NPWP)</label>
                            <input type="text" name="npwp" class="vx-input cell-mono" placeholder="15 atau 16 digit angka NPWP" value="{{ old('npwp', $teacher->npwp) }}" {{ $canEdit ? '' : 'readonly' }}>
                            <div class="cell-sub">Digunakan untuk pelaporan SPT tahunan dan pemotongan PPh 21 resmi.</div>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nama Bank Penyalur</label>
                            <input type="text" name="bank_name" class="vx-input" placeholder="Contoh: Bank BNI, Bank Mandiri, Bank BRI, Bank DKI/BPD" value="{{ old('bank_name', $teacher->bank_name) }}" {{ $canEdit ? '' : 'readonly' }}>
                            <div class="cell-sub">Bank tempat pencairan gaji / honor / tunjangan profesi.</div>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nomor Rekening Bank</label>
                            <input type="text" name="bank_account_number" class="vx-input cell-mono" placeholder="Nomor rekening buku tabungan" value="{{ old('bank_account_number', $teacher->bank_account_number) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Nama Pemilik Rekening (Sesuai Buku Tabungan)</label>
                            <input type="text" name="bank_account_name" class="vx-input" placeholder="Harus identik dengan nama pada rekening bank" value="{{ old('bank_account_name', $teacher->bank_account_name) }}" {{ $canEdit ? '' : 'readonly' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STICKY ACTION SUBMIT BAR -->
        @if($canEdit)
            <div style="position: sticky; bottom: 20px; z-index: 100; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1rem 1.5rem; box-shadow: var(--shadow-dropdown); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div class="cell-sub" style="display: flex; align-items: center; gap: 0.45rem;">
                    <i class='bx bx-check-shield text-success' style="font-size: 1.25rem;"></i>
                    <span>Pastikan data yang Anda isi telah sesuai dengan berkas fisik resmi dan kartu identitas.</span>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Batal</a>
                    <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;">
                        <i class='bx bx-save'></i> Simpan Perubahan Profil GTK
                    </button>
                </div>
            </div>
        @endif
    </form>

</div>

<!-- ==================================================== -->
<!-- MODAL: PENGATURAN JADWAL DURASI EDIT PROFIL (ADMIN)  -->
<!-- ==================================================== -->
@if($isStaff)
<div class="vx-modal-backdrop" id="modalGtkWindowSettings">
    <div class="vx-modal" style="max-width: 520px;">
        <div class="vx-modal-header">
            <h3 class="vx-modal-title">
                <i class='bx bx-calendar-event text-primary'></i> Atur Jadwal Durasi Edit Profil GTK
            </h3>
            <button class="vx-modal-close" onclick="closeModal('modalGtkWindowSettings')">&times;</button>
        </div>
        <form action="{{ route('admin.gtk.profile.window') }}" method="POST">
            @csrf
            <input type="hidden" name="school_id" value="{{ $school->id }}">

            <div class="vx-modal-body">
                <div style="background: var(--bg-subtle); border-left: 4px solid var(--bento-primary); padding: 0.85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.82rem;">
                    Tentukan rentang tanggal di mana para Guru & Tenaga Kependidikan (GTK) diizinkan mengisi dan memutakhirkan biodata mandiri Dapodik mereka.
                </div>

                <div class="form-row">
                    <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                        <input type="checkbox" name="gtk_profile_edit_enabled" value="1" {{ $school->gtk_profile_edit_enabled ? 'checked' : '' }} style="width: auto; margin: 0;">
                        <span class="cell-title" style="font-size: 0.85rem;">
                            Buka Akses Pengeditan Mandiri untuk GTK
                        </span>
                    </label>
                    <div class="cell-sub" style="margin-left: 1.6rem;">Jika dicentang, guru dan tendik dapat memperbarui data mereka selama periode berlaku.</div>
                </div>

                <div class="vx-form-grid form-grid-2" style="margin-top: 1rem;">
                    <div class="form-row">
                        <label class="vx-label">Tanggal Mulai Buka</label>
                        <input type="date" name="gtk_profile_edit_start" class="vx-input" value="{{ $school->gtk_profile_edit_start ? $school->gtk_profile_edit_start->format('Y-m-d') : '' }}">
                        <div class="cell-sub">Kosongkan jika dibuka langsung.</div>
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Tanggal Batas Akhir (Deadline)</label>
                        <input type="date" name="gtk_profile_edit_end" class="vx-input" value="{{ $school->gtk_profile_edit_end ? $school->gtk_profile_edit_end->format('Y-m-d') : '' }}">
                        <div class="cell-sub">Lewat tanggal ini, akun GTK terkunci read-only.</div>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-surface);">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalGtkWindowSettings')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                    <i class='bx bx-save'></i> Simpan Jadwal Durasi
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Tab switching logic
    function switchGtkTab(targetId, btnElement) {
        document.querySelectorAll('.gtk-tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.gtk-tab-btn').forEach(el => el.classList.remove('active'));

        const targetPane = document.getElementById(targetId);
        if (targetPane) {
            targetPane.classList.add('active');
        }
        if (btnElement) {
            btnElement.classList.add('active');
        }
    }

    // Instant Preview Pas Foto 4x6
    function previewPhoto4x6(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('imgPhotoPreview');
                const emptyPlaceholder = document.getElementById('emptyPhotoPlaceholder');
                
                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                }
                if (emptyPlaceholder) {
                    emptyPlaceholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection

