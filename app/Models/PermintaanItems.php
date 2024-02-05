<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_id',
        'nama_barang',
        'jumlah',
        'harga',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanItems::class);
    }
}
