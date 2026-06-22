@extends('layouts.app')

@section('title', 'Master Jenis Pajak')
@section('page-title', 'Master Jenis Pajak')
@section('page-subtitle', 'Kelola jenis pajak yang tersedia di sistem')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span> Jenis Pajak</div>
            <h1 class="page-title">Master Jenis Pajak</h1>
        </div>
        <button onclick="document.getElementById('modal-tambah').style.display='flex'" class="btn btn-primary">
            ➕ Tambah Jenis Pajak
        </button>
    </div>

    <div class="card">
        @if($jenisPajak->isEmpty())
            <div class="table-empty">
                <div class="empty-icon">🏷️</div>
                <p>Belum ada jenis pajak</p>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th><th>Kode</th><th>Nama Jenis Pajak</th><th>Deskripsi</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jenisPajak as $i => $jp)
                    <tr>
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td><span class="badge badge-menunggu" style="font-weight:800;">{{ $jp->kode }}</span></td>
                        <td style="font-weight:600;">{{ $jp->nama_jenis }}</td>
                        <td class="text-muted text-sm" style="max-width:300px;">{{ Str::limit($jp->deskripsi, 80) ?: '-' }}</td>
                        <td>
                            <span class="badge {{ $jp->is_aktif ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ $jp->is_aktif ? '✅ Aktif' : '❌ Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-warning btn-icon" title="Edit"
                                    onclick="openEditModal({{ $jp->id }}, '{{ $jp->kode }}', '{{ addslashes($jp->nama_jenis) }}', '{{ addslashes($jp->deskripsi) }}', {{ $jp->is_aktif ? 1 : 0 }})">✏️</button>
                                <form action="{{ route('jenis-pajak.destroy', $jp) }}" method="POST" onsubmit="return confirm('Hapus jenis pajak ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-icon" title="Hapus">🗑️</button>
                                </form>
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
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">➕ Tambah Jenis Pajak</div>
            <button class="modal-close" onclick="document.getElementById('modal-tambah').style.display='none'">✕</button>
        </div>
        <form action="{{ route('jenis-pajak.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Pajak *</label>
                    <input type="text" name="kode" class="form-control" placeholder="Contoh: PPN, PPH21" required maxlength="20" style="text-transform:uppercase;">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Jenis Pajak *</label>
                    <input type="text" name="nama_jenis" class="form-control" placeholder="Nama lengkap jenis pajak" required maxlength="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat tentang jenis pajak ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-tambah').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">➕ Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-backdrop" id="modal-edit" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">✏️ Edit Jenis Pajak</div>
            <button class="modal-close" onclick="document.getElementById('modal-edit').style.display='none'">✕</button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Pajak *</label>
                    <input type="text" name="kode" id="edit-kode" class="form-control" required maxlength="20">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Jenis Pajak *</label>
                    <input type="text" name="nama_jenis" id="edit-nama" class="form-control" required maxlength="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                    <input type="hidden" name="is_aktif" value="0">
                    <input type="checkbox" name="is_aktif" id="edit-aktif" value="1" style="width:18px;height:18px;accent-color:var(--accent);cursor:pointer;">
                    <label for="edit-aktif" style="font-size:14px;cursor:pointer;">Aktif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-edit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-warning">💾 Simpan</button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('modal-tambah').style.display='flex')</script>
@endif

<script>
function openEditModal(id, kode, nama, deskripsi, aktif) {
    document.getElementById('modal-edit').style.display = 'flex';
    document.getElementById('edit-form').action = '/jenis-pajak/' + id;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('edit-aktif').checked = aktif === 1;
}
</script>
@endsection
