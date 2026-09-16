<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'sales_person',
        'no_wa',
        'alamat',
        'keterangan',
    ];

    // Relasi ke tabel pembelian (jika nanti dibutuhkan)
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}