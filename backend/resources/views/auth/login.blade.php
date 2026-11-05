<!DOCTYPE html>
<html lang="id" dir="ltr" class="">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Masuk — Apex Admin Sekolahku</title>
    <meta name="description" content="Halaman Masuk Apex Admin — Sistem Informasi Manajemen Sekolah Terpadu" />
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

    <!-- Typography: Plus Jakarta Sans (Vibrant Bento Edu Font) -->
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
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            width: 34px;
            height: 34px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.15rem;
        }
        .theme-btn:hover {
            color: var(--text-heading);
            background-color: var(--bg-subtle);
        }

        /* Main Auth Card */
        .auth-container-apex {
            width: 100%;
            max-width: 960px;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
        }

        /* Left Side: Form */
        .auth-left {
            padding: 2.75rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-header-apex {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
            text-decoration: none;
            color: var(--text-heading);
        }
        .brand-icon-apex {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            background: var(--text-heading);
            color: var(--bg-surface);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
        }
        .brand-title-apex {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .auth-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0 0 0.25rem;
            letter-spacing: -0.02em;
        }
        .auth-desc {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin: 0 0 1.5rem;
        }

        /* 1-Click Fast Track Dock */
        .fast-track-box {
            background-color: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.85rem 1rem;
            margin-bottom: 1.5rem;
        }
        .fast-track-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .fast-track-btns {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .fast-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.42rem 0.85rem;
            border-radius: var(--radius-md);
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid var(--border-color);
        }
        .fast-btn-primary {
            background-color: var(--apex-primary);
            color: var(--apex-primary-text);
            border-color: transparent;
        }
        .fast-btn-secondary {
            background-color: var(--bg-surface);
            color: var(--text-heading);
        }
        .fast-btn:hover { opacity: 0.9; }

        /* Form Controls */
        .form-group { margin-bottom: 1.15rem; }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 0.85rem;
            color: var(--text-muted);
            font-size: 1.15rem;
            pointer-events: none;
        }
        .apex-input {
            width: 100%;
            padding: 0.6rem 0.85rem 0.6rem 2.5rem;
            font-family: inherit;
            font-size: 0.88rem;
            color: var(--text-heading);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            outline: none;
            transition: var(--transition);
        }
        .apex-input:focus {
            border-color: var(--apex-primary);
            box-shadow: 0 0 0 1px var(--apex-primary);
        }
        .pwd-toggle {
            position: absolute;
            right: 0.75rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.15rem;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .form-row-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            font-size: 0.8rem;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            color: var(--text-body);
        }
        .checkbox-label input { accent-color: var(--text-heading); }

        .btn-submit-apex {
            width: 100%;
            padding: 0.75rem 1.25rem;
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
            gap: 0.45rem;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.35);
        }
        .btn-submit-apex:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        }

        /* Right Side: Demo Profiles */
        .auth-right {
            background-color: var(--bg-subtle);
            border-left: 1px solid var(--border-color);
            padding: 2.25rem 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .demo-heading {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0 0 0.2rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .demo-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0 0 1rem;
        }
        .demo-list-apex {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            max-height: 380px;
            overflow-y: auto;
        }
        .demo-card-btn {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.6rem 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            text-align: left;
        }
        .demo-card-btn:hover {
            border-color: var(--apex-primary);
            background-color: var(--bg-surface);
        }
        .demo-name-apex {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-heading);
        }
        .demo-role-apex {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .auth-footer-tag {
            margin-top: 1.25rem;
            padding-top: 0.85rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.74rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 860px) {
            .auth-container-apex { grid-template-columns: 1fr; }
            .auth-right { border-left: none; border-top: 1px solid var(--border-color); padding: 1.75rem; }
            .auth-left { padding: 2rem 1.5rem; }
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
        <!-- Left: Form -->
        <div class="auth-left">
            <a href="/" class="brand-header-apex">
                <div class="brand-icon-apex">
                    <i class='bx bxs-graduation'></i>
                </div>
                <span class="brand-title-apex">Apex Sekolahku</span>
            </a>

            <h1 class="auth-title">Masuk ke Portal Sekolah</h1>
            <p class="auth-desc">Silakan masukkan kredensial akun Anda untuk membuka dashboard.</p>

            <!-- Fast-Track 1-Click Access Dock -->
            <div class="fast-track-box">
                <div class="fast-track-label">
                    <i class='bx bx-zap'></i> Akses Instan Sekali Klik
                </div>
                <div class="fast-track-btns">
                    <a href="{{ route('quick-login', ['role' => 'school_admin']) }}" class="fast-btn fast-btn-primary">
                        <i class='bx bxs-dashboard'></i> Masuk Sebagai Admin
                    </a>
                    <a href="{{ route('quick-login', ['role' => 'school_admin', 'target' => 'students']) }}" class="fast-btn fast-btn-secondary">
                        <i class='bx bxs-id-card'></i> Mode Kartu Siswa 4x6
                    </a>
                </div>
            </div>

            @if(session('info'))
                <div style="padding: 0.65rem 0.85rem; border-radius: var(--radius-md); background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(2, 132, 199, 0.25); color: #0284C7; font-size: 0.8rem; margin-bottom: 1rem;">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div style="padding: 0.65rem 0.85rem; border-radius: var(--radius-md); background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.25); color: #DC2626; font-size: 0.8rem; margin-bottom: 1rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="apexLoginForm">
                @csrf
                <div class="form-group">
                    <label for="login" class="form-label">Email atau NISN (Siswa)</label>
                    <div class="input-wrap">
                        <i class='bx bx-user input-icon'></i>
                        <input type="text" id="login" name="login" class="apex-input" value="{{ old('login', old('email', 'admin@sekolahku.sch.id')) }}" placeholder="Email akun atau 10 digit NISN..." required autofocus />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-wrap">
                        <i class='bx bx-lock-alt input-icon'></i>
                        <input type="password" id="password" name="password" class="apex-input" value="password" required />
                        <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility()">
                            <i class='bx bx-hide' id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-row-between">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" checked />
                        <span>Ingat saya</span>
                    </label>
                    <a href="javascript:void(0)" onclick="alert('Hubungi admin IT sekolah untuk reset kata sandi.')" style="color:var(--text-muted); text-decoration:none;">Lupa sandi?</a>
                </div>

                <button type="submit" class="btn-submit-apex" id="btnSubmitApex">
                    <span>Masuk ke Dashboard</span>
                    <i class='bx bx-arrow-right'></i>
                </button>
            </form>
        </div>

        <!-- Right: Demo Quick Switcher -->
        <div class="auth-right">
            <div>
                <div class="demo-heading">
                    <i class='bx bx-user-check'></i> Akun Demo Tersedia
                </div>
                <div class="demo-sub">Klik salah satu profil untuk langsung masuk:</div>

                <div class="demo-list-apex">
                    @foreach($demoAccounts as $acc)
                    <button type="button" class="demo-card-btn" onclick="fillAndSubmitDemo('{{ $acc['email'] }}', '{{ $acc['password'] }}')">
                        <div>
                            <div class="demo-name-apex">{{ $acc['name'] }}</div>
                            <div class="demo-role-apex">{{ $acc['role_label'] }} &bull; {{ $acc['email'] }}</div>
                        </div>
                        <i class='bx bx-chevron-right' style="color:var(--text-muted);"></i>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="auth-footer-tag">
                <span>{{ $school->name ?? 'SMAN 1 Jakarta' }}</span>
                <span>Default sandi: <strong>password</strong></span>
            </div>
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

        function fillAndSubmitDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('btnSubmitApex').innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Memproses...";
            document.getElementById('apexLoginForm').submit();
        }
    </script>
</body>
</html>
