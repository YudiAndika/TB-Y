@extends('layouts.app')

@section('title', 'Audit Log Sistem')

@section('content')
<div class="container-fluid">
    <h4 class="fw-bold mb-4"><i class="bi bi-shield-check me-2 text-primary"></i>Audit Log & Riwayat Aktivitas</h4>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Waktu</th>
                            <th>Pengguna (User)</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                            <th class="pe-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="fw-bold">{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="badge bg-secondary">{{ $log->user->role ?? '-' }}</span></td>
                            <td>{{ $log->aktivitas }}</td>
                            <td class="pe-4 text-muted">{{ $log->deskripsi ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada catatan aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection