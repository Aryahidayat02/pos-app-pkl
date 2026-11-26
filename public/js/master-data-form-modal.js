(function () {
    const modalElement = document.getElementById('masterDataFormModal');
    const modalTitle = document.getElementById('masterDataFormModalTitle');
    const modalContent = document.getElementById('masterDataFormModalContent');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    let activeRequest;

    const showLoading = () => {
        modalContent.innerHTML = '<div class="d-flex align-items-center gap-2 p-4 text-muted"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Memuat formulir...</span></div>';
    };

    const showError = (message) => {
        const errorMessage = document.createElement('p');
        errorMessage.className = 'text-danger mb-0 p-4';
        errorMessage.textContent = message;
        modalContent.replaceChildren(errorMessage);
    };

    document.addEventListener('click', async (event) => {
        const clickedElement = event.target instanceof Element ? event.target : null;
        const trigger = clickedElement?.closest('[data-form-modal-url]');

        if (!(trigger instanceof HTMLElement)) {
            return;
        }

        event.preventDefault();
        activeRequest?.abort();
        activeRequest = new AbortController();
        modalTitle.textContent = trigger.dataset.formModalTitle || 'Form Data';
        showLoading();
        modal.show();

        try {
            const response = await fetch(trigger.dataset.formModalUrl, {
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: activeRequest.signal
            });

            if (!response.ok) {
                throw new Error(`Form request failed with status ${response.status}.`);
            }

            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const form = page.querySelector('.content-wrapper form');

            if (!form) {
                throw new Error('The requested page did not contain a form.');
            }

            const formContent = form.closest('.card-body') || form;
            const pageTitle = page.querySelector('.page-title')?.textContent?.trim();
            modalTitle.textContent = trigger.dataset.formModalTitle || pageTitle || 'Form Data';
            modalContent.replaceChildren(formContent.cloneNode(true));

            const loadedForm = modalContent.querySelector('form');
            window.PosNumberFormat?.initializeForm(loadedForm);
            window.initializeCategorySearch?.(loadedForm);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Unable to load the master data form.', error);
            showError('Form tidak dapat dimuat. Silakan coba lagi.');
        }
    });

    modalContent.addEventListener('click', (event) => {
        const clickedElement = event.target instanceof Element ? event.target : null;
        const cancelButton = clickedElement?.closest('[data-form-modal-cancel]');

        if (cancelButton) {
            event.preventDefault();
            modal.hide();
        }
    });

    modalElement.addEventListener('hide.bs.modal', () => {
        activeRequest?.abort();
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        activeRequest = null;
        modalContent.replaceChildren();
    });
})();
