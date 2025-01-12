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
     * Menyimpan tahun ajaran baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255', // Sesuaikan dengan field di form
        ]);

        // Simpan data ke database
        TahunAjaran::create([
            'nama' => $request->nama, // Sesuaikan dengan field di form
        ]);

        // Redirect ke halaman daftar tahun ajaran dengan pesan sukses
        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
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