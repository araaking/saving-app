<?php

namespace App\Http\Controllers;

use App\Models\BukuTabungan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class BukuTabunganController extends Controller
{
    public function index()
    {
        $bukuTabungans = BukuTabungan::with(['siswa', 'tahunAjaran'])->get();
        return view('buku_tabungan.index', compact('bukuTabungans'));
    }

    public function create()
    {
        $siswas = Siswa::all();
        $tahunAjarans = TahunAjaran::all();
        return view('buku_tabungan.create', compact('siswas', 'tahunAjarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id', // Corrected table name
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id', // Corrected table name
            'nomor_urut' => 'required|integer',
        ]);

        BukuTabungan::create($request->all());

        return redirect()->route('buku_tabungan.index')->with('success', 'Buku Tabungan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $bukuTabungan = BukuTabungan::findOrFail($id);
        $siswas = Siswa::all();
        $tahunAjarans = TahunAjaran::all();
        return view('buku_tabungan.edit', compact('bukuTabungan', 'siswas', 'tahunAjarans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id', // Corrected table name
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id', // Corrected table name
            'nomor_urut' => 'required|integer',
        ]);

        $bukuTabungan = BukuTabungan::findOrFail($id);
        $bukuTabungan->update($request->all());

        return redirect()->route('buku_tabungan.index')->with('success', 'Buku Tabungan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bukuTabungan = BukuTabungan::findOrFail($id);
        $bukuTabungan->delete();

        return redirect()->route('buku_tabungan.index')->with('success', 'Buku Tabungan berhasil dihapus.');
    }
}