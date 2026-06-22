@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola akun staff dan pimpinan')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span> Manajemen User</div>
            <h1 class="page-title">Manajemen User</h1>
        </div>
        <button onclick="document.getElementById('modal-tambah').style.display='flex'" class="btn btn-primary">
            ➕ Tambah User
        </button>
    </div>

    <div class="card">
        @if($users->isEmpty())
            <div class="table-empty"><div class="empty-icon">👥</div><p>Belum ada user</p></div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>No</th><th>Nama</th><th>NIP</th><th>Email</th><th>Jabatan</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $user)
                    <tr>
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="user-avatar" style="width:34px;height:34px;font-size:13px;flex-shrink:0;">
                                    @if($user->foto)
                                        <img src="{{ Storage::url($user->foto) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($user->name,0,1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:700;">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <div class="text-xs" style="color:var(--accent);">Anda</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->nip ?: '-' }}</td>
                        <td class="text-sm">{{ $user->email }}</td>
                        <td class="text-sm text-muted">{{ $user->jabatan ?: '-' }}</td>
                        <td><span class="badge badge-{{ $user->role }}">{{ $user->role === 'pimpinan' ? '👑 Pimpinan' : '👨‍💼 Staff' }}</span></td>
                        <td class="text-muted text-sm">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-warning btn-icon" title="Edit"
                                    onclick="openEditModal({{ $user->id }},'{{ addslashes($user->name) }}','{{ $user->nip }}','{{ $user->email }}','{{ addslashes($user->jabatan) }}','{{ $user->role }}')">✏️</button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-icon" title="Hapus">🗑️</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal-backdrop" id="modal-tambah" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">➕ Tambah User Baru</div>
            <button class="modal-close" onclick="document.getElementById('modal-tambah').style.display='none'">✕</button>
        </div>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Jabatan / posisi">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="staff">👨‍💼 Staff Pajak</option>
                        <option value="pimpinan">👑 Pimpinan</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password *</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-tambah').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">➕ Tambah User</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-backdrop" id="modal-edit" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">✏️ Edit User</div>
            <button class="modal-close" onclick="document.getElementById('modal-edit').style.display='none'">✕</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" id="edit-nip" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" id="edit-jabatan" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role" id="edit-role" class="form-control" required>
                        <option value="staff">👨‍💼 Staff Pajak</option>
                        <option value="pimpinan">👑 Pimpinan</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" minlength="8">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-edit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-warning">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, nip, email, jabatan, role) {
    document.getElementById('modal-edit').style.display = 'flex';
    document.getElementById('edit-form').action = '/users/' + id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-nip').value = nip || '';
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-jabatan').value = jabatan || '';
    document.getElementById('edit-role').value = role;
}
</script>
@endsection
