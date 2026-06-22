@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')
@section('page-title', 'Dashboard Pimpinan')
@section('page-subtitle', 'Ringkasan Keseluruhan Sistem Arsip Pajak')

@section('content')
<div class="animate-in">

    {{-- Stats Grid --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="stat-card gold">
            <div class="stat-icon">📂</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_arsip'] }}</div>
                <div class="stat-label">Total Semua Arsip</div>
            </div>
        </div>
        <div class="stat-card" style="--stat-color:#f59e0b;">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <div class="stat-value" style="color:var(--yellow);">{{ $stats['menunggu'] }}</div>
                <div class="stat-label">Menunggu Approval</div>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['disetujui'] }}</div>
                <div class="stat-label">Telah Disetujui</div>
            </div>
        </div>
        <div class="stat-card red">
            <div class="stat-icon">❌</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['ditolak'] }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_staff'] }}</div>
                <div class="stat-label">Jumlah Staff</div>
            </div>
        </div>
        <div class="stat-card cyan">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:15px;">
                    Rp {{ number_format($stats['total_pajak'],0,',','.') }}
                </div>
                <div class="stat-label">Total Pajak Disetujui</div>
            </div>
        </div>
    </div>

    @if($stats['menunggu'] > 0)
    <div class="alert alert-warning animate-in-delay-1" style="margin-top:4px;">
        <span class="alert-icon">⚠️</span>
        <span>Ada <strong>{{ $stats['menunggu'] }} arsip</strong> yang menunggu persetujuan Anda.</span>
        <a href="{{ route('approval.index') }}" class="btn btn-warning btn-sm" style="margin-left:auto;">Review Sekarang →</a>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:4px;">

        {{-- Arsip Menunggu Approval --}}
        <div class="card animate-in-delay-2">
            <div class="card-header">
                <div class="card-title">
                    <div class="title-icon">⏳</div>
                    Menunggu Approval
                </div>
                <a href="{{ route('approval.index') }}" class="btn btn-warning btn-sm">Lihat Semua</a>
            </div>
            @if($arsip_menunggu->isEmpty())
                <div class="table-empty">
                    <div class="empty-icon">✅</div>
                    <p>Semua arsip sudah diproses</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($arsip_menunggu as $item)
                    <div style="background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.2);border-radius:10px;padding:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <div>
                            <div style="font-weight:700;font-size:13px;color:var(--accent);">{{ $item->nomor_arsip }}</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">
                                {{ $item->user->name }} · {{ $item->jenisPajak->nama_jenis ?? '-' }} · {{ $item->periode }} {{ $item->tahun_pajak }}
                            </div>
                            <div style="font-size:13px;font-weight:600;color:var(--text-primary);margin-top:4px;">
                                Rp {{ number_format($item->jumlah_pajak,0,',','.') }}
                            </div>
                        </div>
                        <a href="{{ route('approval.show', $item) }}" class="btn btn-info btn-sm" style="white-space:nowrap;">Review</a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Arsip Terbaru --}}
        <div class="card animate-in-delay-2">
            <div class="card-header">
                <div class="card-title">
                    <div class="title-icon">📋</div>
                    Aktivitas Terbaru
                </div>
                <a href="{{ route('arsip.index') }}" class="btn btn-secondary btn-sm">Semua</a>
            </div>
            @if($arsip_terbaru->isEmpty())
                <div class="table-empty">
                    <div class="empty-icon">📂</div>
                    <p>Belum ada arsip</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($arsip_terbaru as $item)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(30,58,95,0.5);">
                        <div>
                            <div style="font-size:13px;font-weight:600;">{{ $item->nomor_arsip }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $item->user->name }} · {{ $item->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="badge {{ $item->status_badge }}">{{ $item->status_label }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions Pimpinan --}}
    <div class="card animate-in-delay-3" style="margin-top:20px;">
        <div class="card-title" style="margin-bottom:16px;"><div class="title-icon">⚡</div> Aksi Cepat</div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('approval.index') }}" class="btn btn-warning">⏳ Approval Arsip</a>
            <a href="{{ route('arsip.index') }}" class="btn btn-secondary">📂 Semua Arsip</a>
            <a href="{{ route('laporan.index') }}" class="btn btn-info">📈 Laporan Pajak</a>
            <a href="{{ route('jenis-pajak.index') }}" class="btn btn-secondary">🏷️ Jenis Pajak</a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">👥 Kelola User</a>
        </div>
    </div>
</div>
@endsection
