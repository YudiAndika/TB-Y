@extends('layouts.app')

@section('title', 'Riwayat Pembelian Stok')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0"><i class="bi bi-cart-plus-fill text-success me-2"></i> Purchase Order & Pembelian Stok</h2>
            <p class="text-secondary small m-0">Kelola catatan belanja stok barang dari supplier yang otomatis masuk ke gudang.</p>
        </div>
        <a href="{{ route('pembelian.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Buat Pembelian Baru (PO)
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Daftar Barang Masuk</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembelians as $pembelian)
                        <tr>
                            <td class="ps-4 fw-semibold">
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace">{{ $pembelian->kode_po }}</span>
                            </td>
                            <td class="text-secondary small">{{ date('d/m/Y', strtotime($pembelian->tanggal_pembelian)) }}</td>
                            <td class="fw-bold text-dark">{{ optional($pembelian->supplier)->nama_supplier ?? 'Supplier Dihapus' }}</td>
                            <td>
                                <ul class="list-unstyled m-0 small">
                                    @foreach($pembelian->details as $detail)
                                        <li><i class="bi bi-dot text-success"></i> <strong>{{ optional($detail->barang)->nama_barang ?? 'Barang' }}</strong> ({{ $detail->jumlah }}x @ Rp {{ number_format($detail->harga_beli, 0, ',', '.') }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="fw-bold text-success">Rp {{ number_format($pembelian->total_biaya, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $pembelian->status_pembayaran == 'Lunas' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                    {{ $pembelian->status_pembayaran }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('pembelian.destroy', $pembelian->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pembelian ini? Stok gudang akan dikurangi kembali sesuai jumlah pembelian.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan / Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2"></i> Belum ada riwayat pembelian stok.
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