@extends('layouts.vuexy')

@section('title', 'Tata Tertib, Kategori Pelanggaran & Kredit Poin')
@section('breadcrumb_current', 'Kesiswaan — Tata Tertib & Kredit Poin')
@section('description', 'Master kategori pelanggaran beserta item pasal & besar poin, pengaturan ambang batas sanksi pelanggaran, pengaturan pemutihan poin oleh Waka Kesiswaan, dan buku kredit poin siswa.')

@push('styles')
<style>
    .tatib-pane { display: none; }
    .tatib-pane.active { display: block; animation: tabFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1); }

    .threshold-ladder {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }
    .threshold-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.15rem 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.75rem;
        box-shadow: var(--shadow-card);
        transition: var(--transition);
    }
    .threshold-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card-hover);
    }
</style>
@endpush

@section('content')
@php
    $totalItemsCount = $categories->sum(fn($c) => $c->items->count());
    $sanctionedStudentsCount = $studentSummaries->filter(fn($s) => $s['threshold'] !== null)->count();
@endphp

<!-- Page Header -->
<div class="page-title-row">
    <div class="page-heading">
        <h1>
            <i class='bx bx-shield-quarter' style="color: var(--bento-primary);"></i>
            Tata Tertib, Master Pelanggaran & Kredit Poin
            <span class="vx-badge badge-primary">Modul Waka Kesiswaan</span>
        </h1>
        <p>Kelola master kategori & item poin pelanggaran siswa, seting ambang batas tindakan sanksi, aturan pemutihan poin, serta buku poin siswa.</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn btn-danger btn-sm" onclick="openModal('modalRecordViolation')">
            <i class='bx bx-error-alt'></i> Catat Pelanggaran Siswa
        </button>
        <button type="button" class="btn btn-success btn-sm" onclick="openModal('modalRecordAmnesty')">
            <i class='bx bx-check-shield'></i> Input Pemutihan Poin
        </button>
        @if($canManageSettings)
            <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalAddCategory')">
                <i class='bx bx-folder-plus'></i> Kategori Pelanggaran
            </button>
        @endif
    </div>
</div>

<!-- Summary KPI Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-primary">
            <i class='bx bx-list-check'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Master Kategori & Pasal</div>
            <div class="stat-value">{{ $categories->count() }} / {{ $totalItemsCount }}</div>
            <div class="stat-sub">
                <span class="stat-dot stat-dot-info"></span>
                <span>{{ $categories->count() }} kategori &bull; {{ $totalItemsCount }} item pasal</span>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-warning">
            <i class='bx bx-slider-alt'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Ambang Maksimal & BK</div>
            <div class="stat-value">{{ $policy->max_violation_points }} Poin</div>
            <div class="stat-sub">
                <span class="stat-dot stat-dot-warning"></span>
                <span>Rujuk BK mulai {{ $policy->auto_refer_bk_threshold }} poin</span>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-success">
            <i class='bx bx-refresh'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Kebijakan Pemutihan Poin</div>
            <div class="stat-value">
                @if($policy->amnesty_enabled)
                    -{{ $policy->max_amnesty_points_per_semester }} Poin
                @else
                    Nonaktif
                @endif
            </div>
            <div class="stat-sub">
                <span class="stat-dot stat-dot-success"></span>
                <span>Bebas pelanggaran min. {{ $policy->min_clean_days_before_amnesty }} hari</span>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-danger">
            <i class='bx bx-user-voice'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Siswa Masuk Ambang Sanksi</div>
            <div class="stat-value">{{ $sanctionedStudentsCount }} Siswa</div>
            <div class="stat-sub">
                <span class="stat-dot stat-dot-danger"></span>
                <span>{{ $selectedYear?->name ?? '-' }} ({{ $selectedSemester === 'odd' ? 'Ganjil' : 'Genap' }})</span>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="apex-tabs-nav">
    <button type="button" class="apex-tab-pill tatib-tab-btn active" id="btn-pane-master" onclick="switchTatibTab('pane-master')">
        <i class='bx bx-list-check'></i> Master Kategori & Item Pelanggaran ({{ $totalItemsCount }})
    </button>
    <button type="button" class="apex-tab-pill tatib-tab-btn" id="btn-pane-thresholds" onclick="switchTatibTab('pane-thresholds')">
        <i class='bx bx-slider-alt'></i> Seting Ambang Batas Sanksi ({{ $thresholds->count() }})
    </button>
    <button type="button" class="apex-tab-pill tatib-tab-btn" id="btn-pane-amnesty" onclick="switchTatibTab('pane-amnesty')">
        <i class='bx bx-refresh'></i> Seting Kebijakan & Pemutihan Poin ({{ $amnestyRules->count() }})
    </button>
    <button type="button" class="apex-tab-pill tatib-tab-btn" id="btn-pane-ledger" onclick="switchTatibTab('pane-ledger')">
        <i class='bx bx-book-reader'></i> Buku Kredit Poin Siswa ({{ $students->count() }})
    </button>
</div>

<!-- =========================================================================
     TAB 1: MASTER KATEGORI PELANGGARAN & ITEM POIN SISWA
