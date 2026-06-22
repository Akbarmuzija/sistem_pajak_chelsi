@extends('layouts.app')

@section('title', 'Dashboard Staff')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
<div class="animate-in">

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card gold animate-in-delay-1">
            <div class="stat-icon">📂</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_arsip'] }}</div>
                <div class="stat-label">Total Arsip</div>
            </div>
        </div>
        <div class="stat-card blue animate-in-delay-1">
            <div class="stat-icon">📝</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['draft'] }}</div>
                <div class="stat-label">Draft</div>
            </div>
        </div>
        <div class="stat-card" style="--stat-color:#f59e0b;" class="animate-in-delay-2">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['menunggu'] }}</div>
                <div class="stat-label">Menunggu Approval</div>
            </div>
        </div>
        <div class="stat-card green animate-in-delay-2">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['disetujui'] }}</div>
                <div class="stat-label">Disetujui</div>
            </div>
        </div>
        <div class="stat-card red animate-in-delay-3">
            <div class="stat-icon">❌</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['ditolak'] }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
        <div class="stat-card cyan animate-in-delay-3">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:16px;" data-value="{{ $stats['total_pajak'] }}">
                    Rp {{ number_format($stats['total_pajak'],0,',','.') }}
                </div>
                <div class="stat-label">Total Pajak Disetujui</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:4px;">

        {{-- Arsip Terbaru --}}
        <div class="card animate-in-delay-2">
            <div class="card-header">
                <div class="card-title">
                    <div class="title-icon">📋</div>
                    Arsip Terbaru Saya
                </div>
                <a href="{{ route('arsip.create') }}" class="btn btn-primary btn-sm">
                    ➕ Tambah Arsip
                </a>
            </div>
            @if($arsip_terbaru->isEmpty())
                <div class="table-empty">
                    <div class="empty-icon">📂</div>
                    <p>Belum ada arsip pajak</p>
                    <a href="{{ route('arsip.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px;">Buat Arsip Pertama</a>
                </div>
            @else
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nomor Arsip</th>
                                <th>Jenis Pajak</th>
                                <th>Periode</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arsip_terbaru as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('arsip.show', $item) }}" style="color:var(--accent);font-weight:600;">
                                        {{ $item->nomor_arsip }}
                                    </a>
                                </td>
                                <td>{{ $item->jenisPajak->nama_jenis ?? '-' }}</td>
                                <td>{{ $item->periode }} {{ $item->tahun_pajak }}</td>
                                <td class="currency" data-value="{{ $item->jumlah_pajak }}">
                                    Rp {{ number_format($item->jumlah_pajak,0,',','.') }}
                                </td>
                                <td>
                                    <span class="badge {{ $item->status_badge }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:14px;text-align:right;">
                    <a href="{{ route('arsip.index') }}" class="btn btn-secondary btn-sm">Lihat Semua →</a>
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card animate-in-delay-2">
                <div class="card-title" style="margin-bottom:16px;">
                    <div class="title-icon">⚡</div>
                    Aksi Cepat
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route('arsip.create') }}" class="btn btn-primary btn-block">
                        ➕ Buat Arsip Baru
                    </a>
                    <a href="{{ route('arsip.index') }}?status=draft" class="btn btn-secondary btn-block">
                        📝 Lihat Draft
                    </a>
                    <a href="{{ route('arsip.index') }}?status=ditolak" class="btn btn-secondary btn-block">
                        ❌ Arsip Ditolak
                    </a>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-block">
                        📈 Lihat Laporan
                    </a>
                </div>
            </div>

            <div class="card animate-in-delay-3" style="background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(245,158,11,0.03));border-color:rgba(245,158,11,0.3);">
                <div class="card-title" style="color:var(--accent);margin-bottom:12px;">💡 Info</div>
                <div style="font-size:12px;color:var(--text-secondary);line-height:1.8;">
                    <div>📌 Buat arsip → kirim ke pimpinan</div>
                    <div>📌 Arsip ditolak bisa diedit kembali</div>
                    <div>📌 Arsip disetujui masuk laporan</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
