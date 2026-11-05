<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Mengajar Guru — {{ $selectedTeacher?->full_name ?? 'Laporan KBM' }} — {{ $school->name }}</title>
    
    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <style>
        :root {
            --text-heading: #1e293b;
            --text-body: #334155;
            --border-color: #cbd5e1;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: var(--text-body);
            background: #f1f5f9;
            padding: 24px;
        }

        .screen-toolbar {
            max-width: 1050px;
            margin: 0 auto 20px auto;
            background: white;
            padding: 14px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            gap: 12px;
            flex-wrap: wrap;
        }

        .print-sheet {
            max-width: 1050px;
            margin: 0 auto;
            background: white;
            padding: 32px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            min-height: 100vh;
        }

        /* Kop Surat Sekolah */
        .school-header {
            text-align: center;
            border-bottom: 3px double #1e293b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .school-header h2 {
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .school-header h3 {
            font-size: 13pt;
            font-weight: 700;
            color: #334155;
            margin-top: 2px;
        }

        .school-header p {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }

        .doc-title {
            text-align: center;
            margin: 18px 0;
        }

        .doc-title h4 {
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 10pt;
            font-weight: 600;
            color: #475569;
            margin-top: 4px;
        }

        /* Identitas Guru & Mapel */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 30px;
            margin-bottom: 18px;
            font-size: 10pt;
        }

        .meta-row {
            display: flex;
        }

        .meta-label {
            width: 140px;
            font-weight: 600;
            color: #475569;
        }

        .meta-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Tabel Jurnal */
        .journal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 24px;
        }

        .journal-table th, .journal-table td {
            border: 1px solid #334155;
            padding: 8px 10px;
            vertical-align: top;
        }

        .journal-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
        }

        .journal-table td.center {
            text-align: center;
        }

        /* Lembar Pengesahan Tanda Tangan */
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: center;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }

        .sig-box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 110px;
        }

        .sig-name {
            font-weight: 700;
            text-decoration: underline;
        }

        .sig-nip {
            font-size: 8.5pt;
            color: #475569;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .screen-toolbar {
                display: none !important;
            }

            .print-sheet {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }

            @page {
                size: landscape;
                margin: 1.2cm 1.5cm;
            }
        }
    </style>
