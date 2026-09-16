<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'aktivitas', 'deskripsi'];

    // Fungsi cepat untuk mencatat aktivitas
    public static function catat($aktivitas, $deskripsi = null)
    {
        if (Auth::check()) {
            self::create([
                'user_id' => Auth::id(),
                'aktivitas' => $aktivitas,
                'deskripsi' => $deskripsi,
            ]);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
