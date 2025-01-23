<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas; // Pastikan model Kelas di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran.
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('year_name', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Menampilkan form untuk membuat tahun ajaran baru.
     */
    public function create()
    {
        return view('tahun_ajaran.create');
    }

    /**
     * Menyimpan tahun ajaran baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran',
            'is_active' => 'required|boolean'
        ]);

        // Simpan tahun ajaran aktif sebelumnya
        $tahunSebelumnya = TahunAjaran::where('is_active', true)->first();

        // Nonaktifkan tahun ajaran lain jika tahun ini diaktifkan
        if ($request->is_active) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        // Buat tahun ajaran baru
        $tahunAjaran = TahunAjaran::create($validated);

        // Proses kenaikan kelas jika ada tahun sebelumnya
        if ($tahunAjaran->is_active && $tahunSebelumnya) {
            $this->processClassPromotion($tahunAjaran, $tahunSebelumnya);
        }

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit tahun ajaran.
     */
    public function edit($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('tahun_ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Mengupdate tahun ajaran di database.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran,year_name,' . $id,
            'is_active' => 'required|boolean'
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Simpan tahun ajaran aktif sebelumnya
        $tahunSebelumnya = TahunAjaran::where('is_active', true)->first();

        // Nonaktifkan tahun ajaran lain jika diaktifkan
        if ($request->is_active) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        $tahunAjaran->update($validated);

        // Proses kenaikan kelas jika ada tahun sebelumnya
        if ($tahunAjaran->is_active && $tahunSebelumnya) {
            $this->processClassPromotion($tahunAjaran, $tahunSebelumnya);
        }

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun ajaran dari database.
     */
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Hapus siswa yang terkait dengan tahun ajaran ini
        Siswa::where('academic_year_id', $tahunAjaran->id)->delete();

        $tahunAjaran->delete();

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Proses kenaikan kelas otomatis
     */
    private function processClassPromotion(TahunAjaran $tahunBaru, TahunAjaran $tahunSebelumnya)
    {
        // Ambil siswa aktif di tahun sebelumnya
        $siswaAktif = Siswa::with('kelas')
            ->where('academic_year_id', $tahunSebelumnya->id)
            ->where('status', 'Aktif')
            ->get();

        Log::info("Memproses kenaikan kelas untuk {$siswaAktif->count()} siswa.");

        foreach ($siswaAktif as $siswa) {
            $kelasSekarang = $siswa->kelas;
            $kelasTujuan = $kelasSekarang->nextClass;

            // Fallback: Cari kelas dengan tingkat +1 jika next_class_id tidak ada
            if (!$kelasTujuan) {
                $kelasTujuan = Kelas::where('tingkat', $kelasSekarang->tingkat + 1)
                    ->orderBy('tingkat', 'asc')
                    ->first();
            }

            // Update data siswa
            if ($kelasTujuan) {
                $siswa->update([
                    'class_id'          => $kelasTujuan->id,
                    'academic_year_id' => $tahunBaru->id,
                    'status'            => 'Aktif'
                ]);
                Log::info("Siswa ID {$siswa->id} naik ke kelas {$kelasTujuan->name}");
            } else {
                $siswa->update([
                    'status'            => 'Lulus',
                    'academic_year_id' => $tahunBaru->id
                ]);
                Log::info("Siswa ID {$siswa->id} lulus dari kelas {$kelasSekarang->name}");
            }
        }
    }
}