@if($user->is_active)
    <span class="badge bg-success bg-opacity-10 text-success border border-success">Aktif</span>
@else
    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Nonaktif</span>
@endif
