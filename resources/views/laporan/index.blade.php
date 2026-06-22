@extends('layouts.app')

@section('title', 'Laporan Pajak')
@section('page-title', 'Laporan Pajak')
@section('page-subtitle', 'Rekapitulasi dan analisis pajak')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span> Laporan</div>
            <h1 class="page-title">Laporan Pajak</h1>
        </div>
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Cetak Laporan</button>
    </div>

    {{-- Filter --}}
    <div class="card" style="padding:16px;margin-bottom:20px;">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;">
                <label class="form-label">Tahun Pajak</label>
                <select name="tahun" class="form-control" style="max-width:130px;">
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ $tahun==$thn ? 'selected':'' }}>{{ $thn }}</option>
                    @endforeach
                    @if($tahunList->isEmpty())
                        <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                    @endif
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Jenis Pajak</label>
                <select name="jenis" class="form-control" style="max-width:220px;">
                    <option value="">Semua Jenis Pajak</option>
                    @foreach($jenisPajak as $jp)
                        <option value="{{ $jp->id }}" {{ $jenis==$jp->id ? 'selected':'' }}>{{ $jp->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">📊 Tampilkan</button>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">↺ Reset</a>
        </form>
    </div>

    {{-- Summary Card --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
        <div class="stat-card cyan">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">Rp {{ number_format($total_pajak,0,',','.') }}</div>
                <div class="stat-label">Total Pajak Tahun {{ $tahun }}</div>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">📂</div>
            <div class="stat-info">
                <div class="stat-value">{{ $arsip->count() }}</div>
                <div class="stat-label">Jumlah Arsip Disetujui</div>
            </div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">
                    Rp {{ $arsip->count() > 0 ? number_format($total_pajak / $arsip->count(),0,',','.') : '0' }}
                </div>
                <div class="stat-label">Rata-rata per Arsip</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        {{-- Rekap per Jenis Pajak --}}
        <div class="card animate-in-delay-1">
            <div class="card-header">
                <div class="card-title"><div class="title-icon">🏷️</div> Rekap per Jenis Pajak</div>
            </div>
            @if($rekap_jenis->isEmpty())
                <div class="table-empty"><div class="empty-icon">📊</div><p>Tidak ada data</p></div>
            @else
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($rekap_jenis as $rj)
                @php $pct = $total_pajak > 0 ? ($rj->total / $total_pajak * 100) : 0; @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:13px;font-weight:600;">{{ $rj->jenisPajak->nama_jenis ?? '-' }}</span>
                        <span style="font-size:13px;color:var(--accent);font-weight:700;">Rp {{ number_format($rj->total,0,',','.') }}</span>
                    </div>
                    <div style="background:rgba(255,255,255,0.06);border-radius:4px;height:8px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,var(--accent),var(--accent-light));border-radius:4px;transition:width 1s ease;"></div>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">{{ $rj->jumlah }} arsip · {{ number_format($pct,1) }}%</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Rekap per Periode --}}
        <div class="card animate-in-delay-1">
            <div class="card-header">
                <div class="card-title"><div class="title-icon">📅</div> Rekap per Periode</div>
            </div>
            @if($rekap_bulan->isEmpty())
                <div class="table-empty"><div class="empty-icon">📅</div><p>Tidak ada data</p></div>
            @else
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Periode</th><th>Jumlah Arsip</th><th>Total Pajak</th></tr></thead>
                    <tbody>
                        @foreach($rekap_bulan as $rb)
                        <tr>
                            <td style="font-weight:600;">{{ $rb->periode }}</td>
                            <td><span class="badge badge-disetujui">{{ $rb->jumlah }} arsip</span></td>
                            <td style="font-weight:700;color:var(--accent);">Rp {{ number_format($rb->total,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="card animate-in-delay-2">
        <div class="card-header">
            <div class="card-title"><div class="title-icon">📋</div> Detail Arsip Pajak Tahun {{ $tahun }}</div>
            <span class="badge badge-disetujui">{{ $arsip->count() }} arsip</span>
        </div>
        @if($arsip->isEmpty())
            <div class="table-empty">
                <div class="empty-icon">📊</div>
                <p>Tidak ada arsip pajak yang disetujui untuk tahun {{ $tahun }}</p>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Arsip</th>
                        @if(auth()->user()->isPimpinan()) <th>Staff</th> @endif
                        <th>Jenis Pajak</th>
                        <th>Periode</th>
                        <th>Tgl Setor</th>
                        <th>Jumlah Pajak</th>
                        <th>Tgl Disetujui</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($arsip as $i => $item)
                    <tr>
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td>
                            <a href="{{ route('arsip.show', $item) }}" style="color:var(--accent);font-weight:600;">{{ $item->nomor_arsip }}</a>
                        </td>
                        @if(auth()->user()->isPimpinan())
                        <td>{{ $item->user->name }}</td>
                        @endif
                        <td>{{ $item->jenisPajak->nama_jenis ?? '-' }}</td>
                        <td>{{ $item->periode }}</td>
                        <td class="text-muted text-sm">{{ $item->tanggal_setor?->format('d/m/Y') ?? '-' }}</td>
                        <td style="font-weight:700;color:var(--accent);">Rp {{ number_format($item->jumlah_pajak,0,',','.') }}</td>
                        <td class="text-muted text-sm">{{ $item->tanggal_disetujui?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:rgba(245,158,11,0.08);border-top:2px solid var(--accent);">
                        <td colspan="{{ auth()->user()->isPimpinan() ? 6 : 5 }}" style="text-align:right;font-weight:700;padding:14px 16px;">TOTAL</td>
                        <td style="font-weight:900;font-size:16px;color:var(--accent);padding:14px 16px;">
                            Rp {{ number_format($total_pajak,0,',','.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .filter-bar, form, .btn, .breadcrumb { display:none !important; }
    .main-content { margin-left:0 !important; }
    .page-content { padding:0 !important; }
    body { background:#fff !important; color:#000 !important; }
    .card { border:1px solid #ccc !important; background:#fff !important; }
}
</style>
@endsection
