@extends('layouts.app')

@section('title', 'Arsip Pajak')
@section('page-title', 'Arsip Pajak')
@section('page-subtitle', 'Daftar seluruh dokumen arsip pajak')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span> Arsip Pajak</div>
            <h1 class="page-title">Arsip Pajak</h1>
            <p class="page-subtitle">{{ $arsip->total() }} total arsip ditemukan</p>
        </div>
        @if(auth()->user()->isStaff())
        <a href="{{ route('arsip.create') }}" class="btn btn-primary">➕ Buat Arsip Baru</a>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="card" style="padding:16px;margin-bottom:20px;">
        <form method="GET" action="{{ route('arsip.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="search-input" style="flex:1;min-width:200px;">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="form-control" placeholder="Cari nomor arsip..." value="{{ request('search') }}">
            </div>
            <select name="status" class="form-control" style="max-width:180px;">
                <option value="">Semua Status</option>
                <option value="draft"     {{ request('status')=='draft'     ? 'selected':'' }}>Draft</option>
                <option value="menunggu"  {{ request('status')=='menunggu'  ? 'selected':'' }}>Menunggu</option>
                <option value="disetujui" {{ request('status')=='disetujui' ? 'selected':'' }}>Disetujui</option>
                <option value="ditolak"   {{ request('status')=='ditolak'   ? 'selected':'' }}>Ditolak</option>
            </select>
            <select name="jenis" class="form-control" style="max-width:200px;">
                <option value="">Semua Jenis Pajak</option>
                @foreach($jenisPajak as $jp)
                    <option value="{{ $jp->id }}" {{ request('jenis')==$jp->id ? 'selected':'' }}>{{ $jp->nama_jenis }}</option>
                @endforeach
            </select>
            <select name="tahun" class="form-control" style="max-width:120px;">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ request('tahun')==$thn ? 'selected':'' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="{{ route('arsip.index') }}" class="btn btn-secondary">↺ Reset</a>
        </form>
    </div>

    <div class="card">
        @if($arsip->isEmpty())
            <div class="table-empty">
                <div class="empty-icon">📂</div>
                <p>Belum ada arsip pajak ditemukan</p>
                @if(auth()->user()->isStaff())
                    <a href="{{ route('arsip.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px;">Buat Arsip Pertama</a>
                @endif
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
                        <th>Jumlah Pajak</th>
                        <th>Tgl Setor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($arsip as $i => $item)
                    <tr>
                        <td class="text-muted">{{ $arsip->firstItem() + $i }}</td>
                        <td>
                            <a href="{{ route('arsip.show', $item) }}" style="color:var(--accent);font-weight:700;">
                                {{ $item->nomor_arsip }}
                            </a>
                        </td>
                        @if(auth()->user()->isPimpinan())
                        <td>
                            <div style="font-weight:600;">{{ $item->user->name }}</div>
                            <div class="text-muted text-xs">{{ $item->user->nip }}</div>
                        </td>
                        @endif
                        <td>
                            <div style="font-weight:500;">{{ $item->jenisPajak->nama_jenis ?? '-' }}</div>
                            <div class="text-muted text-xs">{{ $item->jenisPajak->kode ?? '' }}</div>
                        </td>
                        <td>{{ $item->periode }}<br><span class="text-muted text-xs">{{ $item->tahun_pajak }}</span></td>
                        <td class="currency fw-semibold">Rp {{ number_format($item->jumlah_pajak,0,',','.') }}</td>
                        <td class="text-muted text-sm">{{ $item->tanggal_setor ? $item->tanggal_setor->format('d/m/Y') : '-' }}</td>
                        <td><span class="badge {{ $item->status_badge }}">{{ $item->status_label }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('arsip.show', $item) }}" class="btn btn-info btn-icon" title="Detail">👁️</a>
                                @if(auth()->user()->isStaff() && in_array($item->status,['draft','ditolak']))
                                    <a href="{{ route('arsip.edit', $item) }}" class="btn btn-warning btn-icon" title="Edit">✏️</a>
                                    @if($item->status === 'draft')
                                    <form action="{{ route('arsip.kirim', $item) }}" method="POST" onsubmit="return confirm('Kirim arsip ini untuk approval pimpinan?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-icon" title="Kirim Approval">📤</button>
                                    </form>
                                    @endif
                                    <form action="{{ route('arsip.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon" title="Hapus">🗑️</button>
                                    </form>
                                @endif
                                @if(auth()->user()->isPimpinan() && $item->status === 'menunggu')
                                    <a href="{{ route('approval.show', $item) }}" class="btn btn-success btn-sm">Review</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16">
            {{ $arsip->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
