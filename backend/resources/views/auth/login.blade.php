<!DOCTYPE html>
<html lang="id" dir="ltr" class="">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Masuk — Portal Sekolahku</title>
    <meta name="description" content="Portal Masuk — Sistem Informasi Manajemen Sekolah Terpadu" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />

    <!-- Theme Detection (Apex Standard) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('apex_theme') || 'system';
            if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-app:             #F5F7FC;
            --bg-surface:         #FFFFFF;
            --bg-subtle:          #EEF2F9;
            --border-color:       #E2E8F0;
            --text-heading:       #0F172A;
            --text-body:          #334155;
            --text-muted:         #64748B;
            --apex-primary:       #4F46E5;
            --apex-primary-grad:  linear-gradient(135deg, #4338CA 0%, #4F46E5 50%, #6366F1 100%);
            --apex-primary-text:  #FFFFFF;
            --radius-md:          10px;
            --radius-lg:          14px;
            --radius-xl:          20px;
            --shadow-card:        0 10px 30px -5px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
            --transition:         all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        html.dark {
            --bg-app:             #0B0F19;
            --bg-surface:         #111827;
            --bg-subtle:          #1A2234;
            --border-color:       #1F2937;
            --text-heading:       #F8FAFC;
            --text-body:          #CBD5E1;
            --text-muted:         #94A3B8;
            --apex-primary:       #6366F1;
            --apex-primary-grad:  linear-gradient(135deg, #4F46E5 0%, #6366F1 50%, #818CF8 100%);
            --apex-primary-text:  #FFFFFF;
            --shadow-card:        0 16px 36px -4px rgba(0, 0, 0, 0.7), 0 0 1px 1px rgba(255, 255, 255, 0.08);
        }

        *, *::before, *::after { box-sizing: border-box; }
        
        body {
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-app);
            color: var(--text-body);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.012em;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Top Bar Floating Controls */
        .top-auth-bar {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .theme-btn {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.2rem;
        }
        .theme-btn:hover {
            color: var(--text-heading);
            background-color: var(--bg-subtle);
        }

        /* Main Auth Card */
        .auth-container-apex {
            width: 100%;
            max-width: 440px;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            padding: 2.5rem 2.25rem;
        }

        .brand-header-apex {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            text-decoration: none;
            color: var(--text-heading);
        }
        .brand-icon-apex {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            background: var(--apex-primary-grad);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }
        .brand-title-apex {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .auth-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-heading);
            margin: 0 0 0.35rem;
            letter-spacing: -0.02em;
        }
        .auth-desc {
            font-size: 0.83rem;
            color: var(--text-muted);
            margin: 0 0 1.75rem;
            line-height: 1.45;
        }

        /* Form Controls */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 0.4rem;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 0.95rem;
            color: var(--text-muted);
            font-size: 1.2rem;
            pointer-events: none;
        }
        .apex-input {
            width: 100%;
            padding: 0.68rem 0.95rem 0.68rem 2.65rem;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--text-heading);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            outline: none;
            transition: var(--transition);
        }
        .apex-input:focus {
            border-color: var(--apex-primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
        }
        .pwd-toggle {
            position: absolute;
            right: 0.75rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0.25rem;
        }

        .form-row-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.82rem;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
            color: var(--text-body);
        }
        .checkbox-label input { accent-color: var(--apex-primary); }

        .btn-submit-apex {
            width: 100%;
            padding: 0.8rem 1.25rem;
            background: var(--apex-primary-grad);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.35);
        }
        .btn-submit-apex:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        }

        .auth-footer-tag {
            margin-top: 1.75rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        @media (max-width: 480px) {
            .auth-container-apex {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Floating Theme Toggle -->
    <div class="top-auth-bar">
        <button type="button" class="theme-btn" onclick="toggleApexTheme()" id="themeBtn" title="Ganti Mode Terang/Gelap">
            <i class='bx bx-moon' id="authThemeIcon"></i>
        </button>
    </div>

    <!-- Main Card -->
    <div class="auth-container-apex">
        <a href="/" class="brand-header-apex">
            <div class="brand-icon-apex">
                <i class='bx bxs-graduation'></i>
            </div>
            <div>
                <span class="brand-title-apex">{{ $school->name ?? 'Sekolahku' }}</span>
            </div>
        </a>

        <h1 class="auth-title">Masuk ke Akun</h1>
        <p class="auth-desc">Silakan masukkan identitas (Email atau NISN bagi Siswa) dan kata sandi Anda untuk melanjutkan.</p>

        @if(session('info'))
            <div style="padding: 0.7rem 0.95rem; border-radius: var(--radius-md); background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(2, 132, 199, 0.25); color: #0284C7; font-size: 0.82rem; margin-bottom: 1.25rem;">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div style="padding: 0.7rem 0.95rem; border-radius: var(--radius-md); background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.25); color: #DC2626; font-size: 0.82rem; margin-bottom: 1.25rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="apexLoginForm">
            @csrf
            <div class="form-group">
                <label for="login" class="form-label">Email atau NISN (Siswa)</label>
                <div class="input-wrap">
                    <i class='bx bx-user input-icon'></i>
                    <input type="text" id="login" name="login" class="apex-input" value="{{ old('login', old('email')) }}" placeholder="contoh: guru@sekolahku.sch.id atau 0081234561" required autofocus />
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="input-wrap">
                    <i class='bx bx-lock-alt input-icon'></i>
                    <input type="password" id="password" name="password" class="apex-input" placeholder="••••••••" required />
                    <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility()" aria-label="Lihat kata sandi">
                        <i class='bx bx-hide' id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-row-between">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" checked />
                    <span>Ingat saya</span>
                </label>
                <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator sekolah atau tim IT untuk bantuan pemulihan kata sandi.')" style="color:var(--text-muted); text-decoration:none;">Lupa kata sandi?</a>
            </div>

            <button type="submit" class="btn-submit-apex" id="btnSubmitApex">
                <span>Masuk ke Dashboard</span>
                <i class='bx bx-arrow-right'></i>
            </button>
        </form>

        <div class="auth-footer-tag">
            <span>&copy; {{ date('Y') }} {{ $school->name ?? 'Sekolahku' }}</span>
            <span>Sistem Informasi Manajemen Sekolah Terintegrasi</span>
        </div>
    </div>

    <script>
        function updateAuthThemeUI() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = document.getElementById('authThemeIcon');
            if (icon) icon.className = isDark ? 'bx bx-sun' : 'bx bx-moon';
        }
        function toggleApexTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('apex_theme', isDark ? 'dark' : 'light');
            updateAuthThemeUI();
        }
        document.addEventListener('DOMContentLoaded', updateAuthThemeUI);

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.className = 'bx bx-show';
            } else {
                pwdInput.type = 'password';
                eyeIcon.className = 'bx bx-hide';
            }
        }

        document.getElementById('apexLoginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitApex');
            btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Memverifikasi...";
            btn.style.opacity = '0.85';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>
