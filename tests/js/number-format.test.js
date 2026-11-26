import assert from 'node:assert/strict';
import test from 'node:test';

await import('../../public/js/number-format.js');

const numberFormat = globalThis.PosNumberFormat;

test('formats Indonesian thousands separators while preserving entered digits', () => {
    assert.equal(numberFormat.formatLocalizedValue('1090000', 0), '1.090.000');
    assert.equal(numberFormat.parseLocalizedValue('1.090.000'), 1090000);
});

test('formats decimal prices and converts them to numeric form values', () => {
    assert.equal(numberFormat.formatLocalizedValue('1234567,50', 2), '1.234.567,50');
    assert.equal(numberFormat.formatRawValue('1234567.50', 2), '1.234.567,5');
    assert.equal(numberFormat.parseLocalizedValue('1.234.567,50'), 1234567.5);
    assert.equal(numberFormat.toRawValue('1.234.567,50'), '1234567.50');
    assert.equal(numberFormat.formatRawValue('1234567.00', 2), '1.234.567');
});

test('limits decimal precision to the configured number of places', () => {
    assert.equal(numberFormat.formatLocalizedValue('1234,567', 2), '1.234,56');
});
