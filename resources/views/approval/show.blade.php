@extends('layouts.app')

@section('title', 'Review Arsip — ' . $arsip->nomor_arsip)
@section('page-title', 'Review Arsip')
@section('page-subtitle', 'Detail arsip untuk persetujuan')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('approval.index') }}">Approval</a> <span>/</span> {{ $arsip->nomor_arsip }}</div>
            <h1 class="page-title">Review Arsip</h1>
        </div>
        <a href="{{ route('approval.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
        {{-- Detail Arsip --}}
        <div style="display:flex;flex-direction:column;gap:20px;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="title-icon">📋</div> Detail Arsip Pajak</div>
                    <span class="badge {{ $arsip->status_badge }}" style="font-size:13px;padding:6px 14px;">{{ $arsip->status_label }}</span>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nomor Arsip</div>
                        <div class="info-value" style="color:var(--accent);font-weight:800;font-size:18px;">{{ $arsip->nomor_arsip }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Staff Pengaju</div>
                        <div class="info-value" style="font-weight:700;">{{ $arsip->user->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">NIP</div>
                        <div class="info-value">{{ $arsip->user->nip ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jabatan</div>
                        <div class="info-value">{{ $arsip->user->jabatan ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jenis Pajak</div>
                        <div class="info-value">{{ $arsip->jenisPajak->nama_jenis ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Kode Pajak</div>
                        <div class="info-value"><span class="badge badge-menunggu">{{ $arsip->jenisPajak->kode ?? '-' }}</span></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tahun Pajak</div>
                        <div class="info-value">{{ $arsip->tahun_pajak }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Periode</div>
                        <div class="info-value">{{ $arsip->periode }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-label">Jumlah Pajak</div>
                        <div class="info-value" style="font-size:28px;font-weight:900;color:var(--accent);">
                            Rp {{ number_format($arsip->jumlah_pajak,0,',','.') }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Setor</div>
                        <div class="info-value">{{ $arsip->tanggal_setor ? $arsip->tanggal_setor->format('d F Y') : '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nomor Bukti Setor</div>
                        <div class="info-value">{{ $arsip->nomor_bukti_setor ?: '-' }}</div>
                    </div>
                    @if($arsip->keterangan)
                    <div class="info-item full">
                        <div class="info-label">Keterangan</div>
                        <div class="info-value">{{ $arsip->keterangan }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($arsip->file_dokumen)
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;"><div class="title-icon">📎</div> Dokumen Pendukung</div>
                <div style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:10px;">
                    <div style="font-size:32px;">📄</div>
                    <div>
                        <div style="font-weight:600;">Dokumen Arsip Pajak</div>
                        <div class="text-muted text-sm">Klik untuk membuka dan memverifikasi</div>
                    </div>
                    <a href="{{ Storage::url($arsip->file_dokumen) }}" target="_blank" class="btn btn-info" style="margin-left:auto;">
                        👁️ Buka Dokumen
                    </a>
                </div>
            </div>
            @endif
        </div>

        {{-- Action Panel --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            @if($arsip->status === 'menunggu')
            {{-- Approve --}}
            <div class="card" style="border-color:rgba(16,185,129,0.3);">
                <div class="card-title" style="color:var(--green);margin-bottom:14px;">✅ Setujui Arsip</div>
                <form action="{{ route('approval.approve', $arsip) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan_pimpinan" class="form-control" rows="3" placeholder="Catatan persetujuan..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Setujui arsip ini?')">
                        ✅ Setujui Arsip Ini
                    </button>
                </form>
            </div>

            {{-- Reject --}}
            <div class="card" style="border-color:rgba(239,68,68,0.3);">
                <div class="card-title" style="color:var(--red);margin-bottom:14px;">❌ Tolak Arsip</div>
                <form action="{{ route('approval.reject', $arsip) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Alasan Penolakan <span style="color:var(--red)">*</span></label>
                        <textarea name="catatan_pimpinan" class="form-control" rows="4" placeholder="Tuliskan alasan penolakan yang jelas..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Tolak arsip ini?')">
                        ❌ Tolak Arsip Ini
                    </button>
                </form>
            </div>
            @else
            <div class="card">
                <div class="card-title" style="margin-bottom:12px;">📋 Status Akhir</div>
                <span class="badge {{ $arsip->status_badge }}" style="font-size:14px;padding:8px 16px;">{{ $arsip->status_label }}</span>
                @if($arsip->catatan_pimpinan)
                <div style="margin-top:12px;font-size:13px;color:var(--text-secondary);">{{ $arsip->catatan_pimpinan }}</div>
                @endif
            </div>
            @endif

            <div class="card" style="background:rgba(245,158,11,0.05);border-color:rgba(245,158,11,0.2);">
                <div class="card-title" style="color:var(--accent);margin-bottom:12px;">ℹ️ Info</div>
                <p style="font-size:12px;color:var(--text-muted);line-height:1.8;">
                    Pastikan Anda telah memeriksa:<br>
                    ✔ Kesesuaian jenis pajak<br>
                    ✔ Kebenaran jumlah nominal<br>
                    ✔ Dokumen pendukung valid<br>
                    ✔ Periode pajak yang benar
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
