<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Kelas;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi.
     */
    public function index()
    {
        // Ambil data transaksi beserta relasi bukuTabungan, siswa, dan kelas
        // Ambil data transaksi beserta relasi bukuTabungan, siswa, dan kelas, dengan pagination
    $transaksis = Transaksi::with(['bukuTabungan.siswa.kelas'])->paginate(10);

    // Kirim data transaksi ke view
    return view('transaksi.index', compact('transaksis'));

        
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
{
    // Ambil semua Buku Tabungan dengan relasi siswa
    $bukuTabungans = BukuTabungan::with('siswa')->get();

    // Kirim data ke view
    return view('transaksi.create', compact('bukuTabungans'));
}

    /**
     * Mengambil data Buku Tabungan berdasarkan Kelas (AJAX).
     */
    public function getBukuTabunganByKelas($kelasId)
{
    $bukuTabungans = BukuTabungan::whereHas('siswa', function ($query) use ($kelasId) {
        $query->where('kelas_id', $kelasId);
    })->with('siswa')->get();

    // Pastikan selalu mengembalikan array, meskipun kosong
    return response()->json($bukuTabungans);
}

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'buku_tabungan_id' => 'required|exists:buku_tabungan,id',
        'simpanan' => 'nullable|numeric|min:0',
        'cicilan' => 'nullable|numeric|min:0',
        'penarikan' => 'nullable|numeric|min:0',
        'keterangan' => 'nullable|string',
    ]);

    // Pastikan admin mengisi minimal satu dari Simpanan, Cicilan, atau Penarikan
    if (!$request->has('simpanan') && !$request->has('cicilan') && !$request->has('penarikan')) {
        return back()->with('error', 'Harus mengisi minimal satu dari Simpanan, Cicilan, atau Penarikan.');
    }

    // Simpan transaksi
    if ($request->has('simpanan') && $request->simpanan > 0) {
        Transaksi::create([
            'buku_tabungan_id' => $request->buku_tabungan_id,
            'jenis' => 'simpanan',
            'jumlah' => $request->simpanan,
            'tanggal' => now(),
            'keterangan' => $request->keterangan,
        ]);
    }

    if ($request->has('cicilan') && $request->cicilan > 0) {
        Transaksi::create([
            'buku_tabungan_id' => $request->buku_tabungan_id,
            'jenis' => 'cicilan',
            'jumlah' => $request->cicilan,
            'tanggal' => now(),
            'keterangan' => $request->keterangan,
        ]);
    }

    if ($request->has('penarikan') && $request->penarikan > 0) {
        Transaksi::create([
            'buku_tabungan_id' => $request->buku_tabungan_id,
            'jenis' => 'penarikan',
            'jumlah' => $request->penarikan,
            'tanggal' => now(),
            'keterangan' => $request->keterangan,
        ]);
    }

    return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
}


    /**
     * Menampilkan detail transaksi.
     */
    public function show(Transaksi $transaksi)
    {
        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Menampilkan form untuk mengedit transaksi.
     */
    public function edit(Transaksi $transaksi)
    {
        $bukuTabungans = BukuTabungan::all();
        return view('transaksi.edit', compact('transaksi', 'bukuTabungans'));
    }

    /**
     * Mengupdate transaksi di database.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        // Validasi input
        $request->validate([
            'buku_tabungan_id' => 'required|exists:buku_tabungan,id',
            'jenis' => 'required|in:simpanan,penarikan,cicilan', // Sesuai enum di migrasi
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Pertahankan tanggal lama
        $request->merge(['tanggal' => $transaksi->tanggal]);

        // Update transaksi
        $transaksi->update($request->all());

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi dari database.
     */
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}