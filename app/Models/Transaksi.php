<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'transaksi';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'buku_tabungan_id',
        'jenis',
        'jumlah',
        'tanggal',
        'keterangan'
    ];

    // Casting kolom ke tipe data tertentu
    protected $casts = [
        'tanggal' => 'datetime', // Mengubah kolom 'tanggal' ke tipe datetime
        'jumlah' => 'decimal:2',  // Pastikan kolom 'jumlah' di-handle sebagai decimal 2 digit
    ];

    // Relasi ke model BukuTabungan
    public function bukuTabungan()
    {
        return $this->belongsTo(BukuTabungan::class, 'buku_tabungan_id');
    }
}
