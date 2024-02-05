<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_transaksi',
        'nomor_kwitansi',
        'tanggal',
        'suplayer',
        'tipe_pembelian',
        'ppn',
        'diskon',
        'jatuh_tempo',
        'keterangan',
        'foto'
    ];

    public function fakturItems(): HasMany
    {
        return $this->hasMany(FakturItems::class);
    }
}
