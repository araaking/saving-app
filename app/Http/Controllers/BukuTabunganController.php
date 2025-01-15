<?php

namespace App\Http\Controllers;

use App\Models\BukuTabungan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuTabunganController extends Controller
{
    public function index()
    {
        // Gunakan paginate untuk mendukung pagination
        $bukuTabungans = BukuTabungan::with(['siswa', 'tahunAjaran'])->paginate(10);
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
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nomor_urut' => [
                'required',
                'integer',
                Rule::unique('buku_tabungan')->where(function ($query) use ($request) {
                    return $query->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                                 ->where('kelas_id', Siswa::find($request->siswa_id)->kelas_id);
                })
            ],
        ]);

        // Tambahkan kelas_id dari siswa yang dipilih
        $validated['kelas_id'] = Siswa::find($request->siswa_id)->kelas_id;

        BukuTabungan::create($validated);

        return redirect()->route('buku-tabungan.index')
            ->with('success', 'Buku tabungan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $bukuTabungan = BukuTabungan::findOrFail($id);
        $siswas       = Siswa::all();
        $tahunAjarans = TahunAjaran::all();
        return view('buku_tabungan.edit', compact('bukuTabungan', 'siswas', 'tahunAjarans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id'         => 'required|exists:siswa,id',
            'tahun_ajaran_id'  => 'required|exists:tahun_ajaran,id',
            'nomor_urut'       => 'required|integer',
        ]);

        $bukuTabungan = BukuTabungan::findOrFail($id);
        $bukuTabungan->update($request->all());

        return redirect()->route('buku-tabungan.index')
                         ->with('success', 'Buku Tabungan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bukuTabungan = BukuTabungan::findOrFail($id);
        $bukuTabungan->delete();

        return redirect()->route('buku-tabungan.index')
                         ->with('success', 'Buku Tabungan berhasil dihapus.');
    }
}
