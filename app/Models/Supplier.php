<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke tabel pembelian (jika nanti dibutuhkan)
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}