<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldCart extends Model
{
    use HasFactory;

    protected $fillable = ['nama_antrean', 'isi_keranjang'];

    protected $casts = [
        'isi_keranjang' => 'array', // Otomatis mengubah JSON kembali menjadi array PHP
    ];
}