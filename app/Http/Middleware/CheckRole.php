<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Cara pakai di routes:
     *   Route::middleware(['role:owner'])         → hanya owner
     *   Route::middleware(['role:owner,admin'])   → owner atau admin
     *   Route::middleware(['role:owner,admin,kasir']) → semua role
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Daftar role yang diizinkan (dari parameter middleware)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // 2. Ambil role user yang sedang login
        $userRole = auth()->user()->role;

        // 3. Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($userRole, $roles)) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
