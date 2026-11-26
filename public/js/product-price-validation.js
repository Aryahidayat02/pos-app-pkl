(function () {
    function parseIdr(value) {
        if (typeof value !== 'string') {
            return Number(value) || 0;
        }

        const normalized = value
            .replace(/[^0-9,-]/g, '')
            .replace(/\./g, '')
            .replace(',', '.');

        return Number(normalized) || 0;
    }

    function formatIdr(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value);
    }

    function validateForm(form) {
        const purchaseInput = form.querySelector('[name="purchase_price"].product-purchase-price');
        const sellingInput = form.querySelector('[name="selling_price"].product-selling-price');
        const feedback = form.querySelector('.product-price-feedback');

        if (!purchaseInput || !sellingInput) {
            return;
        }

        const purchasePrice = parseIdr(purchaseInput.value);
        const sellingPrice = parseIdr(sellingInput.value);

        sellingInput.classList.toggle('is-invalid', sellingPrice > 0 && sellingPrice < purchasePrice);
        sellingInput.setCustomValidity(
            sellingPrice > 0 && sellingPrice < purchasePrice
                ? 'Harga jual tidak boleh lebih rendah dari harga modal.'
                : '',
        );

        if (feedback) {
            if (purchasePrice > 0 && sellingPrice > 0 && sellingPrice < purchasePrice) {
                feedback.className = 'product-price-feedback form-text text-danger fw-semibold';
                feedback.textContent = 'Harga jual tidak boleh lebih rendah dari harga modal (' +
                    formatIdr(purchasePrice) + ').';
            } else if (purchasePrice > 0 && sellingPrice >= purchasePrice) {
                feedback.className = 'product-price-feedback form-text text-success';
                feedback.textContent = 'Harga jual valid. Margin perkiraan ' +
                    formatIdr(sellingPrice - purchasePrice) + '.';
            } else {
                feedback.className = 'product-price-feedback form-text';
                feedback.textContent = '';
            }
        }

        if (typeof window.updateSubmitButtons === 'function') {
            window.updateSubmitButtons(form);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function (form) {
            if (!form.querySelector('[name="purchase_price"].product-purchase-price')) {
                return;
            }

            form.addEventListener('input', function (event) {
                if (event.target.matches('.product-purchase-price, .product-selling-price')) {
                    validateForm(form);
                }
            });

            validateForm(form);
        });
    });
})();
