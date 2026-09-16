@extends('layouts.app')
@section('title', 'Daftar Piutang Pelanggan')
@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold text-dark fs-4 fs-md-3"><i class="bi bi-journal-bookmark-fill me-2 text-warning"></i>Daftar Piutang</h3>
        <p class="text-muted small m-0">Kelola dan pantau sisa tagihan DP pelanggan.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <!-- text-nowrap untuk scroll di mobile -->
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 ps-md-4">Waktu Transaksi</th>
                        <th>Kode Nota</th>
                        <th>Nama Pelanggan</th>
                        <th>Total Tagihan</th>
                        <th>Dibayar (DP)</th>
                        <th>Sisa Piutang</th>
                        <th class="text-center pe-3 pe-md-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($piutangs as $kode => $items)
                        @php
                            $first = $items->first();
                            $totalTagihan = $items->sum('total_harga');
                            $totalDp = $first->uang_bayar;
                            $sisaTagihan = $first->sisa_piutang;
                        @endphp
                        <tr>
                            <td class="ps-3 ps-md-4 text-muted small">{{ $first->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace fw-bold">{{ $kode }}</span>
                            </td>
                            <td class="fw-bold text-dark">{{ $first->nama_pelanggan }}</td>
                            <td>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($totalDp, 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</td>
                            <td class="text-center pe-3 pe-md-4">
                                <a href="/kasir/struk/{{ $kode }}" target="_blank" class="btn btn-outline-secondary btn-sm me-1" title="Cetak Nota">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <form action="/piutang/lunasi/{{ $kode }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin melunasi nota {{ $kode }} ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm fw-bold">
                                        <i class="bi bi-check-circle me-1"></i> Lunasi
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted text-wrap">
                                <i class="bi bi-check2-all fs-1 text-success d-block mb-2 opacity-50"></i>
                                Bersih! Tidak ada piutang atau pelanggan yang belum lunas saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection