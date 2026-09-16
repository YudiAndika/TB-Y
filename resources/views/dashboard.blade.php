@extends('layouts.app')
@section('title', 'Dashboard Eksekutif')
@section('content')

<!-- Sapaan & Status (Sudah Responsif untuk HP) -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark m-0 fs-4 fs-md-3">Dashboard Eksekutif, Selamat Datang Owner! 👋</h3>
        <p class="text-muted small mt-1">Berikut adalah ringkasan operasional dan performa Toko Bangunan hari ini.</p>
    </div>
    <div class="d-flex w-100 w-md-auto gap-2">
        <a href="/kasir" class="btn btn-primary shadow-sm flex-fill flex-md-grow-0"><i class="bi bi-cart-plus me-1"></i> Buka Kasir</a>
        <a href="/laporan" class="btn btn-outline-secondary shadow-sm flex-fill flex-md-grow-0"><i class="bi bi-file-earmark-text me-1"></i> Laporan</a>
    </div>
</div>

<!-- Kartu Metrik Utama -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100 border-start border-primary border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Omset Hari Ini</small>
            <h4 class="fw-bold text-dark mt-2 mb-0 fs-5 fs-md-4">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}</h4>
            <span class="text-success small mt-1"><i class="bi bi-arrow-up-short"></i> Real-time hari ini</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100 border-start border-success border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Transaksi Berhasil</small>
            <h4 class="fw-bold text-dark mt-2 mb-0 fs-5 fs-md-4">{{ $transaksiHariIni }} Struk</h4>
            <span class="text-muted small mt-1">Nota tercetak hari ini</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100 border-start border-info border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Total Jenis Barang</small>
            <h4 class="fw-bold text-dark mt-2 mb-0 fs-5 fs-md-4">{{ $totalBarang }} Item</h4>
            <span class="text-muted small mt-1">Terdaftar di gudang</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 h-100 border-start border-warning border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Biaya Operasional (Bulan Ini)</small>
            <h4 class="fw-bold text-dark mt-2 mb-0 fs-5 fs-md-4">Rp {{ number_format($totalOperasionalBulanIni ?? 0, 0, ',', '.') }}</h4>
            <span class="text-warning-emphasis small mt-1"><i class="bi bi-wallet2"></i> Akumulasi bulan ini</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Peringatan Stok Kritis -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0 text-dark fs-6"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Peringatan Stok Menipis</h6>
                <a href="/barang" class="small text-decoration-none">Gudang &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="white-space: nowrap;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nama Barang</th>
                                <th>Kategori/Barcode</th>
                                <th class="text-end pe-3">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangKritis as $bk)
                            <tr>
                                <td class="ps-3 fw-semibold text-dark">{{ $bk->nama_barang }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $bk->barcode ?? '-' }}</span></td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-danger px-2 py-1">{{ $bk->stok }} Unit</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i> Aman! Tidak ada barang berstok kritis.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Aktivitas Transaksi Terakhir -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0 text-dark fs-6"><i class="bi bi-clock-history text-primary me-2"></i>Transaksi Terakhir</h6>
                <a href="/laporan" class="small text-decoration-none">Semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="white-space: nowrap;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Waktu</th>
                                <th>Kode Nota</th>
                                <th class="text-end pe-3">Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerakhir as $kode => $items)
                            @php $first = $items->first(); @endphp
                            <tr>
                                <td class="ps-3 small text-muted">{{ $first->created_at->format('H:i') }} WIB</td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $kode }}</span>
                                </td>
                                <td class="text-end pe-3 fw-bold text-success">
                                    Rp {{ number_format($items->sum('total_harga'), 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Belum ada transaksi hari ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Omset Bulanan -->
<div class="row mt-4 mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 border-0">
                <h6 class="fw-bold m-0 text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Statistik Tahun {{ $tahunAktif }}</h6>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 40vh; width: 100%;">
                    <canvas id="omsetChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('omsetChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [@foreach($rekapBulanan as $rb) '{!! substr($rb['nama_bulan'], 0, 3) !!}', @endforeach],
            datasets: [{
                label: 'Omset (Rp)',
                data: [@foreach($rekapBulanan as $rb) {{ $rb['omset'] }}, @endforeach],
                backgroundColor: '#2563eb',
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => 'Rp ' + value.toLocaleString() } }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>

@endsection