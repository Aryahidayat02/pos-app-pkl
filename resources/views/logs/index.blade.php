@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('content')

<div class="card">
    <div class="card-header">Log Aktivitas Sistem</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="fw-semibold">{{ $log->user->name ?? 'Sistem' }}</td>
                    <td>
                        @if($log->action === 'update')
                            <span class="badge bg-warning text-dark">UPDATE</span>
                        @elseif($log->action === 'create')
                            <span class="badge bg-success">CREATE</span>
                        @elseif($log->action === 'delete')
                            <span class="badge bg-danger">DELETE</span>
                        @else
                            <span class="badge bg-secondary">{{ strtoupper($log->action) }}</span>
                        @endif
                    </td>
                    <td>{{ $log->description }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $logs->links() }}</div>

@endsection