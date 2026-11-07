@extends('layouts.vuexy')

@section('title', 'Identitas & Branding Aplikasi')
@section('breadcrumb_current', 'Identitas & Branding')

@push('styles')
<style>
    .branding-grid {
        display: grid;
        grid-template-columns: 1.25fr 0.95fr;
        gap: 1.5rem;
    }
    @media (max-width: 991px) {
        .branding-grid {
            grid-template-columns: 1fr;
        }
    }
    .branding-card {
        background: var(--bg-card, #FFFFFF);
        border: 1px solid var(--border-color, #E2E8F0);
        border-radius: var(--radius-lg, 16px);
        box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .branding-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color, #E2E8F0);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .branding-card-header h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-heading, #0F172A);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .branding-card-body {
        padding: 1.5rem;
    }
    .form-group-custom {
        margin-bottom: 1.25rem;
    }
    .form-label-custom {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-heading, #1E293B);
        margin-bottom: 0.4rem;
    }
    .form-label-custom small {
        font-weight: 400;
        color: var(--text-muted, #64748B);
    }
    .form-control-custom {
        width: 100%;
        padding: 0.65rem 0.95rem;
        border-radius: var(--radius-md, 8px);
        border: 1px solid var(--border-color, #CBD5E1);
        background: var(--bg-input, #F8FAFC);
        color: var(--text-body, #0F172A);
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .form-control-custom:focus {
        border-color: var(--apex-primary, #4680FF);
        box-shadow: 0 0 0 3px rgba(70, 128, 255, 0.15);
        outline: none;
        background: #FFFFFF;
    }
    .file-dropzone {
        border: 2px dashed var(--border-color, #CBD5E1);
        border-radius: var(--radius-md, 12px);
        padding: 1.5rem;
        text-align: center;
        background: var(--bg-hover, #F8FAFC);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .file-dropzone:hover {
        border-color: var(--apex-primary, #4680FF);
        background: rgba(70, 128, 255, 0.04);
    }
    .file-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .preview-sidebar-mockup {
        background: #0F172A;
        border-radius: var(--radius-md, 12px);
        padding: 1.25rem;
        border: 1px solid #1E293B;
    }
    .mockup-brand-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.5rem 0.75rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .mockup-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #EC4899 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 1.45rem;
        overflow: hidden;
        flex-shrink: 0;
    }
    .mockup-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .mockup-text {
        overflow: hidden;
    }
    .mockup-name {
        font-weight: 800;
        font-size: 1.05rem;
        color: #F8FAFC;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .mockup-tag {
        display: inline-block;
        font-size: 0.65rem;
        font-family: 'JetBrains Mono', monospace;
        padding: 2px 7px;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.2);
        color: #34D399;
        font-weight: 700;
        border: 1px solid rgba(52, 211, 153, 0.35);
        margin-top: 2px;
    }
    .current-logo-badge {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem;
        border-radius: var(--radius-md, 10px);
        background: var(--bg-hover, #F1F5F9);
        border: 1px solid var(--border-color, #E2E8F0);
        margin-bottom: 1rem;
    }
    .current-logo-thumb {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        background: #0F172A;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    .current-logo-thumb img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@section('content')
<div class="admindek-page-header">
    <div class="admindek-header-title">
        <h1>
            <i class='bx bx-palette' style="color: #F59E0B;"></i>
            Pengaturan Identitas & Branding Aplikasi
        </h1>
        <p>Atur nama aplikasi, tagline, dan unggah custom logo untuk sidebar navigasi, header sistem, dan halaman login.</p>
    </div>
    <div class="admindek-header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-preset btn-preset-subtle" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
            <i class='bx bx-arrow-back'></i> Kembali ke Dashboard
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); color: #065F46; padding: 0.9rem 1.25rem; border-radius: 10px; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.65rem; font-weight: 600;">
        <i class='bx bxs-check-circle' style="font-size: 1.35rem; color: #10B981;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #991B1B; padding: 0.9rem 1.25rem; border-radius: 10px; margin-bottom: 1.25rem;">
        <div style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <i class='bx bxs-error-circle' style="font-size: 1.25rem; color: #EF4444;"></i>
            Terjadi Kesalahan:
        </div>
        <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.88rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="branding-grid">
    <!-- Kolom Kiri: Form Perubahan -->
    <div>
        <div class="branding-card">
            <div class="branding-card-header">
                <h3><i class='bx bx-cog' style="color: var(--apex-primary, #4680FF);"></i> Form Identitas Aplikasi</h3>
                <span style="font-size: 0.75rem; background: rgba(70, 128, 255, 0.1); color: var(--apex-primary, #4680FF); padding: 3px 8px; border-radius: 6px; font-weight: 700;">Role: Super Admin</span>
            </div>
            <div class="branding-card-body">
                <form action="{{ route('admin.settings.branding.update') }}" method="POST" enctype="multipart/form-data" id="brandingForm">
                    @csrf

                    <!-- Nama Aplikasi -->
                    <div class="form-group-custom">
                        <label for="app_name" class="form-label-custom">
                            Nama Aplikasi <span style="color: #EF4444;">*</span>
                            <small>(Muncul di sidebar header, tab browser, &amp; login)</small>
                        </label>
                        <input type="text" name="app_name" id="app_name" class="form-control-custom" value="{{ old('app_name', $branding['app_name'] ?? 'Sekolahku') }}" required maxlength="80" placeholder="Contoh: Sekolahku atau Apex Sekolahku">
                    </div>

                    <!-- Tagline / Versi -->
                    <div class="form-group-custom">
                        <label for="app_tagline" class="form-label-custom">
                            Tagline / Versi Aplikasi
                            <small>(Badge kecil di sebelah nama aplikasi, misal: v2.0 atau Cerdas)</small>
                        </label>
                        <input type="text" name="app_tagline" id="app_tagline" class="form-control-custom" value="{{ old('app_tagline', $branding['app_tagline'] ?? 'v2.0') }}" maxlength="40" placeholder="Contoh: v2.0">
                    </div>

                    <!-- Upload Logo -->
                    <div class="form-group-custom">
                        <label class="form-label-custom">
                            Logo Aplikasi
                            <small>(Format: PNG, JPG, JPEG, SVG, WebP. Maks 2MB. Disarankan rasio 1:1 atau logo transparan)</small>
                        </label>

                        @if(!empty($branding['app_logo']))
                        <div class="current-logo-badge">
                            <div class="current-logo-thumb">
                                <img src="{{ $branding['app_logo'] }}" alt="Logo Saat Ini">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-heading, #0F172A);">Logo Custom Aktif</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted, #64748B);">Sedang digunakan pada sidebar, login, dan header.</div>
                            </div>
                            <div>
                                <button type="button" onclick="confirmRemoveLogo()" class="btn-preset" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.45rem 0.85rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                    <i class='bx bx-trash'></i> Hapus Logo
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="file-dropzone" id="dropzoneBox">
                            <input type="file" name="app_logo" id="logoInput" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp">
                            <i class='bx bx-cloud-upload' style="font-size: 2.2rem; color: var(--apex-primary, #4680FF); margin-bottom: 0.35rem; display: block;"></i>
                            <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-heading, #1E293B);">Pilih gambar atau seret file logo ke sini</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted, #64748B); margin-top: 0.25rem;">Klik untuk menelusuri file dari perangkat Anda</div>
                            <div id="fileSelectedName" style="margin-top: 0.5rem; font-size: 0.8rem; font-weight: 600; color: #10B981; display: none;"></div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color, #E2E8F0);">
                        <button type="submit" class="btn-preset btn-preset-primary" style="padding: 0.7rem 1.6rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <i class='bx bx-save' style="font-size: 1.15rem;"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

                @if(!empty($branding['app_logo']))
                <form id="removeLogoForm" action="{{ route('admin.settings.branding.remove-logo') }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Pratinjau Tampilan & Reset -->
    <div>
        <!-- Card Pratinjau Live -->
        <div class="branding-card" style="margin-bottom: 1.5rem;">
            <div class="branding-card-header">
                <h3><i class='bx bx-show' style="color: #10B981;"></i> Pratinjau Tampilan (Live Preview)</h3>
                <span style="font-size: 0.75rem; color: var(--text-muted, #64748B);">Real-time</span>
            </div>
            <div class="branding-card-body">
                <p style="font-size: 0.82rem; color: var(--text-muted, #64748B); margin-top: 0; margin-bottom: 1rem;">
                    Begini tampilan identitas aplikasi yang akan dilihat oleh pengguna pada bagian atas sidebar menu:
                </p>

                <!-- Sidebar Box Mockup -->
                <div class="preview-sidebar-mockup">
                    <div style="font-size: 0.7rem; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
                        Simulasi Sidebar Header
                    </div>
                    <div class="mockup-brand-item">
                        <div class="mockup-icon" id="previewIconBox">
                            @if(!empty($branding['app_logo']))
                                <img src="{{ $branding['app_logo'] }}" alt="Logo Preview" id="previewImg">
                            @else
                                <i class='bx bxs-graduation' id="previewFallbackIcon"></i>
                            @endif
                        </div>
                        <div class="mockup-text">
                            <div class="mockup-name" id="previewAppName">{{ $branding['app_name'] ?? 'Sekolahku' }}</div>
                            <span class="mockup-tag" id="previewAppTagline">{{ $branding['app_tagline'] ?? 'v2.0' }}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 1.25rem; padding: 0.85rem; border-radius: 10px; background: rgba(59, 130, 246, 0.06); border: 1px solid rgba(59, 130, 246, 0.2); font-size: 0.8rem; color: #1E40AF; display: flex; gap: 0.5rem;">
                    <i class='bx bx-info-circle' style="font-size: 1.15rem; flex-shrink: 0; margin-top: 1px;"></i>
                    <span>Setelah disimpan, perubahan logo, nama aplikasi, dan tagline akan langsung aktif di seluruh sistem (sidebar, judul browser, dan halaman login).</span>
                </div>
            </div>
        </div>

        <!-- Card Reset Default -->
        <div class="branding-card">
            <div class="branding-card-header">
                <h3><i class='bx bx-reset' style="color: #64748B;"></i> Reset ke Standar Pabrik</h3>
            </div>
            <div class="branding-card-body">
                <p style="font-size: 0.83rem; color: var(--text-muted, #64748B); margin-top: 0; margin-bottom: 1rem;">
                    Kembalikan nama aplikasi ke <strong>"Sekolahku"</strong>, tagline ke <strong>"v2.0"</strong>, dan hapus logo kustom kembali ke ikon standar toga.
                </p>
                <form action="{{ route('admin.settings.branding.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh identitas branding ke pengaturan awal sistem?');">
                    @csrf
                    <button type="submit" class="btn-preset" style="background: rgba(100, 116, 139, 0.1); color: #475569; border: 1px solid rgba(100, 116, 139, 0.25); padding: 0.55rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                        <i class='bx bx-revision'></i> Reset ke Standar Sistem
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live update preview text
    const nameInput = document.getElementById('app_name');
    const taglineInput = document.getElementById('app_tagline');
    const previewName = document.getElementById('previewAppName');
    const previewTagline = document.getElementById('previewAppTagline');
    const logoInput = document.getElementById('logoInput');
    const previewIconBox = document.getElementById('previewIconBox');
    const fileSelectedName = document.getElementById('fileSelectedName');

    if (nameInput && previewName) {
        nameInput.addEventListener('input', function() {
            previewName.textContent = this.value.trim() || 'Sekolahku';
        });
    }

    if (taglineInput && previewTagline) {
        taglineInput.addEventListener('input', function() {
            const val = this.value.trim();
            if (val) {
                previewTagline.textContent = val;
                previewTagline.style.display = 'inline-block';
            } else {
                previewTagline.style.display = 'none';
            }
        });
    }

    // Live preview uploaded file
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileSelectedName.textContent = 'File dipilih: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                fileSelectedName.style.display = 'block';

                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewIconBox.innerHTML = '<img src="' + evt.target.result + '" alt="Preview Logo" style="width:100%;height:100%;object-fit:contain;">';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function confirmRemoveLogo() {
        if (confirm('Apakah Anda yakin ingin menghapus logo ini dan kembali ke ikon bawaan sistem?')) {
            document.getElementById('removeLogoForm').submit();
        }
    }
</script>
@endpush
@endsection
