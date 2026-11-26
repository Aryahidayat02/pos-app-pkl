@if($log->action === 'create')
    <span class="badge bg-success bg-opacity-10 text-success border border-success">Create</span>
@elseif($log->action === 'update')
    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Update</span>
@elseif($log->action === 'delete')
    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Delete</span>
@else
    <span class="badge bg-secondary">{{ $log->action }}</span>
@endif
