@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Stok Produk</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-success me-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Print
        </button>
        <button class="btn btn-primary" onclick="exportToExcel()">
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="stockReportTable">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Stok Min</th>
                        <th>Status Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Nilai Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalStockValue = 0;
                    @endphp
                    @foreach($products as $product)
                    @php
                        $stockValue = $product->stock * $product->purchase_price;
                        $totalStockValue += $stockValue;
                    @endphp
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->stock }} {{ $product->unit }}</td>
                        <td>{{ $product->min_stock }} {{ $product->unit }}</td>
                        <td>
                            @if($product->stock_status == 'out-of-stock')
                                <span class="badge bg-danger">Habis</span>
                            @elseif($product->stock_status == 'low-stock')
                                <span class="badge bg-warning">Rendah</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($stockValue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <td colspan="8" class="text-end"><strong>Total Nilai Stok:</strong></td>
                        <td><strong>Rp {{ number_format($totalStockValue, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($products->where('stock_status', 'low-stock')->count() > 0)
<div class="card mt-4 border-warning">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>Produk dengan Stok Rendah
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Minimum</th>
                        <th>Kekurangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products->where('stock_status', 'low-stock') as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->stock }} {{ $product->unit }}</td>
                        <td>{{ $product->min_stock }} {{ $product->unit }}</td>
                        <td class="text-danger">
                            {{ $product->min_stock - $product->stock }} {{ $product->unit }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#stockReportTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

    function exportToExcel() {
        // Simple Excel export (you can use more advanced libraries like SheetJS)
        let table = document.getElementById('stockReportTable');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + escape(html);
        let link = document.createElement('a');
        link.href = url;
        link.download = 'laporan-stok-' + new Date().toISOString().split('T')[0] + '.xls';
        link.click();
    }
</script>
@endpush