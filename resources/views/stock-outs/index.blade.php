@extends('layouts.app')

@section('title', 'Daftar Stok Keluar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Daftar Stok Keluar</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-outs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Stok Keluar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Quantity</th>
                        <th>Harga/Unit</th>
                        <th>Total</th>
                        <th>Tipe</th>
                        <th>Catatan</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $stockOut)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $stockOut->invoice_number }}</strong>
                        </td>
                        <td>{{ $stockOut->date->format('d/m/Y') }}</td>
                        <td>
                            {{ $stockOut->product->name }}<br>
                            <small class="text-muted">{{ $stockOut->product->code }}</small>
                        </td>
                        <td class="text-end">
                            <span class="text-danger fw-bold">-{{ $stockOut->quantity }}</span>
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($stockOut->price, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($stockOut->quantity * $stockOut->price, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($stockOut->type == 'sale')
                                <span class="badge bg-success">Penjualan</span>
                            @elseif($stockOut->type == 'return')
                                <span class="badge bg-warning text-dark">Retur</span>
                            @elseif($stockOut->type == 'damage')
                                <span class="badge bg-danger">Rusak</span>
                            @else
                                <span class="badge bg-secondary">Lainnya</span>
                            @endif
                        </td>
                        <td>{{ $stockOut->note ?? '-' }}</td>
                        <td>
                            <small>{{ $stockOut->user->name }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('stock-outs.show', $stockOut->id) }}" 
                                   class="btn btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('stock-outs.edit', $stockOut->id) }}" 
                                   class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stock-outs.destroy', $stockOut->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Hapus stok keluar ini?')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                            Belum ada data stok keluar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Info jumlah data --}}
@if($stockOuts->count() > 0)
<div class="mt-3 text-muted">
    Total: {{ $stockOuts->count() }} data stok keluar
</div>
@endif
    </div>
</div>
@endsection