========================================================================= -->
<div id="pane-master" class="tatib-pane active">
    <div class="apex-card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.85rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 260px;">
                <input type="text" id="searchViolationItemInput" class="vx-input" style="max-width: 360px;"
                    placeholder="Cari kode pasal atau nama pelanggaran siswa..." oninput="filterViolationItems()">
                <select id="filterCategorySeverity" class="vx-input" style="max-width: 210px;" onchange="filterViolationItems()">
                    <option value="">Semua Tingkat Pelanggaran</option>
                    <option value="RINGAN">Kategori Ringan</option>
                    <option value="SEDANG">Kategori Sedang</option>
                    <option value="BERAT">Kategori Berat</option>
                </select>
            </div>
            @if($canManageSettings)
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openModal('modalAddCategory')">
                        <i class='bx bx-folder-plus'></i> Tambah Kategori
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddItemModal('')">
                        <i class='bx bx-plus-circle'></i> Tambah Item Pelanggaran
                    </button>
                </div>
            @endif
        </div>
    </div>

    @forelse($categories as $cat)
        <div class="apex-card cat-block" data-severity="{{ $cat->severity }}" style="border-left: 4px solid {{ $cat->color }};">
            <div class="apex-card-header" style="background-color: var(--bg-subtle);">
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="vx-badge cell-mono" style="background: {{ $cat->color }}; color: #fff;">
                        {{ $cat->code }}
                    </span>
                    <div>
                        <h2 class="apex-card-title" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span>{{ $cat->name }}</span>
                            @if($cat->severity === 'BERAT')
                                <span class="vx-badge badge-danger">Pelanggaran Berat</span>
                            @elseif($cat->severity === 'SEDANG')
                                <span class="vx-badge badge-warning">Pelanggaran Sedang</span>
                            @else
                                <span class="vx-badge badge-info">Pelanggaran Ringan</span>
                            @endif
                        </h2>
                        @if($cat->description)
                            <p class="apex-card-desc">{{ $cat->description }}</p>
                        @endif
                    </div>
                </div>
                @if($canManageSettings)
                    <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                        <button type="button" class="btn btn-primary btn-xs" onclick="openAddItemModal({{ $cat->id }})">
                            <i class='bx bx-plus'></i> Tambah Item Pasal
                        </button>
                        <button type="button" class="btn btn-secondary btn-xs"
                            onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->code) }}', '{{ addslashes($cat->name) }}', '{{ $cat->severity }}', '{{ $cat->color }}', '{{ addslashes($cat->description ?? '') }}', {{ $cat->sort_order }})">
                            <i class='bx bx-edit-alt'></i> Edit Kategori
                        </button>
                        <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Kategori"
                            onclick="deleteDisciplineCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table class="vx-table">
                    <thead>
                        <tr>
                            <th style="width: 110px;">Kode Pasal</th>
                            <th>Item Pelanggaran Siswa</th>
                            <th style="width: 135px; text-align: center;">Besar Poin</th>
                            <th>Tindakan / Sanksi Awal</th>
                            @if($canManageSettings)
                                <th style="width: 110px; text-align: right;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cat->items as $item)
                            <tr class="violation-item-row" data-search="{{ strtolower($item->code.' '.$item->name.' '.$cat->name) }}">
                                <td>
                                    <span class="vx-badge badge-dark cell-mono">{{ $item->code }}</span>
                                </td>
                                <td>
                                    <div class="cell-title">{{ $item->name }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="vx-badge badge-danger cell-mono">+{{ $item->points }} Poin</span>
                                </td>
                                <td>
                                    <div class="cell-sub" style="font-size: 0.8rem;">{{ $item->default_sanction ?: 'Pembinaan sesuai tata tertib' }}</div>
                                </td>
                                @if($canManageSettings)
                                    <td style="text-align: right;">
                                        <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                            <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Edit Item & Poin"
                                                onclick="openEditItemModal({{ $item->id }}, {{ $cat->id }}, '{{ addslashes($item->code) }}', '{{ addslashes($item->name) }}', {{ $item->points }}, '{{ addslashes($item->default_sanction ?? '') }}')">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                            <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Item"
                                                onclick="deleteDisciplineItem({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageSettings ? 5 : 4 }}" style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-size: 0.82rem;">
                                    Belum ada item pelanggaran pada kategori ini. Klik <strong>Tambah Item Pasal</strong> untuk menambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="apex-card" style="padding: 2.5rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
            Belum ada kategori pelanggaran.
        </div>
    @endforelse
</div>

<!-- =========================================================================
     TAB 2: PENGATURAN AMBANG BATAS PELANGGARAN & SANKSI (WAKA KESISWAAN)
========================================================================= -->
<div id="pane-thresholds" class="tatib-pane">
    <div class="apex-card">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title">Tangga Ambang Batas Poin Pelanggaran & Eskalasi Sanksi</h2>
                <p class="apex-card-desc">
                    Waka Kesiswaan dapat mengatur rentang poin untuk setiap tahapan pembinaan, Surat Peringatan (SP1/SP2/SP3), panggilan orang tua, dan rujukan wajib Guru BK.
                </p>
            </div>
            @if($canManageSettings)
                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalAddThreshold')">
                    <i class='bx bx-plus-circle'></i> Tambah Ambang Batas Sanksi
                </button>
            @endif
        </div>

        <div class="apex-card-body">
            <div class="threshold-ladder">
                @foreach($thresholds as $th)
                    <div class="threshold-card" style="border-top: 3px solid {{ $th->color }};">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <span class="vx-badge cell-mono" style="background: {{ $th->color }}; color: #fff;">
                                    {{ $th->level_code }}
                                </span>
                                <span class="cell-mono" style="font-size: 0.8rem; font-weight: 700; color: {{ $th->color }};">
                                    @if($th->max_points)
                                        {{ $th->min_points }} &ndash; {{ $th->max_points }} Poin
                                    @else
                                        &ge; {{ $th->min_points }} Poin
                                    @endif
                                </span>
                            </div>
                            <div class="cell-title" style="margin-bottom: 0.3rem;">
                                {{ $th->name }}
                            </div>
                            <div class="cell-sub" style="line-height: 1.45;">
                                {{ $th->sanction_action }}
                            </div>
                        </div>

                        <div>
                            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.65rem;">
                                @if($th->require_parent_call)
                                    <span class="vx-badge badge-warning"><i class='bx bx-phone-call'></i> Panggilan Ortu</span>
                                @endif
                                @if($th->require_bk_counseling)
                                    <span class="vx-badge badge-info"><i class='bx bx-conversation'></i> Wajib Konseling BK</span>
                                @endif
                            </div>

                            @if($canManageSettings)
                                <div style="display: flex; justify-content: flex-end; gap: 0.35rem; border-top: 1px solid var(--border-subtle); padding-top: 0.65rem;">
                                    <button type="button" class="btn btn-secondary btn-xs"
                                        onclick="openEditThresholdModal({{ $th->id }}, '{{ addslashes($th->level_code) }}', '{{ addslashes($th->name) }}', {{ $th->min_points }}, '{{ $th->max_points ?? '' }}', '{{ addslashes($th->sanction_action ?? '') }}', {{ $th->require_parent_call ? 1 : 0 }}, {{ $th->require_bk_counseling ? 1 : 0 }}, '{{ $th->color }}')">
                                        <i class='bx bx-edit-alt'></i> Seting Ambang
                                    </button>
                                    <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Ambang Batas"
                                        onclick="deleteThreshold({{ $th->id }}, '{{ addslashes($th->name) }}')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     TAB 3: PENGATURAN KEBIJAKAN & PEMUTIHAN POIN (WAKA KESISWAAN)
========================================================================= -->
<div id="pane-amnesty" class="tatib-pane">
    <!-- Kebijakan Umum Waka Kesiswaan -->
    <div class="apex-card">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title">Pengaturan Kebijakan Ambang Batas & Pemutihan Poin (Waka Kesiswaan)</h2>
                <p class="apex-card-desc">Atur batas maksimal poin pelanggaran, periode reset poin, syarat masa bebas pelanggaran, dan kuota pemutihan per semester.</p>
            </div>
        </div>
        <form onsubmit="submitDisciplinePolicy(event)" class="apex-card-body">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-form-grid form-grid-2">
                <div class="form-row">
                    <label class="vx-label">Ambang Batas Maksimal Poin Pelanggaran *</label>
                    <input type="number" class="vx-input" name="max_violation_points" value="{{ $policy->max_violation_points }}" min="10" max="1000" {{ !$canManageSettings ? 'disabled' : '' }} required>
                    <div class="cell-sub" style="margin-top: 0.25rem;">Batas puncak poin disiplin (umumnya 100 poin untuk Konferensi Kasus / SP3).</div>
                </div>
                <div class="form-row">
                    <label class="vx-label">Periode Reset Perhitungan Poin *</label>
                    <select class="vx-input" name="reset_period" {{ !$canManageSettings ? 'disabled' : '' }}>
                        <option value="SEMESTER" {{ $policy->reset_period === 'SEMESTER' ? 'selected' : '' }}>Reset Setiap Pergantian Semester</option>
                        <option value="ACADEMIC_YEAR" {{ $policy->reset_period === 'ACADEMIC_YEAR' ? 'selected' : '' }}>Reset Setiap Tahun Ajaran Baru</option>
                        <option value="NEVER" {{ $policy->reset_period === 'NEVER' ? 'selected' : '' }}>Akumulasi Selama Menjadi Siswa (Tidak Reset)</option>
                    </select>
                </div>
                <div class="form-row">
                    <label class="vx-label">Ambang Poin Otomatis Rujukan Guru BK *</label>
                    <input type="number" class="vx-input" name="auto_refer_bk_threshold" value="{{ $policy->auto_refer_bk_threshold }}" min="5" max="1000" {{ !$canManageSettings ? 'disabled' : '' }} required>
                </div>
                <div class="form-row">
                    <label class="vx-label">Status Program Pemutihan / Pengurangan Poin *</label>
                    <select class="vx-input" name="amnesty_enabled" {{ !$canManageSettings ? 'disabled' : '' }}>
                        <option value="1" {{ $policy->amnesty_enabled ? 'selected' : '' }}>Aktif (Siswa Dapat Mengurangi Poin lewat Pembinaan/Prestasi)</option>
                        <option value="0" {{ !$policy->amnesty_enabled ? 'selected' : '' }}>Nonaktif (Poin Pelanggaran Tidak Dapat Dikurangi)</option>
                    </select>
                </div>
                <div class="form-row">
                    <label class="vx-label">Maksimal Poin Pemutihan per Semester *</label>
                    <input type="number" class="vx-input" name="max_amnesty_points_per_semester" value="{{ $policy->max_amnesty_points_per_semester }}" min="0" max="500" {{ !$canManageSettings ? 'disabled' : '' }} required>
                </div>
                <div class="form-row">
                    <label class="vx-label">Minimal Hari Bebas Pelanggaran Sebelum Pemutihan *</label>
                    <input type="number" class="vx-input" name="min_clean_days_before_amnesty" value="{{ $policy->min_clean_days_before_amnesty }}" min="0" max="365" {{ !$canManageSettings ? 'disabled' : '' }} required>
                </div>
                <div class="form-row span-2">
                    <label class="vx-label">Izinkan Pemutihan bagi Siswa dengan Pelanggaran Kategori BERAT?</label>
                    <select class="vx-input" name="allow_amnesty_for_heavy_violations" {{ !$canManageSettings ? 'disabled' : '' }}>
                        <option value="0" {{ !$policy->allow_amnesty_for_heavy_violations ? 'selected' : '' }}>Tidak — Pelanggaran Berat hanya dapat diputihkan lewat otorisasi khusus Waka Kesiswaan</option>
                        <option value="1" {{ $policy->allow_amnesty_for_heavy_violations ? 'selected' : '' }}>Ya — Diizinkan mengikuti program pemutihan reguler</option>
                    </select>
                </div>
            </div>
            @if($canManageSettings)
                <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class='bx bx-save'></i> Simpan Pengaturan Kebijakan Waka Kesiswaan
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Master Program / Kriteria Pemutihan Poin -->
    <div class="apex-card">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title">Daftar Program & Kriteria Pemutihan Poin Siswa</h2>
                <p class="apex-card-desc">Tentukan jenis pembinaan atau prestasi yang berhak mendapatkan pengurangan (pemutihan) poin pelanggaran beserta syaratnya.</p>
            </div>
            @if($canManageSettings)
                <button type="button" class="btn btn-success btn-sm" onclick="openModal('modalAddAmnestyRule')">
                    <i class='bx bx-plus-circle'></i> Tambah Program Pemutihan
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th style="width: 110px;">Kode</th>
                        <th>Nama Program Pemutihan / Pembinaan</th>
                        <th style="text-align: center;">Kredit Pengurangan</th>
                        <th style="text-align: center;">Min. Poin Aktif</th>
                        <th style="text-align: center;">Masa Bebas Pelanggaran</th>
                        <th style="text-align: center;">Batas Klaim / Smt</th>
                        @if($canManageSettings)
                            <th style="text-align: right;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($amnestyRules as $rule)
                        <tr>
                            <td><span class="vx-badge badge-dark cell-mono">{{ $rule->code }}</span></td>
                            <td>
                                <div class="cell-title">{{ $rule->name }}</div>
                                @if($rule->description)
                                    <div class="cell-sub">{{ $rule->description }}</div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="vx-badge badge-success cell-mono">-{{ $rule->credit_points }} Poin</span>
                            </td>
                            <td style="text-align: center;" class="cell-mono">{{ $rule->min_violation_points_required }} Poin</td>
                            <td style="text-align: center;" class="cell-mono">{{ $rule->clean_days_required }} Hari</td>
                            <td style="text-align: center;" class="cell-mono">{{ $rule->max_claims_per_semester ? $rule->max_claims_per_semester.'x' : 'Tanpa Batas' }}</td>
                            @if($canManageSettings)
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                        <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Edit Program Pemutihan"
                                            onclick="openEditAmnestyModal({{ $rule->id }}, '{{ addslashes($rule->code) }}', '{{ addslashes($rule->name) }}', {{ $rule->credit_points }}, {{ $rule->min_violation_points_required }}, {{ $rule->clean_days_required }}, '{{ $rule->max_claims_per_semester ?? '' }}', '{{ addslashes($rule->description ?? '') }}')">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Program Pemutihan"
                                            onclick="deleteAmnestyRule({{ $rule->id }}, '{{ addslashes($rule->name) }}')">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.82rem;">Belum ada program pemutihan poin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
     TAB 4: BUKU KREDIT POIN SISWA & LOG RIWAYAT
========================================================================= -->
<div id="pane-ledger" class="tatib-pane">
    <div class="apex-card">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title">Buku Saldo Kredit Poin Disiplin Siswa</h2>
                <p class="apex-card-desc">Poin Bersih = Total Poin Pelanggaran dikurangi Kredit Pemutihan Poin.</p>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-danger btn-xs" onclick="openModal('modalRecordViolation')">
                    <i class='bx bx-plus'></i> Catat Pelanggaran
                </button>
                <button type="button" class="btn btn-success btn-xs" onclick="openModal('modalRecordAmnesty')">
                    <i class='bx bx-check-shield'></i> Input Pemutihan
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>Nama Peserta Didik</th>
                        <th>Kelas</th>
                        <th style="text-align: center;">Total Pelanggaran</th>
                        <th style="text-align: center;">Total Pemutihan</th>
                        <th style="text-align: center;">Poin Bersih</th>
                        <th>Status Ambang Sanksi</th>
                        <th style="text-align: right;">Tindakan Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentSummaries as $row)
                        @php
                            $st = $row['student'];
                            $th = $row['threshold'];
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-title">{{ $st->full_name }}</div>
                                <div class="cell-sub cell-mono">NIS: {{ $st->nis }}</div>
                            </td>
                            <td><span class="vx-badge badge-dark">{{ $st->schoolClass?->name ?? '-' }}</span></td>
                            <td style="text-align: center;">
                                <span class="cell-mono" style="color: var(--vx-danger); font-weight: 600;">+{{ $row['violation_points'] }}</span>
                                <span class="cell-sub">({{ $row['violation_count'] }}x)</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="cell-mono" style="color: var(--vx-success); font-weight: 600;">-{{ $row['amnesty_points'] }}</span>
                                <span class="cell-sub">({{ $row['amnesty_count'] }}x)</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="vx-badge cell-mono" style="background: {{ $th ? $th->color : 'var(--vx-success)' }}; color: #fff;">
                                    {{ $row['net_points'] }} Poin
                                </span>
                            </td>
                            <td>
                                @if($th)
                                    <span class="vx-badge" style="background: {{ $th->color }}18; color: {{ $th->color }}; border-color: {{ $th->color }}44;">
                                        {{ $th->level_code }} — {{ $th->name }}
                                    </span>
                                @else
                                    <span class="vx-badge badge-success">Aman / Tertib</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                    <button type="button" class="btn btn-danger-light btn-xs" onclick="quickRecordForStudent({{ $st->id }}, 'violation')">
                                        + Pelanggaran
                                    </button>
                                    @if($row['net_points'] > 0 && $policy->amnesty_enabled)
                                        <button type="button" class="btn btn-secondary btn-xs" onclick="quickRecordForStudent({{ $st->id }}, 'amnesty')">
                                            - Pemutihan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log Riwayat Terbaru -->
    <div class="apex-card">
        <div class="apex-card-header">
            <h2 class="apex-card-title">Log Riwayat Pelanggaran & Pemutihan Terbaru</h2>
        </div>
        <div class="table-responsive">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th>Pasal Pelanggaran / Program Pemutihan</th>
                        <th style="text-align: center;">Poin</th>
                        <th>Catatan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRecords as $rec)
                        <tr>
                            <td class="cell-mono">{{ $rec->record_date?->format('d M Y') }}</td>
                            <td>
                                <div class="cell-title">{{ $rec->student?->full_name ?? '-' }}</div>
                                <div class="cell-sub">{{ $rec->student?->schoolClass?->name ?? '' }}</div>
                            </td>
                            <td>
                                @if($rec->record_type === 'VIOLATION')
                                    <span class="vx-badge badge-danger">Pelanggaran</span>
                                @else
                                    <span class="vx-badge badge-success">Pemutihan</span>
                                @endif
                            </td>
                            <td>
                                <div class="cell-title">{{ $rec->title }}</div>
                            </td>
                            <td style="text-align: center;">
                                @if($rec->record_type === 'VIOLATION')
                                    <span class="vx-badge badge-danger cell-mono">+{{ $rec->points }}</span>
                                @else
                                    <span class="vx-badge badge-success cell-mono">-{{ $rec->points }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="cell-sub">{{ $rec->notes ?: '-' }}</div>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Riwayat" onclick="deleteDisciplineRecord({{ $rec->id }})">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.82rem;">Belum ada riwayat pencatatan pelanggaran atau pemutihan poin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODALS: KATEGORI, ITEM PELANGGARAN, AMBANG BATAS, PEMUTIHAN & PENCATATAN
========================================================================= -->

<!-- MODAL 1: TAMBAH KATEGORI PELANGGARAN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddCategory">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Tambah Master Kategori Pelanggaran</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddCategory')">&times;</button>
        </div>
        <form onsubmit="submitAddCategory(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Kategori *</label>
                        <input type="text" class="vx-input" name="code" placeholder="KAT-E" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tingkat Keparahan *</label>
                        <select class="vx-input" name="severity" required>
                            <option value="RINGAN">Ringan</option>
                            <option value="SEDANG">Sedang</option>
                            <option value="BERAT">Berat</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kategori Pelanggaran *</label>
                        <input type="text" class="vx-input" name="name" placeholder="Contoh: Ketertiban Penggunaan Gawai & Media Sosial" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Warna Label Kategori</label>
                        <input type="color" class="vx-input" name="color" value="#f59e0b" style="height: 38px; padding: 3px;">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Urutan Tampil</label>
                        <input type="number" class="vx-input" name="sort_order" value="{{ $categories->count() + 1 }}" min="1">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi Kategori</label>
                        <input type="text" class="vx-input" name="description" placeholder="Penjelasan ruang lingkup kategori pelanggaran">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: EDIT KATEGORI PELANGGARAN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditCategory">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Edit Master Kategori Pelanggaran</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditCategory')">&times;</button>
        </div>
        <form onsubmit="submitEditCategory(event)">
            <input type="hidden" id="editDiscCatId">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Kategori *</label>
                        <input type="text" class="vx-input" id="editDiscCatCode" name="code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tingkat Keparahan *</label>
                        <select class="vx-input" id="editDiscCatSeverity" name="severity" required>
                            <option value="RINGAN">Ringan</option>
                            <option value="SEDANG">Sedang</option>
                            <option value="BERAT">Berat</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kategori Pelanggaran *</label>
                        <input type="text" class="vx-input" id="editDiscCatName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Warna Label</label>
                        <input type="color" class="vx-input" id="editDiscCatColor" name="color" style="height: 38px; padding: 3px;">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Urutan Tampil</label>
                        <input type="number" class="vx-input" id="editDiscCatOrder" name="sort_order" min="1">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi</label>
                        <input type="text" class="vx-input" id="editDiscCatDesc" name="description">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: TAMBAH ITEM PELANGGARAN & POIN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddItem">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Tambah Item Pelanggaran Siswa & Besar Poin</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddItem')">&times;</button>
        </div>
        <form onsubmit="submitAddItem(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Kategori Pelanggaran *</label>
                        <select class="vx-input" id="addItemCategoryId" name="category_id" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">[{{ $cat->code }}] {{ $cat->name }} ({{ $cat->severity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Pasal / Item *</label>
                        <input type="text" class="vx-input" name="code" placeholder="Contoh: A-05" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Besar Poin Pelanggaran *</label>
                        <input type="number" class="vx-input" name="points" value="10" min="1" max="500" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Uraian Item Pelanggaran Siswa *</label>
                        <input type="text" class="vx-input" name="name" placeholder="Contoh: Menggunakan ponsel saat KBM tanpa izin guru pengajar" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Tindakan / Sanksi Pembinaan Langsung</label>
                        <input type="text" class="vx-input" name="default_sanction" placeholder="Contoh: Teguran & penitipan gawai di ruang Tatib s/d jam pulang">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddItem')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Item & Poin</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: EDIT ITEM PELANGGARAN & POIN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditItem">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Edit Item Pelanggaran & Besar Poin</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditItem')">&times;</button>
        </div>
        <form onsubmit="submitEditItem(event)">
            <input type="hidden" id="editDiscItemId">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Kategori Pelanggaran *</label>
                        <select class="vx-input" id="editItemCategoryId" name="category_id" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">[{{ $cat->code }}] {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Pasal *</label>
                        <input type="text" class="vx-input" id="editItemCode" name="code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Besar Poin Pelanggaran *</label>
                        <input type="number" class="vx-input" id="editItemPoints" name="points" min="1" max="500" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Uraian Item Pelanggaran Siswa *</label>
                        <input type="text" class="vx-input" id="editItemName" name="name" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Tindakan / Sanksi Pembinaan Langsung</label>
                        <input type="text" class="vx-input" id="editItemSanction" name="default_sanction">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditItem')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 5: TAMBAH AMBANG BATAS PELANGGARAN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddThreshold">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Tambah Ambang Batas Sanksi Pelanggaran</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddThreshold')">&times;</button>
        </div>
        <form onsubmit="submitAddThreshold(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Level (Misal: SP1, SP2) *</label>
                        <input type="text" class="vx-input" name="level_code" placeholder="SP1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Warna Indikator</label>
                        <input type="color" class="vx-input" name="color" value="#f59e0b" style="height: 38px; padding: 3px;">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Tahapan Sanksi *</label>
                        <input type="text" class="vx-input" name="name" placeholder="Contoh: Surat Peringatan 1 (SP1) & Panggilan Orang Tua" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Batas Minimal Poin *</label>
                        <input type="number" class="vx-input" name="min_points" placeholder="50" min="1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Batas Maksimal Poin (Kosongkan jika &ge; Min)</label>
                        <input type="number" class="vx-input" name="max_points" placeholder="74" min="1">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Uraian Sanksi & Tindak Lanjut *</label>
                        <input type="text" class="vx-input" name="sanction_action" placeholder="Tindakan pembinaan wajib yang dijalankan" required>
                    </div>
                    <div class="form-row">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" name="require_parent_call" checked> Wajib Panggilan Orang Tua
                        </label>
                    </div>
                    <div class="form-row">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" name="require_bk_counseling" checked> Wajib Rujukan Konseling BK
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddThreshold')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Ambang Batas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 6: EDIT AMBANG BATAS PELANGGARAN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditThreshold">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Seting Ambang Batas Sanksi (Waka Kesiswaan)</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditThreshold')">&times;</button>
        </div>
        <form onsubmit="submitEditThreshold(event)">
            <input type="hidden" id="editThId">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Level *</label>
                        <input type="text" class="vx-input" id="editThCode" name="level_code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Warna Indikator</label>
                        <input type="color" class="vx-input" id="editThColor" name="color" style="height: 38px; padding: 3px;">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Tahapan Sanksi *</label>
                        <input type="text" class="vx-input" id="editThName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Batas Minimal Poin *</label>
                        <input type="number" class="vx-input" id="editThMin" name="min_points" min="1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Batas Maksimal Poin</label>
                        <input type="number" class="vx-input" id="editThMax" name="max_points" min="1">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Uraian Sanksi & Tindak Lanjut</label>
                        <input type="text" class="vx-input" id="editThAction" name="sanction_action">
                    </div>
                    <div class="form-row">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" id="editThParentCall" name="require_parent_call"> Wajib Panggilan Orang Tua
                        </label>
                    </div>
                    <div class="form-row">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" id="editThBk" name="require_bk_counseling"> Wajib Rujukan Konseling BK
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditThreshold')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan Ambang</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 7: TAMBAH PROGRAM PEMUTIHAN POIN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddAmnestyRule">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Tambah Program Pemutihan / Pengurangan Poin</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddAmnestyRule')">&times;</button>
        </div>
        <form onsubmit="submitAddAmnestyRule(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Program *</label>
                        <input type="text" class="vx-input" name="code" placeholder="PMT-05" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Besar Kredit Pengurangan Poin *</label>
                        <input type="number" class="vx-input" name="credit_points" value="15" min="1" max="200" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Program Pemutihan / Pembinaan *</label>
                        <input type="text" class="vx-input" name="name" placeholder="Contoh: Tugas Literasi & Resume Buku di Perpustakaan" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Syarat Minimal Poin Aktif</label>
                        <input type="number" class="vx-input" name="min_violation_points_required" value="15" min="0">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Syarat Hari Bebas Pelanggaran</label>
                        <input type="number" class="vx-input" name="clean_days_required" value="7" min="0">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Maksimal Klaim per Semester</label>
                        <input type="number" class="vx-input" name="max_claims_per_semester" value="2" min="1">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Keterangan / Ketentuan Verifikasi</label>
                        <input type="text" class="vx-input" name="description" placeholder="Syarat penyelesaian pembinaan yang diverifikasi Waka Kesiswaan">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddAmnestyRule')">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Program Pemutihan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 8: EDIT PROGRAM PEMUTIHAN POIN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditAmnestyRule">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Edit Program Pemutihan Poin</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditAmnestyRule')">&times;</button>
        </div>
        <form onsubmit="submitEditAmnestyRule(event)">
            <input type="hidden" id="editAmnestyId">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Program *</label>
                        <input type="text" class="vx-input" id="editAmnestyCode" name="code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Besar Kredit Pengurangan Poin *</label>
                        <input type="number" class="vx-input" id="editAmnestyCredit" name="credit_points" min="1" max="200" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Program Pemutihan *</label>
                        <input type="text" class="vx-input" id="editAmnestyName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Syarat Minimal Poin Aktif</label>
                        <input type="number" class="vx-input" id="editAmnestyMinPts" name="min_violation_points_required" min="0">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Syarat Hari Bebas Pelanggaran</label>
                        <input type="number" class="vx-input" id="editAmnestyCleanDays" name="clean_days_required" min="0">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Maksimal Klaim per Semester</label>
                        <input type="number" class="vx-input" id="editAmnestyMaxClaims" name="max_claims_per_semester" min="1">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi Ketentuan</label>
                        <input type="text" class="vx-input" id="editAmnestyDesc" name="description">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditAmnestyRule')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 9: CATAT PELANGGARAN SISWA -->
