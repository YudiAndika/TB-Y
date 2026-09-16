@extends('layouts.app')

@section('title', 'Menu Utama')

@section('content')
<style>
    .icon-shortcut {
        width: 100%;
        min-height: 110px;
        border-radius: 12px;
        transition: all 0.2s ease;
        text-decoration: none;
        color: #475569;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 15px 10px;
        background: #ffffff;
    }
    .icon-shortcut:hover {
        background-color: #f1f5f9;
        transform: translateY(-4px);
        color: #4f46e5;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .icon-shortcut i {
        font-size: 2.5rem;
        margin-bottom: 8px;
    }
    .icon-shortcut span {
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
    }
    .icon-pos {
        border: 2px solid #4f46e5;
        background-color: #e0e7ff;
    }
    .icon-pos:hover {
        background-color: #c7d2fe;
    }
</style>

<div class="container-fluid">
    <h5 class="text-secondary mb-4 border-bottom pb-2 fs-5 fs-md-4">Jalan Pintas Menu Utama</h5>
    
    <div class="row g-3">
        <!-- AUDIT LOG SHORTCUT -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-auto" style="min-width: 130px;">
            <a href="/audit-log" class="icon-shortcut shadow-sm border">
                <i class="bi bi-shield-check text-warning"></i>
                <span>Audit Log</span>
            </a>
        </div>

        <!-- F4 POS -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-auto" style="min-width: 130px;">
            <a href="/kasir" class="icon-shortcut icon-pos shadow-sm">
                <i class="bi bi-pc-display text-primary"></i>
                <span>F4 POS</span>
            </a>
        </div>

        <!-- INVENTARIS STOK -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-auto" style="min-width: 130px;">
            <a href="/barang" class="icon-shortcut shadow-sm border">
                <i class="bi bi-boxes text-warning"></i>
                <span>Stok Barang</span>
            </a>
        </div>

        <!-- DATA PIUTANG -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-auto" style="min-width: 130px;">
            <a href="/piutang" class="icon-shortcut shadow-sm border">
                <i class="bi bi-journal-bookmark text-danger"></i>
                <span>Data Piutang</span>
            </a>
        </div>

        <!-- LAPORAN -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-auto" style="min-width: 130px;">
            <a href="/laporan" class="icon-shortcut shadow-sm border">
                <i class="bi bi-file-earmark-bar-graph text-success"></i>
                <span>Laporan</span>
            </a>
        </div>
    </div>
</div>
@endsection