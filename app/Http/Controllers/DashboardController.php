<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // 1. TANGGAL DAN PERIODE
        // =========================

        // Tanggal hari ini dan 7 hari terakhir
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6); // 7 hari terakhir
        $prevStartDate = $startDate->copy()->subDays(7); // 7 hari sebelumnya
        $prevEndDate = $startDate->copy()->subDays(1);

        // =========================
        // 2. HITUNG METRIK DASHBOARD
        // =========================

        // Total simpanan 7 hari terakhir
        $currentSimpanan = Transaksi::where('jenis', 'simpanan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('jumlah');

        // Total simpanan 7 hari sebelumnya
        $previousSimpanan = Transaksi::where('jenis', 'simpanan')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('jumlah');

        // Total cicilan 7 hari terakhir
        $currentCicilan = Transaksi::where('jenis', 'cicilan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('jumlah');

        // Total cicilan 7 hari sebelumnya
        $previousCicilan = Transaksi::where('jenis', 'cicilan')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('jumlah');

        // Total pendapatan (8% fee dari total simpanan setiap siswa)
        $currentPendapatan = BukuTabungan::with('transaksis')
            ->get()
            ->map(function($book) use ($startDate, $endDate) {
                $simpananPerBook = $book->transaksis
                    ->where('jenis', 'simpanan')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('jumlah');
                return $simpananPerBook * 0.08;
            })->sum();

        $previousPendapatan = BukuTabungan::with('transaksis')
            ->get()
            ->map(function($book) use ($prevStartDate, $prevEndDate) {
                $simpananPerBook = $book->transaksis
                    ->where('jenis', 'simpanan')
                    ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                    ->sum('jumlah');
                return $simpananPerBook * 0.08;
            })->sum();

        // Total siswa seluruh sekolah
        $totalSiswa = Siswa::count();

        // =========================
        // 3. HITUNG PERSENTASE PERUBAHAN
        // =========================

        $persentaseSimpanan = $previousSimpanan > 0
            ? (($currentSimpanan - $previousSimpanan) / $previousSimpanan) * 100
            : 0;

        $persentaseCicilan = $previousCicilan > 0
            ? (($currentCicilan - $previousCicilan) / $previousCicilan) * 100
            : 0;

        $persentasePendapatan = $previousPendapatan > 0
            ? (($currentPendapatan - $previousPendapatan) / $previousPendapatan) * 100
            : 0;

        // =========================
        // 4. PERSIAPAN DATA TABLE
        // =========================

        $kelasFilter = $request->input('kelas');
        $search = $request->input('search');

        $query = Transaksi::with(['bukuTabungan.transaksis', 'bukuTabungan.siswa.kelas']);

        if ($kelasFilter) {
            $query->whereHas('bukuTabungan.siswa.kelas', function ($q) use ($kelasFilter) {
                $q->where('nama', $kelasFilter);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('bukuTabungan.siswa', function ($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bukuTabungan', function ($q3) use ($search) {
                    $q3->where('nomor_urut', 'like', '%' . $search . '%');
                });
            });
        }

        $transaksiAll = $query->get()->filter(function ($transaksi) {
            return $transaksi->bukuTabungan &&
                   $transaksi->bukuTabungan->siswa &&
                   $transaksi->bukuTabungan->siswa->kelas &&
                   $transaksi->bukuTabungan->transaksis;
        });

        $grouped = $transaksiAll->groupBy(function ($transaksi) {
            return $transaksi->bukuTabungan->siswa->id;
        });

        $results = $grouped->map(function ($transaksiGroup) {
            $transaksi = $transaksiGroup->first();

            $totalTabungan = $transaksi->bukuTabungan->transaksis
                ->where('jenis', 'simpanan')
                ->sum('jumlah');

            $totalCicilan = $transaksi->bukuTabungan->transaksis
                ->where('jenis', 'cicilan')
                ->sum('jumlah');

            $totalKeseluruhan = ($totalTabungan - ($totalTabungan * 0.08)) - $totalCicilan;

            return (object)[
                'id' => $transaksi->id,
                'nomor_tabungan' => $transaksi->bukuTabungan->nomor_urut,
                'nama' => $transaksi->bukuTabungan->siswa->nama,
                'kelas' => $transaksi->bukuTabungan->siswa->kelas->nama,
                'total_tabungan' => $totalTabungan,
                'total_cicilan' => $totalCicilan,
                'total_keseluruhan' => $totalKeseluruhan,
            ];
        })->values();

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

        // =========================
        // 5. KIRIM SEMUA DATA KE VIEW
        // =========================

        return view('dashboard', [
            'transaksis' => $paginatedResults,
            'totalSimpanan' => $currentSimpanan,
            'totalCicilan' => $currentCicilan,
            'totalPendapatan' => $currentPendapatan,
            'totalSiswa' => $totalSiswa,
            'persentaseSimpanan' => $persentaseSimpanan,
            'persentaseCicilan' => $persentaseCicilan,
            'persentasePendapatan' => $persentasePendapatan,
        ]);
    }
}
