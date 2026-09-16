<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    // Membuka gembok keamanan agar form bisa menyimpan data
    protected $guarded = [];

    // Menghubungkan transaksi dengan data barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}