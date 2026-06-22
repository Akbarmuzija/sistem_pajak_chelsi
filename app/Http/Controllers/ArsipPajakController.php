<?php

namespace App\Http\Controllers;

use App\Models\ArsipPajak;
use App\Models\JenisPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArsipPajakController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = ArsipPajak::with(['jenisPajak', 'user']);

        if ($user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_pajak_id', $request->jenis);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_pajak', $request->tahun);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_arsip', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        $arsip      = $query->latest()->paginate(10)->withQueryString();
        $jenisPajak = JenisPajak::where('is_aktif', true)->get();
        $tahunList  = ArsipPajak::selectRaw('DISTINCT tahun_pajak')->orderByDesc('tahun_pajak')->pluck('tahun_pajak');

        return view('arsip.index', compact('arsip', 'jenisPajak', 'tahunList'));
    }

    public function create()
    {
        $jenisPajak = JenisPajak::where('is_aktif', true)->get();
        $periodeList = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember',
            'Triwulan I','Triwulan II','Triwulan III','Triwulan IV','Tahunan'
        ];
        return view('arsip.create', compact('jenisPajak', 'periodeList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pajak_id'   => 'required|exists:jenis_pajak,id',
            'tahun_pajak'      => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'periode'          => 'required|string',
            'jumlah_pajak'     => 'required|numeric|min:0',
            'tanggal_setor'    => 'nullable|date',
            'nomor_bukti_setor'=> 'nullable|string|max:100',
            'keterangan'       => 'nullable|string|max:500',
            'file_dokumen'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['user_id']     = Auth::id();
        $validated['nomor_arsip'] = ArsipPajak::generateNomorArsip();
        $validated['status']      = 'draft';

        if ($request->hasFile('file_dokumen')) {
            $validated['file_dokumen'] = $request->file('file_dokumen')->store('dokumen-pajak', 'public');
        }

        ArsipPajak::create($validated);

        return redirect()->route('arsip.index')->with('success', 'Arsip pajak berhasil disimpan dengan nomor ' . $validated['nomor_arsip']);
    }

    public function show(ArsipPajak $arsip)
    {
        if (Auth::user()->isStaff() && $arsip->user_id !== Auth::id()) {
            abort(403);
        }
        $arsip->load(['jenisPajak', 'user', 'approvedBy']);
        return view('arsip.show', compact('arsip'));
    }

    public function edit(ArsipPajak $arsip)
    {
        if ($arsip->user_id !== Auth::id()) abort(403);
        if (!in_array($arsip->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Arsip yang sudah dikirim tidak dapat diedit.');
        }

        $jenisPajak = JenisPajak::where('is_aktif', true)->get();
        $periodeList = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember',
            'Triwulan I','Triwulan II','Triwulan III','Triwulan IV','Tahunan'
        ];
        return view('arsip.edit', compact('arsip', 'jenisPajak', 'periodeList'));
    }

    public function update(Request $request, ArsipPajak $arsip)
    {
        if ($arsip->user_id !== Auth::id()) abort(403);
        if (!in_array($arsip->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Arsip tidak dapat diubah.');
        }

        $validated = $request->validate([
            'jenis_pajak_id'   => 'required|exists:jenis_pajak,id',
            'tahun_pajak'      => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'periode'          => 'required|string',
            'jumlah_pajak'     => 'required|numeric|min:0',
            'tanggal_setor'    => 'nullable|date',
            'nomor_bukti_setor'=> 'nullable|string|max:100',
            'keterangan'       => 'nullable|string|max:500',
            'file_dokumen'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('file_dokumen')) {
            if ($arsip->file_dokumen) Storage::disk('public')->delete($arsip->file_dokumen);
            $validated['file_dokumen'] = $request->file('file_dokumen')->store('dokumen-pajak', 'public');
        }

        $validated['status'] = 'draft';
        $arsip->update($validated);

        return redirect()->route('arsip.show', $arsip)->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy(ArsipPajak $arsip)
    {
        if ($arsip->user_id !== Auth::id()) abort(403);
        if ($arsip->status === 'disetujui') {
            return back()->with('error', 'Arsip yang sudah disetujui tidak dapat dihapus.');
        }

        if ($arsip->file_dokumen) Storage::disk('public')->delete($arsip->file_dokumen);
        $arsip->delete();

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil dihapus.');
    }

    public function kirimApproval(ArsipPajak $arsip)
    {
        if ($arsip->user_id !== Auth::id()) abort(403);
        if ($arsip->status !== 'draft' && $arsip->status !== 'ditolak') {
            return back()->with('error', 'Arsip tidak dapat dikirim untuk approval.');
        }

        $arsip->update(['status' => 'menunggu']);
        return back()->with('success', 'Arsip berhasil dikirim untuk approval pimpinan.');
    }
}
