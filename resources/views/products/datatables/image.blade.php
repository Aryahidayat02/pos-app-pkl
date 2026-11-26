@if($product->image)
    <img src="{{ asset('storage/'.$product->image) }}" class="rounded" width="40" height="40" alt="Gambar {{ $product->name }}" style="object-fit: cover;">
@else
    <span class="bg-light rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <i class="bi bi-box text-muted" aria-hidden="true"></i>
        <span class="visually-hidden">Tidak ada gambar</span>
    </span>
@endif
