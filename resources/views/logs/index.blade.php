@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Log Aktivitas</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            {{-- FORMAT WAKTU WIB --}}
                            <td class="ps-4">{{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
                            <td class="fw-bold">{{ $log->user->name ?? 'System' }}</td>
                            <td>
                                @if($log->action == 'create')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">Create</span>
                                @elseif($log->action == 'update')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Update</span>
                                @elseif($log->action == 'delete')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Delete</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION KECIL & BERFUNGSI --}}
    @if($logs->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} data
            </small>
            <div>
                {{ $logs->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
</div>
@endsection