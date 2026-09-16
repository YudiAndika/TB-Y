<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    // Tambahkan 'kategori' ke dalam daftar ini
    protected $fillable = [
        'nama_barang',
        'barcode',
        'kategori', // <--- INI YANG TADI KETINGGALAN
        'harga_beli',
        'harga_jual',
        'stok'
    ];
}