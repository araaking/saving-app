<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('year_name', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran',
        ]);

        // Nonaktifkan semua tahun ajaran
        TahunAjaran::query()->update(['is_active' => false]);

        // Buat tahun ajaran baru
        $tahunAjaran = TahunAjaran::create([
            'year_name' => $validated['year_name'],
            'is_active' => true
        ]);

        // Proses kenaikan kelas jika ada tahun sebelumnya
        $tahunSebelumnya = TahunAjaran::where('is_active', false)
            ->orderBy('id', 'desc')
            ->first();

        if ($tahunSebelumnya) {
            $this->processClassPromotion($tahunAjaran, $tahunSebelumnya);
        }

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('tahun_ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran,year_name,' . $id,
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($validated);

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Hapus siswa terkait tahun ini
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
                    'academic_year_id' => $tahunBaru->id, // Update academic_year_id ke tahun baru
                    'status'            => 'Aktif'
                ]);
                Log::info("Siswa ID {$siswa->id} naik ke kelas {$kelasTujuan->name}");
            } else {
                $siswa->update([
                    'status'            => 'Lulus',
                    'academic_year_id' => $tahunBaru->id // Tetap update academic_year_id
                ]);
                Log::info("Siswa ID {$siswa->id} lulus dari kelas {$kelasSekarang->name}");
            }
        }
    }
}