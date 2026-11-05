@extends('layouts.app')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')
    <div class="card" style="max-width: 600px;">
        <div class="card-header">{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</div>
        <div class="card-body">
            <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
                @csrf @if($category->exists) @method('PUT') @endif
                <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection