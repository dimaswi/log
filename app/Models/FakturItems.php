<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FakturItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'harga_id',
        'nama_barang',
        'harga_lama',
        'jumlah',
        'harga_baru',
    ];

    public function faktur(): BelongsTo
    {
        return $this->belongsTo(FakturItems::class);
    }
}
