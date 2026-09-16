<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_po',
        'supplier_id',
        'tanggal_pembelian',
        'total_biaya',
        'status_pembayaran',
        'catatan',
    ];

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke Detail Pembelian (Barang-barang yang dibeli)
    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}