<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBukuTabunganTable extends Migration
{
    public function up()
    {
        Schema::create('buku_tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->integer('nomor_urut');
            $table->unique(['tahun_ajaran_id', 'siswa_id', 'nomor_urut']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buku_tabungan');
    }
}