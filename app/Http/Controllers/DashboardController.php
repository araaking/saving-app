<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ============================
        // 1. HITUNG METRIK DASHBOARD
        // ============================

        // Total simpanan seluruh sekolah: jumlah seluruh transaksi dengan jenis 'simpanan'
        $totalSimpanan = Transaksi::where('jenis', 'simpanan')->sum('jumlah');

        // Total cicilan seluruh siswa: jumlah seluruh transaksi dengan jenis 'cicilan'
        $totalCicilan = Transaksi::where('jenis', 'cicilan')->sum('jumlah');

        /* Total pendapatan sekolah:
           Misal admin mendapat 8% fee per siswa atas total simpanan pada buku tabungan.
           Jika tiap siswa hanya punya 1 buku, maka:
              total pendapatan = 8% * (total simpanan masing-masing siswa, dijumlahkan)
           Untuk menghindari duplikasi (jika ada lebih dari 1 transaksi per siswa), kita
           ambil data dari BukuTabungan, lalu untuk masing-masing, jumlahkan transaksi simpanan,
           kemudian hitung 8% fee-nya dan jumlahkan semuanya.
        */
        $totalPendapatan = BukuTabungan::with('transaksis')
            ->get()
            ->map(function($book) {
                // Jumlahkan seluruh transaksi simpanan pada buku tabungan tersebut
                $simpananPerBook = $book->transaksis->where('jenis', 'simpanan')->sum('jumlah');
                // Fee 8% per buku (diasumsikan 1 buku per siswa)
                return $simpananPerBook * 0.08;
            })->sum();

        // Total siswa seluruh sekolah
        $totalSiswa = Siswa::count();


        // ===========================================
        // 2. PERSIAPAN DATA TABLE (dengan Filter & Search)
        // ===========================================

        // Ambil input filter
        $kelasFilter = $request->input('kelas'); // misal: "3A", "4A", dll.
        $search      = $request->input('search');  // pencarian berdasarkan nama siswa atau nomor buku tabungan

        // Buat query dasar dengan relasi yang diperlukan
        $query = Transaksi::with(['bukuTabungan.transaksis', 'bukuTabungan.siswa.kelas']);

        // Filter berdasarkan kelas via relasi (nama kelas)
        if ($kelasFilter) {
            $query->whereHas('bukuTabungan.siswa.kelas', function ($q) use ($kelasFilter) {
                $q->where('nama', $kelasFilter);
            });
        }

        // Filter berdasarkan pencarian (nama siswa atau nomor buku tabungan)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('bukuTabungan.siswa', function ($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bukuTabungan', function ($q3) use ($search) {
                    // Pada model BukuTabungan, nomor buku disimpan di kolom "nomor_urut"
                    $q3->where('nomor_urut', 'like', '%' . $search . '%');
                });
            });
        }

        // Ambil seluruh data transaksi sesuai filter
        $transaksiAll = $query->get()->filter(function ($transaksi) {
            // Pastikan relasi penting ada
            return $transaksi->bukuTabungan &&
                   $transaksi->bukuTabungan->siswa &&
                   $transaksi->bukuTabungan->siswa->kelas &&
                   $transaksi->bukuTabungan->transaksis;
        });

        // Kelompokkan data transaksi berdasarkan siswa (agar tiap siswa hanya tampil satu baris)
        $grouped = $transaksiAll->groupBy(function ($transaksi) {
            return $transaksi->bukuTabungan->siswa->id;
        });

        // Untuk setiap grup, ambil transaksi pertama dan hitung total simpanan & cicilan per siswa
        $results = $grouped->map(function ($transaksiGroup) {
            $transaksi = $transaksiGroup->first();

            // Hitung total tabungan untuk satu siswa (berdasarkan transaksi dari buku tabungan)
            $totalTabungan = $transaksi->bukuTabungan->transaksis
                ->where('jenis', 'simpanan')
                ->sum('jumlah');

            // Hitung total cicilan untuk satu siswa
            $totalCicilan = $transaksi->bukuTabungan->transaksis
                ->where('jenis', 'cicilan')
                ->sum('jumlah');

            // Hitung total keseluruhan untuk siswa tersebut
            // Total keseluruhan = total tabungan setelah dikurangi fee 8% dan dikurangi cicilan
            $totalKeseluruhan = ($totalTabungan - ($totalTabungan * 0.08)) - $totalCicilan;

            return (object)[
                'id'                => $transaksi->id,
                // Nomor buku tabungan diambil dari kolom "nomor_urut" pada BukuTabungan
                'nomor_tabungan'    => $transaksi->bukuTabungan->nomor_urut,
                'nama'              => $transaksi->bukuTabungan->siswa->nama,
                'kelas'             => $transaksi->bukuTabungan->siswa->kelas->nama,
                'total_tabungan'    => $totalTabungan,
                'total_cicilan'     => $totalCicilan,
                'total_keseluruhan' => $totalKeseluruhan,
            ];
        })->values();

        // Pagination manual pada Collection hasil grouping
        $page = $request->get('page', 1);
        $perPage = 10;
        $total = $results->count();
        $currentItems = $results->slice(($page - 1) * $perPage, $perPage)->values();

        $paginatedResults = new LengthAwarePaginator(
            $currentItems,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // KIRIM SEMUA DATA ke view
        return view('dashboard', [
            'transaksis'    => $paginatedResults,
            'totalSimpanan' => $totalSimpanan,
            'totalCicilan'  => $totalCicilan,
            'totalPendapatan' => $totalPendapatan,
            'totalSiswa'    => $totalSiswa,
        ]);
    }
}