<div class="vx-modal-backdrop modal-backdrop" id="modalRecordViolation">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Catat Pelanggaran Tata Tertib Siswa</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalRecordViolation')">&times;</button>
        </div>
        <form onsubmit="submitRecordViolation(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <input type="hidden" name="academic_year_id" value="{{ $selectedYear?->id }}">
            <input type="hidden" name="academic_semester" value="{{ $selectedSemester }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Siswa *</label>
                        <select class="vx-input" id="recordViolationStudentId" name="student_id" required>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->schoolClass?->name ?? '-' }} — NIS: {{ $st->nis }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Item Pasal Pelanggaran *</label>
                        <select class="vx-input" name="discipline_item_id" required>
                            @foreach($categories as $cat)
                                <optgroup label="[{{ $cat->code }}] {{ $cat->name }} ({{ $cat->severity }})">
                                    @foreach($cat->items as $item)
                                        <option value="{{ $item->id }}">[{{ $item->code }}] {{ $item->name }} (+{{ $item->points }} Poin)</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Kejadian *</label>
                        <input type="date" class="vx-input" name="record_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Lokasi Kejadian</label>
                        <input type="text" class="vx-input" name="location" placeholder="Gerbang Sekolah / Kelas / Kantin">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Catatan Kronologi / Pembinaan</label>
                        <input type="text" class="vx-input" name="notes" placeholder="Keterangan tambahan dari Guru Piket / Tim Tatib">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalRecordViolation')">Batal</button>
                <button type="submit" class="btn btn-danger">Simpan Catatan Pelanggaran</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 10: INPUT PEMUTIHAN POIN SISWA -->
<div class="vx-modal-backdrop modal-backdrop" id="modalRecordAmnesty">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">Input Pemutihan / Pengurangan Poin Siswa</h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalRecordAmnesty')">&times;</button>
        </div>
        <form onsubmit="submitRecordAmnesty(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <input type="hidden" name="academic_year_id" value="{{ $selectedYear?->id }}">
            <input type="hidden" name="academic_semester" value="{{ $selectedSemester }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Siswa *</label>
                        <select class="vx-input" id="recordAmnestyStudentId" name="student_id" required>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->schoolClass?->name ?? '-' }} — NIS: {{ $st->nis }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Program Pemutihan / Pembinaan *</label>
                        <select class="vx-input" name="amnesty_rule_id" required>
                            @foreach($amnestyRules as $rule)
                                <option value="{{ $rule->id }}">[{{ $rule->code }}] {{ $rule->name }} (-{{ $rule->credit_points }} Poin)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Verifikasi Pemutihan *</label>
                        <input type="date" class="vx-input" name="record_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kredit Poin Kustom (Opsional)</label>
                        <input type="number" class="vx-input" name="custom_credit_points" placeholder="Ikuti default program" min="1" max="200">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Bukti Pembinaan / Prestasi</label>
                        <input type="text" class="vx-input" name="notes" placeholder="Contoh: Telah menyelesaikan pembinaan kebersihan & kontrak perilaku BK">
                    </div>
                    @if($canManageSettings)
                        <div class="form-row span-2">
                            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; cursor: pointer;">
                                <input type="checkbox" name="force_override"> Otorisasi Khusus Waka Kesiswaan (Lewati validasi masa tunggu hari bebas pelanggaran)
                            </label>
                        </div>
                    @endif
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalRecordAmnesty')">Batal</button>
                <button type="submit" class="btn btn-success">Terapkan Pemutihan Poin</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function switchTatibTab(paneId) {
        document.querySelectorAll('.tatib-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tatib-tab-btn').forEach(b => b.classList.remove('active'));

        document.getElementById(paneId)?.classList.add('active');
        document.getElementById('btn-' + paneId)?.classList.add('active');

        const url = new URL(window.location.href);
        url.searchParams.set('tab', paneId);
        window.history.replaceState({}, '', url);
    }

    function filterViolationItems() {
        const q = (document.getElementById('searchViolationItemInput')?.value || '').toLowerCase();
        const sev = document.getElementById('filterCategorySeverity')?.value || '';

        document.querySelectorAll('.cat-block').forEach(block => {
            const blockSev = block.getAttribute('data-severity') || '';
            if (sev && blockSev !== sev) {
                block.style.display = 'none';
                return;
            }
            block.style.display = '';
            block.querySelectorAll('.violation-item-row').forEach(row => {
                const hay = row.getAttribute('data-search') || '';
                row.style.display = (!q || hay.includes(q)) ? '' : 'none';
            });
        });
    }

    async function apiRequest(url, method, payload = null) {
        const opts = {
            method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        };
        if (payload) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(payload);
        }
        const res = await fetch(url, opts);
        const data = await res.json();
        if (!res.ok || !data.success) {
            let msg = data.message || 'Terjadi kesalahan saat memproses permintaan.';
            if (data.errors) {
                const firstErr = Object.values(data.errors)[0];
                if (firstErr) msg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
            }
            if (window.vxShowToast) vxShowToast(msg, 'error');
            else alert(msg);
            return null;
        }
        if (window.vxShowToast) vxShowToast(data.message || 'Berhasil disimpan!', 'success');
        return data;
    }

    // 1. Kategori Pelanggaran
    async function submitAddCategory(e) {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.sort_order = parseInt(payload.sort_order || '1', 10);
        if (await apiRequest('/api/v1/discipline/categories', 'POST', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    function openEditCategoryModal(id, code, name, severity, color, desc, order) {
        document.getElementById('editDiscCatId').value = id;
        document.getElementById('editDiscCatCode').value = code;
        document.getElementById('editDiscCatName').value = name;
        document.getElementById('editDiscCatSeverity').value = severity;
        document.getElementById('editDiscCatColor').value = color || '#f59e0b';
        document.getElementById('editDiscCatDesc').value = desc || '';
        document.getElementById('editDiscCatOrder').value = order || 1;
        openModal('modalEditCategory');
    }

    async function submitEditCategory(e) {
        e.preventDefault();
        const id = document.getElementById('editDiscCatId').value;
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.sort_order = parseInt(payload.sort_order || '1', 10);
        if (await apiRequest(`/api/v1/discipline/categories/${id}`, 'PUT', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    async function deleteDisciplineCategory(id, name) {
        if (!confirm(`Hapus kategori "${name}" beserta seluruh item pelanggaran di dalamnya?`)) return;
        if (await apiRequest(`/api/v1/discipline/categories/${id}`, 'DELETE')) {
            setTimeout(() => window.location.reload(), 400);
        }
    }

    // 2. Item Pelanggaran & Poin
    function openAddItemModal(categoryId) {
        if (categoryId) {
            document.getElementById('addItemCategoryId').value = String(categoryId);
        }
        openModal('modalAddItem');
    }

    async function submitAddItem(e) {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.category_id = parseInt(payload.category_id, 10);
        payload.points = parseInt(payload.points, 10);
        if (await apiRequest('/api/v1/discipline/items', 'POST', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    function openEditItemModal(id, categoryId, code, name, points, sanction) {
        document.getElementById('editDiscItemId').value = id;
        document.getElementById('editItemCategoryId').value = String(categoryId);
        document.getElementById('editItemCode').value = code;
        document.getElementById('editItemName').value = name;
        document.getElementById('editItemPoints').value = points;
        document.getElementById('editItemSanction').value = sanction || '';
        openModal('modalEditItem');
    }

    async function submitEditItem(e) {
        e.preventDefault();
        const id = document.getElementById('editDiscItemId').value;
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.category_id = parseInt(payload.category_id, 10);
        payload.points = parseInt(payload.points, 10);
        if (await apiRequest(`/api/v1/discipline/items/${id}`, 'PUT', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    async function deleteDisciplineItem(id, name) {
        if (!confirm(`Hapus item pelanggaran "${name}"?`)) return;
        if (await apiRequest(`/api/v1/discipline/items/${id}`, 'DELETE')) {
            setTimeout(() => window.location.reload(), 400);
        }
    }

    // 3. Ambang Batas Sanksi
    async function submitAddThreshold(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.min_points = parseInt(payload.min_points, 10);
        payload.max_points = payload.max_points ? parseInt(payload.max_points, 10) : null;
        payload.require_parent_call = form.querySelector('[name="require_parent_call"]').checked;
        payload.require_bk_counseling = form.querySelector('[name="require_bk_counseling"]').checked;
        if (await apiRequest('/api/v1/discipline/thresholds', 'POST', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    function openEditThresholdModal(id, code, name, minPts, maxPts, action, parentCall, bk, color) {
        document.getElementById('editThId').value = id;
        document.getElementById('editThCode').value = code;
        document.getElementById('editThName').value = name;
        document.getElementById('editThMin').value = minPts;
        document.getElementById('editThMax').value = maxPts || '';
        document.getElementById('editThAction').value = action || '';
        document.getElementById('editThParentCall').checked = Boolean(parentCall);
        document.getElementById('editThBk').checked = Boolean(bk);
        document.getElementById('editThColor').value = color || '#f59e0b';
        openModal('modalEditThreshold');
    }

    async function submitEditThreshold(e) {
        e.preventDefault();
        const id = document.getElementById('editThId').value;
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.min_points = parseInt(payload.min_points, 10);
        payload.max_points = payload.max_points ? parseInt(payload.max_points, 10) : null;
        payload.require_parent_call = form.querySelector('[name="require_parent_call"]').checked;
        payload.require_bk_counseling = form.querySelector('[name="require_bk_counseling"]').checked;
        if (await apiRequest(`/api/v1/discipline/thresholds/${id}`, 'PUT', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    async function deleteThreshold(id, name) {
        if (!confirm(`Hapus ambang batas "${name}"?`)) return;
        if (await apiRequest(`/api/v1/discipline/thresholds/${id}`, 'DELETE')) {
            setTimeout(() => window.location.reload(), 400);
        }
    }

    // 4. Kebijakan & Program Pemutihan Waka Kesiswaan
    async function submitDisciplinePolicy(e) {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.school_id = parseInt(payload.school_id, 10);
        payload.max_violation_points = parseInt(payload.max_violation_points, 10);
        payload.auto_refer_bk_threshold = parseInt(payload.auto_refer_bk_threshold, 10);
        payload.max_amnesty_points_per_semester = parseInt(payload.max_amnesty_points_per_semester, 10);
        payload.min_clean_days_before_amnesty = parseInt(payload.min_clean_days_before_amnesty, 10);
        payload.amnesty_enabled = payload.amnesty_enabled === '1';
        payload.allow_amnesty_for_heavy_violations = payload.allow_amnesty_for_heavy_violations === '1';

        if (await apiRequest('/api/v1/discipline/policy', 'PUT', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    async function submitAddAmnestyRule(e) {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.credit_points = parseInt(payload.credit_points, 10);
        payload.min_violation_points_required = parseInt(payload.min_violation_points_required || '0', 10);
        payload.clean_days_required = parseInt(payload.clean_days_required || '0', 10);
        payload.max_claims_per_semester = payload.max_claims_per_semester ? parseInt(payload.max_claims_per_semester, 10) : null;
        if (await apiRequest('/api/v1/discipline/amnesty-rules', 'POST', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    function openEditAmnestyModal(id, code, name, credit, minPts, cleanDays, maxClaims, desc) {
        document.getElementById('editAmnestyId').value = id;
        document.getElementById('editAmnestyCode').value = code;
        document.getElementById('editAmnestyName').value = name;
        document.getElementById('editAmnestyCredit').value = credit;
        document.getElementById('editAmnestyMinPts').value = minPts;
        document.getElementById('editAmnestyCleanDays').value = cleanDays;
        document.getElementById('editAmnestyMaxClaims').value = maxClaims || '';
        document.getElementById('editAmnestyDesc').value = desc || '';
        openModal('modalEditAmnestyRule');
    }

    async function submitEditAmnestyRule(e) {
        e.preventDefault();
        const id = document.getElementById('editAmnestyId').value;
        const payload = Object.fromEntries(new FormData(e.target).entries());
        payload.credit_points = parseInt(payload.credit_points, 10);
        payload.min_violation_points_required = parseInt(payload.min_violation_points_required || '0', 10);
        payload.clean_days_required = parseInt(payload.clean_days_required || '0', 10);
        payload.max_claims_per_semester = payload.max_claims_per_semester ? parseInt(payload.max_claims_per_semester, 10) : null;
        if (await apiRequest(`/api/v1/discipline/amnesty-rules/${id}`, 'PUT', payload)) {
            setTimeout(() => window.location.reload(), 500);
        }
    }

    async function deleteAmnestyRule(id, name) {
        if (!confirm(`Hapus program pemutihan "${name}"?`)) return;
        if (await apiRequest(`/api/v1/discipline/amnesty-rules/${id}`, 'DELETE')) {
            setTimeout(() => window.location.reload(), 400);
        }
    }

    // 5. Pencatatan Pelanggaran & Pemutihan Siswa
    function quickRecordForStudent(studentId, mode) {
        if (mode === 'violation') {
            document.getElementById('recordViolationStudentId').value = String(studentId);
            openModal('modalRecordViolation');
        } else {
            document.getElementById('recordAmnestyStudentId').value = String(studentId);
            openModal('modalRecordAmnesty');
        }
    }

    async function submitRecordViolation(e) {
        e.preventDefault();
        const payload = Object.fromEntries(new FormData(e.target).entries());
        if (await apiRequest('/api/v1/discipline/violations', 'POST', payload)) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'pane-ledger');
            window.location.href = url.toString();
        }
    }

    async function submitRecordAmnesty(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        if (!payload.custom_credit_points) delete payload.custom_credit_points;
        payload.force_override = form.querySelector('[name="force_override"]')?.checked || false;
        if (await apiRequest('/api/v1/discipline/amnesties', 'POST', payload)) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'pane-ledger');
            window.location.href = url.toString();
        }
    }

    async function deleteDisciplineRecord(id) {
        if (!confirm('Hapus catatan buku poin ini?')) return;
        if (await apiRequest(`/api/v1/discipline/records/${id}`, 'DELETE')) {
            setTimeout(() => window.location.reload(), 400);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tabParam = new URLSearchParams(window.location.search).get('tab');
        if (tabParam && document.getElementById(tabParam)) {
            switchTatibTab(tabParam);
        }
    });
</script>
@endpush

