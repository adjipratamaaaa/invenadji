@extends('layouts.app')

@section('title', 'Detail Stok Keluar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Stok Keluar</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-outs.edit', $stockOut->id) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Stok Keluar</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nomor Invoice</th>
                        <td>{{ $stockOut->invoice_number }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $stockOut->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Produk</th>
                        <td>{{ $stockOut->product->name }}</td>
                    </tr>
                    <tr>
                        <th>Quantity</th>
                        <td>{{ $stockOut->quantity }} {{ $stockOut->product->unit }}</td>
                    </tr>
                    <tr>
                        <th>Harga per Unit</th>
                        <td>Rp {{ number_format($stockOut->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>Rp {{ number_format($stockOut->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tipe</th>
                        <td>
                            @if($stockOut->type == 'sale')
                                <span class="badge bg-success">Penjualan</span>
                            @elseif($stockOut->type == 'return')
                                <span class="badge bg-warning">Retur</span>
                            @elseif($stockOut->type == 'damage')
                                <span class="badge bg-danger">Rusak</span>
                            @else
                                <span class="badge bg-info">Lainnya</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $stockOut->note ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Produk</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Kode Produk</th>
                        <td>{{ $stockOut->product->code }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $stockOut->product->category->name }}</td>
                    </tr>
                    <tr>
                        <th>Stok Saat Ini</th>
                        <td>
                            <span class="badge bg-{{ $stockOut->product->stock_status_color }}">
                                {{ $stockOut->product->stock }} {{ $stockOut->product->unit }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>Rp {{ number_format($stockOut->product->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td>Rp {{ number_format($stockOut->product->selling_price, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi User</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Dibuat Oleh</th>
                        <td>{{ $stockOut->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ ucfirst($stockOut->user->role) }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ $stockOut->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate Pada</th>
                        <td>{{ $stockOut->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection