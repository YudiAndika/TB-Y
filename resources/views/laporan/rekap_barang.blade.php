@extends('layouts.app')

@section('title', 'Rekap Barang Terjual')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark m-0"><i class="bi bi-box-seam text-primary me-2"></i> Histori Rekap Barang Terjual</h2>
            <p class="text-secondary small m-0">Analisis mendalam performa produk beserta rincian tanggal terjual.</p>
        </div>
    </div>

    <!-- KOTAK FILTER PENCARIAN -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('rekap.barang') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Rentang Waktu</label>
                    <select name="tipe" class="form-select" onchange="this.form.submit()">
                        <option value="semua" {{ $tipe == 'semua' ? 'selected' : '' }}>Semua Waktu (Keseluruhan)</option>
                        <option value="bulanan" {{ $tipe == 'bulanan' ? 'selected' : '' }}>Per Bulan</option>
                        <option value="mingguan" {{ $tipe == 'mingguan' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="harian" {{ $tipe == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                    </select>
                </div>
                
                @if($tipe == 'bulanan')
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Bulan</label>
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Tahun</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @for($t=date('Y'); $t>=date('Y')-3; $t--)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endfor
                    </select>
                </div>
                @endif
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold m-0 text-dark">Rincian Penjualan Produk</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Barang</th>
                            <th>Histori Tanggal Terjual</th>
                            <th class="text-center">Total Terjual (Qty)</th>
                            <th class="text-end pe-4">Total Pendapatan Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapBarang as $index => $barang)
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">{{ $loop->iteration }}</td>
                            <td class="fw-bold text-dark">{{ $barang['nama_barang'] }}</td>
                            <td>
                                <!-- Rincian Tanggal -->
                                <ul class="list-unstyled m-0 small">
                                    @foreach($barang['rincian_tanggal'] as $rincian)
                                        <li class="mb-1 border-bottom pb-1 border-light">
                                            <i class="bi bi-calendar2-check text-primary me-2"></i> 
                                            <strong>{{ $rincian['tanggal'] }}</strong> : Laku <span class="badge bg-secondary-subtle text-secondary">{{ $rincian['qty'] }} Pcs</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fs-6">
                                    {{ $barang['total_qty'] }} Pcs
                                </span>
                            </td>
                            <td class="text-end pe-4 fw-bold text-success fs-5">
                                Rp {{ number_format($barang['total_pendapatan'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i> Tidak ada barang terjual pada periode ini.
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