<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    // Daftar kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'barang_id',
        'jumlah',
        'diskon',
        'total_harga',
        'kode_transaksi',
        'nama_pelanggan',
        'no_wa',
        'alamat',
        'metode_pembayaran',
        'uang_bayar',
        'uang_kembali',
        'sisa_piutang',
    ];

    // Menghubungkan transaksi dengan data barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}