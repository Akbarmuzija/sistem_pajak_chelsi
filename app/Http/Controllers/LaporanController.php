<?php

namespace App\Http\Controllers;

use App\Models\ArsipPajak;
use App\Models\JenisPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $tahunList  = ArsipPajak::selectRaw('DISTINCT tahun_pajak')->orderByDesc('tahun_pajak')->pluck('tahun_pajak');
        $jenisPajak = JenisPajak::where('is_aktif', true)->get();

        $tahun  = $request->get('tahun', date('Y'));
        $jenis  = $request->get('jenis');

        $query = ArsipPajak::with(['jenisPajak', 'user'])
                    ->where('status', 'disetujui')
                    ->where('tahun_pajak', $tahun);

        if ($user->isStaff()) {
            $query->where('user_id', $user->id);
        }
        if ($jenis) {
            $query->where('jenis_pajak_id', $jenis);
        }

        $arsip        = $query->latest()->get();
        $total_pajak  = $arsip->sum('jumlah_pajak');

        // Rekapitulasi per bulan
        $rekap_bulan = ArsipPajak::selectRaw('periode, SUM(jumlah_pajak) as total, COUNT(*) as jumlah')
            ->where('status', 'disetujui')
            ->where('tahun_pajak', $tahun)
            ->when($user->isStaff(), fn($q) => $q->where('user_id', $user->id))
            ->when($jenis, fn($q) => $q->where('jenis_pajak_id', $jenis))
            ->groupBy('periode')
            ->get();

        // Rekapitulasi per jenis pajak
        $rekap_jenis = ArsipPajak::selectRaw('jenis_pajak_id, SUM(jumlah_pajak) as total, COUNT(*) as jumlah')
            ->with('jenisPajak')
            ->where('status', 'disetujui')
            ->where('tahun_pajak', $tahun)
            ->when($user->isStaff(), fn($q) => $q->where('user_id', $user->id))
            ->groupBy('jenis_pajak_id')
            ->get();

        return view('laporan.index', compact(
            'arsip', 'total_pajak', 'tahunList', 'jenisPajak',
            'tahun', 'jenis', 'rekap_bulan', 'rekap_jenis'
        ));
    }
}
