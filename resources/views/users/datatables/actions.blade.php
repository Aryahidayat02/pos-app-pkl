<div class="d-flex justify-content-end gap-1">
    <a href="{{ route('users.edit', $user) }}" data-form-modal-url="{{ route('users.edit', $user) }}" data-form-modal-title="Edit User" class="btn btn-sm btn-outline-primary" aria-label="Edit user {{ $user->name }}">
        <i class="bi bi-pencil"></i>
    </a>
    @if(auth()->id() !== $user->id)
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" data-delete-name="{{ $user->name }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus user {{ $user->name }}">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif
</div>
