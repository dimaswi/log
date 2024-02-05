<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_unit',
        'nomor',
        'nomor_permintaan',
        'tanggal',
        'status',
    ];

    public function permintaanItems(): HasMany
    {
        return $this->hasMany(PermintaanItems::class);
    }
}
