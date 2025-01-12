<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    public function up()
{
    Schema::create('transaksi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('buku_tabungan_id')->constrained('buku_tabungan')->onDelete('cascade');
        $table->enum('jenis', ['simpanan', 'penarikan', 'cicilan']);
        $table->decimal('jumlah', 15, 2);
        $table->timestamp('tanggal')->useCurrent(); // Changed to timestamp and set default to current timestamp
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}