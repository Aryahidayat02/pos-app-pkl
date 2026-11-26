@if(auth()->user()->role === 'admin')
    <div class="d-flex justify-content-end gap-1">
        <a href="{{ route('products.edit', $product) }}" data-form-modal-url="{{ route('products.edit', $product) }}" data-form-modal-title="Edit Produk" class="btn btn-sm btn-outline-primary" aria-label="Edit produk {{ $product->name }}">
            <i class="bi bi-pencil"></i>
        </a>
        @if($product->sale_items_count > 0 || $product->purchase_items_count > 0)
            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Produk memiliki riwayat transaksi" aria-label="Produk tidak dapat dihapus">
                <i class="bi bi-trash"></i>
            </button>
        @else
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" data-delete-name="{{ $product->name }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus produk {{ $product->name }}">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        @endif
    </div>
@endif
