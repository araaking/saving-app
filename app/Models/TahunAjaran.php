<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'year_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected static function booted()
    {
        static::saving(function ($tahunAjaran) {
            if ($tahunAjaran->is_active) {
                $tahunAjaran->deactivateOthers();
            }
        });
    }

    public function deactivateOthers(): void
    {
        if ($this->is_active) {
            self::query()
                ->where('id', '!=', $this->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }
    }

    public function bukuTabungans()
    {
        return $this->hasMany(BukuTabungan::class);
    }
}
