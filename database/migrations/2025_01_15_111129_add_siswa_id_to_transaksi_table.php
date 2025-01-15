<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiswaIdToTransaksiTable extends Migration
{
    public function up()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn('siswa_id');
        });
    }
}
