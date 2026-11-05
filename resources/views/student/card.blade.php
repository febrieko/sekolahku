@extends('layouts.student')

@section('title', 'Kartu Pelajar Digital — ' . $student->full_name . ' | Sekolahku')
@section('breadcrumb', 'Kartu Pelajar Digital')

@push('styles')
<style>
    .id-passbook-wrap {
        display: grid;
        grid-template-columns: 420px 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    @media (max-width: 960px) {
        .id-passbook-wrap { grid-template-columns: 1fr; }
    }
    .student-id-card {
        background: linear-gradient(145deg, #1e1b4b 0%, #312e81 55%, #4f46e5 100%);
        color: #ffffff;
        border-radius: 22px;
        padding: 1.5rem;
        box-shadow: 0 20px 40px -12px rgba(49, 46, 129, 0.5);
        position: relative;
        overflow: hidden;
    }
    .student-id-card::before {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.07);
        top: -80px;
        right: -60px;
    }
    .id-photo-4x6 {
        width: 104px;
        height: 138px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        background: #312e81;
    }
    @media print {
        .std-sidebar, .std-topbar, .mobile-bottom-nav, .no-print { display: none !important; }
        .std-content { padding: 0 !important; }
        .id-passbook-wrap { grid-template-columns: 420px; }
    }
</style>
@endpush

@section('content')
@php
    $previewQuery = (!empty($isStaffPreview) && request('student_id')) ? ['student_id' => request('student_id')] : [];
    $avatarUrl = $student->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=4338ca&color=fff&size=260';
    $qrPayload = urlencode("SEKOLAHKU|NIS:{$student->nis}|NISN:" . ($student->nisn ?? '-') . "|NAMA:{$student->full_name}");
    $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qrPayload}";
@endphp

<div class="id-passbook-wrap">
    <!-- KARTU PELAJAR VISUAL PASSBOOK 4x6 -->
    <div>
        <div class="student-id-card">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.18); padding-bottom: 0.85rem; margin-bottom: 1.15rem;">
                <div>
                    <div style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #c7d2fe; font-weight: 700;">Kartu Identitas Peserta Didik</div>
                    <div style="font-size: 1rem; font-weight: 800;">{{ $school->name ?? 'SMA Negeri 1 Jakarta' }}</div>
                </div>
                <span class="vx-badge cell-mono" style="background: rgba(255,255,255,0.18); color: #fff;">NPSN {{ $school->npsn ?? '-' }}</span>
            </div>

            <div style="display: flex; gap: 1.1rem; align-items: center; margin-bottom: 1.2rem;">
                <img src="{{ $avatarUrl }}" alt="{{ $student->full_name }}" class="id-photo-4x6">
                <div style="min-width: 0;">
                    <div style="font-size: 1.18rem; font-weight: 800; line-height: 1.25;">{{ $student->full_name }}</div>
                    <div class="cell-mono" style="font-size: 0.8rem; color: #c7d2fe; margin-top: 0.35rem;">NIS: <strong style="color: #fff;">{{ $student->nis }}</strong></div>
                    <div class="cell-mono" style="font-size: 0.8rem; color: #c7d2fe;">NISN: <strong style="color: #fff;">{{ $student->nisn ?? '-' }}</strong></div>
                    <div style="margin-top: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.35rem;">
                        <span class="vx-badge" style="background: #10b981; color: #fff;">Kelas {{ $student->schoolClass?->name ?? '-' }}</span>
                        @if($student->religion)
                            <span class="vx-badge" style="background: rgba(255,255,255,0.16); color: #fff;">{{ $student->religion }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.95); color: #0f172a; border-radius: 14px; padding: 0.85rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.85rem;">
                <div>
                    <div style="font-size: 0.68rem; text-transform: uppercase; font-weight: 800; color: #64748b;">Angkatan & Masa Aktif</div>
                    <div style="font-size: 0.84rem; font-weight: 800;">Angkatan {{ $student->entryAcademicYear?->name ?? ($activeYear?->name ?? '-') }}</div>
                    <div style="font-size: 0.73rem; color: #475569; margin-top: 2px;">
                        TTL: {{ $student->birth_place ?? '-' }}{{ $student->birth_date ? ', '.$student->birth_date->format('d/m/Y') : '' }}
                    </div>
                    <div style="font-size: 0.72rem; color: #059669; font-weight: 700; margin-top: 3px;">
                        STATUS: PESERTA DIDIK AKTIF
                    </div>
                </div>
                <img src="{{ $qrImageUrl }}" alt="QR Identitas Siswa" style="width: 74px; height: 74px; border-radius: 8px; border: 1px solid #e2e8f0;">
            </div>
        </div>

        <div class="no-print" style="display: flex; gap: 0.65rem; margin-top: 1rem;">
            <button type="button" onclick="window.print()" class="btn btn-primary" style="flex: 1;">
                <i class='bx bx-printer'></i> Cetak Kartu Pelajar
            </button>
            <a href="{{ route('student.profile', $previewQuery) }}" class="btn btn-secondary" style="flex: 1;">
                <i class='bx bx-edit'></i> Update Foto 4x6 & Biodata
            </a>
        </div>
    </div>

    <!-- KOLOM KANAN: CHECKLIST BIODATA DAPODIK & RIWAYAT ROMBEL ANGKATAN -->
    <div class="no-print">
        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title"><i class='bx bx-list-check' style="color: var(--vx-success);"></i> Checklist Kelengkapan Atribut Dapodik ({{ $profileStats['percentage'] }}%)</h2>
                    <p class="apex-card-desc">Pastikan seluruh atribut pokok terisi benar untuk keperluan E-Rapor dan Ijazah.</p>
                </div>
                <a href="{{ route('student.profile', $previewQuery) }}" class="btn btn-primary btn-sm">Lengkapi Data</a>
            </div>
            <div class="apex-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.55rem;">
                    @foreach($profileStats['checks'] as $label => $isComplete)
                        <div style="padding: 0.55rem 0.75rem; border-radius: 10px; border: 1px solid var(--border-color); background: var(--bg-subtle); display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.8rem; font-weight: 600;">{{ $label }}</span>
                            @if($isComplete)
                                <i class='bx bxs-check-circle' style="color: var(--vx-success); font-size: 1.1rem;"></i>
                            @else
                                <i class='bx bxs-info-circle' style="color: var(--vx-warning); font-size: 1.1rem;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title"><i class='bx bx-transfer-alt' style="color: var(--vx-primary);"></i> Riwayat Penempatan Rombel & Kenaikan Kelas</h2>
                    <p class="apex-card-desc">Jejak historis rombongan belajar sejak pertama terdaftar di sekolah.</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="vx-table">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Rombongan Belajar</th>
                            <th>Tingkat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->classHistories as $hist)
                            <tr>
                                <td class="cell-mono"><strong>{{ $hist->academicYear?->name ?? '-' }}</strong></td>
                                <td><span class="cell-title">{{ $hist->schoolClass?->name ?? '-' }}</span></td>
                                <td>Kelas {{ $hist->schoolClass?->grade_level ?? '-' }}</td>
                                <td><span class="vx-badge badge-success">{{ strtoupper($hist->status ?? 'aktif') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td class="cell-mono"><strong>{{ $activeYear?->name ?? '-' }}</strong></td>
                                <td><span class="cell-title">{{ $student->schoolClass?->name ?? '-' }}</span></td>
                                <td>Kelas {{ $student->schoolClass?->grade_level ?? '-' }}</td>
                                <td><span class="vx-badge badge-success">AKTIF</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
