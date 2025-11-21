@extends('layouts.app')

@section('title', 'Stok Masuk')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stok Masuk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('stock-ins.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Stok Masuk
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="stockInsTable">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Quantity</th>
                        <th>Harga</th>
                        <th>Total</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockIns as $stockIn)
                    <tr>
                        <td>{{ $stockIn->invoice_number }}</td>
                        <td>{{ $stockIn->date->format('d/m/Y') }}</td>
                        <td>{{ $stockIn->product->name }}</td>
                        <td>{{ $stockIn->quantity }} {{ $stockIn->product->unit }}</td>
                        <td>Rp {{ number_format($stockIn->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($stockIn->total, 0, ',', '.') }}</td>
                        <td>{{ $stockIn->user->name }}</td>
                        <td>
                            <a href="{{ route('stock-ins.show', $stockIn->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('stock-ins.edit', $stockIn->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('stock-ins.destroy', $stockIn->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus stok masuk ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#stockInsTable').DataTable({
            order: [[1, 'desc']]
        });
    });
</script>
@endpush