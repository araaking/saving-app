<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get all classes
        $kelasList = Kelas::all();

        foreach ($kelasList as $kelas) {
            // Generate a random number of students between 20 and 30
            $numberOfStudents = rand(20, 30);

            for ($i = 0; $i < $numberOfStudents; $i++) {
                Siswa::create([
                    'nama' => $faker->name,
                    'nis' => $kelas->id . str_pad($i + 1, 3, '0', STR_PAD_LEFT), // Generate NIS
                    'kelas_id' => $kelas->id,
                ]);
            }
        }
    }
}
