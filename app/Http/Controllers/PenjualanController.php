<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AuditLog;
use App\Models\HoldCart;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $barangs = Barang::where('stok', '>', 0)->get();
        $keranjang = session()->get('keranjang', []);
        
        return view('kasir.index', compact('barangs', 'keranjang'));
    }

    public function addCart(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = null;

        if ($request->filled('barcode')) {
            $barang = Barang::where('barcode', $request->barcode)->first();
            if (!$barang) {
                return redirect()->back()->with('error', 'Barcode tidak dikenali di sistem!');
            }
        } elseif ($request->filled('barang_id')) {
            $barang = Barang::find($request->barang_id);
            if (!$barang) {
                return redirect()->back()->with('error', 'Barang tidak ditemukan!');
            }
        } else {
            return redirect()->back()->with('error', 'Silakan scan barcode atau pilih barang manual!');
        }

        $keranjang = session()->get('keranjang', []);
        $qtyDiKeranjang = isset($keranjang[$barang->id]) ? $keranjang[$barang->id]['jumlah'] : 0;
        $totalDiminta = $qtyDiKeranjang + $request->jumlah;

        if ($barang->stok < $totalDiminta) {
            $sisaBisaDitambah = max(0, $barang->stok - $qtyDiKeranjang);
            return redirect()->back()->with('error', 'Stok ' . $barang->nama_barang . ' tidak cukup! Stok gudang: ' . $barang->stok . ', sudah di keranjang: ' . $qtyDiKeranjang . ', sisa yang bisa diambil: ' . $sisaBisaDitambah);
        }

        if (isset($keranjang[$barang->id])) {
            $keranjang[$barang->id]['jumlah'] += $request->jumlah;
            
            $diskon = $keranjang[$barang->id]['diskon'] ?? 0;
            $hargaSetelahDiskon = $barang->harga_jual - $diskon;
            
            $keranjang[$barang->id]['total'] = $keranjang[$barang->id]['jumlah'] * $hargaSetelahDiskon;
        } else {
            // TARIK DISKON DEFAULT DARI GUDANG (MASTER BARANG)
            $diskonGudang = $barang->diskon ?? 0;
            $hargaSetelahDiskon = $barang->harga_jual - $diskonGudang;
            
            $keranjang[$barang->id] = [
                'nama_barang' => $barang->nama_barang,
                'jumlah' => $request->jumlah,
                'harga' => $barang->harga_jual,
                'diskon' => $diskonGudang, 
                'total' => $hargaSetelahDiskon * $request->jumlah
            ];
        }

        session()->put('keranjang', $keranjang);
        return redirect()->back()->with('success', $barang->nama_barang . ' masuk ke keranjang!');
    }

    public function hapusCart($id)
    {
        $keranjang = session()->get('keranjang');
        if (isset($keranjang[$id])) {
            unset($keranjang[$id]);
            session()->put('keranjang', $keranjang);
        }
        return redirect()->back()->with('success', 'Barang dihapus dari keranjang.');
    }

    public function updateDiskon(Request $request, $id)
    {
        $request->validate([
            'diskon' => 'nullable|numeric|min:0'
        ]);

        $keranjang = session()->get('keranjang');
        
        if (isset($keranjang[$id])) {
            $diskon = $request->diskon ?? 0;
            $keranjang[$id]['diskon'] = $diskon;
            
            $hargaSetelahDiskon = $keranjang[$id]['harga'] - $diskon;
            $keranjang[$id]['total'] = $hargaSetelahDiskon * $keranjang[$id]['jumlah'];
            
            session()->put('keranjang', $keranjang);
        }

        return redirect()->back()->with('success', 'Diskon berhasil diterapkan pada item!');
    }

    public function prosesBayar(Request $request)
    {
        $keranjang = session()->get('keranjang');
        if (!$keranjang) {
            return redirect()->back()->with('error', 'Keranjang belanja masih kosong!');
        }

        // --- VALIDASI SPLIT BILL ---
        $request->validate([
            'uang_bayar' => 'required|numeric|min:0',
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'metode_kedua' => 'nullable|string',
            'uang_bayar_kedua' => 'nullable|numeric|min:0',
        ]);

        $totalBelanja = array_sum(array_column($keranjang, 'total'));
        $kodeTransaksi = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $uangBayar1 = $request->uang_bayar ?? 0;
        $uangBayar2 = $request->uang_bayar_kedua ?? 0;

        // Gabungkan total uang yang dibayar dari metode 1 dan metode 2 (Split Bill)
        $totalUangDibayar = $uangBayar1 + $uangBayar2;

        // Gabungkan string metode pembayaran jika menggunakan 2 metode
        $metodeFinal = $request->metode_pembayaran;
        if ($uangBayar2 > 0 && !empty($request->metode_kedua)) {
            $metodeFinal = $request->metode_pembayaran . ' & ' . $request->metode_kedua;
        }

        // Logika Piutang, Lunas, atau Kembalian Berdasarkan Total Gabungan
        if ($totalUangDibayar >= $totalBelanja) {
            $uangKembali = $totalUangDibayar - $totalBelanja;
            $sisaPiutang = 0;
        } else {
            $uangKembali = 0;
            $sisaPiutang = $totalBelanja - $totalUangDibayar;
        }

        // ---> BUNGKUS SEMUA OPERASI DATABASE DALAM TRANSACTION <---
        // Jika ada error di tengah proses (misal barang ke-3 gagal),
        // semua perubahan (stok & penjualan) otomatis di-rollback.
        DB::beginTransaction();
        try {
            foreach ($keranjang as $id => $item) {
                $barang = Barang::find($id);

                // Validasi stok sekali lagi saat proses bayar (double check)
                if (!$barang || $barang->stok < $item['jumlah']) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Stok barang "' . ($barang->nama_barang ?? 'ID:'.$id) . '" tidak mencukupi saat proses pembayaran. Transaksi dibatalkan.');
                }

                $barang->stok -= $item['jumlah'];
                $barang->save();

                Penjualan::create([
                    'barang_id' => $id,
                    'jumlah' => $item['jumlah'],
                    'diskon' => $item['diskon'] ?? 0,
                    'total_harga' => $item['total'],
                    'kode_transaksi' => $kodeTransaksi,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_wa' => $request->no_wa,
                    'alamat' => $request->alamat,
                    'metode_pembayaran' => $metodeFinal,
                    'uang_bayar' => $totalUangDibayar,
                    'uang_kembali' => $uangKembali,
                    'sisa_piutang' => $sisaPiutang,
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses pembayaran. Transaksi dibatalkan. (' . $e->getMessage() . ')');
        }
        // ---> AKHIR TRANSACTION <---

        session()->forget('keranjang');

        // ---> AUDIT LOG <---
        if ($sisaPiutang > 0) {
            AuditLog::catat('Transaksi Kasir (Piutang/Split)', "Kasir memproses transaksi $kodeTransaksi sejumlah Rp " . number_format($totalBelanja, 0, ',', '.') . " (Sisa Piutang: Rp " . number_format($sisaPiutang, 0, ',', '.') . ")");
        } else {
            AuditLog::catat('Transaksi Kasir (Lunas/Split)', "Kasir memproses transaksi $kodeTransaksi sejumlah Rp " . number_format($totalBelanja, 0, ',', '.') . " secara Lunas.");
        }
        // -------------------------------

        $pesanStatus = $sisaPiutang > 0
            ? "Transaksi Berhasil Dicatat sebagai Piutang! Kurang: Rp " . number_format($sisaPiutang, 0, ',', '.')
            : "Pembayaran Berhasil Lunas!";

        return redirect()->back()->with('success', $pesanStatus)->with('kode_transaksi', $kodeTransaksi);
    }

    public function cetakStruk($kode)
    {
        $penjualans = Penjualan::with('barang')->where('kode_transaksi', $kode)->get();

        if ($penjualans->isEmpty()) {
            return abort(404, 'Transaksi tidak ditemukan');
        }

        $transaksi = $penjualans->first();
        $totalBelanja = $penjualans->sum('total_harga');

        return view('kasir.struk', compact('penjualans', 'transaksi', 'kode', 'totalBelanja'));
    }

    public function laporan(Request $request)
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
        
        $totalKeuntungan = $totalOmset - $totalModal;

        $rekapBarangKeluar = $semuaPenjualan->groupBy('barang_id')->map(function ($items) {
            $first = $items->first();
            return [
                'nama_barang' => $first->barang->nama_barang ?? 'Barang Dihapus',
                'total_terjual' => $items->sum('jumlah'),
                'total_pendapatan' => $items->sum('total_harga')
            ];
        })->sortByDesc('total_terjual');

        $tahunAktif = date('Y');
        
        $penjualanPerBulan = Penjualan::with('barang')
            ->whereYear('created_at', $tahunAktif)
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('m');
            });

        $rekapBulanan = [];
        $totalOmsetSetahun = 0;

        // PERULANGAN BULAN
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

        // --- MULAI REKAP PEMBAYARAN ---
        $rekapPembayaran = [];

        foreach ($semuaPenjualan as $p) {
            $metode = $p->metode_pembayaran ?? 'Tunai'; 
            
            if (!isset($rekapPembayaran[$metode])) {
                $rekapPembayaran[$metode] = [
                    'omset' => 0,
                    'modal' => 0,
                    'keuntungan' => 0,
                    'jumlah_transaksi' => [] 
                ];
            }

            $rekapPembayaran[$metode]['omset'] += $p->total_harga;
            $rekapPembayaran[$metode]['jumlah_transaksi'][$p->kode_transaksi] = true;

            if ($p->barang) {
                $modal = $p->barang->harga_beli * $p->jumlah;
                $rekapPembayaran[$metode]['modal'] += $modal;
                $rekapPembayaran[$metode]['keuntungan'] += ($p->total_harga - $modal);
            }
        }
        // --- SELESAI REKAP PEMBAYARAN ---

        return view('laporan.index', compact(
            'riwayatTransaksi', 
            'semuaPenjualan',
            'rekapBarangKeluar',
            'totalOmset', 
            'totalModal', 
            'totalKeuntungan', 
            'dariTanggal', 
            'sampaiTanggal',
            'rekapBulanan',
            'totalOmsetSetahun',
            'tahunAktif',
            'rekapPembayaran' 
        ));
    }

    public function indexPiutang()
    {
        $piutangs = Penjualan::where('sisa_piutang', '>', 0)->latest()->get()->groupBy('kode_transaksi');
        
        return view('kasir.piutang', compact('piutangs'));
    }

    public function lunasiPiutang($kode)
    {
        $penjualans = Penjualan::where('kode_transaksi', $kode)->get();
        
        if ($penjualans->isEmpty()) {
            return redirect()->back()->with('error', 'Data piutang tidak ditemukan!');
        }

        foreach ($penjualans as $p) {
            $p->uang_bayar = $p->uang_bayar + $p->sisa_piutang;
            $p->sisa_piutang = 0;
            $p->save();
        }

        // ---> PASANG CCTV AUDIT LOG <---
        AuditLog::catat('Pelunasan Piutang', "Piutang untuk nota $kode telah dilunasi.");
        // -------------------------------

        return redirect()->back()->with('success', 'Transaksi dengan nota ' . $kode . ' berhasil dilunasi!');
    }

    // --- FITUR HOLD KERANJANG ---
    public function holdCart(Request $request)
    {
        $keranjang = session()->get('keranjang', []);
        if (empty($keranjang)) {
            return redirect()->back()->with('error', 'Keranjang masih kosong, tidak ada yang bisa di-hold!');
        }

        $request->validate([
            'nama_antrean' => 'required|string|max:255'
        ]);

        HoldCart::create([
            'nama_antrean' => $request->nama_antrean,
            'isi_keranjang' => $keranjang
        ]);

        session()->forget('keranjang');

        return redirect()->back()->with('success', 'Transaksi berhasil ditunda (Hold)! Antrean siap dilanjutkan nanti.');
    }

    // --- FITUR RESUME KERANJANG ---
    public function resumeCart($id)
    {
        $hold = HoldCart::findOrFail($id);

        session()->put('keranjang', $hold->isi_keranjang);
        $hold->delete();

        return redirect()->back()->with('success', 'Antrean "' . $hold->nama_antrean . '" berhasil dipanggil kembali (Resume)!');
    }

    // --- FITUR VOID TRANSAKSI (BATAL NOTA) ---
    public function voidTransaksi($kode)
    {
        $penjualans = Penjualan::where('kode_transaksi', $kode)->get();

        if ($penjualans->isEmpty()) {
            return redirect()->back()->with('error', 'Nota transaksi tidak ditemukan!');
        }

        foreach ($penjualans as $p) {
            $barang = Barang::find($p->barang_id);
            if ($barang) {
                $barang->stok += $p->jumlah;
                $barang->save();
            }
            $p->delete();
        }

        AuditLog::catat('Void Transaksi', "Membatalkan/Void nota transaksi: {$kode} dan mengembalikan stok barang.");

        return redirect()->back()->with('success', 'Transaksi dengan nota ' . $kode . ' berhasil di-VOID (dibatalkan) dan stok barang telah dikembalikan.');
    }

    // --- FITUR REKAP BARANG TERJUAL ---
    public function rekapBarang(Request $request)
    {
        $tipe = $request->input('tipe', 'bulanan'); 
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = Penjualan::with('barang')->latest();

        // Logika Filter Waktu
        if ($tipe == 'bulanan') {
            $query->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
        } elseif ($tipe == 'mingguan') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($tipe == 'harian') {
            $query->whereDate('created_at', date('Y-m-d'));
        }
        // Jika 'semua', tidak ada filter where yang ditambahkan

        $penjualans = $query->get();

        // Kelompokkan data per barang, lalu per tanggal
        $rekapBarang = $penjualans->groupBy('barang_id')->map(function ($items) {
            return [
                'nama_barang' => $items->first()->barang->nama_barang ?? 'Barang Dihapus',
                'total_qty' => $items->sum('jumlah'),
                'total_pendapatan' => $items->sum('total_harga'),
                
                // Rincian per tanggal untuk barang ini
                'rincian_tanggal' => $items->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d');
                })->map(function($hari, $tanggal) {
                    return [
                        'tanggal' => \Carbon\Carbon::parse($tanggal)->format('d F Y'),
                        'qty' => $hari->sum('jumlah'),
                        'pendapatan' => $hari->sum('total_harga'),
                    ];
                })
            ];
        })->sortByDesc('total_qty'); // Urutkan dari yang paling laku

        return view('laporan.rekap_barang', compact('rekapBarang', 'tipe', 'bulan', 'tahun'));
    }

}