@extends('layouts.vuexy')

@section('title', 'Sesi KBM Pertemuan #' . $journal->meeting_number . ' — ' . ($journal->subject?->name ?? 'Jurnal Mengajar'))
@section('breadcrumb_parent', 'Jurnal Mengajar')
@section('breadcrumb_current', 'Pertemuan #' . $journal->meeting_number)

@push('styles')
<style>
    /* Teaching Journal Detail Bento Styling */
    .detail-hero-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-card);
        border-left: 5px solid {{ $journal->subject?->color_badge ?: 'var(--bento-primary)' }};
    }
    .att-segment-group {
        display: inline-flex;
        border-radius: var(--radius-pill);
        padding: 2px;
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        gap: 2px;
    }
    .att-segment-btn {
        display: none;
    }
    .att-segment-label {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.76rem;
        padding: 0.28rem 0.55rem;
        border-radius: var(--radius-pill);
        cursor: pointer;
        transition: var(--transition);
        user-select: none;
        color: var(--text-muted);
        min-width: 28px;
        text-align: center;
        margin-bottom: 0;
    }
    .att-segment-label:hover {
        background: rgba(0, 0, 0, 0.05);
        color: var(--text-heading);
    }
    
    /* Checked status colors */
    .att-segment-btn[value="present"]:checked + .att-segment-label {
        background: var(--vx-success);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(44, 168, 127, 0.35);
    }
    .att-segment-btn[value="sick"]:checked + .att-segment-label {
        background: var(--vx-info);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(4, 169, 245, 0.35);
    }
    .att-segment-btn[value="excused"]:checked + .att-segment-label {
        background: var(--vx-warning);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(229, 138, 0, 0.35);
    }
    .att-segment-btn[value="absent"]:checked + .att-segment-label {
        background: var(--vx-danger);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(248, 113, 113, 0.35);
    }
    .att-segment-btn[value="dispensation"]:checked + .att-segment-label {
        background: var(--vx-purple, #8b5cf6);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(139, 92, 246, 0.35);
    }

    .student-avatar-pill {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
        color: var(--bento-primary);
        flex-shrink: 0;
    }
    .live-tally-bar {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
    }
    .tally-chip {
        padding: 0.2rem 0.55rem;
        border-radius: var(--radius-sm);
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .tally-chip.h { background: var(--vx-success-light); color: var(--vx-success); border: 1px solid var(--vx-success-border); }
    .tally-chip.s { background: var(--vx-info-light); color: var(--vx-info); border: 1px solid var(--vx-info-border); }
    .tally-chip.i { background: var(--vx-warning-light); color: var(--vx-warning); border: 1px solid var(--vx-warning-border); }
    .tally-chip.a { background: var(--vx-danger-light); color: var(--vx-danger); border: 1px solid var(--vx-danger-border); }
    .tally-chip.d { background: rgba(139, 92, 246, 0.15); color: #7c3aed; border: 1px solid rgba(139, 92, 246, 0.3); }

    .form-section-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--text-heading);
        letter-spacing: -0.01em;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- HEADER / HERO KBM -->
    <div class="detail-hero-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('admin.kurikulum.jurnal') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="border-radius: var(--radius-md);">
                        <i class='bx bx-left-arrow-alt'></i> Kembali ke Daftar Jurnal
                    </a>
                    <span class="vx-badge badge-primary font-monospace" style="font-size: 0.75rem;">
                        <i class='bx bx-bookmark'></i> Pertemuan #{{ $journal->meeting_number }}
                    </span>
                    @if($journal->status === 'completed')
                        <span class="badge bg-success d-inline-flex align-items-center gap-1 px-2.5 py-1" style="border-radius: var(--radius-pill); font-size: 0.74rem;">
                            <i class='bx bx-check-circle'></i> KBM Selesai & Terkunci
                        </span>
                    @else
                        <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1 px-2.5 py-1" style="border-radius: var(--radius-pill); font-size: 0.74rem;">
                            <i class='bx bx-loader-alt bx-spin'></i> Sesi KBM Sedang Berlangsung
                        </span>
                    @endif
                </div>

                <h1 class="h3 font-weight-bold mb-1 text-heading d-flex align-items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif; letter-spacing: -0.02em;">
                    {{ $journal->subject?->name }}
                    <span class="text-muted" style="font-size: 1.15rem; font-weight: 500;">&bull;</span>
                    <span style="color: var(--bento-primary);">{{ $journal->schoolClass?->name ?? ($journal->teachingGroup?->name ?? 'Kelas') }}</span>
                </h1>

                <div class="d-flex align-items-center gap-3 text-muted small flex-wrap mt-1">
                    <span><i class='bx bx-calendar text-primary'></i> {{ $journal->date->isoFormat('dddd, D MMMM Y') }}</span>
                    <span>&bull;</span>
                    <span><i class='bx bx-time-five text-info'></i> {{ $journal->time_slot_label }}</span>
                    <span>&bull;</span>
                    <span><i class='bx bx-user text-success'></i> Guru: <strong>{{ $journal->teacher?->formatted_name ?? $journal->teacher?->full_name }}</strong></span>
                    @if($journal->schoolRoom)
                        <span>&bull;</span>
                        <span><i class='bx bx-building text-warning'></i> Ruang: <strong>{{ $journal->schoolRoom->name }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- Verification Action -->
            <div class="d-flex align-items-center gap-2">
                @if($journal->verified_by_waka_at)
                    <div class="p-2.5 px-3 rounded text-white d-flex align-items-center gap-2.5 shadow-sm" style="background: linear-gradient(135deg, #04a9f5, #0284c7); border-radius: var(--radius-lg);">
                        <i class='bx bx-check-shield' style="font-size: 24px;"></i>
                        <div>
                            <div class="font-weight-bold" style="font-size: 0.82rem; line-height: 1.2;">Supervisi Terverifikasi</div>
                            <small style="opacity: 0.9; font-size: 0.72rem;">Oleh {{ $journal->verifiedByWaka?->name }} &bull; {{ $journal->verified_by_waka_at->isoFormat('D MMM Y') }}</small>
                        </div>
                    </div>
                @elseif(auth()->user()?->isSuperAdmin() || auth()->user()?->isWakaKurikulum())
                    <button type="button" class="btn btn-info text-white d-inline-flex align-items-center gap-1.5 shadow-sm" onclick="openVerifyModal()" style="border-radius: var(--radius-md); font-weight: 600;">
                        <i class='bx bx-check-shield' style="font-size: 1.15rem;"></i> Validasi Supervisi Waka
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: var(--radius-lg); border: 1px solid var(--vx-success-border); background: var(--vx-success-light); color: var(--vx-success);">
            <i class='bx bx-check-circle' style="font-size: 1.35rem;"></i>
            <div class="font-weight-600">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FORM UTAMA KBM -->
    <form action="{{ route('admin.kurikulum.jurnal.update', $journal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- KOLOM KIRI: FORM MATERI & AKTIVITAS PEMBELAJARAN -->
            <div class="col-12 col-xl-5">
                <div class="apex-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="p-3.5 border-bottom">
                            <h5 class="mb-0 font-weight-bold d-flex align-items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif; color: var(--text-heading);">
                                <i class='bx bx-edit-alt' style="color:var(--bento-primary); font-size: 1.2rem;"></i>
                                Materi & Aktivitas Pembelajaran
                            </h5>
                            <small class="text-muted">Isikan kompetensi yang diajarkan dan proses tatap muka kelas hari ini.</small>
                        </div>

                        <div class="p-3.5">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label font-weight-bold small text-heading">Pertemuan Ke- <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text font-monospace" style="background: var(--bg-subtle);">#</span>
                                        <input type="number" name="meeting_number" class="form-control font-weight-bold" value="{{ old('meeting_number', $journal->meeting_number) }}" min="1" max="36" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label font-weight-bold small text-heading">Guru Pengganti</label>
                                    <select name="substitute_teacher_id" class="form-select form-select-sm" style="border-radius: var(--radius-md);">
                                        <option value="">-- Guru Utama --</option>
                                        @foreach($teachers as $t)
                                            <option value="{{ $t->id }}" {{ old('substitute_teacher_id', $journal->substitute_teacher_id) == $t->id ? 'selected' : '' }}>
                                                {{ $t->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold small text-heading">
                                    Capaian / Tujuan Pembelajaran (TP) & Pokok Bahasan <span class="text-danger">*</span>
                                </label>
                                <textarea name="learning_objective" class="form-control" rows="3" placeholder="Contoh: Siswa memahami konsep percabangan IF-ELSE dalam Python dan mampu menyusun program penentu kelulusan nilai ujian." required style="font-size: 0.85rem; border-radius: var(--radius-md);">{{ old('learning_objective', $journal->learning_objective) }}</textarea>
                                <small class="text-muted" style="font-size: 0.74rem;">Indikator kompetensi atau topik bahasan modul ajar / RPP.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold small text-heading">Uraian Aktivitas KBM & Metode Pembelajaran</label>
                                <textarea name="teaching_activity" class="form-control" rows="3" placeholder="Contoh: Penjelasan teori dasar 20 menit, demonstrasi live code di proyektor, latihan terbimbing kelompok, dan kuis penutup." style="font-size: 0.85rem; border-radius: var(--radius-md);">{{ old('teaching_activity', $journal->teaching_activity) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold small text-heading">Hambatan Belajar & Tindak Lanjut Guru</label>
                                <textarea name="obstacles_and_followup" class="form-control" rows="2" placeholder="Contoh: 3 siswa masih keliru sintaks indentasi; diberikan bimbingan khusus mandiri saat praktikum." style="font-size: 0.85rem; border-radius: var(--radius-md);">{{ old('obstacles_and_followup', $journal->obstacles_and_followup) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold small text-heading">Tugas Mandiri / PR / Tagihan Formatif (Opsional)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" style="background: var(--bg-subtle);"><i class='bx bx-task text-primary'></i></span>
                                    <input type="text" name="homework_assignment" class="form-control" placeholder="Misal: Tugas Modul Bab 3 Halaman 45 Soal 1-5" value="{{ old('homework_assignment', $journal->homework_assignment) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL AKSI SUBMIT -->
                    <div class="p-3.5 border-top" style="background: var(--bg-subtle);">
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2" style="border-radius: var(--radius-md); font-weight: 600;">
                                <i class='bx bx-save'></i> Simpan Perubahan Materi & Presensi (Draft)
                            </button>

                            @if($journal->status !== 'completed')
                                <button type="submit" name="action" value="complete" class="btn btn-success d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border-radius: var(--radius-md); font-weight: 700;" onclick="return confirm('Apakah sesi KBM sudah selesai? Jurnal yang dikunci akan tercatat resmi dalam buku kerja supervisi.');">
                                    <i class='bx bx-check-double'></i> Selesai & Kunci Jurnal KBM
                                </button>
                            @else
                                <div class="text-center text-muted small py-1">
                                    <i class='bx bx-lock-alt'></i> Sesi KBM telah selesai. Simpan perubahan akan memperbarui data terverifikasi.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: PRESENSI SISWA PER JAM KBM -->
            <div class="col-12 col-xl-7">
                <div class="apex-card h-100">
                    <div class="p-3.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0 font-weight-bold d-flex align-items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif; color: var(--text-heading);">
                                <i class='bx bx-user-check' style="color:var(--bento-primary); font-size: 1.2rem;"></i>
                                Presensi Kehadiran Siswa di Kelas
                            </h5>
                            <small class="text-muted">Total: <strong>{{ $journal->attendances->count() }}</strong> Siswa Terdaftar</small>
                        </div>

                        <!-- Quick Actions & Search -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" onclick="setAllPresent()" style="font-size: 0.76rem; border-radius: var(--radius-md); font-weight: 600;">
                                <i class='bx bx-check-double'></i> Set Semua Hadir
                            </button>
                        </div>
                    </div>

                    <!-- Live Tally & Filter Toolbar -->
                    <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: var(--bg-subtle);">
                        <div class="live-tally-bar">
                            <span class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Rekap:</span>
                            <span class="tally-chip h"><i class='bx bx-check'></i> <span id="tallyH">0</span> Hadir</span>
                            <span class="tally-chip s"><i class='bx bx-plus-medical'></i> <span id="tallyS">0</span> Sakit</span>
                            <span class="tally-chip i"><i class='bx bx-info-circle'></i> <span id="tallyI">0</span> Izin</span>
                            <span class="tally-chip a"><i class='bx bx-x'></i> <span id="tallyA">0</span> Alpa</span>
                            <span class="tally-chip d"><i class='bx bx-award'></i> <span id="tallyD">0</span> Dispen</span>
                        </div>

                        <div style="width: 200px;">
                            <input type="text" id="studentSearchInput" class="form-control form-control-sm" placeholder="Cari nama / NIS siswa..." style="border-radius: var(--radius-pill); font-size: 0.78rem;" onkeyup="filterStudents()">
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="table-responsive" style="max-height: 580px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="attendanceTable" style="font-size: 0.84rem;">
                            <thead class="sticky-top" style="z-index: 10; background: var(--bg-surface); border-bottom: 2px solid var(--border-color);">
                                <tr>
                                    <th class="text-center" style="width: 44px; color: var(--text-heading);">No</th>
                                    <th style="color: var(--text-heading);">Nama Siswa & NIS</th>
                                    <th style="width: 200px; text-align: center; color: var(--text-heading);">Status Kehadiran</th>
                                    <th style="color: var(--text-heading);">Catatan / Keterangan Khusus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($journal->attendances as $idx => $att)
                                    @php $st = $att->student; @endphp
                                    <tr class="student-row" data-name="{{ strtolower($st?->full_name ?? '') }}" data-nis="{{ $st?->nis ?? '' }}">
                                        <td class="text-muted text-center font-monospace" style="font-size: 0.78rem;">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="student-avatar-pill">
                                                    {{ substr($st?->full_name ?? 'S', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-700 text-heading student-name-label" style="line-height: 1.25;">
                                                        {{ $st?->full_name ?? 'Siswa' }}
                                                    </div>
                                                    <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                                        NIS: {{ $st?->nis ?? '-' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="att-segment-group">
                                                <input type="radio" class="att-segment-btn" name="attendances[{{ $st->id }}][status]" id="status_h_{{ $st->id }}" value="present" {{ $att->status === 'present' ? 'checked' : '' }} onchange="updateTally()">
                                                <label class="att-segment-label" for="status_h_{{ $st->id }}" title="Hadir">H</label>

                                                <input type="radio" class="att-segment-btn" name="attendances[{{ $st->id }}][status]" id="status_s_{{ $st->id }}" value="sick" {{ $att->status === 'sick' ? 'checked' : '' }} onchange="updateTally()">
                                                <label class="att-segment-label" for="status_s_{{ $st->id }}" title="Sakit">S</label>

                                                <input type="radio" class="att-segment-btn" name="attendances[{{ $st->id }}][status]" id="status_i_{{ $st->id }}" value="excused" {{ $att->status === 'excused' ? 'checked' : '' }} onchange="updateTally()">
                                                <label class="att-segment-label" for="status_i_{{ $st->id }}" title="Izin">I</label>

                                                <input type="radio" class="att-segment-btn" name="attendances[{{ $st->id }}][status]" id="status_a_{{ $st->id }}" value="absent" {{ $att->status === 'absent' ? 'checked' : '' }} onchange="updateTally()">
                                                <label class="att-segment-label" for="status_a_{{ $st->id }}" title="Alpa / Bolos">A</label>

                                                <input type="radio" class="att-segment-btn" name="attendances[{{ $st->id }}][status]" id="status_d_{{ $st->id }}" value="dispensation" {{ $att->status === 'dispensation' ? 'checked' : '' }} onchange="updateTally()">
                                                <label class="att-segment-label" for="status_d_{{ $st->id }}" title="Dispensasi Lomba / Tugas">D</label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="attendances[{{ $st->id }}][notes]" class="form-control form-control-sm" placeholder="Catatan khusus..." value="{{ $att->notes }}" style="font-size: 0.78rem; border-radius: var(--radius-sm);">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class='bx bx-user-x' style="font-size: 2.2rem; opacity: 0.4;"></i>
                                            <p class="mt-2 mb-0">Tidak ada siswa terdaftar pada kelas ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- MODAL VERIFIKASI SUPERVISI WAKA KURIKULUM -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-dropdown);">
            <div class="modal-header border-bottom p-3.5">
                <h5 class="modal-title font-weight-bold d-flex align-items-center gap-2 text-heading" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class='bx bx-check-shield text-info' style="font-size: 1.4rem;"></i>
                    Verifikasi Supervisi Akademik KBM
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3.5">
                <div class="p-3 rounded mb-3" style="background: var(--bg-subtle); border-left: 4px solid var(--vx-info); font-size: 0.82rem;">
                    Sebagai <strong>Waka Kurikulum / Kepala Sekolah</strong>, Anda akan mengesahkan bahwa pelaksanaan KBM tatap muka pertemuan #{{ $journal->meeting_number }} ini telah sesuai dengan perangkat ajar dan jadwal resmi.
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold small text-heading">Catatan Supervisi / Umpan Balik Guru (Opsional)</label>
                    <textarea id="wakaNotes" class="form-control" rows="3" placeholder="Contoh: Materi tersampaikan dengan sangat baik, keterlibatan siswa aktif, pertahankan metode diskusi dan latihan terbimbing." style="font-size: 0.84rem; border-radius: var(--radius-md);"></textarea>
                </div>
            </div>
            <div class="modal-footer p-3 border-top">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: var(--radius-md);">Batal</button>
                <button type="button" class="btn btn-info text-white btn-sm d-inline-flex align-items-center gap-1.5" onclick="submitVerify()" style="border-radius: var(--radius-md); font-weight: 600;">
                    <i class='bx bx-check-shield'></i> Sahkan & Verifikasi Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateTally() {
        const counts = { present: 0, sick: 0, excused: 0, absent: 0, dispensation: 0 };
        document.querySelectorAll('.att-segment-btn:checked').forEach(r => {
            if (counts[r.value] !== undefined) {
                counts[r.value]++;
            }
        });

        document.getElementById('tallyH').innerText = counts.present;
        document.getElementById('tallyS').innerText = counts.sick;
        document.getElementById('tallyI').innerText = counts.excused;
        document.getElementById('tallyA').innerText = counts.absent;
        document.getElementById('tallyD').innerText = counts.dispensation;
    }

    function setAllPresent() {
        document.querySelectorAll('input[type="radio"][value="present"]').forEach(r => {
            r.checked = true;
        });
        updateTally();
    }

    function filterStudents() {
        const q = document.getElementById('studentSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.student-row');
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nis = row.getAttribute('data-nis') || '';
            if (!q || name.includes(q) || nis.includes(q)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function openVerifyModal() {
        const modal = new bootstrap.Modal(document.getElementById('verifyModal'));
        modal.show();
    }

    async function submitVerify() {
        const notes = document.getElementById('wakaNotes').value;
        try {
            const res = await fetch('{{ route("admin.kurikulum.jurnal.verify", $journal->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notes: notes })
            });
            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal memverifikasi');
            }
        } catch (e) {
            alert('Terjadi kesalahan koneksi');
        }
    }

    // Initialize tally count on load
    document.addEventListener('DOMContentLoaded', () => {
        updateTally();
    });
</script>
@endpush
@endsection
