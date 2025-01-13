<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa; // Pastikan import model Siswa

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = ['nama'];

    // Ubah nama method dari siswas() ke siswa() supaya view dapat mengakses $item->siswa
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
