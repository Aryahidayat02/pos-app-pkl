(function (global) {
    function getDecimals(input) {
        const decimals = Number.parseInt(input.dataset.numberDecimals || '0', 10);

        return Number.isFinite(decimals) ? Math.max(0, decimals) : 0;
    }

    function groupDigits(value) {
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function formatLocalizedValue(value, decimals) {
        const source = String(value ?? '');
        const decimalSeparatorIndex = decimals > 0 ? source.indexOf(',') : -1;
        const hasDecimalSeparator = decimalSeparatorIndex !== -1;
        let integer = (hasDecimalSeparator ? source.slice(0, decimalSeparatorIndex) : source)
            .replace(/\D/g, '');

        if (!integer && hasDecimalSeparator) {
            integer = '0';
        }

        const fraction = hasDecimalSeparator
            ? source.slice(decimalSeparatorIndex + 1).replace(/\D/g, '').slice(0, decimals)
            : '';

        return `${groupDigits(integer)}${hasDecimalSeparator ? `,${fraction}` : ''}`;
    }

    function formatRawValue(value, decimals) {
        const [rawInteger, rawFraction = ''] = String(value ?? '').trim().split('.');
        const integer = rawInteger.replace(/\D/g, '');
        const fraction = rawFraction.replace(/\D/g, '').slice(0, decimals).replace(/0+$/, '');

        return formatLocalizedValue(`${integer}${fraction ? `,${fraction}` : ''}`, decimals);
    }

    function trimDecimalZeros(value) {
        const [integer, fraction] = value.split(',');
        if (fraction === undefined) {
            return value;
        }

        const trimmedFraction = fraction.replace(/0+$/, '');

        return trimmedFraction ? `${integer},${trimmedFraction}` : integer;
    }

    function parseLocalizedValue(value) {
        const normalized = String(value ?? '').trim();
        if (!normalized) {
            return 0;
        }

        const numericValue = Number(normalized.replace(/\./g, '').replace(',', '.'));

        return Number.isFinite(numericValue) ? numericValue : null;
    }

    function toRawValue(value) {
        const normalized = String(value ?? '').trim().replace(/\./g, '');
        if (!normalized) {
            return '';
        }

        const [integer, fraction = ''] = normalized.split(',');
        const digits = integer.replace(/\D/g, '') || '0';
        const decimalDigits = fraction.replace(/\D/g, '');

        return `${digits}${decimalDigits ? `.${decimalDigits}` : ''}`;
    }

    function updateValidity(input) {
        const value = input.value.trim();
        if (!value) {
            input.setCustomValidity('');
            return;
        }

        const numericValue = parseLocalizedValue(value);
        if (numericValue === null) {
            input.setCustomValidity('Masukkan angka yang valid.');
            return;
        }

        const minimum = Number(input.dataset.numberMin);
        const maximum = Number(input.dataset.numberMax);

        if (input.dataset.numberMin !== undefined && numericValue < minimum) {
            input.setCustomValidity(`Nilai minimal ${groupDigits(String(minimum))}.`);
        } else if (input.dataset.numberMax !== undefined && numericValue > maximum) {
            input.setCustomValidity(`Nilai maksimal ${groupDigits(String(maximum))}.`);
        } else {
            input.setCustomValidity('');
        }
    }

    function formatInput(input, preserveCaret = true) {
        const previousValue = input.value;
        const previousCaret = preserveCaret ? input.selectionStart : null;
        const previousPrefix = previousCaret === null ? '' : previousValue.slice(0, previousCaret);
        const digitsBeforeCaret = (previousPrefix.match(/\d/g) || []).length;
        const decimalSeparatorBeforeCaret = previousPrefix.includes(',');
        const formattedValue = formatLocalizedValue(previousValue, getDecimals(input));

        input.value = formattedValue;

        if (previousCaret !== null && document.activeElement === input) {
            let caret = 0;
            let digitsSeen = 0;

            while (caret < formattedValue.length && digitsSeen < digitsBeforeCaret) {
                if (/\d/.test(formattedValue[caret])) {
                    digitsSeen++;
                }

                caret++;
            }

            if (decimalSeparatorBeforeCaret && formattedValue.includes(',')) {
                caret = Math.max(caret, formattedValue.indexOf(',') + 1);
            }

            input.setSelectionRange(caret, caret);
        }

        updateValidity(input);
    }

    function setValue(input, rawValue) {
        input.value = formatRawValue(rawValue, getDecimals(input));
        updateValidity(input);
    }

    function initializeForm(form) {
        if (form.dataset.numberFormatInitialized === 'true') {
            return;
        }

        form.dataset.numberFormatInitialized = 'true';
        form.addEventListener('formdata', function (event) {
            form.querySelectorAll('[data-number-format="id"][name]').forEach(function (input) {
                event.formData.set(input.name, toRawValue(input.value));
            });
        });
    }

    function initialize() {
        document.querySelectorAll('[data-number-format="id"]').forEach(function (input) {
            input.value = trimDecimalZeros(input.value);
            formatInput(input, false);
            input.classList.add('number-format-input');
        });

        document.addEventListener('input', function (event) {
            if (event.target instanceof HTMLInputElement && event.target.matches('[data-number-format="id"]')) {
                event.target.classList.add('number-format-input');
                formatInput(event.target);
            }
        }, true);

        document.addEventListener('focusout', function (event) {
            if (!(event.target instanceof HTMLInputElement) || !event.target.matches('[data-number-format="id"]')) {
                return;
            }

            event.target.value = trimDecimalZeros(event.target.value);
            formatInput(event.target, false);
        }, true);

        document.querySelectorAll('form').forEach(initializeForm);
    }

    global.PosNumberFormat = {
        initializeForm,
        formatLocalizedValue,
        formatRawValue,
        parseLocalizedValue,
        setValue,
        toRawValue,
    };

    if (typeof document !== 'undefined') {
        initialize();
    }
})(typeof window !== 'undefined' ? window : globalThis);
