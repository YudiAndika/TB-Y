@extends('layouts.app')

@section('title', 'Histori Rekap Barang Terjual')

@section('content')
<div class="container-fluid">
    <!-- Header & Tombol Kembali -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ url('/laporan') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Laporan Keuangan
            </a>
            <h2 class="fw-bold text-dark m-0">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i> Histori Rekap Barang Terjual
            </h2>
            <p class="text-secondary small m-0">Analisis mendalam performa produk berdasarkan rentang waktu mingguan, bulanan, dan tahunan.</p>
        </div>

        <!-- Tombol Filter Waktu -->
        <div class="btn-group shadow-sm" role="group">
            <a href="{{ url('/laporan/histori-barang?filter=minggu') }}" class="btn btn-outline-primary {{ request('filter') == 'minggu' ? 'active' : '' }}">Mingguan</a>
            <a href="{{ url('/laporan/histori-barang?filter=bulan') }}" class="btn btn-outline-primary {{ request('filter') == 'bulan' ? 'active' : '' }}">Bulanan</a>
            <a href="{{ url('/laporan/histori-barang?filter=tahun') }}" class="btn btn-outline-primary {{ request('filter') == 'tahun' ? 'active' : '' }}">Tahunan</a>
            <a href="{{ url('/laporan/histori-barang?filter=semua') }}" class="btn btn-outline-secondary {{ request('filter') == 'semua' || !request('filter') ? 'active' : '' }}">Semua</a>
        </div>
    </div>

    <!-- TABEL REKAPITULASI BARANG -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-boxes me-2 text-primary"></i> Akumulasi Penjualan Produk</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Barang / Material</th>
                            <th class="text-center">Total Terjual (Qty)</th>
                            <th class="text-end pe-4">Total Pendapatan Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangTerjual as $index => $item)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <!-- Menggunakan relasi barang -->
                                <div class="fw-bold text-dark">{{ $item->barang->nama_barang ?? 'Barang Tidak Ditemukan' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-bold">
                                    {{ $item->total_qty }} Unit / Pcs
                                </span>
                            </td>
                            <td class="text-end pe-4 fw-bold text-success">
                                Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada data penjualan pada rentang waktu ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection