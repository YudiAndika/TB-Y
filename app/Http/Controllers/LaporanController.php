<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        $query = Penjualan::with('barang')->latest();

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereBetween('created_at', [$dariTanggal . ' 00:00:00', $sampaiTanggal . ' 23:59:59']);
        }

        $semuaPenjualan = $query->get();
        $riwayatTransaksi = $semuaPenjualan->groupBy('kode_transaksi');

        $totalOmset = 0;
        $totalModal = 0;

        foreach ($semuaPenjualan as $p) {
            if ($p->barang) {
                $totalOmset += $p->total_harga;
                $totalModal += $p->barang->harga_beli * $p->jumlah;
            }
        }
        
        // Laba bersih murni dari perputaran barang toko
        $totalKeuntungan = $totalOmset - $totalModal;

        return view('laporan.index', compact(
            'riwayatTransaksi', 
            'totalOmset', 
            'totalModal', 
            'totalKeuntungan', 
            'dariTanggal', 
            'sampaiTanggal'
        ));
    }

    // Menampilkan tab khusus Histori Barang Terjual dengan filter waktu
    public function historiBarang(Request $request)
    {
        $filter = $request->get('filter', 'semua');
        $query = Penjualan::with('barang');

        if ($filter == 'minggu') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter == 'bulan') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($filter == 'tahun') {
            $query->whereYear('created_at', now()->year);
        }

        $barangTerjual = $query->select('barang_id', \DB::raw('sum(jumlah) as total_qty'), \DB::raw('sum(total_harga) as total_pendapatan'))
                            ->groupBy('barang_id')
                            ->orderBy('total_qty', 'desc')
                            ->get();

        return view('laporan.histori-barang', compact('barangTerjual', 'filter'));
    }
}