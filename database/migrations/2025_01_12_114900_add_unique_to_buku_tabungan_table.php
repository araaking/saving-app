<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_tabungan', function (Blueprint $table) {
            $table->unique(
                ['tahun_ajaran_id', 'kelas_id', 'siswa_id', 'nomor_urut'],
                'buku_tabungan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('buku_tabungan', function (Blueprint $table) {
            $table->dropUnique('buku_tabungan_unique');
        });
    }
};
