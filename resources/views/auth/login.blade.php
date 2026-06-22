<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Sistem Arsip Pajak KPU Provinsi Jawa Tengah">
    <title>Login — Sistem Arsip Pajak | KPU Jawa Tengah</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/logo-kpu-transparent.png') }}">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            background: #0a0f1e;
        }

        /* Left Panel */
        .login-left {
            flex: 1;
            background: linear-gradient(160deg, #0d1b3e 0%, #1a2a6c 40%, #0f3460 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 70%);
            top: -100px; right: -150px;
            border-radius: 50%;
            animation: pulse-glow 4s ease-in-out infinite alternate;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
            bottom: -50px; left: -100px;
            border-radius: 50%;
        }
        @keyframes pulse-glow {
            from { transform: scale(1); opacity: 0.6; }
            to   { transform: scale(1.2); opacity: 1; }
        }

        .left-content { position: relative; z-index: 1; text-align: center; max-width: 380px; }
        .logo-wrapper {
            width: 130px; height: 130px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            border: 2px solid rgba(245,158,11,0.3);
            box-shadow: 0 0 40px rgba(245,158,11,0.15), 0 0 80px rgba(245,158,11,0.06);
            animation: logo-float 3s ease-in-out infinite alternate;
        }
        @keyframes logo-float {
            from { transform: translateY(0); box-shadow: 0 0 40px rgba(245,158,11,0.15); }
            to   { transform: translateY(-8px); box-shadow: 0 12px 50px rgba(245,158,11,0.25); }
        }
        .logo-wrapper img { width: 90px; height: 90px; object-fit: contain; }

        .org-name {
            font-size: 11px;
            color: rgba(245,158,11,0.8);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .org-title {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 8px;
        }
        .org-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 32px;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            text-align: left;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
        }
        .feature-icon {
            width: 32px; height: 32px;
            background: rgba(245,158,11,0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
            border: 1px solid rgba(245,158,11,0.2);
        }

        /* Red-White accent bar (Indonesia flag) */
        .flag-bar {
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #dc2626 50%, #fff 50%);
            opacity: 0.4;
            margin: 24px 0;
            border-radius: 2px;
        }

        /* Right Panel */
        .login-right {
            width: 460px;
            background: var(--bg-secondary);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 48px;
            border-left: 1px solid var(--border);
            position: relative;
        }
        .login-form-wrapper {
            width: 100%;
            max-width: 360px;
            animation: fadeInUp 0.5s ease;
        }

        .form-header { text-align: center; margin-bottom: 32px; }
        .form-header h2 { font-size: 24px; font-weight: 800; margin-bottom: 6px; }
        .form-header p { font-size: 13px; color: var(--text-muted); }

        .divider-text {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .divider-text::before, .divider-text::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .demo-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
            font-family: inherit;
            color: var(--text-primary);
        }
        .demo-btn:hover { background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.3); }
        .demo-btn .demo-role { font-size: 12px; font-weight: 700; color: var(--accent); }
        .demo-btn .demo-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { width: 100%; padding: 32px 24px; }
        }
    </style>
</head>
<body style="background:#0a0f1e;">
<div class="login-page">

    {{-- LEFT PANEL --}}
    <div class="login-left">
        <div class="left-content">
            <div class="logo-wrapper">
                <div style="width:100%;height:100%;border-radius:50%;overflow:hidden;background:#fff;">
                    <img src="{{ asset('images/logo-kpu-transparent.png') }}" alt="Logo KPU Jawa Tengah"
                        style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>
            <div class="org-name">Komisi Pemilihan Umum</div>
            <div class="org-title">Provinsi Jawa Tengah</div>
            <div class="org-subtitle">Sistem Informasi Arsip Pajak</div>

            <div class="flag-bar"></div>

            <ul class="feature-list">
                <li>
                    <div class="feature-icon">📂</div>
                    <div>Pengelolaan arsip pajak digital yang terstruktur dan aman</div>
                </li>
                <li>
                    <div class="feature-icon">✅</div>
                    <div>Alur persetujuan dokumen oleh pimpinan secara real-time</div>
                </li>
                <li>
                    <div class="feature-icon">📊</div>
                    <div>Laporan dan rekapitulasi pajak yang komprehensif</div>
                </li>
                <li>
                    <div class="feature-icon">🔒</div>
                    <div>Sistem keamanan berbasis role pengguna</div>
                </li>
            </ul>
        </div>
    </div>

    {{-- RIGHT PANEL (Form) --}}
    <div class="login-right">
        <div class="login-form-wrapper">

            {{-- Mobile Logo (hidden on desktop) --}}
            <div style="text-align:center;margin-bottom:24px;display:none;" class="mobile-logo">
                <img src="{{ asset('images/logo-kpu-transparent.png') }}" alt="KPU" style="width:60px;height:60px;object-fit:contain;">
            </div>

            <div class="form-header">
                <h2>Masuk ke Sistem</h2>
                <p>Silakan masukkan kredensial akun Anda</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom:20px;">
                    <span class="alert-icon">❌</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom:20px;">
                    <span class="alert-icon">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="login-form">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">📧</span>
                        <input type="email" id="email" name="email"
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}" placeholder="nama@kpu-jateng.go.id"
                            required autofocus>
                    </div>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password"
                            class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" id="remember" name="remember"
                        style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer;">
                    <label for="remember" style="font-size:13px;color:var(--text-secondary);cursor:pointer;">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block" id="login-btn">
                    🔐 Masuk ke Sistem
                </button>
            </form>

            <div class="divider-text">Akun Demo</div>

            <div class="demo-grid">
                <button class="demo-btn" onclick="fillLogin('pimpinan@pajak.com')">
                    <div class="demo-role">👑 Pimpinan</div>
                    <div class="demo-email">pimpinan@pajak.com</div>
                </button>
                <button class="demo-btn" onclick="fillLogin('staff1@pajak.com')">
                    <div class="demo-role">👨‍💼 Staff</div>
                    <div class="demo-email">staff1@pajak.com</div>
                </button>
            </div>
            <p style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:10px;">
                Password demo: <strong style="color:var(--accent)">password</strong>
            </p>

            <div style="margin-top:32px;padding-top:20px;border-top:1px solid var(--border);text-align:center;">
                <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:6px;">
                    <img src="{{ asset('images/logo-kpu-transparent.png') }}" alt="" style="width:18px;height:18px;object-fit:contain;opacity:0.6;">
                    <span style="font-size:11px;color:var(--text-muted);">KPU Provinsi Jawa Tengah</span>
                </div>
                <p style="font-size:10px;color:var(--text-muted);">
                    Sistem Informasi Arsip Pajak &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function fillLogin(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
    document.getElementById('login-form').submit();
}
document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('login-btn');
    btn.innerHTML = '⏳ Memproses...';
    btn.disabled = true;
});
</script>
</body>
</html>

