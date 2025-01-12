<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTabungan extends Model
{
    use HasFactory;

    protected $table = 'buku_tabungan';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'nomor_urut' // digunakan sebagai nomor buku tabungan
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
