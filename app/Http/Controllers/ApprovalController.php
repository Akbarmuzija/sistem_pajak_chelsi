<?php

namespace App\Http\Controllers;

use App\Models\ArsipPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = ArsipPajak::with(['user', 'jenisPajak'])->where('status', 'menunggu');

        if ($request->filled('jenis')) {
            $query->where('jenis_pajak_id', $request->jenis);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_arsip', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $arsip      = $query->latest()->paginate(10)->withQueryString();
        $riwayat    = ArsipPajak::with(['user', 'jenisPajak'])
                        ->whereIn('status', ['disetujui', 'ditolak'])
                        ->latest('tanggal_disetujui')
                        ->take(10)->get();

        return view('approval.index', compact('arsip', 'riwayat'));
    }

    public function show(ArsipPajak $arsip)
    {
        $arsip->load(['user', 'jenisPajak', 'approvedBy']);
        return view('approval.show', compact('arsip'));
    }

    public function approve(Request $request, ArsipPajak $arsip)
    {
        if ($arsip->status !== 'menunggu') {
            return back()->with('error', 'Arsip sudah diproses sebelumnya.');
        }

        $arsip->update([
            'status'            => 'disetujui',
            'catatan_pimpinan'  => $request->catatan_pimpinan,
            'disetujui_oleh'    => Auth::id(),
            'tanggal_disetujui' => now(),
        ]);

        return redirect()->route('approval.index')->with('success', 'Arsip ' . $arsip->nomor_arsip . ' berhasil disetujui.');
    }

    public function reject(Request $request, ArsipPajak $arsip)
    {
        $request->validate([
            'catatan_pimpinan' => 'required|string|max:500',
        ]);

        if ($arsip->status !== 'menunggu') {
            return back()->with('error', 'Arsip sudah diproses sebelumnya.');
        }

        $arsip->update([
            'status'            => 'ditolak',
            'catatan_pimpinan'  => $request->catatan_pimpinan,
            'disetujui_oleh'    => Auth::id(),
            'tanggal_disetujui' => now(),
        ]);

        return redirect()->route('approval.index')->with('success', 'Arsip ' . $arsip->nomor_arsip . ' telah ditolak.');
    }
}
