@extends('layouts.app')

@section('title', 'Detail Log Aktivitas')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Detail Log Aktivitas</h5>
            <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <div class="card-body">
            @include('logs.partials.detail-content')
        </div>
    </div>
@endsection
