@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Riwayat Transaksi</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="sales-table" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 300,
            ajax: @json(route('sales.index')),
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[2, 'desc']],
            columns: [
                { data: 'DT_RowIndex', name: 'sales.id', orderable: false, searchable: false },
                { data: 'invoice_number', name: 'sales.invoice_number' },
                { data: 'sale_date', name: 'sales.sale_date' },
                { data: 'cashier_name', name: 'cashier_name' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'total', name: 'sales.total', searchable: false },
                { data: 'status_display', name: 'sales.status', searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi',
                infoEmpty: 'Tidak ada transaksi',
                infoFiltered: '(disaring dari _MAX_ transaksi)',
                zeroRecords: 'Transaksi tidak ditemukan',
                emptyTable: 'Belum ada data transaksi',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });
    });
</script>
@endpush
