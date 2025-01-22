<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran; // Pastikan model TahunAjaran sudah dibuat

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran.
     */
    public function index()
    {
        // Ambil data tahun ajaran dari database
        $tahunAjaran = TahunAjaran::all();

        // Tampilkan view tahun_ajaran.index dengan data
        return view('tahun_ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Menampilkan form untuk membuat tahun ajaran baru.
     */
    public function create()
    {
        // Tampilkan view tahun_ajaran.create
        return view('tahun_ajaran.create');
    }

    /**
     * Menampilkan form untuk mengedit tahun ajaran.
     */
    public function edit($id)
    {
        // Ambil data tahun ajaran berdasarkan ID
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Tampilkan view tahun_ajaran.edit dengan data
        return view('tahun_ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Menyimpan tahun ajaran baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran',
            'is_active' => 'required|integer|in:0,1'
        ]);

        TahunAjaran::create($validated);

        // Redirect ke halaman daftar tahun ajaran dengan pesan sukses
        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Mengupdate tahun ajaran di database.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran,year_name,'.$id,
            'is_active' => 'required|integer|in:0,1'
        ]);

        $tahun = TahunAjaran::findOrFail($id);
        $tahun->update($validated);

        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil diupdate.');
    }

    /**
     * Menghapus tahun ajaran dari database.
     */
    public function destroy($id)
    {
        // Ambil data tahun ajaran berdasarkan ID
        $tahun = TahunAjaran::findOrFail($id);

        // Hapus data
        $tahun->delete();

        // Redirect ke halaman daftar tahun ajaran dengan pesan sukses
        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
