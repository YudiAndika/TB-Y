@extends('layouts.app')
@section('title', 'Laporan Keuangan & Laba Rugi')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0">Laporan Keuangan & Riwayat Transaksi</h3>
        <p class="text-muted small m-0">Rekapitulasi penjualan terstruktur per transaksi untuk audit owner.</p>
    </div>
    
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Filter Berdasarkan Tanggal -->
        <form method="GET" action="/laporan" class="d-flex gap-2 align-items-center bg-white p-2 rounded-3 shadow-sm border m-0">
            <input type="date" name="dari_tanggal" value="{{ $dariTanggal ?? '' }}" class="form-control form-control-sm">
            <span class="text-muted">s/d</span>
            <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal ?? '' }}" class="form-control form-control-sm">
            <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            <a href="/laporan" class="btn btn-outline-secondary btn-sm">Reset</a>
        </form>

        <!-- TOMBOL CETAK / PDF -->
        <button onclick="window.print()" class="btn btn-dark btn-sm px-3 shadow-sm py-2">
            <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
        </button>
    </div>
</div>

<!-- Kartu Statistik Utama -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-4 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <small class="text-uppercase fw-semibold opacity-75">Total Omset</small>
            <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($totalOmset, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
            <small class="text-uppercase fw-semibold opacity-75">Total Modal Kulakan</small>
            <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($totalModal, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
            <small class="text-uppercase fw-semibold opacity-75">Laba Bersih (Profit)</small>
            <h3 class="fw-bold mb-0 mt-1">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

<!-- ================= START: REKAP METODE PEMBAYARAN ================= -->
<div class="card shadow-sm border-0 mb-4 mt-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-wallet2 me-2 text-primary"></i>Rincian Pendapatan per Metode Pembayaran
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="text-start ps-4">Metode Pembayaran</th>
                        <th>Jml. Transaksi</th>
                        <th>Omset (Kotor)</th>
                        <th>Laba Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPembayaran as $metode => $data)
                        <tr>
                            <td class="text-start ps-4 fw-bold">
                                @if($metode == 'Tunai')
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success"><i class="bi bi-cash"></i> {{ $metode }}</span>
                                @elseif($metode == 'Transfer')
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info"><i class="bi bi-bank"></i> {{ $metode }}</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary"><i class="bi bi-qr-code-scan"></i> {{ $metode }}</span>
                                @endif
                            </td>
                            <td>{{ count($data['jumlah_transaksi']) }} Nota</td>
                            <td class="text-primary fw-bold">Rp {{ number_format($data['omset'], 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($data['keuntungan'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted py-3">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- ================= END: REKAP METODE PEMBAYARAN ================= -->

<!-- Rekap Omset Per Bulan & Tahunan -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0"><i class="bi bi-bar-chart-line text-primary me-2"></i>Rekapitulasi Omset Per Bulan (Tahun {{ $tahunAktif }})</h5>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-bold">
                    Total Omset Tahun {{ $tahunAktif }}: Rp {{ number_format($totalOmsetSetahun, 0, ',', '.') }}
                </span>
            </div>
            <div class="card-body px-0 pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                @foreach($rekapBulanan as $rb)
                                    <th>{{ substr($rb['nama_bulan'], 0, 3) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($rekapBulanan as $rb)
                                    <td class="small fw-semibold text-dark">
                                        Rp {{ number_format($rb['omset'], 0, ',', '.') }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Ringkasan Akumulasi Barang Terjual (Praktis untuk Cek Gudang) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0 text-dark"><i class="bi bi-clipboard-data text-primary me-2"></i>Ringkasan Total Barang Terjual (Akumulasi Periode Ini)</h5>
        <a href="{{ url('/laporan/histori-barang') }}" class="btn btn-sm btn-success fw-bold text-decoration-none">
            <i class="bi bi-eye me-1"></i> Buka Histori Lengkap & Filter
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Barang</th>
                        <th>Total Qty Terjual</th>
                        <th class="pe-4 text-end">Total Pendapatan Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapBarangKeluar ?? [] as $rekap)
                    <tr>
                        <td class="ps-4 text-muted" style="width: 60px;">{{ $loop->iteration }}</td>
                        <td class="fw-semibold text-dark">{{ $rekap['nama_barang'] }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2">
                                {{ $rekap['total_terjual'] }} Unit / Pcs
                            </span>
                        </td>
                        <td class="pe-4 text-end fw-bold text-success">
                            Rp {{ number_format($rekap['total_pendapatan'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            Belum ada data barang terjual pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabel Riwayat Berdasarkan Struk / Transaksi -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3 border-0">
        <h5 class="fw-bold m-0"><i class="bi bi-journal-text text-primary me-2"></i>Daftar Riwayat Transaksi (Per Struk)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Kode Transaksi (Nota)</th>
                        <th>Jumlah Item</th>
                        <th>Total Belanja</th>
                        <th>Bayar / Kembali</th>
                        <th class="text-end pe-4 action-column">Aksi / Struk & Void</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatTransaksi as $kode => $items)
                    @php
                        $transaksiPertama = $items->first();
                        $totalStruk = $items->sum('total_harga');
                        $totalItemCount = $items->sum('jumlah');
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted small">
                            {{ $transaksiPertama->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace border px-2 py-1 fw-bold">
                                {{ $kode }}
                            </span>
                        </td>
                        <td><span class="fw-semibold">{{ $totalItemCount }} Pcs/Unit</span></td>
                        <td class="fw-bold text-primary">Rp {{ number_format($totalStruk, 0, ',', '.') }}</td>
                        <td class="small text-muted">
                            Bayar: Rp {{ number_format($transaksiPertama->uang_bayar ?? $totalStruk, 0, ',', '.') }} <br>
                            Kembali: Rp {{ number_format($transaksiPertama->uang_kembali ?? 0, 0, ',', '.') }}
                        </td>
                        
                        <!-- KOLOM AKSI: CETAK STRUK & TOMBOL VOID -->
                        <td class="text-end pe-4 action-column">
                            <div class="d-flex justify-content-end gap-1">
                                <!-- Tombol Cetak Struk -->
                                <a href="/kasir/struk/{{ $kode }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-printer me-1"></i> Cetak
                                </a>

                                <!-- TOMBOL VOID TRANSAKSI -->
                                <form action="/kasir/void/{{ $kode }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan (Void) nota {{ $kode }} ini? Stok barang akan dikembalikan ke gudang!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm fw-bold" title="Batalkan Nota">
                                        <i class="bi bi-x-octagon-fill me-1"></i> Void
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada riwayat transaksi pada rentang tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabel Audit Rincian Barang Keluar (Untuk Kontrol Stok Gudang) -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0 text-secondary"><i class="bi bi-box-arrow-right me-2"></i>Rincian Item Barang Keluar (Audit Stok)</h5>
        <small class="text-muted">Menampilkan seluruh kuantitas item yang terjual berdasarkan filter tanggal</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Kode Nota</th>
                        <th>Nama Barang</th>
                        <th>Terjual (Qty)</th>
                        <th>Harga Satuan</th>
                        <th class="pe-4 text-end">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semuaPenjualan as $p)
                    <tr>
                        <td class="ps-4 small text-muted">
                            {{ $p->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace border px-2 py-1">
                                {{ $p->kode_transaksi }}
                            </span>
                        </td>
                        <td class="fw-semibold text-dark">
                            {{ $p->barang->nama_barang ?? 'Barang Telah Dihapus' }}
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">
                                {{ $p->jumlah }} Unit
                            </span>
                        </td>
                        <td>Rp {{ number_format($p->total_harga / max($p->jumlah, 1), 0, ',', '.') }}</td>
                        <td class="pe-4 text-end fw-bold text-success">
                            Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Belum ada rincian barang keluar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= CSS KHUSUS CETAK / PDF ================= -->
<style>
    @media print {
        /* Sembunyikan Sidebar, Navbar, Tombol Filter, dan Kolom Aksi */
        .sidebar, .navbar, .btn, form, footer, .action-column {
            display: none !important;
        }

        /* Atur warna dan ukuran font agar bersih di kertas/PDF */
        body {
            background-color: white !important;
            color: black !important;
            font-size: 11pt;
        }

        /* Hilangkan bayangan card agar tampil datar rapi */
        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 15px !important;
        }

        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Pastikan tabel tetap jelas garisnya */
        .table {
            border-color: #dee2e6 !important;
        }
    }
</style>

@endsection