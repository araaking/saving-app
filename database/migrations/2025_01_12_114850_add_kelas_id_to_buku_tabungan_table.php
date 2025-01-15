<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_tabungan', function (Blueprint $table) {
            // Tambahkan kolom nullable terlebih dahulu
            $table->foreignId('kelas_id')->after('tahun_ajaran_id')
                  ->nullable()
                  ->constrained('kelas')
                  ->onDelete('cascade');
        });

        // Update data yang ada dengan kelas_id default
        DB::table('buku_tabungan')->update(['kelas_id' => DB::raw('(SELECT kelas_id FROM siswa WHERE siswa.id = buku_tabungan.siswa_id)')]);

        // Ubah kolom menjadi not nullable setelah data diupdate
        Schema::table('buku_tabungan', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('buku_tabungan', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
