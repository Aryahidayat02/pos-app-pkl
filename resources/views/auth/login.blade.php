@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="text-center mb-4">
        <i class="bi bi-shop" style="font-size: 2.5rem; color: #0f766e;"></i>
        <h4 class="mt-2 fw-bold">POS App</h4>
        <p class="text-muted small">Silakan login untuk melanjutkan</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-1"></i>Login
        </button>
    </form>

    <div class="mt-4 text-center small text-muted">
        <div>Admin: <code>admin@pos.test</code> / <code>password</code></div>
        <div>Kasir: <code>kasir@pos.test</code> / <code>password</code></div>
    </div>
@endsection