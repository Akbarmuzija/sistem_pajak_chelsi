@extends('layouts.app')

@section('title', 'Edit Arsip Pajak')
@section('page-title', 'Edit Arsip Pajak')
@section('page-subtitle', 'Perbarui data arsip pajak')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb">
                <a href="{{ route('arsip.index') }}">Arsip Pajak</a> <span>/</span>
                <a href="{{ route('arsip.show', $arsip) }}">{{ $arsip->nomor_arsip }}</a> <span>/</span> Edit
            </div>
            <h1 class="page-title">Edit Arsip</h1>
        </div>
        <a href="{{ route('arsip.show', $arsip) }}" class="btn btn-secondary">← Kembali</a>
    </div>

    @if($arsip->status === 'ditolak' && $arsip->catatan_pimpinan)
    <div class="alert alert-error" style="margin-bottom:20px;">
        <span class="alert-icon">❌</span>
        <div>
            <strong>Arsip Ditolak</strong> — Catatan Pimpinan:<br>
            {{ $arsip->catatan_pimpinan }}
        </div>
    </div>
    @endif

    <form action="{{ route('arsip.update', $arsip) }}" method="POST" enctype="multipart/form-data" id="arsip-form">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
            <div class="card">
                <div class="form-section">
                    <div class="form-section-title">📋 Informasi Pajak</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nomor Arsip</label>
                            <input type="text" class="form-control" value="{{ $arsip->nomor_arsip }}" disabled style="opacity:0.5;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="jenis_pajak_id">Jenis Pajak *</label>
                            <select name="jenis_pajak_id" id="jenis_pajak_id" class="form-control" required>
                                @foreach($jenisPajak as $jp)
                                    <option value="{{ $jp->id }}" {{ $arsip->jenis_pajak_id == $jp->id ? 'selected' : '' }}>
                                        [{{ $jp->kode }}] {{ $jp->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="tahun_pajak">Tahun Pajak *</label>
                            <input type="number" name="tahun_pajak" id="tahun_pajak" class="form-control"
                                value="{{ old('tahun_pajak', $arsip->tahun_pajak) }}" min="2000" max="{{ date('Y')+1 }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="periode">Periode *</label>
                            <select name="periode" id="periode" class="form-control" required>
                                @foreach($periodeList as $p)
                                    <option value="{{ $p }}" {{ $arsip->periode == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="jumlah_pajak">Jumlah Pajak (Rp) *</label>
                            <input type="number" name="jumlah_pajak" id="jumlah_pajak" class="form-control"
                                value="{{ old('jumlah_pajak', $arsip->jumlah_pajak) }}" min="0" step="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tanggal_setor">Tanggal Setor</label>
                            <input type="date" name="tanggal_setor" id="tanggal_setor" class="form-control"
                                value="{{ old('tanggal_setor', $arsip->tanggal_setor?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nomor_bukti_setor">Nomor Bukti Setor</label>
                        <input type="text" name="nomor_bukti_setor" id="nomor_bukti_setor" class="form-control"
                            value="{{ old('nomor_bukti_setor', $arsip->nomor_bukti_setor) }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="nomor_ktp">NIK KTP (16 digit)</label>
                            <input type="text" name="nomor_ktp" id="nomor_ktp" class="form-control {{ $errors->has('nomor_ktp') ? 'is-invalid' : '' }}"
                                value="{{ old('nomor_ktp', $arsip->nomor_ktp) }}" maxlength="16" placeholder="Masukkan NIK KTP">
                            @error('nomor_ktp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nomor_npwp">Nomor NPWP</label>
                            <input type="text" name="nomor_npwp" id="nomor_npwp" class="form-control {{ $errors->has('nomor_npwp') ? 'is-invalid' : '' }}"
                                value="{{ old('nomor_npwp', $arsip->nomor_npwp) }}" maxlength="20" placeholder="Masukkan Nomor NPWP">
                            @error('nomor_npwp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control">{{ old('keterangan', $arsip->keterangan) }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">📎 Dokumen</div>
                    @if($arsip->file_dokumen)
                    <div style="margin-bottom:12px;padding:12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:8px;display:flex;align-items:center;gap:10px;">
                        <span>📄</span>
                        <span style="font-size:13px;color:var(--green);">Dokumen Utama sudah ada</span>
                        <a href="{{ Storage::url($arsip->file_dokumen) }}" target="_blank" class="btn btn-secondary btn-sm" style="margin-left:auto;">Lihat</a>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="form-label">Upload Dokumen Utama Baru (opsional - akan mengganti yang lama)</label>
                        <div class="file-upload-area" onclick="document.getElementById('file_dokumen').click()">
                            <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this)">
                            <div id="upload-placeholder">
                                <div class="upload-icon">📎</div>
                                <p>Klik untuk upload dokumen baru</p>
                            </div>
                            <div id="file-name" style="display:none;color:var(--accent);font-weight:600;"></div>
                        </div>
                    </div>

                    {{-- Scan KTP --}}
                    <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:15px;">
                        @if($arsip->file_ktp)
                        <div style="margin-bottom:12px;padding:12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:8px;display:flex;align-items:center;gap:10px;">
                            <span>📄</span>
                            <span style="font-size:13px;color:var(--green);">Scan KTP sudah ada</span>
                            <a href="{{ Storage::url($arsip->file_ktp) }}" target="_blank" class="btn btn-secondary btn-sm" style="margin-left:auto;">Lihat</a>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label" for="file_ktp">Upload Scan KTP Baru (opsional)</label>
                            <input type="file" name="file_ktp" id="file_ktp" class="form-control {{ $errors->has('file_ktp') ? 'is-invalid' : '' }}" accept=".pdf,.jpg,.jpeg,.png">
                            @error('file_ktp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Scan NPWP --}}
                    <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:15px;">
                        @if($arsip->file_npwp)
                        <div style="margin-bottom:12px;padding:12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:8px;display:flex;align-items:center;gap:10px;">
                            <span>📄</span>
                            <span style="font-size:13px;color:var(--green);">Scan NPWP sudah ada</span>
                            <a href="{{ Storage::url($arsip->file_npwp) }}" target="_blank" class="btn btn-secondary btn-sm" style="margin-left:auto;">Lihat</a>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label" for="file_npwp">Upload Scan NPWP Baru (opsional)</label>
                            <input type="file" name="file_npwp" id="file_npwp" class="form-control {{ $errors->has('file_npwp') ? 'is-invalid' : '' }}" accept=".pdf,.jpg,.jpeg,.png">
                            @error('file_npwp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;">
                <div class="card">
                    <div class="card-title" style="margin-bottom:14px;">💾 Simpan</div>
                    <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                    <p style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:8px;">Status kembali ke Draft setelah edit</p>
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
</script>
@endsection
