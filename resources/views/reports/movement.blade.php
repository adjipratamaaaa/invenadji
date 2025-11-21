@extends('layouts.app')

@section('title', 'Laporan Pergerakan Stok')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Pergerakan Stok</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-success me-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Print
        </button>
        <button class="btn btn-primary" onclick="exportToExcel()">
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </button>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.movement') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('reports.movement') }}" class="btn btn-secondary">
                                <i class="fas fa-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="movementTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Stok Masuk</th>
                        <th>Stok Keluar</th>
                        <th>Tipe</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                    <tr>
                        <td>{{ $movement->date->format('d/m/Y') }}</td>
                        <td>{{ $movement->product->name }}</td>
                        <td>{{ $movement->product->category->name }}</td>
                        <td>
                            @if($movement->type == 'in')
                                <span class="badge bg-success">{{ $movement->in_quantity }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($movement->type == 'out')
                                <span class="badge bg-danger">{{ $movement->out_quantity }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($movement->type == 'in')
                                <span class="badge bg-success">MASUK</span>
                            @else
                                @if(isset($movement->stockOut))
                                    @if($movement->stockOut->type == 'sale')
                                        <span class="badge bg-info">PENJUALAN</span>
                                    @elseif($movement->stockOut->type == 'return')
                                        <span class="badge bg-warning">RETUR</span>
                                    @elseif($movement->stockOut->type == 'damage')
                                        <span class="badge bg-danger">RUSAK</span>
                                    @else
                                        <span class="badge bg-secondary">LAINNYA</span>
                                    @endif
                                @else
                                    <span class="badge bg-danger">KELUAR</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($movement->type == 'in')
                                {{ $movement->invoice_number }}
                            @else
                                {{ $movement->invoice_number }}
                            @endif
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
        $('#movementTable').DataTable({
            order: [[0, 'desc']],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

    function exportToExcel() {
        let table = document.getElementById('movementTable');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + escape(html);
        let link = document.createElement('a');
        link.href = url;
        link.download = 'laporan-pergerakan-stok-' + new Date().toISOString().split('T')[0] + '.xls';
        link.click();
    }
</script>
@endpush