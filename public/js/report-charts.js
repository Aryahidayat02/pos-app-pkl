(function () {
    const data = window.reportChartData;
    if (!data) {
        return;
    }

    const namespace = 'http://www.w3.org/2000/svg';
    const width = 900;
    const height = 300;
    const padding = { top: 20, right: 24, bottom: 42, left: 76 };
    const currency = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });
    const compactCurrency = new Intl.NumberFormat('id-ID', {
        notation: 'compact',
        maximumFractionDigits: 1,
    });

    function createSvg(container, title) {
        const svg = document.createElementNS(namespace, 'svg');
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svg.setAttribute('role', 'img');
        svg.setAttribute('aria-label', title);
        svg.classList.add('w-100');

        return svg;
    }

    function createElement(tag, attributes = {}) {
        const element = document.createElementNS(namespace, tag);
        Object.entries(attributes).forEach(([name, value]) => element.setAttribute(name, value));

        return element;
    }

    function appendEmpty(container) {
        const message = document.createElement('p');
        message.className = 'text-center text-muted my-5';
        message.textContent = 'Belum ada transaksi pada rentang tanggal ini.';
        container.replaceChildren(message);
    }

    function addGrid(svg, minimum, maximum) {
        for (let index = 0; index <= 4; index++) {
            const ratio = index / 4;
            const y = padding.top + ratio * (height - padding.top - padding.bottom);
            const value = maximum - ratio * (maximum - minimum);
            const line = createElement('line', {
                x1: padding.left,
                x2: width - padding.right,
                y1: y,
                y2: y,
                stroke: 'currentColor',
                'stroke-opacity': 0.12,
            });
            const label = createElement('text', {
                x: padding.left - 10,
                y: y + 4,
                'text-anchor': 'end',
                fill: 'currentColor',
                'font-size': 12,
            });
            label.textContent = compactCurrency.format(value);
            svg.append(line, label);
        }
    }

    function addDateLabel(svg, date, x) {
        const label = createElement('text', {
            x,
            y: height - 12,
            'text-anchor': 'middle',
            fill: 'currentColor',
            'font-size': 11,
        });
        label.textContent = date.slice(5);
        svg.append(label);
    }

    function renderLineChart(container, points) {
        if (points.length === 0) {
            appendEmpty(container);
            return;
        }

        const values = points.map((point) => point.value);
        const maximum = Math.max(...values, 1);
        const plotWidth = width - padding.left - padding.right;
        const plotHeight = height - padding.top - padding.bottom;
        const xPosition = (index) => points.length === 1
            ? padding.left + plotWidth / 2
            : padding.left + index * plotWidth / (points.length - 1);
        const yPosition = (value) => padding.top + plotHeight * (1 - value / maximum);
        const svg = createSvg(container, 'Grafik garis omzet penjualan harian');
        addGrid(svg, 0, maximum);

        const path = createElement('path', {
            d: points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${xPosition(index)} ${yPosition(point.value)}`).join(' '),
            fill: 'none',
            stroke: 'var(--pos-primary)',
            'stroke-width': 3,
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round',
        });
        svg.append(path);

        const labelStep = Math.max(1, Math.ceil(points.length / 8));
        points.forEach((point, index) => {
            const circle = createElement('circle', {
                cx: xPosition(index),
                cy: yPosition(point.value),
                r: 4,
                fill: 'var(--pos-primary)',
            });
            const title = createElement('title');
            title.textContent = `${point.date}: ${currency.format(point.value)}`;
            circle.append(title);
            svg.append(circle);

            if (index % labelStep === 0 || index === points.length - 1) {
                addDateLabel(svg, point.date, xPosition(index));
            }
        });

        container.replaceChildren(svg);
    }

    function renderCandlesticks(container, candles) {
        if (candles.length === 0) {
            appendEmpty(container);
            return;
        }

        const minimumValue = Math.min(...candles.map((candle) => candle.low));
        const maximumValue = Math.max(...candles.map((candle) => candle.high));
        const range = Math.max(maximumValue - minimumValue, 1);
        const minimum = Math.max(0, minimumValue - range * 0.08);
        const maximum = maximumValue + range * 0.08;
        const plotWidth = width - padding.left - padding.right;
        const plotHeight = height - padding.top - padding.bottom;
        const step = plotWidth / candles.length;
        const bodyWidth = Math.max(4, Math.min(24, step * 0.55));
        const yPosition = (value) => padding.top + plotHeight * (1 - (value - minimum) / (maximum - minimum));
        const svg = createSvg(container, 'Grafik candlestick total transaksi harian');
        addGrid(svg, minimum, maximum);

        const labelStep = Math.max(1, Math.ceil(candles.length / 8));
        candles.forEach((candle, index) => {
            const x = padding.left + index * step + step / 2;
            const rising = candle.close >= candle.open;
            const color = rising ? '#198754' : '#dc3545';
            const wick = createElement('line', {
                x1: x,
                x2: x,
                y1: yPosition(candle.high),
                y2: yPosition(candle.low),
                stroke: color,
                'stroke-width': 2,
            });
            const top = Math.min(yPosition(candle.open), yPosition(candle.close));
            const body = createElement('rect', {
                x: x - bodyWidth / 2,
                y: top,
                width: bodyWidth,
                height: Math.max(2, Math.abs(yPosition(candle.open) - yPosition(candle.close))),
                fill: color,
                rx: 1,
            });
            const title = createElement('title');
            title.textContent = `${candle.date} — O ${currency.format(candle.open)}, H ${currency.format(candle.high)}, L ${currency.format(candle.low)}, C ${currency.format(candle.close)}`;
            body.append(title);
            svg.append(wick, body);

            if (index % labelStep === 0 || index === candles.length - 1) {
                addDateLabel(svg, candle.date, x);
            }
        });

        container.replaceChildren(svg);
    }

    renderLineChart(document.getElementById('sales-line-chart'), data.line);
    renderCandlesticks(document.getElementById('sales-candlestick-chart'), data.candles);
})();
