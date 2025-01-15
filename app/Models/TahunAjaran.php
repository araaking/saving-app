<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'is_active'
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function aktifkan()
    {
        // Nonaktifkan semua tahun ajaran terlebih dahulu
        self::query()->update(['is_active' => false]);
        
        // Aktifkan tahun ajaran ini
        $this->update(['is_active' => true]);
    }

    public function nonaktifkan()
    {
        $this->update(['is_active' => false]);
    }

    public function bukuTabungans()
    {
        return $this->hasMany(BukuTabungan::class);
    }
}
