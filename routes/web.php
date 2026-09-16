
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenjualanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PiutangController;
use App\Models\AuditLog;
use App\Http\Controllers\PelangganController;


// ============================================================================
// RUTE AWAL
// ============================================================================

// Rute awal saat buka aplikasi (Diarahkan ke login)
Route::get('/', function () {
    return redirect('/login');
});


// ============================================================================
// RUTE UTAMA APLIKASI
// Dilindungi Middleware Auth (Wajib Login)
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {


    // =========================================================================
    // 1. AKSES UMUM
    // Bisa dibuka oleh: Owner, Admin, Kasir
    // =========================================================================

    Route::middleware(['role:owner,admin,kasir'])->group(function () {

        // ---------------------------------------------------------------------
        // Dashboard & Home
        // ---------------------------------------------------------------------

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/home', function () {
            return view('home');
        });


        // ---------------------------------------------------------------------
        // Kasir POS Utama
        // Keranjang, Pembayaran, Struk, Hold / Resume
        // ---------------------------------------------------------------------

        Route::get('/kasir', [PenjualanController::class, 'index']);

        Route::post('/kasir/add-cart', [PenjualanController::class, 'addCart']);

        Route::get('/kasir/hapus/{id}', [PenjualanController::class, 'hapusCart']);

        Route::post('/kasir/bayar', [PenjualanController::class, 'prosesBayar']);

        Route::get('/kasir/struk/{kode}', [PenjualanController::class, 'cetakStruk']);

        Route::post('/kasir/update-diskon/{id}', [PenjualanController::class, 'updateDiskon']);

        Route::post('/kasir/hold', [PenjualanController::class, 'holdCart']);

        Route::get('/kasir/resume/{id}', [PenjualanController::class, 'resumeCart']);


        // ---------------------------------------------------------------------
        // Profile Bawaan Laravel Breeze
        // ---------------------------------------------------------------------

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });



    // =========================================================================
    // 2. AKSES MANAJEMEN
    // Hanya bisa dibuka oleh: Owner, Admin
    // =========================================================================

    Route::middleware(['role:owner,admin'])->group(function () {


        // ---------------------------------------------------------------------
        // Barang / Inventaris Gudang & Barcode
        // ---------------------------------------------------------------------

        Route::get('/barang', [BarangController::class, 'index']);

        Route::post('/barang', [BarangController::class, 'store']);

        Route::get('/barang/{id}/edit', [BarangController::class, 'edit']);

        Route::put('/barang/{id}', [BarangController::class, 'update']);

        Route::delete('/barang/{id}', [BarangController::class, 'destroy']);

        // FIX: Kurang tanda ] pada kode sebelumnya
        Route::get('/barang/barcode/{id}', [BarangController::class, 'cetakBarcode']);


        // ---------------------------------------------------------------------
        // Data Piutang & Pelunasan Piutang
        // ---------------------------------------------------------------------

        Route::get('/piutang', [PenjualanController::class, 'indexPiutang']);

        Route::post('/piutang/lunasi/{kode}', [PenjualanController::class, 'lunasiPiutang']);


        // ---------------------------------------------------------------------
        // Void Transaksi / Pembatalan Nota
        // ---------------------------------------------------------------------

        Route::delete('/kasir/void/{kode}', [PenjualanController::class, 'voidTransaksi']);
    });



    // =========================================================================
    // 3. AKSES SANGAT RAHASIA
    // Hanya bisa dibuka oleh: Owner
    // =========================================================================

    Route::middleware(['role:owner'])->group(function () {


        // ---------------------------------------------------------------------
        // Laporan Keuangan
        // ---------------------------------------------------------------------

        Route::get('/laporan', [PenjualanController::class, 'laporan']);
// ---> TAMBAHKAN RUTE REKAP BARANG DI SINI <---
        Route::get('/rekap-barang', [App\Http\Controllers\PenjualanController::class, 'rekapBarang'])->name('rekap.barang');

        // ---------------------------------------------------------------------
        // Data Supplier (Modul 5)
        // ---------------------------------------------------------------------
        Route::get('/supplier', [App\Http\Controllers\SupplierController::class, 'index'])->name('supplier.index');
        Route::post('/supplier', [App\Http\Controllers\SupplierController::class, 'store'])->name('supplier.store');
        Route::put('/supplier/{id}', [App\Http\Controllers\SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{id}', [App\Http\Controllers\SupplierController::class, 'destroy'])->name('supplier.destroy');
        
        // ---------------------------------------------------------------------
        // Pembelian Stok / Purchase Order (Modul 5)
        // ---------------------------------------------------------------------

        Route::get('/pembelian', [App\Http\Controllers\PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('/pembelian/create', [App\Http\Controllers\PembelianController::class, 'create'])->name('pembelian.create');
        Route::post('/pembelian', [App\Http\Controllers\PembelianController::class, 'store'])->name('pembelian.store');
        Route::delete('/pembelian/{id}', [App\Http\Controllers\PembelianController::class, 'destroy'])->name('pembelian.destroy');

        // ---------------------------------------------------------------------
        // Biaya Operasional / Pengeluaran Toko (Modul 6)
        // ---------------------------------------------------------------------
        Route::get('/pengeluaran', [App\Http\Controllers\PengeluaranController::class, 'index'])->name('pengeluaran.index');
        Route::post('/pengeluaran', [App\Http\Controllers\PengeluaranController::class, 'store'])->name('pengeluaran.store');
        Route::put('/pengeluaran/{id}', [App\Http\Controllers\PengeluaranController::class, 'update'])->name('pengeluaran.update');
        Route::delete('/pengeluaran/{id}', [App\Http\Controllers\PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

        // ---------------------------------------------------------------------
        // Histori Barang
        // Hanya bisa diakses oleh OWNER
        // ---------------------------------------------------------------------

        Route::get(
            '/laporan/histori-barang',
            [LaporanController::class, 'historiBarang']
        )->name('laporan.histori-barang');


        // ---------------------------------------------------------------------
        // DATABASE PELANGGAN & HISTORI BELANJA
        // Hanya Owner
        // ---------------------------------------------------------------------

        Route::get('/pelanggan', [PelangganController::class, 'index']);

        Route::post('/pelanggan', [PelangganController::class, 'store']);

        Route::get('/pelanggan/{id}', [PelangganController::class, 'show']);

        Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy']);


        // ---------------------------------------------------------------------
        // Audit Log (CCTV Sistem)
        // Hanya Owner
        // ---------------------------------------------------------------------

        Route::get('/audit-log', function () {

            $logs = AuditLog::with('user')->latest()->get();

            return view('audit.index', compact('logs'));

        });
    });

});


// ============================================================================
// RUTE AUTENTIKASI
// Login, Register, Forgot Password, dll.
// ============================================================================

require __DIR__.'/auth.php';
