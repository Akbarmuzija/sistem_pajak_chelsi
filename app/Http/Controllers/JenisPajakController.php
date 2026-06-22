<?php

namespace App\Http\Controllers;

use App\Models\JenisPajak;
use Illuminate\Http\Request;

class JenisPajakController extends Controller
{
    public function index()
    {
        $jenisPajak = JenisPajak::latest()->get();
        return view('jenis-pajak.index', compact('jenisPajak'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'       => 'required|string|max:20|unique:jenis_pajak,kode',
            'nama_jenis' => 'required|string|max:100',
            'deskripsi'  => 'nullable|string|max:500',
        ]);

        JenisPajak::create($validated);
        return back()->with('success', 'Jenis pajak berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPajak $jenisPajak)
    {
        $validated = $request->validate([
            'kode'       => 'required|string|max:20|unique:jenis_pajak,kode,' . $jenisPajak->id,
            'nama_jenis' => 'required|string|max:100',
            'deskripsi'  => 'nullable|string|max:500',
            'is_aktif'   => 'boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif');
        $jenisPajak->update($validated);
        return back()->with('success', 'Jenis pajak berhasil diperbarui.');
    }

    public function destroy(JenisPajak $jenisPajak)
    {
        if ($jenisPajak->arsipPajak()->count() > 0) {
            return back()->with('error', 'Jenis pajak tidak dapat dihapus karena sudah digunakan.');
        }
        $jenisPajak->delete();
        return back()->with('success', 'Jenis pajak berhasil dihapus.');
    }
}
