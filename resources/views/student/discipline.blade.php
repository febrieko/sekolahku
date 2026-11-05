@extends('layouts.student')

@section('title', 'Tata Tertib & Kredit Poin Saya — ' . $student->full_name . ' | Sekolahku')
@section('breadcrumb', 'Tata Tertib & Poin Saya')

@section('content')
@php
    $policy = $disciplineData['policy'];
    $netPts = $disciplineData['netViolationPoints'];
    $creditScore = $disciplineData['positiveCreditScore'];
    $maxCap = $disciplineData['maxCap'];
    $curThreshold = $disciplineData['currentThreshold'];
    $nextThreshold = $disciplineData['nextThreshold'];
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-success"><i class='bx bx-shield-quarter'></i></div>
        <div class="stat-body">
            <div class="stat-label">Saldo Kredit Positif</div>
            <div class="stat-value">{{ $creditScore }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">/ {{ $maxCap }}</span></div>
            <div class="stat-sub">Status: <strong>{{ $curThreshold ? $curThreshold->name : 'Disiplin Baik / Aman' }}</strong></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-danger"><i class='bx bx-error-alt'></i></div>
        <div class="stat-body">
            <div class="stat-label">Akumulasi Pelanggaran Bersih</div>
            <div class="stat-value">{{ $netPts }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Poin</span></div>
            <div class="stat-sub">Total Pelanggaran: {{ $disciplineData['violationPoints'] }} Poin</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-info"><i class='bx bx-refresh'></i></div>
        <div class="stat-body">
            <div class="stat-label">Pemutihan / Pengurangan Poin</div>
            <div class="stat-value">-{{ $disciplineData['amnestyPoints'] }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Poin</span></div>
            <div class="stat-sub">{{ $disciplineData['cleanDays'] }} Hari Bersih Beruntun</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-warning"><i class='bx bx-bell'></i></div>
        <div class="stat-body">
            <div class="stat-label">Batas Ambang Berikutnya</div>
            <div class="stat-value" style="font-size: 1.05rem;">{{ $nextThreshold ? $nextThreshold->name.' ('.$nextThreshold->min_points.' Poin)' : 'Batas Maksimal' }}</div>
            <div class="stat-sub">Reset Poin: {{ $policy->reset_period }}</div>
        </div>
    </div>
</div>

<!-- Ambang Batas Sanksi & Program Pemutihan Sekolah -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
    <div class="apex-card" style="margin-bottom: 0;">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title"><i class='bx bx-slider-alt' style="color: var(--vx-warning);"></i> Tahapan Ambang Batas Pembinaan Sekolah</h2>
                <p class="apex-card-desc">Ditetapkan oleh Waka Kesiswaan sebagai pedoman kedisiplinan siswa.</p>
            </div>
        </div>
        <div class="apex-card-body" style="display: flex; flex-direction: column; gap: 0.6rem;">
            @foreach($disciplineData['thresholds'] as $th)
                @php
                    $isActiveLevel = $curThreshold && $curThreshold->id === $th->id;
                @endphp
                <div style="padding: 0.7rem 0.9rem; border-radius: 12px; border: 1.5px solid {{ $isActiveLevel ? ($th->color ?? '#f59e0b') : 'var(--border-color)' }}; background: {{ $isActiveLevel ? 'var(--vx-warning-light)' : 'var(--bg-subtle)' }}; display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                    <div>
                        <div class="cell-title" style="display: flex; align-items: center; gap: 0.4rem;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $th->color ?? '#f59e0b' }}; display: inline-block;"></span>
                            <span>{{ $th->name }}</span>
                            @if($isActiveLevel)
                                <span class="vx-badge badge-warning">Posisi Saat Ini</span>
                            @endif
                        </div>
                        <div class="cell-sub">{{ $th->sanction_action }}</div>
                    </div>
                    <span class="vx-badge badge-dark cell-mono">{{ $th->min_points }}{{ $th->max_points ? '–'.$th->max_points : '+' }} Poin</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="apex-card" style="margin-bottom: 0;">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title"><i class='bx bx-heart-circle' style="color: var(--vx-success);"></i> Program Pemutihan / Pengurangan Poin</h2>
                <p class="apex-card-desc">Bentuk pembinaan positif untuk memulihkan poin pelanggaran melalui kegiatan baik.</p>
            </div>
        </div>
        <div class="apex-card-body" style="display: flex; flex-direction: column; gap: 0.6rem;">
            @forelse($disciplineData['amnestyRules'] as $am)
                <div style="padding: 0.7rem 0.9rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-subtle); display: flex; justify-content: space-between; align-items: center; gap: 0.75rem;">
                    <div>
                        <div class="cell-title">{{ $am->name }}</div>
                        <div class="cell-sub">Syarat minimal {{ $am->clean_days_required }} hari bersih &bull; Maks {{ $am->max_claims_per_semester }}x / semester</div>
                    </div>
                    <span class="vx-badge badge-success cell-mono">-{{ $am->credit_points }} Poin</span>
                </div>
            @empty
                <div style="color: var(--text-muted); font-size: 0.82rem;">Belum ada aturan pemutihan poin aktif.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Riwayat Catatan Poin Pribadi Siswa -->
<div class="apex-card">
    <div class="apex-card-header">
        <div>
            <h2 class="apex-card-title"><i class='bx bx-notepad' style="color: var(--vx-primary);"></i> Riwayat Catatan Poin Disiplin Saya</h2>
            <p class="apex-card-desc">Transparansi pencatatan pelanggaran maupun pemutihan poin pada periode aktif.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="vx-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Pasal / Keterangan</th>
                    <th>Poin</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disciplineData['records'] as $rec)
                    <tr>
                        <td class="cell-mono">{{ $rec->record_date ? $rec->record_date->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($rec->record_type === 'AMNESTY')
                                <span class="vx-badge badge-success">Pemutihan Poin</span>
                            @else
                                <span class="vx-badge badge-danger">Pelanggaran</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-title">{{ $rec->title ?: ($rec->disciplineItem?->name ?? $rec->amnestyRule?->name ?? '-') }}</div>
                            @if($rec->notes)
                                <div class="cell-sub">{{ $rec->notes }}</div>
                            @endif
                        </td>
                        <td class="cell-mono">
                            @if($rec->record_type === 'AMNESTY')
                                <strong style="color: var(--vx-success);">-{{ $rec->points }}</strong>
                            @else
                                <strong style="color: var(--vx-danger);">+{{ $rec->points }}</strong>
                            @endif
                        </td>
                        <td>{{ $rec->recorder?->name ?? 'Tim Kesiswaan' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--vx-success); font-weight: 700;">
                            <i class='bx bx-party' style="font-size: 1.3rem;"></i> Hebat! Tidak ada catatan pelanggaran tata tertib atas namamu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Buku Saku Pasal Tata Tertib Sekolah -->
<div class="apex-card">
    <div class="apex-card-header">
        <div>
            <h2 class="apex-card-title"><i class='bx bx-book-bookmark' style="color: var(--vx-info);"></i> Buku Saku Pasal Tata Tertib Sekolah</h2>
            <p class="apex-card-desc">Daftar kategori aturan & bobot poin pelanggaran yang berlaku di {{ $school->name }}.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="vx-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Kategori</th>
                    <th>Butir Tata Tertib / Larangan</th>
                    <th>Tingkat</th>
                    <th style="text-align: right;">Bobot Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($disciplineData['categories'] as $cat)
                    @foreach($cat->items as $item)
                        <tr>
                            <td class="cell-mono"><span class="vx-badge badge-dark">{{ $item->code }}</span></td>
                            <td><span class="cell-title">{{ $cat->name }}</span></td>
                            <td>{{ $item->name }}</td>
                            <td><span class="vx-badge badge-info">{{ $item->severity }}</span></td>
                            <td class="cell-mono" style="text-align: right; font-weight: 800; color: var(--vx-danger);">{{ $item->points }} Poin</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
