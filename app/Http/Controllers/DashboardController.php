<?php

namespace App\Http\Controllers;

use App\Models\ArsipPajak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isPimpinan()) {
            $stats = [
                'total_arsip'     => ArsipPajak::count(),
                'menunggu'        => ArsipPajak::where('status', 'menunggu')->count(),
                'disetujui'       => ArsipPajak::where('status', 'disetujui')->count(),
                'ditolak'         => ArsipPajak::where('status', 'ditolak')->count(),
                'total_staff'     => User::where('role', 'staff')->count(),
                'total_pajak'     => ArsipPajak::where('status', 'disetujui')->sum('jumlah_pajak'),
            ];

            $arsip_menunggu = ArsipPajak::with(['user', 'jenisPajak'])
                ->where('status', 'menunggu')
                ->latest()
                ->take(5)
                ->get();

            $arsip_terbaru = ArsipPajak::with(['user', 'jenisPajak'])
                ->latest()
                ->take(8)
                ->get();

            return view('dashboard.pimpinan', compact('stats', 'arsip_menunggu', 'arsip_terbaru'));
        }

        // Staff dashboard
        $stats = [
            'total_arsip'  => ArsipPajak::where('user_id', $user->id)->count(),
            'draft'        => ArsipPajak::where('user_id', $user->id)->where('status', 'draft')->count(),
            'menunggu'     => ArsipPajak::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'disetujui'    => ArsipPajak::where('user_id', $user->id)->where('status', 'disetujui')->count(),
            'ditolak'      => ArsipPajak::where('user_id', $user->id)->where('status', 'ditolak')->count(),
            'total_pajak'  => ArsipPajak::where('user_id', $user->id)->where('status', 'disetujui')->sum('jumlah_pajak'),
        ];

        $arsip_terbaru = ArsipPajak::with('jenisPajak')
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.staff', compact('stats', 'arsip_terbaru'));
    }
}
