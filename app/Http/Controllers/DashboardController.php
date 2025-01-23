<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tahun ajaran aktif
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();

        // 2. Setup periode tanggal
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6);
        $prevStartDate = $startDate->copy()->subDays(7);
        $prevEndDate = $startDate->copy()->subDays(1);

        // 3. Hitung metrik utama
        $currentSimpanan = $this->hitungTransaksi('simpanan', $startDate, $endDate, $tahunAktif);
        $previousSimpanan = $this->hitungTransaksi('simpanan', $prevStartDate, $prevEndDate);
        
        $currentCicilan = $this->hitungTransaksi('cicilan', $startDate, $endDate, $tahunAktif);
        $previousCicilan = $this->hitungTransaksi('cicilan', $prevStartDate, $prevEndDate);

        // 4. Hitung pendapatan
        $currentPendapatan = $this->hitungPendapatan($tahunAktif, $startDate, $endDate);
        $previousPendapatan = $this->hitungPendapatan(null, $prevStartDate, $prevEndDate);

        // 5. Total siswa aktif
        $totalSiswa = Siswa::where('status', 'Aktif')
            ->where('academic_year_id', $tahunAktif->id)
            ->count();

        // 6. Persentase perubahan
        $persentase = [
            'simpanan' => $this->hitungPersentase($previousSimpanan, $currentSimpanan),
            'cicilan' => $this->hitungPersentase($previousCicilan, $currentCicilan),
            'pendapatan' => $this->hitungPersentase($previousPendapatan, $currentPendapatan)
        ];

        // 7. Data tabel
        $dataTable = $this->prosesDataTable($request, $tahunAktif);

        return view('dashboard', array_merge([
            'totalSimpanan' => $currentSimpanan,
            'totalCicilan' => $currentCicilan,
            'totalPendapatan' => $currentPendapatan,
            'totalSiswa' => $totalSiswa,
            'persentaseSimpanan' => $persentase['simpanan'],
            'persentaseCicilan' => $persentase['cicilan'],
            'persentasePendapatan' => $persentase['pendapatan'],
        ], $dataTable));
    }

    private function hitungTransaksi($jenis, $start, $end, $tahunAktif = null)
    {
        $query = Transaksi::where('jenis', $jenis)
            ->whereBetween('created_at', [$start, $end]);

        if ($tahunAktif) {
            $query->whereHas('bukuTabungan', function($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif->id)
                  ->whereHas('siswa', function($q) {
                      $q->where('status', 'Aktif');
                  });
            });
        }

        return $query->sum('jumlah');
    }

    private function hitungPendapatan($tahunAktif, $start, $end)
    {
        $query = BukuTabungan::with('transaksis')
            ->whereHas('siswa', function($q) {
                $q->where('status', 'Aktif');
            });

        if ($tahunAktif) {
            $query->where('tahun_ajaran_id', $tahunAktif->id);
        }

        return $query->get()
            ->map(function($book) use ($start, $end) {
                $simpanan = $book->transaksis
                    ->where('jenis', 'simpanan')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('jumlah');
                return $simpanan * 0.08;
            })->sum();
    }

    private function prosesDataTable($request, $tahunAktif)
    {
        $query = Transaksi::with([
                'bukuTabungan.siswa.kelas',
                'bukuTabungan.transaksis'
            ])
            ->whereHas('bukuTabungan', function($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif->id)
                  ->whereHas('siswa', function($q) {
                      $q->where('status', 'Aktif');
                  });
            });

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->whereHas('bukuTabungan.siswa.kelas', function($q) use ($request) {
                $q->where('name', $request->kelas);
            });
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('bukuTabungan.siswa', function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%');
                })->orWhereHas('bukuTabungan', function($q) use ($request) {
                    $q->where('nomor_urut', 'like', '%'.$request->search.'%');
                });
            });
        }

        // Proses data
        $transaksiAll = $query->get()
            ->groupBy('bukuTabungan.siswa.id')
            ->map(function ($transaksiGroup) {
                $bukuTabungan = $transaksiGroup->first()->bukuTabungan;
                $siswa = $bukuTabungan->siswa;
                $kelas = $siswa->kelas;

                $totalTabungan = $bukuTabungan->transaksis
                    ->where('jenis', 'simpanan')
                    ->sum('jumlah');

                $totalCicilan = $bukuTabungan->transaksis
                    ->where('jenis', 'cicilan')
                    ->sum('jumlah');

                return (object)[
                    'id' => $bukuTabungan->id,
                    'nomor_tabungan' => $bukuTabungan->nomor_urut,
                    'nama' => $siswa->name,
                    'kelas' => $kelas->name,
                    'total_tabungan' => $totalTabungan,
                    'total_cicilan' => $totalCicilan,
                    'total_keseluruhan' => ($totalTabungan * 0.92) - $totalCicilan,
                ];
            })->values();

        // Pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        
        return [
            'transaksis' => new LengthAwarePaginator(
                $transaksiAll->forPage($page, $perPage),
                $transaksiAll->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            )
        ];
    }

    private function hitungPersentase($lama, $baru)
    {
        if ($lama == 0) return $baru > 0 ? 100 : 0;
        return round((($baru - $lama) / $lama) * 100, 2);
    }
}