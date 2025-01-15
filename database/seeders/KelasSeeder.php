<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kelas = [
            'TkA',
            'TkB',
            '1A',
            '1B',
            '2A',
            '2B',
            '3A',
            '3B',
            '3C',
            '4A',
            '4B',
            '5',
            '6',
        ];

        foreach ($kelas as $nama) {
            Kelas::create(['nama' => $nama]);
        }
    }
}
