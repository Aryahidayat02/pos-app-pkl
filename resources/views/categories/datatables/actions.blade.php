<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>
<div class="d-flex justify-content-end gap-1">
    <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#editCategoryModal"
        data-update-url="{{ route('categories.update', $category) }}"
        data-name="{{ $category->name }}"
        data-description="{{ $category->description }}"
        aria-label="Edit kategori {{ $category->name }}"
    >
        <i class="bi bi-pencil"></i>
    </button>
    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" data-delete-name="{{ $category->name }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Hapus kategori {{ $category->name }}">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
