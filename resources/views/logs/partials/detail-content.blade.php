<dl class="row mb-4">
    <dt class="col-sm-3">Waktu</dt>
    <dd class="col-sm-9">{{ $activityLog->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }} WIB</dd>
    <dt class="col-sm-3">Admin</dt>
    <dd class="col-sm-9">{{ $activityLog->user?->name ?? 'System' }}</dd>
    <dt class="col-sm-3">Aksi</dt>
    <dd class="col-sm-9">{{ ucfirst($activityLog->action) }}</dd>
    <dt class="col-sm-3">Data</dt>
    <dd class="col-sm-9">{{ $activityLog->model }}{{ $activityLog->model_id ? ' #'.$activityLog->model_id : '' }}</dd>
    <dt class="col-sm-3">Ringkasan</dt>
    <dd class="col-sm-9">{{ $activityLog->description ?: 'Tidak ada ringkasan.' }}</dd>
</dl>

@php($details = $activityLog->details ?? [])
@foreach(['before' => 'Sebelum perubahan', 'after' => 'Sesudah perubahan'] as $key => $heading)
    @if(!empty($details[$key]))
        <h6 class="fw-bold">{{ $heading }}</h6>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr><th>Kolom</th><th>Nilai</th></tr>
                </thead>
                <tbody>
                    @foreach($details[$key] as $attribute => $value)
                        <tr>
                            <th>{{ \Illuminate\Support\Str::headline($attribute) }}</th>
                            <td class="text-break">{{ is_null($value) ? '—' : (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach

@if(empty($details['before']) && empty($details['after']))
    <p class="text-muted mb-0">Detail input tidak tersedia untuk aktivitas lama ini.</p>
@endif
