<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BukuTabungan;
use App\Models\Siswa;

class BukuTabunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all students
        $siswaList = Siswa::all();

        foreach ($siswaList as $siswa) {
            BukuTabungan::create([
                'siswa_id' => $siswa->id,
                'nomor_urut' => $siswa->id, // Use student ID as the book number
                'tahun_ajaran_id' => \App\Models\TahunAjaran::latest()->first()->id, // Get the latest active academic year ID
                'kelas_id' => $siswa->kelas_id, // Get the class ID from the student
            ]);
        }
    }
}
