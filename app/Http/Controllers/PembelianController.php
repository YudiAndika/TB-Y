<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Supplier;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    // Menampilkan daftar riwayat pembelian / PO
    public function index()
    {
        $pembelians = Pembelian::with(['supplier', 'details.barang'])->latest()->get();
        return view('pembelian.index', compact('pembelians'));
    }

    // Halaman form tambah pembelian baru
    public function create()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();
        $kodePO = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        
        return view('pembelian.create', compact('suppliers', 'barangs', 'kodePO'));
    }

    // Menyimpan transaksi pembelian dan menambah stok barang
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pembelian' => 'required|date',
            'status_pembayaran' => 'required|string',
            'barangs' => 'required|array|min:1',
            'barangs.*.barang_id' => 'required|exists:barangs,id',
            'barangs.*.jumlah' => 'required|integer|min:1',
            'barangs.*.harga_beli' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $kodePO = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $totalBiaya = 0;

            foreach ($request->barangs as $item) {
                $totalBiaya += $item['jumlah'] * $item['harga_beli'];
            }

            // Simpan header pembelian
            $pembelian = Pembelian::create([
                'kode_po' => $kodePO,
                'supplier_id' => $request->supplier_id,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'total_biaya' => $totalBiaya,
                'status_pembayaran' => $request->status_pembayaran,
                'catatan' => $request->catatan,
            ]);

            // Simpan detail dan update stok barang di gudang
            foreach ($request->barangs as $item) {
                $subtotal = $item['jumlah'] * $item['harga_beli'];

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);

                // OTOMATIS TAMBAH STOK BARANG & UPDATE HARGA BELI TERBARU
                $barang = Barang::find($item['barang_id']);
                if ($barang) {
                    $barang->stok += $item['jumlah'];
                    $barang->harga_beli = $item['harga_beli']; // Update harga modal terbaru jika berubah
                    $barang->save();
                }
            }

            DB::commit();

            AuditLog::catat('Tambah Pembelian (PO)', "Membuat Purchase Order $kodePO sejumlah Rp " . number_format($totalBiaya, 0, ',', '.'));

            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil dicatat dan stok gudang bertambah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    // Menghapus data pembelian (opsional, mengembalikan stok jika dibatalkan)
    public function destroy($id)
    {
        $pembelian = Pembelian::with('details')->findOrFail(id);

        DB::beginTransaction();
        try {
            // Kembalikan stok gudang (dikurangi kembali)
            foreach ($pembelian->details as $detail) {
                $barang = Barang::find($detail->barang_id);
                if ($barang) {
                    $barang->stok -= $detail->jumlah;
                    if ($barang->stok < 0) $barang->stok = 0;
                    $barang->save();
                }
            }

            $pembelian->delete();
            DB::commit();

            AuditLog::catat('Hapus Pembelian', "Menghapus data PO {$pembelian->kode_po}");

            return redirect()->back()->with('success', 'Data pembelian berhasil dihapus dan stok gudang disesuaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}