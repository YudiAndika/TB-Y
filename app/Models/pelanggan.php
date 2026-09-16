<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    protected $fillable = ['nama_pelanggan', 'no_wa', 'alamat', 'kategori'];

    // Relasi ke Penjualan (Histori Belanja)
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'nama_pelanggan', 'nama_pelanggan');
    }
}