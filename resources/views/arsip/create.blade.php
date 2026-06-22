@extends('layouts.app')

@section('title', 'Tambah Arsip Pajak')
@section('page-title', 'Tambah Arsip Pajak')
@section('page-subtitle', 'Buat dokumen arsip pajak baru')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span>
                <a href="{{ route('arsip.index') }}">Arsip Pajak</a> <span>/</span>
                Tambah Baru
            </div>
            <h1 class="page-title">Tambah Arsip Pajak</h1>
        </div>
        <a href="{{ route('arsip.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    <form action="{{ route('arsip.store') }}" method="POST" enctype="multipart/form-data" id="arsip-form">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

            {{-- Form Utama --}}
            <div class="card">
                <div class="form-section">
                    <div class="form-section-title">📋 Informasi Pajak</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="jenis_pajak_id">Jenis Pajak <span style="color:var(--red)">*</span></label>
                            <select name="jenis_pajak_id" id="jenis_pajak_id" class="form-control {{ $errors->has('jenis_pajak_id') ? 'is-invalid' : '' }}" required>
                                <option value="">-- Pilih Jenis Pajak --</option>
                                @foreach($jenisPajak as $jp)
                                    <option value="{{ $jp->id }}" {{ old('jenis_pajak_id') == $jp->id ? 'selected' : '' }}>
                                        [{{ $jp->kode }}] {{ $jp->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_pajak_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tahun_pajak">Tahun Pajak <span style="color:var(--red)">*</span></label>
                            <input type="number" name="tahun_pajak" id="tahun_pajak" class="form-control {{ $errors->has('tahun_pajak') ? 'is-invalid' : '' }}"
                                value="{{ old('tahun_pajak', date('Y')) }}" min="2000" max="{{ date('Y')+1 }}" required>
                            @error('tahun_pajak') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="periode">Periode <span style="color:var(--red)">*</span></label>
                            <select name="periode" id="periode" class="form-control {{ $errors->has('periode') ? 'is-invalid' : '' }}" required>
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periodeList as $p)
                                    <option value="{{ $p }}" {{ old('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('periode') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="jumlah_pajak">Jumlah Pajak (Rp) <span style="color:var(--red)">*</span></label>
                            <input type="number" name="jumlah_pajak" id="jumlah_pajak" class="form-control {{ $errors->has('jumlah_pajak') ? 'is-invalid' : '' }}"
                                value="{{ old('jumlah_pajak') }}" min="0" step="1" placeholder="0" required>
                            @error('jumlah_pajak') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="tanggal_setor">Tanggal Setor</label>
                            <input type="date" name="tanggal_setor" id="tanggal_setor" class="form-control"
                                value="{{ old('tanggal_setor') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nomor_bukti_setor">Nomor Bukti Setor</label>
                            <input type="text" name="nomor_bukti_setor" id="nomor_bukti_setor" class="form-control"
                                value="{{ old('nomor_bukti_setor') }}" placeholder="Nomor NTPN / bukti setor">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">📎 Dokumen Pendukung</div>
                    <div class="form-group">
                        <label class="form-label" for="file_dokumen">Upload Dokumen (PDF/Gambar, maks. 5MB)</label>
                        <div class="file-upload-area" id="file-drop-area" onclick="document.getElementById('file_dokumen').click()">
                            <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this)">
                            <div id="upload-placeholder">
                                <div class="upload-icon">📎</div>
                                <p>Klik atau seret file ke sini</p>
                                <p style="font-size:11px;margin-top:4px;">PDF, JPG, PNG — Maksimal 5MB</p>
                            </div>
                            <div id="file-name" style="display:none;color:var(--accent);font-weight:600;"></div>
                        </div>
                        @error('file_dokumen') <span class="invalid-feedback" style="display:block;margin-top:6px;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="card" style="background:linear-gradient(135deg,rgba(245,158,11,0.08),rgba(245,158,11,0.02));border-color:rgba(245,158,11,0.3);">
                    <div class="card-title" style="color:var(--accent);margin-bottom:14px;">💡 Petunjuk</div>
                    <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
                        <li style="font-size:12.5px;color:var(--text-secondary);display:flex;gap:8px;"><span>1️⃣</span> Pilih jenis dan periode pajak</li>
                        <li style="font-size:12.5px;color:var(--text-secondary);display:flex;gap:8px;"><span>2️⃣</span> Isi nominal pajak yang disetor</li>
                        <li style="font-size:12.5px;color:var(--text-secondary);display:flex;gap:8px;"><span>3️⃣</span> Upload bukti dokumen (opsional)</li>
                        <li style="font-size:12.5px;color:var(--text-secondary);display:flex;gap:8px;"><span>4️⃣</span> Simpan dulu sebagai draft</li>
                        <li style="font-size:12.5px;color:var(--text-secondary);display:flex;gap:8px;"><span>5️⃣</span> Kirim ke pimpinan untuk approval</li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom:14px;">📤 Simpan & Kirim</div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <button type="submit" form="arsip-form" class="btn btn-primary btn-block">
                            💾 Simpan sebagai Draft
                        </button>
                        <p style="font-size:11px;color:var(--text-muted);text-align:center;">
                            Setelah disimpan, Anda dapat mengirim untuk approval pimpinan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function showFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('upload-placeholder').style.display = 'none';
        const fn = document.getElementById('file-name');
        fn.style.display = 'block';
        fn.textContent = '✅ ' + input.files[0].name;
    }
}

// Drag & drop
const dropArea = document.getElementById('file-drop-area');
['dragenter','dragover'].forEach(e => dropArea.addEventListener(e, ev => { ev.preventDefault(); dropArea.classList.add('dragover'); }));
['dragleave','drop'].forEach(e => dropArea.addEventListener(e, ev => { ev.preventDefault(); dropArea.classList.remove('dragover'); }));
dropArea.addEventListener('drop', ev => {
    const files = ev.dataTransfer.files;
    if (files.length) { document.getElementById('file_dokumen').files = files; showFileName(document.getElementById('file_dokumen')); }
});

// Format angka rupiah saat input
document.getElementById('jumlah_pajak').addEventListener('input', function() {
    if (this.value < 0) this.value = 0;
});
</script>
@endsection
