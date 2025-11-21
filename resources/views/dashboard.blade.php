@extends('layouts.app')

@section('title', 'Dashboard - JIPARTS')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 mb-1">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard JIPARTS
        </h1>
        <p class="text-muted mb-0">Overview sistem inventory toko computer parts</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <span class="btn btn-outline-secondary">
                <i class="fas fa-calendar me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted text-uppercase">Total Produk</h6>
                        <h2 class="mb-0">{{ $stats['total_products'] }}</h2>
                        <small class="text-success">
                            <i class="fas fa-box me-1"></i>Items in inventory
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-boxes fs-1" style="color: #2c3e50;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted text-uppercase">Kategori</h6>
                        <h2 class="mb-0">{{ $stats['total_categories'] }}</h2>
                        <small class="text-info">
                            <i class="fas fa-tags me-1"></i>Product categories
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fs-1" style="color: #2c3e50;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted text-uppercase">Stok Masuk</h6>
                        <h2 class="mb-0">{{ $stats['total_stock_ins'] }}</h2>
                        <small class="text-warning">
                            <i class="fas fa-arrow-down me-1"></i>Total transactions
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-down fs-1" style="color: #2c3e50;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted text-uppercase">Stok Keluar</h6>
                        <h2 class="mb-0">{{ $stats['total_stock_outs'] }}</h2>
                        <small class="text-primary">
                            <i class="fas fa-arrow-up me-1"></i>Total transactions
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-up fs-1" style="color: #2c3e50;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($stats['low_stock_products'] > 0)
<div class="alert alert-warning alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Ada <strong>{{ $stats['low_stock_products'] }}</strong> produk dengan stok rendah!
    <a href="{{ route('products.index') }}" class="alert-link">Lihat detail</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Recent Stock Ins -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-arrow-down me-2"></i>Stok Masuk Terbaru
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recentStockIns) && $recentStockIns->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStockIns as $stockIn)
                                <tr>
                                    <td><code>{{ $stockIn->invoice_number }}</code></td>
                                    <td>{{ $stockIn->product->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">{{ $stockIn->quantity }}</span></td>
                                    <td>{{ $stockIn->date->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Belum ada stok masuk
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Stock Outs -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-arrow-up me-2"></i>Stok Keluar Terbaru
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recentStockOuts) && $recentStockOuts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStockOuts as $stockOut)
                                <tr>
                                    <td><code>{{ $stockOut->invoice_number }}</code></td>
                                    <td>{{ $stockOut->product->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-danger">{{ $stockOut->quantity }}</span></td>
                                    <td>{{ $stockOut->date->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-cash-register fa-2x mb-2 d-block"></i>
                        Belum ada stok keluar
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @can('gudang')
                    <div class="col-md-3">
                        <a href="{{ route('products.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>Tambah Produk
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('stock-ins.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-arrow-down me-2"></i>Stok Masuk
                        </a>
                    </div>
                    @endcan
                    
                    @can('kasir')
                    <div class="col-md-3">
                        <a href="{{ route('stock-outs.create') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-arrow-up me-2"></i>Stok Keluar
                        </a>
                    </div>
                    @endcan
                    
                    <div class="col-md-3">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-search me-2"></i>Cari Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection