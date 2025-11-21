@extends('layouts.app')

@section('title', 'Detail Stok Masuk')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Stok Masuk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-ins.edit', $stockIn->id) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <a href="{{ route('stock-ins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Stok Masuk</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nomor Invoice</th>
                        <td>{{ $stockIn->invoice_number }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $stockIn->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Produk</th>
                        <td>{{ $stockIn->product->name }}</td>
                    </tr>
                    <tr>
                        <th>Quantity</th>
                        <td>{{ $stockIn->quantity }} {{ $stockIn->product->unit }}</td>
                    </tr>
                    <tr>
                        <th>Harga per Unit</th>
                        <td>Rp {{ number_format($stockIn->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>Rp {{ number_format($stockIn->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $stockIn->note ?? '-' }}</td>
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
                        <td>{{ $stockIn->product->code }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $stockIn->product->category->name }}</td>
                    </tr>
                    <tr>
                        <th>Stok Saat Ini</th>
                        <td>
                            <span class="badge bg-{{ $stockIn->product->stock_status_color }}">
                                {{ $stockIn->product->stock }} {{ $stockIn->product->unit }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>Rp {{ number_format($stockIn->product->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td>Rp {{ number_format($stockIn->product->selling_price, 0, ',', '.') }}</td>
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
                        <td>{{ $stockIn->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ ucfirst($stockIn->user->role) }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ $stockIn->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate Pada</th>
                        <td>{{ $stockIn->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection