@extends('layouts.app')

@section('title', 'Detail Arsip — ' . $arsip->nomor_arsip)
@section('page-title', 'Detail Arsip Pajak')
@section('page-subtitle', $arsip->nomor_arsip)

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb">
                <a href="{{ route('arsip.index') }}">Arsip Pajak</a> <span>/</span> {{ $arsip->nomor_arsip }}
            </div>
            <h1 class="page-title">{{ $arsip->nomor_arsip }}</h1>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('arsip.index') }}" class="btn btn-secondary">← Kembali</a>
            @if(auth()->user()->isStaff() && in_array($arsip->status, ['draft','ditolak']))
                <a href="{{ route('arsip.edit', $arsip) }}" class="btn btn-warning">✏️ Edit</a>
            @endif
            @if(auth()->user()->isStaff() && $arsip->status === 'draft')
                <form action="{{ route('arsip.kirim', $arsip) }}" method="POST" onsubmit="return confirm('Kirim arsip ini untuk approval pimpinan?')">
                    @csrf
                    <button type="submit" class="btn btn-success">📤 Kirim untuk Approval</button>
                </form>
            @endif
            @if(auth()->user()->isPimpinan() && $arsip->status === 'menunggu')
                <a href="{{ route('approval.show', $arsip) }}" class="btn btn-primary">✅ Review & Approve</a>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

        {{-- Detail Utama --}}
        <div style="display:flex;flex-direction:column;gap:20px;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><div class="title-icon">📋</div> Informasi Arsip</div>
                    <span class="badge {{ $arsip->status_badge }}" style="font-size:13px;padding:6px 14px;">{{ $arsip->status_label }}</span>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nomor Arsip</div>
                        <div class="info-value" style="color:var(--accent);font-weight:700;font-size:16px;">{{ $arsip->nomor_arsip }}</div>
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
                    <div class="info-item">
                        <div class="info-label">Jumlah Pajak</div>
                        <div class="info-value" style="font-size:20px;font-weight:800;color:var(--accent);">
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
                    <div class="info-item">
                        <div class="info-label">NIK KTP</div>
                        <div class="info-value">{{ $arsip->nomor_ktp ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nomor NPWP</div>
                        <div class="info-value">{{ $arsip->nomor_npwp ?: '-' }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-label">Keterangan</div>
                        <div class="info-value">{{ $arsip->keterangan ?: '-' }}</div>
                    </div>
                </div>
            </div>

            @if($arsip->file_dokumen || $arsip->file_ktp || $arsip->file_npwp)
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;"><div class="title-icon">📎</div> Dokumen Pendukung</div>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @if($arsip->file_dokumen)
                    <div style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:10px;">
                        <div style="font-size:32px;">📄</div>
                        <div>
                            <div style="font-weight:600;">Dokumen Utama Arsip</div>
                            <div class="text-muted text-sm">{{ basename($arsip->file_dokumen) }}</div>
                        </div>
                        <a href="{{ Storage::url($arsip->file_dokumen) }}" target="_blank" class="btn btn-info btn-sm" style="margin-left:auto;">
                            👁️ Lihat Dokumen
                        </a>
                    </div>
                    @endif

                    @if($arsip->file_ktp)
                    <div style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:10px;">
                        <div style="font-size:32px;">🪪</div>
                        <div>
                            <div style="font-weight:600;">Scan KTP</div>
                            <div class="text-muted text-sm">{{ basename($arsip->file_ktp) }}</div>
                        </div>
                        <a href="{{ Storage::url($arsip->file_ktp) }}" target="_blank" class="btn btn-info btn-sm" style="margin-left:auto;">
                            👁️ Lihat KTP
                        </a>
                    </div>
                    @endif

                    @if($arsip->file_npwp)
                    <div style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:10px;">
                        <div style="font-size:32px;">💳</div>
                        <div>
                            <div style="font-weight:600;">Scan NPWP</div>
                            <div class="text-muted text-sm">{{ basename($arsip->file_npwp) }}</div>
                        </div>
                        <a href="{{ Storage::url($arsip->file_npwp) }}" target="_blank" class="btn btn-info btn-sm" style="margin-left:auto;">
                            👁️ Lihat NPWP
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($arsip->catatan_pimpinan)
            <div class="card" style="border-color:{{ $arsip->status === 'disetujui' ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }};">
                <div class="card-title" style="margin-bottom:12px;color:{{ $arsip->status === 'disetujui' ? 'var(--green)' : 'var(--red)' }};">
                    <div class="title-icon">{{ $arsip->status === 'disetujui' ? '✅' : '❌' }}</div>
                    Catatan Pimpinan
                </div>
                <p style="font-size:14px;color:var(--text-secondary);line-height:1.7;">{{ $arsip->catatan_pimpinan }}</p>
                @if($arsip->approvedBy)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);font-size:12px;color:var(--text-muted);">
                    Oleh: <strong style="color:var(--text-primary);">{{ $arsip->approvedBy->name }}</strong>
                    · {{ $arsip->tanggal_disetujui?->format('d F Y H:i') }}
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;"><div class="title-icon">👤</div> Informasi Staff</div>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <div class="user-avatar" style="width:44px;height:44px;font-size:16px;">
                        {{ strtoupper(substr($arsip->user->name,0,1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;">{{ $arsip->user->name }}</div>
                        <div class="text-muted text-xs">{{ $arsip->user->nip }}</div>
                        <div class="text-muted text-xs">{{ $arsip->user->jabatan }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-title" style="margin-bottom:14px;"><div class="title-icon">📅</div> Riwayat</div>
                <ul class="timeline">
                    <li class="timeline-item done">
                        <div class="timeline-text">Arsip Dibuat</div>
                        <div class="timeline-date">{{ $arsip->created_at->format('d M Y H:i') }}</div>
                    </li>
                    @if(in_array($arsip->status, ['menunggu','disetujui','ditolak']))
                    <li class="timeline-item {{ in_array($arsip->status,['disetujui','ditolak']) ? 'done' : 'active' }}">
                        <div class="timeline-text">Dikirim untuk Approval</div>
                        <div class="timeline-date">{{ $arsip->updated_at->format('d M Y H:i') }}</div>
                    </li>
                    @endif
                    @if($arsip->status === 'disetujui')
                    <li class="timeline-item done">
                        <div class="timeline-text" style="color:var(--green);">✅ Disetujui</div>
                        <div class="timeline-date">{{ $arsip->tanggal_disetujui?->format('d M Y H:i') }}</div>
                    </li>
                    @elseif($arsip->status === 'ditolak')
                    <li class="timeline-item rejected">
                        <div class="timeline-text" style="color:var(--red);">❌ Ditolak</div>
                        <div class="timeline-date">{{ $arsip->tanggal_disetujui?->format('d M Y H:i') }}</div>
                    </li>
                    @endif
                </ul>
            </div>

            @if(auth()->user()->isStaff() && in_array($arsip->status, ['draft','ditolak']))
            <div class="card" style="background:rgba(239,68,68,0.05);border-color:rgba(239,68,68,0.2);">
                <div class="card-title" style="color:var(--red);margin-bottom:12px;">🗑️ Hapus Arsip</div>
                <form action="{{ route('arsip.destroy', $arsip) }}" method="POST" onsubmit="return confirm('Yakin hapus arsip ini? Tidak bisa dikembalikan!')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block btn-sm">Hapus Arsip Ini</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
