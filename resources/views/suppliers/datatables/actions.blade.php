<div>
    <!-- Order your soul. Reduce your wants. - Augustine -->
</div>
<div class="d-flex justify-content-end gap-1">
    <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#editSupplierModal"
        data-update-url="{{ route('suppliers.update', $supplier) }}"
        data-name="{{ $supplier->name }}"
        data-contact-person="{{ $supplier->contact_person }}"
        data-phone="{{ $supplier->phone }}"
        data-address="{{ $supplier->address }}"
        aria-label="Edit supplier {{ $supplier->name }}"
    >
        <i class="bi bi-pencil"></i>
    </button>
    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" data-delete-name="{{ $supplier->name }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus supplier {{ $supplier->name }}">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
