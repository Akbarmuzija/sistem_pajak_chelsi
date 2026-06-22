@extends('layouts.app')

@section('title', 'Approval Arsip')
@section('page-title', 'Approval Arsip Pajak')
@section('page-subtitle', 'Review dan setujui arsip yang dikirimkan staff')

@section('content')
<div class="animate-in">
    <div class="page-header">
        <div class="page-header-left">
            <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> <span>/</span> Approval</div>
            <h1 class="page-title">Approval Arsip</h1>
        </div>
    </div>

    {{-- Menunggu Approval --}}
    <div class="card mb-24">
        <div class="card-header">
            <div class="card-title">
                <div class="title-icon">⏳</div>
                Menunggu Persetujuan
                @if($arsip->total() > 0)
                    <span class="badge badge-menunggu" style="margin-left:4px;">{{ $arsip->total() }}</span>
                @endif
            </div>
            <form method="GET" style="display:flex;gap:8px;">
                <div class="search-input">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" class="form-control" placeholder="Cari arsip atau staff..." value="{{ request('search') }}" style="max-width:220px;">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>

        @if($arsip->isEmpty())
            <div class="table-empty">
                <div class="empty-icon">✅</div>
                <p>Tidak ada arsip yang menunggu persetujuan</p>
                <p class="text-muted text-sm" style="margin-top:6px;">Semua arsip sudah diproses!</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($arsip as $item)
                <div style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.2);border-radius:12px;padding:18px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                            <span style="font-weight:800;font-size:15px;color:var(--accent);">{{ $item->nomor_arsip }}</span>
                            <span class="badge badge-menunggu">⏳ Menunggu</span>
                        </div>
                        <div style="display:flex;gap:20px;flex-wrap:wrap;">
                            <div>
                                <div class="text-muted text-xs">Staff</div>
                                <div style="font-weight:600;font-size:13px;">{{ $item->user->name }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-xs">Jenis Pajak</div>
                                <div style="font-weight:600;font-size:13px;">{{ $item->jenisPajak->nama_jenis ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-xs">Periode</div>
                                <div style="font-weight:600;font-size:13px;">{{ $item->periode }} {{ $item->tahun_pajak }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-xs">Jumlah Pajak</div>
                                <div style="font-weight:800;font-size:16px;color:var(--accent-light);">Rp {{ number_format($item->jumlah_pajak,0,',','.') }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-xs">Dikirim</div>
                                <div style="font-size:12px;color:var(--text-secondary);">{{ $item->updated_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-shrink:0;">
                        <a href="{{ route('approval.show', $item) }}" class="btn btn-primary">📋 Review</a>

                        {{-- Quick Approve --}}
                        <form action="{{ route('approval.approve', $item) }}" method="POST" onsubmit="return confirm('Setujui arsip {{ $item->nomor_arsip }}?')">
                            @csrf
                            <button type="submit" class="btn btn-success">✅ Setujui</button>
                        </form>

                        {{-- Quick Reject --}}
                        <button type="button" class="btn btn-danger" onclick="openRejectModal('{{ $item->id }}', '{{ $item->nomor_arsip }}')">❌ Tolak</button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-16">{{ $arsip->links() }}</div>
        @endif
    </div>

    {{-- Riwayat --}}
    <div class="card animate-in-delay-2">
        <div class="card-header">
            <div class="card-title"><div class="title-icon">📋</div> Riwayat Approval Terakhir</div>
            <a href="{{ route('arsip.index') }}" class="btn btn-secondary btn-sm">Semua Arsip</a>
        </div>
        @if($riwayat->isEmpty())
            <div class="table-empty"><div class="empty-icon">📂</div><p>Belum ada riwayat</p></div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nomor Arsip</th><th>Staff</th><th>Jenis Pajak</th><th>Jumlah</th><th>Status</th><th>Tgl Diproses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $item)
                    <tr>
                        <td><a href="{{ route('arsip.show', $item) }}" style="color:var(--accent);font-weight:600;">{{ $item->nomor_arsip }}</a></td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->jenisPajak->nama_jenis ?? '-' }}</td>
                        <td>Rp {{ number_format($item->jumlah_pajak,0,',','.') }}</td>
                        <td><span class="badge {{ $item->status_badge }}">{{ $item->status_label }}</span></td>
                        <td class="text-muted text-sm">{{ $item->tanggal_disetujui?->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal-backdrop" id="reject-modal" style="display:none;" onclick="if(event.target===this)closeRejectModal()">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">❌ Tolak Arsip</div>
            <button class="modal-close" onclick="closeRejectModal()">✕</button>
        </div>
        <form id="reject-form" method="POST">
            @csrf
            <div class="modal-body">
                <p style="color:var(--text-secondary);margin-bottom:16px;">Arsip: <strong id="reject-nomor" style="color:var(--accent);"></strong></p>
                <div class="form-group">
                    <label class="form-label">Alasan Penolakan <span style="color:var(--red)">*</span></label>
                    <textarea name="catatan_pimpinan" class="form-control" rows="4" placeholder="Tuliskan alasan penolakan yang jelas agar staff dapat memperbaiki..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn btn-danger">❌ Tolak Arsip</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id, nomor) {
    document.getElementById('reject-modal').style.display = 'flex';
    document.getElementById('reject-nomor').textContent = nomor;
    document.getElementById('reject-form').action = '/approval/' + id + '/tolak';
}
function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}
</script>
@endsection
