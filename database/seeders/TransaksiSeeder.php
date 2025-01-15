<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Siswa;
use App\Models\BukuTabungan;
use Faker\Factory as Faker;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get all students
        $siswaList = Siswa::all();

        foreach ($siswaList as $siswa) {
            // Generate a random number of transactions between 1 and 5
            $numberOfTransactions = rand(1, 5);

            for ($i = 0; $i < $numberOfTransactions; $i++) {
                Transaksi::create([
                    'siswa_id' => $siswa->id,
                    'buku_tabungan_id' => BukuTabungan::where('siswa_id', $siswa->id)->first()->id,
                    'jumlah' => $faker->randomFloat(2, 1000, 5000), // Random amount between 1000 and 5000
                    'transaction_date' => $faker->date(),
                ]);
            }
        }
    }
}
