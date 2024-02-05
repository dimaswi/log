<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fakturs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi');
            $table->string('nomor_kwitansi');
            $table->string('tanggal');
            $table->string('suplayer');
            $table->string('tipe_pembelian');
            $table->string('ppn');
            $table->string('diskon');
            $table->string('jatuh_tempo');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakturs');
    }
};