</head>
<body>

    <!-- SCREEN TOOLBAR (FILTER & PRINT BUTTON) -->
    <div class="screen-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.kurikulum.jurnal') }}" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-size: 13px; font-weight: 600; color: #475569;">
                <i class='bx bx-arrow-back'></i> Kembali ke Portal KBM
            </a>
            <span style="color: #cbd5e1;">|</span>
            <span style="font-size: 13px; font-weight: 700;">Format Laporan Buku Kerja Guru 3</span>
        </div>

        <form method="GET" action="{{ route('admin.kurikulum.jurnal.report') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <select name="teacher_id" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px;" onchange="this.form.submit()">
                <option value="">-- Pilih Guru --</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->full_name }}</option>
                @endforeach
            </select>

            <select name="subject_id" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px;" onchange="this.form.submit()">
                <option value="">-- Semua Mapel --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>

            <select name="class_id" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px;" onchange="this.form.submit()">
                <option value="">-- Semua Kelas --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <button type="button" onclick="window.print()" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; background: #4680ff; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer;">
                <i class='bx bx-printer'></i> Cetak Laporan (Print)
            </button>
        </form>
    </div>

    <!-- PRINTABLE SHEET -->
    <div class="print-sheet">
        <!-- KOP SEKOLAH -->
        <div class="school-header">
            <h2>{{ $school->name }}</h2>
            <h3>PEMERINTAH PROVINSI / DAERAH SATUAN PENDIDIKAN</h3>
            <p>NPSN: {{ $school->npsn ?? '-' }} • Alamat: {{ $school->address ?? 'Kampus Satuan Pendidikan' }} • Tenant: {{ $school->tenant_id }}</p>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title">
            <h4>JURNAL AGENDA MENGAJAR GURU</h4>
            <p>Tahun Ajaran {{ $selectedYear->name ?? '2026/2027' }} — Semester {{ ucfirst($semester) }}</p>
        </div>

        <!-- IDENTITAS GURU & KELAS -->
        <div class="meta-grid">
            <div>
                <div class="meta-row">
                    <span class="meta-label">Nama Guru</span>
                    <span class="meta-value">: {{ $selectedTeacher?->formatted_name ?? ($selectedTeacher?->full_name ?? '(Semua Guru)') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">NIP / NUPTK</span>
                    <span class="meta-value">: {{ $selectedTeacher?->nip ?? ($selectedTeacher?->nuptk ?? '-') }}</span>
                </div>
            </div>
            <div>
                <div class="meta-row">
                    <span class="meta-label">Mata Pelajaran</span>
                    <span class="meta-value">: {{ $selectedSubject?->name ?? '(Semua Mata Pelajaran)' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Kelas / Rombel</span>
                    <span class="meta-value">: {{ $selectedClass?->name ?? '(Semua Kelas)' }}</span>
                </div>
            </div>
        </div>

        <!-- TABEL JURNAL KBM -->
        <table class="journal-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th style="width: 75px;">Hari / Tgl</th>
                    <th style="width: 60px;">Jam Ke</th>
                    <th style="width: 75px;">Kelas</th>
                    <th>Tujuan Pembelajaran (TP) / Pokok Bahasan</th>
                    <th>Aktivitas & Metode Pembelajaran</th>
                    <th style="width: 100px;">Kehadiran Siswa</th>
                    <th style="width: 110px;">Hambatan & Tindak Lanjut</th>
                    <th style="width: 60px;">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @forelse($journals as $idx => $j)
                    <tr>
                        <td class="center">{{ $j->meeting_number }}</td>
                        <td class="center">
                            <strong>{{ $j->date->isoFormat('D/MM/YY') }}</strong><br>
                            <small>{{ $j->date->isoFormat('ddd') }}</small>
                        </td>
                        <td class="center">{{ $j->start_period }} - {{ $j->end_period }}</td>
                        <td class="center"><strong>{{ $j->schoolClass?->name ?? ($j->teachingGroup?->name ?? '-') }}</strong></td>
                        <td>
                            <strong>{{ $j->learning_objective }}</strong>
                            @if($j->homework_assignment)
                                <div style="font-size: 8pt; color: #475569; margin-top: 4px;">Tugas: {{ $j->homework_assignment }}</div>
                            @endif
                        </td>
                        <td>{{ $j->teaching_activity ?: '-' }}</td>
                        <td class="center" style="font-family: 'JetBrains Mono', monospace; font-size: 8.5pt;">
                            @if($j->attendance_summary)
                                H: {{ $j->attendance_summary['present'] ?? 0 }}<br>
                                S: {{ $j->attendance_summary['sick'] ?? 0 }} | I: {{ $j->attendance_summary['excused'] ?? 0 }}<br>
                                A: {{ $j->attendance_summary['absent'] ?? 0 }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="font-size: 8.5pt;">{{ $j->obstacles_and_followup ?: '-' }}</td>
                        <td class="center" style="vertical-align: middle;">
                            @if($j->verified_by_waka_at)
                                <span style="font-size: 8pt; font-weight: 700; color: #0284c7;">✓ VERIF</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="center" style="padding: 30px; color: #64748b;">
                            Tidak ada data sesi KBM yang tercatat / selesai pada filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- LEMBAR PENGESAHAN TANDA TANGAN -->
        <div class="signature-section">
            <div class="sig-box">
                <div>
                    Mengetahui,<br>
                    Kepala Sekolah
                </div>
                <div>
                    <div class="sig-name">{{ $school->headmaster_name ?? 'Drs. H. Mulyadi, M.Pd.' }}</div>
                    <div class="sig-nip">NIP. {{ $school->headmaster_nip ?? '197505102000031002' }}</div>
                </div>
            </div>

            <div class="sig-box">
                <div>
                    Diverifikasi Oleh,<br>
                    Waka Bidang Kurikulum
                </div>
                <div>
                    <div class="sig-name">Waka Kurikulum Sekolah</div>
                    <div class="sig-nip">NIP. 198203152006041008</div>
                </div>
            </div>

            <div class="sig-box">
                <div>
                    Kota, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                    Guru Mata Pelajaran
                </div>
                <div>
                    <div class="sig-name">{{ $selectedTeacher?->formatted_name ?? ($selectedTeacher?->full_name ?? 'Guru Pengampu') }}</div>
                    <div class="sig-nip">NIP. {{ $selectedTeacher?->nip ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

