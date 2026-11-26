(function () {
    const previewUrls = new WeakMap();

    document.addEventListener('change', function (event) {
        if (!(event.target instanceof HTMLInputElement) || !event.target.matches('[data-product-image-input]')) {
            return;
        }

        const input = event.target;
        const form = input.closest('form');
        const preview = form?.querySelector('[data-product-image-preview]');
        const image = preview?.querySelector('[data-product-image-preview-image]');
        const placeholder = preview?.querySelector('[data-product-image-preview-placeholder]');
        const file = input.files?.[0];

        if (!image || !placeholder) {
            return;
        }

        const restoreOriginalPreview = function () {
            const originalSrc = image.dataset.originalSrc;
            if (originalSrc) {
                image.src = originalSrc;
                image.hidden = false;
                placeholder.hidden = true;

                return;
            }

            image.hidden = true;
            image.removeAttribute('src');
            placeholder.hidden = false;
        };

        const previousUrl = previewUrls.get(input);
        if (previousUrl) {
            URL.revokeObjectURL(previousUrl);
            previewUrls.delete(input);
        }

        if (!file) {
            restoreOriginalPreview();

            return;
        }

        if (!file.type.startsWith('image/')) {
            input.value = '';
            restoreOriginalPreview();

            return;
        }

        const previewUrl = URL.createObjectURL(file);
        previewUrls.set(input, previewUrl);
        image.src = previewUrl;
        image.hidden = false;
        placeholder.hidden = true;
    });
})();
