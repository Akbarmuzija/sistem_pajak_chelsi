@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span> Profil</div>
            <h1 class="page-title">Profil Saya</h1>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;">

        {{-- Avatar Card --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card" style="text-align:center;">
                <div style="position:relative;display:inline-block;margin:0 auto 16px;">
                    <div class="user-avatar" style="width:90px;height:90px;font-size:36px;margin:0 auto;">
                        @if($user->foto)
                            <img src="{{ Storage::url($user->foto) }}" alt="Foto Profil">
                        @else
                            {{ strtoupper(substr($user->name,0,1)) }}
                        @endif
                    </div>
                </div>
                <h2 style="font-size:18px;font-weight:800;margin-bottom:4px;">{{ $user->name }}</h2>
                <p class="text-muted text-sm">{{ $user->email }}</p>
                <div style="margin:12px auto 0;">
                    <span class="badge badge-{{ $user->role }}" style="font-size:13px;padding:6px 16px;">
                        {{ $user->role === 'pimpinan' ? '👑 Pimpinan Staff Pajak' : '👨‍💼 Staff Pajak' }}
                    </span>
                </div>
                <hr class="divider">
                <div style="text-align:left;display:flex;flex-direction:column;gap:10px;">
                    @if($user->nip)
                    <div style="display:flex;justify-content:space-between;">
                        <span class="text-muted text-sm">NIP</span>
                        <span style="font-weight:600;font-size:13px;">{{ $user->nip }}</span>
                    </div>
                    @endif
                    @if($user->jabatan)
                    <div style="display:flex;justify-content:space-between;">
                        <span class="text-muted text-sm">Jabatan</span>
                        <span style="font-weight:600;font-size:13px;">{{ $user->jabatan }}</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;">
                        <span class="text-muted text-sm">Bergabung</span>
                        <span style="font-weight:600;font-size:13px;">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Statistik singkat --}}
            @if($user->isStaff())
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;font-size:14px;"><div class="title-icon">📊</div> Statistik Saya</div>
                @php
                    $myTotal    = $user->arsipPajak()->count();
                    $myApproved = $user->arsipPajak()->where('status','disetujui')->count();
                    $myPending  = $user->arsipPajak()->where('status','menunggu')->count();
                @endphp
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span class="text-muted text-sm">Total Arsip</span>
                        <span style="font-weight:700;color:var(--accent);">{{ $myTotal }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span class="text-muted text-sm">Disetujui</span>
                        <span style="font-weight:700;color:var(--green);">{{ $myApproved }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span class="text-muted text-sm">Menunggu</span>
                        <span style="font-weight:700;color:var(--yellow);">{{ $myPending }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Form Edit --}}
        <div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="card" style="margin-bottom:16px;">
                    <div class="form-section-title">👤 Informasi Pribadi</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid':'' }}"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control {{ $errors->has('nip') ? 'is-invalid':'' }}"
                                value="{{ old('nip', $user->nip) }}" placeholder="Nomor Induk Pegawai">
                            @error('nip')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled style="opacity:0.5;">
                            <small class="text-muted text-xs">Email tidak dapat diubah</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control"
                                value="{{ old('jabatan', $user->jabatan) }}" placeholder="Jabatan Anda">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Profil</label>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div class="user-avatar" style="width:50px;height:50px;font-size:20px;flex-shrink:0;">
                                @if($user->foto)
                                    <img src="{{ Storage::url($user->foto) }}" alt="">
                                @else
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                @endif
                            </div>
                            <input type="file" name="foto" id="foto" accept="image/jpg,image/jpeg,image/png"
                                class="form-control" onchange="previewFoto(this)" style="flex:1;">
                        </div>
                        <small class="text-muted text-xs">JPG/PNG, maks 2MB</small>
                    </div>
                </div>

                <div class="card" style="margin-bottom:16px;">
                    <div class="form-section-title">🔒 Ubah Password</div>
                    <p class="text-muted text-sm" style="margin-bottom:14px;">Kosongkan jika tidak ingin mengubah password</p>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" minlength="8">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.querySelectorAll('.user-avatar').forEach(el => {
                el.innerHTML = '<img src="'+e.target.result+'" alt="" style="width:100%;height:100%;object-fit:cover;">';
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
