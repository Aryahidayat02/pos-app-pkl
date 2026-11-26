@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Log Aktivitas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="activity-logs-table" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Waktu (WIB)</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="activityLogDetailModal" tabindex="-1" aria-labelledby="activityLogDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="activityLogDetailModalTitle">Detail Log Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="activityLogDetailContent" aria-live="polite">
                <div class="d-flex align-items-center gap-2 text-muted">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span>Memuat detail aktivitas...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#activity-logs-table').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 300,
            ajax: @json(route('logs.index')),
            pageLength: 15,
            lengthMenu: [[15, 25, 50], [15, 25, 50]],
            order: [[1, 'desc']],
            columns: [
                { data: 'DT_RowIndex', name: 'activity_logs.id', orderable: false, searchable: false },
                { data: 'created_at', name: 'activity_logs.created_at' },
                { data: 'user_name', name: 'user_name' },
                { data: 'action_display', name: 'activity_logs.action', searchable: false },
                { data: 'description', name: 'activity_logs.description' },
                { data: 'details_action', name: 'details_action', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ aktivitas',
                infoEmpty: 'Tidak ada aktivitas',
                infoFiltered: '(disaring dari _MAX_ aktivitas)',
                zeroRecords: 'Aktivitas tidak ditemukan',
                emptyTable: 'Belum ada aktivitas',
                paginate: { first: 'Awal', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Akhir' }
            }
        });

        const detailModal = document.getElementById('activityLogDetailModal');
        const detailContent = document.getElementById('activityLogDetailContent');
        let detailRequest;

        detailModal.addEventListener('show.bs.modal', async function(event) {
            detailRequest?.abort();

            const trigger = event.relatedTarget;
            const detailUrl = trigger instanceof HTMLElement ? trigger.dataset.detailUrl : null;

            if (!detailUrl) {
                detailContent.textContent = 'Tautan detail aktivitas tidak tersedia.';

                return;
            }

            detailRequest = new AbortController();
            detailContent.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Memuat detail aktivitas...</span></div>';

            try {
                const response = await fetch(detailUrl, {
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: detailRequest.signal
                });

                if (!response.ok) {
                    throw new Error(`Detail log request failed with status ${response.status}.`);
                }

                detailContent.innerHTML = await response.text();
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                console.error('Unable to load activity log details.', error);
                detailContent.textContent = 'Detail aktivitas gagal dimuat. Silakan coba lagi.';
            }
        });

        detailModal.addEventListener('hide.bs.modal', function() {
            detailRequest?.abort();
        });
    });
</script>
@endpush
