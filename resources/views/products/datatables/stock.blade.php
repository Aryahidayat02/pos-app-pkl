<span class="{{ $product->stock <= $product->min_stock ? 'text-danger fw-bold' : '' }}">
    {{ $product->stock }} {{ $product->unit }}
</span>
