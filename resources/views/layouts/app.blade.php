<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Arsip Pajak KPU Provinsi Jawa Tengah">
    <title>@yield('title', 'Dashboard') — Sistem Arsip Pajak | KPU Jawa Tengah</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/logo-kpu-transparent.png') }}">
</head>
<body>
<div class="app-layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        {{-- Brand / Logo --}}
        <div class="sidebar-brand" style="padding:20px 18px 16px;gap:10px;flex-direction:column;text-align:center;border-bottom:1px solid var(--border);background:linear-gradient(180deg,rgba(245,158,11,0.08),transparent);">
            {{-- Logo dalam lingkaran putih profesional --}}
            <div style="width:76px;height:76px;border-radius:50%;overflow:hidden;border:2.5px solid rgba(245,158,11,0.5);box-shadow:0 0 0 4px rgba(245,158,11,0.08),0 4px 20px rgba(0,0,0,0.4);margin:0 auto;background:#fff;flex-shrink:0;">
                <img src="{{ asset('images/logo-kpu-transparent.png') }}" alt="Logo KPU"
                    style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div style="text-align:center;margin-top:4px;">
                <div style="font-size:11.5px;font-weight:800;color:var(--accent);letter-spacing:0.6px;line-height:1.4;">KPU PROVINSI</div>
                <div style="font-size:11.5px;font-weight:800;color:var(--accent);letter-spacing:0.6px;">JAWA TENGAH</div>
                <div style="font-size:9px;color:var(--text-muted);margin-top:4px;text-transform:uppercase;letter-spacing:0.8px;background:rgba(255,255,255,0.05);border-radius:4px;padding:2px 8px;display:inline-block;">Sistem Arsip Pajak</div>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                @if(auth()->user()->foto)
                    <img src="{{ Storage::url(auth()->user()->foto) }}" alt="Foto">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role === 'pimpinan' ? '👑 Pimpinan' : '👨‍💼 Staff Pajak' }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>

            <a href="{{ route('arsip.index') }}" class="nav-item {{ request()->routeIs('arsip.*') ? 'active' : '' }}">
                <span class="nav-icon">📂</span> Arsip Pajak
            </a>

            <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Laporan
            </a>

            @if(auth()->user()->isPimpinan())
                <div class="nav-section">Pimpinan</div>

                <a href="{{ route('approval.index') }}" class="nav-item {{ request()->routeIs('approval.*') ? 'active' : '' }}">
                    <span class="nav-icon">✅</span> Approval
                    @php $pending = \App\Models\ArsipPajak::where('status','menunggu')->count(); @endphp
                    @if($pending > 0)
                        <span class="nav-badge">{{ $pending }}</span>
                    @endif
                </a>

                <a href="{{ route('jenis-pajak.index') }}" class="nav-item {{ request()->routeIs('jenis-pajak.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏷️</span> Jenis Pajak
                </a>

                <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span> Manajemen User
                </a>
            @endif

            <div class="nav-section">Akun</div>

            <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="nav-icon">👤</span> Profil Saya
            </a>
        </nav>

        <div class="sidebar-footer">
            <div style="font-size:10px;color:var(--text-muted);text-align:center;margin-bottom:10px;line-height:1.6;">
                Komisi Pemilihan Umum<br>Provinsi Jawa Tengah
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary btn-block" style="font-size:13px;">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        {{-- Top Government Banner --}}
        <div style="background:linear-gradient(135deg,#0d1b3e,#1a2a6c,#0f3460);border-bottom:2px solid var(--accent);padding:5px 24px;display:flex;align-items:center;gap:14px;">
            <div style="width:34px;height:34px;border-radius:50%;overflow:hidden;background:#fff;border:1.5px solid rgba(245,158,11,0.5);flex-shrink:0;">
                <img src="{{ asset('images/logo-kpu-transparent.png') }}" alt="KPU" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div style="border-left:1px solid rgba(255,255,255,0.15);padding-left:14px;">
                <div style="font-size:12px;font-weight:800;color:var(--accent);letter-spacing:0.8px;">KOMISI PEMILIHAN UMUM PROVINSI JAWA TENGAH</div>
                <div style="font-size:10px;color:rgba(255,255,255,0.5);margin-top:1px;letter-spacing:0.3px;">Sistem Informasi Arsip Pajak</div>
            </div>
            <div style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.5);text-align:right;" id="topbar-clock"></div>
        </div>

        <header class="topbar">
            <div class="topbar-left">
                <button class="btn btn-secondary btn-icon" id="sidebarToggle" style="display:none;">☰</button>
                <div>
                    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                    <div class="topbar-subtitle">@yield('page-subtitle', 'KPU Provinsi Jawa Tengah')</div>
                </div>
            </div>
            <div class="topbar-right">
                <div style="display:flex;align-items:center;gap:8px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;padding:6px 12px;">
                    <div class="user-avatar" style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                        @if(auth()->user()->foto)
                            <img src="{{ Storage::url(auth()->user()->foto) }}" alt="">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div style="line-height:1.3;">
                        <div style="font-size:12px;font-weight:700;">{{ auth()->user()->name }}</div>
                        <div style="font-size:10px;color:var(--accent);">{{ auth()->user()->role === 'pimpinan' ? 'Pimpinan' : 'Staff Pajak' }}</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success" id="flash-alert">
                    <span class="alert-icon">✅</span>
                    <span>{{ session('success') }}</span>
                    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" id="flash-alert">
                    <span class="alert-icon">❌</span>
                    <span>{{ session('error') }}</span>
                    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        <footer style="padding:12px 28px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(0,0,0,0.2);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:24px;height:24px;border-radius:50%;overflow:hidden;background:#fff;border:1px solid rgba(245,158,11,0.3);flex-shrink:0;">
                    <img src="{{ asset('images/logo-kpu-transparent.png') }}" alt="KPU" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <span style="font-size:11px;color:var(--text-muted);">© {{ date('Y') }} Komisi Pemilihan Umum Provinsi Jawa Tengah</span>
            </div>
            <span style="font-size:11px;color:var(--text-muted);">Sistem Informasi Arsip Pajak v1.0</span>
        </footer>
    </div>
</div>

<script>
// Clock
function updateClock() {
    const now = new Date();
    const str = now.toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'})
              + ' · ' + now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
    document.getElementById('topbar-clock').textContent = str;
}
updateClock(); setInterval(updateClock, 1000);

// Auto-dismiss flash
setTimeout(() => {
    const el = document.getElementById('flash-alert');
    if (el) { el.style.opacity = '0'; el.style.transition = 'opacity 0.5s'; setTimeout(() => el.remove(), 500); }
}, 5000);

// Sidebar toggle mobile
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
if (window.innerWidth <= 768) {
    document.getElementById('sidebarToggle').style.display = 'flex';
}
</script>
</body>
</html>

