<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\BukuTabungan;
use DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::all();
        return view('tahun_ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        // Buat tahun ajaran baru
        TahunAjaran::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->delete();

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus');
    }

    public function aktifkan($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->aktifkan();

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diaktifkan');
    }

    public function nonaktifkan($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->nonaktifkan();

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dinonaktifkan');
    }
}
