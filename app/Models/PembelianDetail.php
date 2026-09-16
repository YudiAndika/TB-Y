<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Master Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}