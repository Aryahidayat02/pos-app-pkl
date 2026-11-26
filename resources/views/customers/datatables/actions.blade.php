<div>
    <!-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius -->
</div>
<div class="d-flex justify-content-end gap-1">
    <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#editCustomerModal"
        data-update-url="{{ route('customers.update', $customer) }}"
        data-name="{{ $customer->name }}"
        data-phone="{{ $customer->phone }}"
        data-email="{{ $customer->email }}"
        data-address="{{ $customer->address }}"
        aria-label="Edit pelanggan {{ $customer->name }}"
    >
        <i class="bi bi-pencil"></i>
    </button>
    @if(auth()->user()->role === 'admin')
        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" data-delete-name="{{ $customer->name }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus pelanggan {{ $customer->name }}">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif
</div>
