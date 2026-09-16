@extends('layouts.app')

@section('title', 'Histori Belanja - ' . $pelanggan->nama_pelanggan)

@section('content')
<div class="container-fluid">
    <!-- Tombol Kembali & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ url('/pelanggan') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pelanggan
            </a>
            <h2 class="fw-bold text-dark m-0">
                <i class="bi bi-person-badge-fill me-2 text-primary"></i> Histori Belanja: {{ $pelanggan->nama_pelanggan }}
            </h2>
            <p class="text-secondary small m-0">Kategori: <strong>{{ $pelanggan->kategori }}</strong> | No. WA: <strong>{{ $pelanggan->no_wa ?? '-' }}</strong></p>
        </div>
    </div>

    <!-- TABEL REKAP HISTORI BELANJA PER NOTA -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i> Rincian Struk & Nota Pembelian</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Transaksi</th>
                            <th>Tanggal & Waktu</th>
                            <th>Daftar Barang Belanjaan</th>
                            <th>Total Belanja</th>
                            <th>Metode Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiBelanja as $kodeNota => $items)
                        <tr>
                            <td class="ps-4 fw-semibold">
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                    {{ $kodeNota ?? 'TRANSAKSI-MANUAL' }}
                                </span>
                            </td>
                            <td class="text-secondary small">{{ $items->first()->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <ul class="list-unstyled m-0 small">
                                    @foreach($items as $item)
                                        <li>
                                            <i class="bi bi-dot text-primary"></i> 
                                            {{-- Menampilkan nama barang dan menghitung harga satuan dari total_harga --}}
                                            <strong>{{ optional($item->barang)->nama_barang ?? 'Barang Tanpa Nama' }}</strong> 
                                            ({{ $item->jumlah ?? 0 }}x @ Rp {{ number_format(($item->total_harga ?? 0) / max($item->jumlah ?? 1, 1), 0, ',', '.') }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($items->sum('total_harga'), 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $items->first()->metode_pembayaran ?? 'Tunai' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-2 d-block mb-2"></i> Pelanggan ini belum memiliki catatan histori belanja.
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