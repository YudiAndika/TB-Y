<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Pengeluaran; // <--- JANGAN LUPA IMPORT MODEL PENGELUARAN
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Hari Ini
        $hariIni = Carbon::today();
        
        $penjualanHariIni = Penjualan::whereDate('created_at', $hariIni)->get();
        $omsetHariIni = $penjualanHariIni->sum('total_harga');
        $transaksiHariIni = $penjualanHariIni->groupBy('kode_transaksi')->count();

        // 2. Total Inventaris
        $totalBarang = Barang::count();

        // 3. Barang dengan Stok Kritis (Kurang dari atau sama dengan 5)
        $barangKritis = Barang::where('stok', '<=', 5)->orderBy('stok', 'asc')->get();

        // 4. Transaksi Terakhir (5 Transaksi Terkini)
        $transaksiTerakhir = Penjualan::with('barang')
            ->latest()
            ->take(5)
            ->get()
            ->groupBy('kode_transaksi');

        // 5. Statistik / Rekap Omset Per Bulan (Tahun Berjalan)
        $tahunAktif = date('Y');
        $penjualanPerBulan = Penjualan::with('barang')
            ->whereYear('created_at', $tahunAktif)
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('m');
            });

        $rekapBulanan = [];
        $totalOmsetSetahun = 0;

        for ($i = 1; $i <= 12; $i++) {
            $bulanKey = str_pad($i, 2, '0', STR_PAD_LEFT);
            $omsetBulanIni = 0;
            
            if (isset($penjualanPerBulan[$bulanKey])) {
                foreach ($penjualanPerBulan[$bulanKey] as $trx) {
                    $omsetBulanIni += $trx->total_harga;
                }
            }

            $rekapBulanan[$i] = [
                'nama_bulan' => date('F', mktime(0, 0, 0, $i, 10)),
                'omset' => $omsetBulanIni
            ];

            $totalOmsetSetahun += $omsetBulanIni;
        }

        // 6. Total Biaya Operasional Bulan Ini (Tambahan Modul Keuangan)
        $totalOperasionalBulanIni = Pengeluaran::whereMonth('tanggal_pengeluaran', date('m'))
            ->whereYear('tanggal_pengeluaran', $tahunAktif)
            ->sum('jumlah_biaya');

        return view('dashboard', compact(
            'omsetHariIni',
            'transaksiHariIni',
            'totalBarang',
            'barangKritis',
            'transaksiTerakhir',
            'rekapBulanan',
            'totalOmsetSetahun',
            'tahunAktif',
            'totalOperasionalBulanIni' // <--- DIKIRIM KE VIEW
        ));
    }
}