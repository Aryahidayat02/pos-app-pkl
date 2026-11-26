@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Edit Profil</div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-secondary-subtle d-inline-flex align-items-center justify-content-center overflow-hidden" style="width: 100px; height: 100px;" data-image-preview>
                            <img
                                src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : '' }}"
                                alt="Preview foto profil"
                                class="w-100 h-100 object-fit-cover"
                                data-image-preview-image
                                data-original-src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : '' }}"
                                @if(! auth()->user()->photo) hidden @endif
                            >
                            <span class="text-secondary" data-image-preview-placeholder @if(auth()->user()->photo) hidden @endif>
                                <i class="bi bi-person" style="font-size: 3rem;" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="profile-photo" class="form-label fw-bold">Foto Profil</label>
                        <input type="file" id="profile-photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png" data-image-preview-input>
                        <div class="form-text">Preview foto akan tampil setelah file dipilih.</div>
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Ganti Password</div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Lama</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Ganti Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection