<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\BiayaSekolah;
use App\Models\TahunAjaran;
use App\Models\BukuTabungan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    // Tampilkan semua pembayaran
    public function index()
    {
        $pembayarans = Pembayaran::with(['siswa', 'tahunAjaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pembayaran.index', compact('pembayarans'));
    }

    // Form pembayaran
    public function create(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $kelasList = Kelas::all();
        $selectedKelasId = $request->input('kelas_id');
        $siswas = [];

        if ($selectedKelasId) {
            $siswas = Siswa::where('class_id', $selectedKelasId)
                ->where('academic_year_id', $tahunAktif->id)
                ->where('status', 'Aktif')
                ->get();
        }

        return view('pembayaran.create', compact(
            'kelasList',
            'tahunAktif',
            'siswas',
            'selectedKelasId'
        ));
    }

    // Simpan pembayaran
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_biaya' => [
                'required',
                Rule::in(['SPP', 'IKK', 'THB', 'Uang Pangkal', 'Raport', 'Wisuda', 'Foto', 'Seragam'])
            ],
            'bulan_hijri' => [
                Rule::requiredIf($request->jenis_biaya === 'SPP'),
                'nullable',
                Rule::in([
                    'Muharram', 'Safar', 'Rabiul Awwal', 'Rabiul Akhir',
                    'Jumadil Awwal', 'Jumadil Akhir', 'Rajab', 'Sya\'ban',
                    'Ramadan', 'Syawwal', 'Dzulqaidah', 'Dzulhijjah'
                ])
            ],
            'jumlah' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,cicilan,tabungan',
            'keterangan' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $siswa = Siswa::findOrFail($request->siswa_id);
            $this->validateStudentCategory($siswa, $request->jenis_biaya);

            // Cek biaya sekolah sesuai kategori siswa
            $biayaSekolah = BiayaSekolah::where('tahun_ajaran_id', $siswa->academic_year_id)
                ->where('jenis_biaya', $request->jenis_biaya)
                ->where('kategori_siswa', $siswa->category)
                ->first();

            if ($biayaSekolah) {
                $maxJumlah = $biayaSekolah->jumlah;
                
                // Validasi jumlah untuk kategori khusus
                if ($siswa->category === 'Kakak Beradik' && in_array($request->jenis_biaya, ['SPP', 'IKK'])) {
                    $maxJumlah *= 0.8; // Diskon 20% untuk kakak beradik
                }

                if ($request->jumlah > $maxJumlah) {
                    return back()->with('error', 'Jumlah melebihi ketentuan untuk kategori siswa ini.');
                }
            }

            // Validasi duplikat pembayaran
            $existingPayment = Pembayaran::where('siswa_id', $request->siswa_id)
                ->where('tahun_ajaran_id', $siswa->academic_year_id)
                ->where('jenis_biaya', $request->jenis_biaya)
                ->when($request->jenis_biaya === 'SPP', function ($query) use ($request) {
                    $query->where('bulan_hijri', $request->bulan_hijri);
                })
                ->exists();

            if ($existingPayment) {
                return back()->with('error', 'Siswa ini sudah melakukan pembayaran untuk jenis biaya dan periode tersebut.');
            }

            // Simpan pembayaran
            $pembayaran = Pembayaran::create([
                'siswa_id' => $request->siswa_id,
                'tahun_ajaran_id' => $siswa->academic_year_id,
                'jenis_biaya' => $request->jenis_biaya,
                'bulan_hijri' => $request->bulan_hijri,
                'jumlah' => $request->jumlah,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'is_processed' => $request->metode_pembayaran === 'cash' // Langsung diproses jika cash
            ]);

            // Proses berdasarkan metode pembayaran
            switch ($request->metode_pembayaran) {
                case 'cicilan':
                    $this->createCicilanTransaction($pembayaran);
                    break;
                    
                case 'tabungan':
                    // Akan diproses saat eksekusi akhir periode
                    break;
            }

            DB::commit();

            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Validasi kategori siswa
    private function validateStudentCategory(Siswa $siswa, $jenisBiaya)
    {
        $invalidCases = [
            'Anak Guru' => ['SPP', 'IKK', 'Uang Pangkal'],
            'Anak Yatim' => ['SPP'],
        ];

        if (array_key_exists($siswa->category, $invalidCases) && 
            in_array($jenisBiaya, $invalidCases[$siswa->category])) {
            abort(422, "Siswa kategori {$siswa->category} tidak diizinkan membayar $jenisBiaya.");
        }
    }

    // Buat transaksi cicilan
    private function createCicilanTransaction(Pembayaran $pembayaran)
    {
        $bukuTabungan = $pembayaran->siswa->bukuTabungans()
            ->where('tahun_ajaran_id', $pembayaran->tahun_ajaran_id)
            ->firstOrFail();

        Transaksi::create([
            'buku_tabungan_id' => $bukuTabungan->id,
            'jenis' => 'cicilan',
            'jumlah' => $pembayaran->jumlah,
            'tanggal' => now(),
            'keterangan' => "Pembayaran {$pembayaran->jenis_biaya} - {$pembayaran->keterangan}"
        ]);
    }

    // Hitung diskon berdasarkan kategori siswa
    private function calculateStudentDiscount($category)
    {
        return match ($category) {
            'Anak Guru' => 0.05,
            'Anak Yatim' => 0.08,
            'Kakak Beradik' => 0.10,
            default => 0.08,
        };
    }

    // Tambahkan method baru untuk export PDF
    public function exportIndex(Request $request)
    {
        $kelasList = Kelas::all();
        $selectedKelasId = $request->input('kelas_id');
        
        $query = Siswa::with(['kelas', 'bukuTabungans'])
            ->whereHas('bukuTabungans', function($q) {
                $q->whereHas('tahunAjaran', function($q) {
                    $q->where('is_active', true);
                });
            });

        if ($selectedKelasId) {
            $query->where('class_id', $selectedKelasId);
        }

        $siswas = $query->paginate(20);

        return view('pembayaran.export-index', compact(
            'kelasList',
            'siswas',
            'selectedKelasId'
        ));
    }

    public function generatePDF(Siswa $siswa, Request $request)
    {
        $tahunAktif = TahunAjaran::getActive();
        
        // Validasi buku tabungan
        $bukuTabungan = $siswa->bukuTabungans()
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->firstOrFail();

        // Hitung total tabungan
        $totalTabungan = $bukuTabungan->transaksis()
            ->where('jenis', 'simpanan')
            ->sum('jumlah');

        // Hitung biaya admin
        $biayaAdminPersen = $this->hitungPersentaseAdmin($siswa->category);
        $biayaAdmin = $totalTabungan * $biayaAdminPersen;

        // Hitung semua potongan
        $potongan = $this->hitungSemuaPotongan($siswa, $tahunAktif);
        $totalPotongan = $potongan->sum('jumlah');

        // Hitung sisa tabungan
        $sisaTabungan = ($totalTabungan - $biayaAdmin) - $totalPotongan;

        $pdf = Pdf::loadView('pembayaran.pdf', compact(
            'siswa',
            'tahunAktif',
            'totalTabungan',
            'biayaAdminPersen',
            'biayaAdmin',
            'potongan',
            'totalPotongan',
            'sisaTabungan'
        ));

        return $request->has('preview') 
            ? $pdf->stream("pembayaran-{$siswa->nis}.pdf")
            : $pdf->download("pembayaran-{$siswa->nis}.pdf");
    }

    private function hitungPersentaseAdmin($kategori)
    {
        return match($kategori) {
            'Anak Guru' => 0.05,
            'Anak Yatim' => 0.08,
            'Kakak Beradik' => 0.08,
            default => 0.08
        };
    }

    private function hitungSemuaPotongan(Siswa $siswa, TahunAjaran $tahun)
    {
        $potongan = collect();
        
        // Hitung SPP
        $this->hitungPotonganSPP($siswa, $tahun, $potongan);
        
        // Hitung IKK
        $this->hitungPotonganIKK($siswa, $tahun, $potongan);
        
        // Hitung biaya lainnya (THB, Uang Pangkal, dll)
        $this->hitungBiayaLainnya($siswa, $tahun, $potongan);

        return $potongan;
    }

    private function hitungPotonganSPP($siswa, $tahun, &$potongan)
    {
        $spp = BiayaSekolah::where('jenis_biaya', 'SPP')
            ->where('tahun_ajaran_id', $tahun->id)
            ->where('kategori_siswa', $siswa->category)
            ->first();

        if ($spp) {
            $bulanTerbayar = $siswa->pembayarans()
                ->where('jenis_biaya', 'SPP')
                ->where('tahun_ajaran_id', $tahun->id)
                ->count();

            $sppPerBulan = $spp->jumlah;
            $bulanBelumBayar = 12 - $bulanTerbayar;
            
            if ($bulanBelumBayar > 0) {
                $potongan->push([
                    'jenis' => 'SPP ('.$bulanBelumBayar.' Bulan)',
                    'jumlah' => $bulanBelumBayar * $sppPerBulan
                ]);
            }
        }
    }

    private function hitungPotonganIKK($siswa, $tahun, &$potongan)
    {
        $ikk = BiayaSekolah::where('jenis_biaya', 'IKK')
            ->where('tahun_ajaran_id', $tahun->id)
            ->where('kategori_siswa', $siswa->category)
            ->first();

        if ($ikk) {
            $sudahBayar = $siswa->pembayarans()
                ->where('jenis_biaya', 'IKK')
                ->where('tahun_ajaran_id', $tahun->id)
                ->exists();

            if (!$sudahBayar) {
                $potongan->push([
                    'jenis' => 'IKK',
                    'jumlah' => $ikk->jumlah
                ]);
            }
        }
    }

    private function hitungBiayaLainnya($siswa, $tahun, &$potongan)
    {
        $biayaLain = BiayaSekolah::where('tahun_ajaran_id', $tahun->id)
            ->whereIn('jenis_biaya', [
                'THB', 
                'Uang Pangkal', 
                'Raport', 
                'Wisuda', 
                'Foto'
            ])
            ->get();

        foreach ($biayaLain as $biaya) {
            $sudahBayar = $siswa->pembayarans()
                ->where('jenis_biaya', $biaya->jenis_biaya)
                ->where('tahun_ajaran_id', $tahun->id)
                ->exists();

            if (!$sudahBayar) {
                $jumlah = $this->hitungDiskonKategori(
                    $biaya->jumlah, 
                    $siswa->category, 
                    $biaya->jenis_biaya
                );

                $potongan->push([
                    'jenis' => $biaya->jenis_biaya,
                    'jumlah' => $jumlah
                ]);
            }
        }
    }

    private function hitungDiskonKategori($jumlah, $kategori, $jenisBiaya)
    {
        if ($kategori === 'Kakak Beradik' && in_array($jenisBiaya, ['SPP', 'IKK'])) {
            return $jumlah * 0.8;
        }
        
        return $jumlah;
    }
}