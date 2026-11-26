(function (global) {
    function initializeCategorySearch(root) {
        root.querySelectorAll('[data-category-search]').forEach((categorySearch) => {
            if (categorySearch.dataset.categorySearchInitialized === 'true') {
                return;
            }

            categorySearch.dataset.categorySearchInitialized = 'true';

            const input = categorySearch.querySelector('[data-category-search-input]');
            const categoryIdInput = categorySearch.querySelector('[data-category-search-value]');
            const options = Array.from(categorySearch.querySelectorAll('datalist option'));
            const form = input.form;

            const updateCategory = () => {
                const selectedOption = options.find((option) => option.value === input.value);

                categoryIdInput.value = selectedOption?.dataset.categoryId ?? '';
                input.setCustomValidity(
                    input.value !== '' && categoryIdInput.value === ''
                        ? 'Pilih kategori dari daftar pencarian.'
                        : '',
                );
            };

            input.addEventListener('input', updateCategory);
            input.addEventListener('change', updateCategory);
            form?.addEventListener('submit', (event) => {
                updateCategory();

                if (categoryIdInput.value === '') {
                    input.setCustomValidity('Pilih kategori dari daftar pencarian.');
                    input.reportValidity();
                    event.preventDefault();
                }
            });

            updateCategory();
        });
    }

    global.initializeCategorySearch = initializeCategorySearch;
    initializeCategorySearch(document);
})(typeof window !== 'undefined' ? window : globalThis);
