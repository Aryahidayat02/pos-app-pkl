<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS App')</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light' || savedTheme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
            }
        })();
    </script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- SELECT2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --pos-primary: #4f46e5;
            --pos-primary-rgb: 79, 70, 229;
            --pos-sidebar-width: 272px;
            --pos-sidebar-bg: #111827;
            --pos-bg: #f4f6fb;
            --pos-surface: #ffffff;
            --pos-surface-muted: #f8f9fc;
            --pos-text: #172033;
            --pos-muted: #64748b;
            --pos-border: #e6eaf1;
            --pos-radius: 1rem;
            --pos-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        [data-bs-theme="dark"] {
            --pos-bg: #0d1422;
            --pos-surface: #151e2d;
            --pos-surface-muted: #111a29;
            --pos-text: #e5eaf3;
            --pos-muted: #9aa8bd;
            --pos-border: #273448;
            --pos-sidebar-bg: #09111f;
            --pos-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--pos-bg);
            color: var(--pos-text);
            overflow-x: hidden;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .bg-light {
            background-color: var(--pos-surface) !important;
        }
        [data-bs-theme="dark"] .text-dark {
            color: var(--bs-body-color) !important;
        }
        [data-bs-theme="dark"] .table-light {
            --bs-table-bg: var(--pos-surface-muted);
            --bs-table-color: var(--bs-body-color);
            --bs-table-border-color: var(--bs-border-color);
        }
        [data-bs-theme="dark"] .topbar,
        [data-bs-theme="dark"] .card-header,
        [data-bs-theme="dark"] .card-footer,
        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: var(--bs-border-color) !important;
        }
        [data-bs-theme="dark"] .btn-light {
            --bs-btn-color: var(--bs-body-color);
            --bs-btn-bg: var(--pos-surface);
            --bs-btn-border-color: var(--bs-border-color);
            --bs-btn-hover-color: var(--bs-body-color);
            --bs-btn-hover-bg: var(--bs-secondary-bg);
            --bs-btn-hover-border-color: var(--bs-border-color);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection,
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown,
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-search__field {
            color: var(--bs-body-color);
            background-color: var(--pos-surface-muted);
            border-color: var(--bs-border-color);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option {
            color: var(--bs-body-color);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted {
            color: #fff;
            background-color: var(--pos-primary);
        }
        [data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option[aria-selected="true"] {
            color: var(--bs-body-color);
            background-color: var(--pos-surface);
        }

        .sidebar {
            width: var(--pos-sidebar-width);
            height: 100vh;
            background: var(--pos-sidebar-bg);
            color: #fff;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            box-shadow: 12px 0 36px rgba(15, 23, 42, 0.12);
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            min-height: 82px;
            padding: 1rem 1.35rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            width: 100%;
            overflow: hidden;
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: transparent;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 750;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .sidebar-brand:hover,
        .sidebar-brand:focus-visible {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand > img,
        .sidebar-brand > i {
            width: 2.7rem;
            height: 2.7rem;
            flex: 0 0 2.7rem;
            display: grid;
            place-items: center;
            border-radius: 0.85rem;
            background: rgba(var(--pos-primary-rgb), 0.22);
            color: #c7d2fe;
            font-size: 1.25rem;
            object-fit: cover;
        }

        .sidebar-brand-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-menu {
            flex: 1;
            margin: 0;
            padding: 1rem 0.85rem 1.25rem;
            overflow-x: hidden;
            overflow-y: auto;
            list-style: none;
            scrollbar-color: rgba(255, 255, 255, 0.22) transparent;
            scrollbar-width: thin;
        }

        .sidebar-menu li + li.menu-header {
            margin-top: 1.5rem;
        }

        .menu-header {
            margin: 0.65rem 0.85rem 0.55rem;
            color: #94a3b8;
            font-size: 0.68rem;
            font-weight: 750;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin: 0.2rem 0;
            padding: 0.72rem 0.85rem;
            border: 1px solid transparent;
            border-radius: 0.75rem;
            color: #cbd5e1;
            font-size: 0.9rem;
            font-weight: 500;
            line-height: 1.3;
            text-decoration: none;
            transition: color 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .sidebar-menu li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.07);
            transform: translateX(2px);
        }

        .sidebar-menu li a.active {
            color: #fff;
            border-color: rgba(165, 180, 252, 0.18);
            background: linear-gradient(110deg, rgba(var(--pos-primary-rgb), 0.95), rgba(99, 102, 241, 0.78));
            box-shadow: 0 7px 18px rgba(var(--pos-primary-rgb), 0.25);
            font-weight: 650;
        }

        .sidebar-menu li a i {
            width: 1.35rem;
            flex: 0 0 1.35rem;
            color: #a5b4fc;
            font-size: 1.05rem;
            text-align: center;
        }

        .sidebar-menu li a.active i {
            color: #fff;
        }

        .main-content {
            min-width: 0;
            min-height: 100vh;
            margin-left: var(--pos-sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .topbar {
            min-height: 78px;
            position: sticky;
            top: 0;
            z-index: 1010;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 0.9rem 1.8rem;
            border-bottom: 1px solid var(--pos-border);
            background: var(--pos-surface);
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.035);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .topbar-start {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .page-title {
            margin: 0;
            color: var(--pos-text);
            font-size: clamp(1.05rem, 1.4vw, 1.3rem);
            font-weight: 750;
            letter-spacing: -0.025em;
            line-height: 1.25;
        }

        .sidebar-toggle {
            display: none;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.9rem;
        }

        .user-info > .btn,
        .user-info .dropdown > .btn {
            width: 2.5rem;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 0.75rem;
        }

        .profile-trigger {
            width: auto !important;
            min-height: 2.75rem;
            justify-content: flex-start !important;
            padding: 0.2rem 0.6rem !important;
            border: 1px solid transparent !important;
            border-radius: 0.85rem !important;
            color: var(--pos-text) !important;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .profile-trigger:hover,
        .profile-trigger:focus-visible {
            border-color: var(--pos-border) !important;
            background: var(--pos-surface-muted) !important;
        }

        .profile-trigger img,
        .profile-avatar {
            width: 2.25rem;
            height: 2.25rem;
            flex: 0 0 2.25rem;
            object-fit: cover;
        }

        .profile-trigger .profile-name {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-logout {
            min-height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.85rem;
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 0.75rem;
            background: rgba(239, 68, 68, 0.06);
            color: #dc3545;
            font-size: 0.84rem;
            font-weight: 650;
            text-decoration: none;
            transition: color 0.18s ease, background-color 0.18s ease, border-color 0.18s ease;
        }

        .btn-logout:hover,
        .btn-logout:focus-visible {
            border-color: #dc3545;
            background: #dc3545;
            color: #fff;
        }

        .content-wrapper {
            width: 100%;
            max-width: 1800px;
            flex: 1;
            margin: 0 auto;
            padding: 1.65rem 1.8rem 2rem;
        }

        .alert {
            margin-bottom: 1.25rem;
            padding: 0.9rem 1rem;
            border: 1px solid transparent;
            border-radius: 0.85rem;
            box-shadow: 0 5px 16px rgba(15, 23, 42, 0.04);
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* ========================================= */
        /* CUSTOM SELECT2 BIAR ADA ICON KACA PEMBESAR */
        /* ========================================= */
        .select2-container--bootstrap-5 .select2-dropdown .select2-search {
            position: relative;
            padding: 5px 10px;
        }
        .select2-container--bootstrap-5 .select2-dropdown .select2-search::before {
            content: "\F52A"; /* Kode icon Bootstrap Icons (bi-search) */
            font-family: "bootstrap-icons";
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.9rem;
            pointer-events: none;
        }
        .select2-container--bootstrap-5 .select2-dropdown .select2-search__field {
            padding-left: 35px !important; /* Geser teks biar gak nabrak icon */
        }

        .page-loading-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            overflow: hidden;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s, visibility 0.15s;
        }
        .page-loading-indicator::after {
            content: "";
            display: block;
            width: 30%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--pos-primary), #a5b4fc);
            box-shadow: 0 0 10px rgba(79, 70, 229, 0.45);
            transform: translateX(-100%);
        }
        .page-loading-indicator.active {
            opacity: 1;
            visibility: visible;
        }
        .page-loading-indicator.active::after {
            animation: pageLoadingProgress 1s ease-in-out infinite;
        }
        @keyframes pageLoadingProgress {
            to { transform: translateX(400%); }
        }

        .save-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: grid;
            place-items: center;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.24);
            backdrop-filter: blur(3px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s ease, visibility 0.15s ease;
        }
        .save-loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .save-loading-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 260px;
            max-width: 100%;
            padding: 1.25rem 1.5rem;
            color: var(--bs-body-color);
            background: var(--bs-body-bg);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(15, 23, 42, 0.18);
        }
        .save-loading-spinner {
            width: 2rem;
            height: 2rem;
            flex: 0 0 auto;
            border: 3px solid rgba(79, 70, 229, 0.18);
            border-top-color: var(--pos-primary);
            border-radius: 50%;
            animation: saveLoadingSpin 0.65s linear infinite;
        }
        @keyframes saveLoadingSpin {
            to { transform: rotate(360deg); }
        }

        .fade-out-scale {
            animation: fadeOutScale 0.18s ease forwards;
        }
        @keyframes fadeOutScale {
            0% { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(0.8); }
        }

        .card {
            border-color: var(--pos-border);
            border-radius: var(--pos-radius);
            background-color: var(--pos-surface);
            box-shadow: var(--pos-shadow);
        }

        .card-header,
        .card-footer {
            border-color: var(--pos-border);
            background-color: transparent;
        }

        .table {
            --bs-table-color: var(--pos-text);
            --bs-table-border-color: var(--pos-border);
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table > :not(caption) > * > * {
            padding: 0.8rem 0.85rem;
            border-bottom-color: var(--pos-border);
        }

        .table:not(.table-dark) thead th {
            background: var(--pos-surface-muted);
            color: var(--pos-muted);
            font-size: 0.72rem;
            font-weight: 750;
            letter-spacing: 0.055em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table-hover > tbody > tr:hover > * {
            --bs-table-bg-state: rgba(var(--pos-primary-rgb), 0.045);
        }

        .form-label {
            color: var(--pos-text);
            font-size: 0.88rem;
            font-weight: 650;
        }

        .form-control,
        .form-select,
        .input-group-text {
            min-height: 2.65rem;
            border-color: var(--pos-border);
            border-radius: 0.7rem;
            background-color: var(--pos-surface);
            color: var(--pos-text);
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .input-group > .form-control,
        .input-group > .form-select,
        .input-group > .input-group-text {
            border-radius: 0.7rem;
        }

        .form-control::placeholder {
            color: var(--pos-muted);
            opacity: 0.8;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(var(--pos-primary-rgb), 0.6);
            background-color: var(--pos-surface);
            color: var(--pos-text);
            box-shadow: 0 0 0 0.22rem rgba(var(--pos-primary-rgb), 0.13);
        }

        .form-control[type="file"] {
            padding: 0.45rem 0.7rem;
        }

        .btn {
            border-radius: 0.7rem;
            font-weight: 600;
            transition: color 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .btn-primary {
            --bs-btn-bg: var(--pos-primary);
            --bs-btn-border-color: var(--pos-primary);
            --bs-btn-hover-bg: #4338ca;
            --bs-btn-hover-border-color: #4338ca;
            --bs-btn-active-bg: #3730a3;
            --bs-btn-active-border-color: #3730a3;
            --bs-btn-focus-shadow-rgb: var(--pos-primary-rgb);
        }

        .btn-primary:hover,
        .btn-success:hover,
        .btn-danger:hover {
            transform: translateY(-1px);
        }

        .btn:focus-visible,
        a:focus-visible,
        .sidebar-brand:focus-visible {
            outline: 3px solid rgba(var(--pos-primary-rgb), 0.35);
            outline-offset: 2px;
        }

        .badge {
            border-radius: 999px;
            font-weight: 650;
            letter-spacing: 0.01em;
        }

        .pagination {
            gap: 0.25rem;
        }

        .pagination .page-link {
            min-width: 2.25rem;
            border-color: var(--pos-border);
            border-radius: 0.65rem !important;
            background-color: var(--pos-surface);
            color: var(--pos-text);
            text-align: center;
        }

        .pagination .page-item.active .page-link {
            border-color: var(--pos-primary);
            background-color: var(--pos-primary);
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            background-color: var(--pos-surface-muted);
            color: var(--pos-muted);
        }

        .dropdown-menu {
            border-color: var(--pos-border);
            border-radius: 0.85rem;
            background-color: var(--pos-surface);
            box-shadow: var(--pos-shadow);
        }

        .dropdown-item {
            padding: 0.6rem 0.85rem;
            color: var(--pos-text);
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: var(--pos-surface-muted);
            color: var(--pos-text);
        }

        .modal-content {
            overflow: hidden;
            border: 1px solid var(--pos-border) !important;
            border-radius: 1.1rem;
            background-color: var(--pos-surface);
            color: var(--pos-text);
            box-shadow: 0 1.5rem 4rem rgba(15, 23, 42, 0.18);
        }

        .modal-header,
        .modal-footer {
            padding: 1.1rem 1.35rem;
            border-color: var(--pos-border);
        }

        .modal-body {
            padding: 1.35rem;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            min-height: 2.4rem;
            margin: 0 0.35rem;
            padding: 0.35rem 0.65rem;
            border: 1px solid var(--pos-border);
            border-radius: 0.65rem;
            background-color: var(--pos-surface);
            color: var(--pos-text);
        }

        [data-bs-theme="dark"] .table:not(.table-dark) thead th {
            background: var(--pos-surface-muted);
        }

        [data-bs-theme="dark"] .table-hover > tbody > tr:hover > * {
            --bs-table-bg-state: rgba(148, 163, 184, 0.08);
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter input,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length select {
            border-color: var(--pos-border);
            background-color: var(--pos-surface-muted);
            color: var(--pos-text);
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: var(--pos-surface);
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            --bs-btn-color: #cbd5e1;
            --bs-btn-border-color: #475569;
            --bs-btn-hover-bg: #334155;
            --bs-btn-hover-border-color: #64748b;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1020;
            border: 0;
            background: rgba(15, 23, 42, 0.52);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .sidebar-backdrop.is-visible {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 1199.98px) {
            :root {
                --pos-sidebar-width: 248px;
            }

            .topbar {
                padding-right: 1.35rem;
                padding-left: 1.35rem;
            }

            .content-wrapper {
                padding-right: 1.35rem;
                padding-left: 1.35rem;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: min(var(--pos-sidebar-width), 86vw);
                transform: translateX(-105%);
                box-shadow: none;
            }

            .sidebar.is-open {
                transform: translateX(0);
                box-shadow: 12px 0 36px rgba(15, 23, 42, 0.2);
            }

            .sidebar-backdrop {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: inline-flex;
            }

            .topbar {
                min-height: 70px;
            }

            .content-wrapper {
                padding-top: 1.25rem;
                padding-bottom: 1.5rem;
            }

            body.sidebar-open {
                overflow: hidden;
            }
        }

        @media (max-width: 575.98px) {
            .topbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 0.75rem;
                padding: 0.8rem 0.9rem;
            }

            .topbar-start,
            .user-info {
                width: 100%;
            }

            .user-info {
                justify-content: flex-end;
                gap: 0.45rem;
            }

            .content-wrapper {
                padding: 0.9rem 0.75rem 1.25rem;
            }

            .card {
                border-radius: 0.85rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-header,
            .modal-footer {
                padding: 0.9rem 1rem;
            }

            .table > :not(caption) > * > * {
                padding: 0.65rem 0.7rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="page-loading-indicator" id="pageLoadingIndicator" role="status" aria-label="Memuat halaman" aria-hidden="true"></div>
    <div class="save-loading-overlay" id="saveLoadingOverlay" role="status" aria-live="polite" aria-hidden="true">
        <div class="save-loading-card">
            <span class="save-loading-spinner" aria-hidden="true"></span>
            <div>
                <div class="fw-semibold" id="saveLoadingMessage">Menyimpan data...</div>
                <div class="small text-secondary">Mohon tunggu sebentar</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center p-4">
                    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-trash3 fs-3"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="deleteConfirmTitle">Hapus data ini?</h5>
                    <p class="text-secondary mb-4" id="deleteConfirmMessage">Data ini akan dihapus permanen dan tidak dapat dipulihkan.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteButton">
                            <i class="bi bi-trash3 me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold" id="adminProfileModalTitle">Profil Admin</h5>
                        <p class="text-secondary small mb-0">Kelola profil dan keamanan akun Anda.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_modal" value="adminProfileModal">
                                <h6 class="fw-bold mb-3">Informasi Profil</h6>
                                <div class="text-center mb-3">
                                    @if(auth()->user()->photo)
                                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="rounded-circle" style="width: 76px; height: 76px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 76px; height: 76px;">
                                            <i class="bi bi-person text-white fs-2"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="adminProfilePhoto" class="form-label">Foto Profil</label>
                                    <input type="file" name="photo" id="adminProfilePhoto" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png">
                                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="adminProfileName" class="form-label">Nama</label>
                                    <input type="text" name="name" id="adminProfileName" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="adminProfileEmail" class="form-label">Email</label>
                                    <input type="email" name="email" id="adminProfileEmail" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Profil</button>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_modal" value="adminProfileModal">
                                <h6 class="fw-bold mb-3">Ganti Password</h6>
                                <div class="mb-3">
                                    <label for="adminCurrentPassword" class="form-label">Password Lama</label>
                                    <input type="password" name="current_password" id="adminCurrentPassword" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="adminNewPassword" class="form-label">Password Baru</label>
                                    <input type="password" name="password" id="adminNewPassword" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="adminPasswordConfirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" id="adminPasswordConfirmation" class="form-control" autocomplete="new-password" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Ganti Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="storeProfileModal" tabindex="-1" aria-labelledby="storeProfileModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_modal" value="storeProfileModal">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="storeProfileModalTitle">Profil Toko</h5>
                            <p class="text-secondary small mb-0">Ubah identitas yang tampil pada aplikasi dan struk.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4 text-center">
                            @if(\App\Models\Setting::get('store_logo'))
                                <img src="{{ asset('storage/' . \App\Models\Setting::get('store_logo')) }}" class="rounded-circle mb-2" style="width: 84px; height: 84px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2" style="width: 84px; height: 84px;">
                                    <i class="bi bi-shop text-white fs-2"></i>
                                </div>
                            @endif
                            <input type="file" name="logo" id="storeProfileLogo" class="form-control @error('logo') is-invalid @enderror" accept="image/jpeg,image/png">
                            <small class="text-secondary">Logo toko (JPG/PNG, maksimal 2MB)</small>
                            @error('logo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="storeProfileName" class="form-label">Nama Toko</label>
                                <input type="text" name="store_name" id="storeProfileName" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', \App\Models\Setting::get('store_name', 'POS App')) }}" required>
                                @error('store_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="storeProfilePhone" class="form-label">No. Telepon</label>
                                <input type="text" name="store_phone" id="storeProfilePhone" class="form-control @error('store_phone') is-invalid @enderror" value="{{ old('store_phone', \App\Models\Setting::get('store_phone', '0812-3456-7890')) }}" required>
                                @error('store_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="storeProfileAddress" class="form-label">Alamat</label>
                                <input type="text" name="store_address" id="storeProfileAddress" class="form-control @error('store_address') is-invalid @enderror" value="{{ old('store_address', \App\Models\Setting::get('store_address', 'Jl. Contoh Alamat No. 123')) }}" required>
                                @error('store_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="storeProfileFooter" class="form-label">Footer Struk</label>
                                <input type="text" name="receipt_footer" id="storeProfileFooter" class="form-control @error('receipt_footer') is-invalid @enderror" value="{{ old('receipt_footer', \App\Models\Setting::get('receipt_footer', 'Terima kasih telah berbelanja!')) }}">
                                @error('receipt_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Profil Toko</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <button type="button" class="sidebar-backdrop" id="sidebarBackdrop" aria-label="Tutup menu"></button>

    <!-- SIDEBAR -->
    <div class="sidebar" id="appSidebar">
        <button type="button" class="sidebar-brand" data-bs-toggle="modal" data-bs-target="#storeProfileModal" aria-label="Buka profil toko">
            @if(\App\Models\Setting::get('store_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('store_logo')) }}" alt="Logo toko">
            @else
                <i class="bi bi-shop"></i>
            @endif
            <span class="sidebar-brand-name">{{ \App\Models\Setting::get('store_name', 'POS App') }}</span>
        </button>
        
        <ul class="sidebar-menu">
            <li class="menu-header">Utama</li>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                    <i class="bi bi-cart"></i> Kasir
                </a>
            </li>
            <li>
                <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Riwayat Transaksi
                </a>
            </li>

            @if(auth()->user()->role === 'admin')
                <li class="menu-header">Master Data</li>
                <li>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Produk
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Supplier
                    </a>
                </li>
                <li>
                    <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                    </a>
                </li>

                <li class="menu-header">Laporan & Setting</li>
                <li>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i> Manajemen User
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>
                <li>
                    <a href="{{ route('logs.index') }}" class="{{ request()->routeIs('logs.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Log Aktivitas
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-start">
                <button type="button" class="btn btn-outline-secondary sidebar-toggle" id="sidebarToggle" aria-label="Buka menu" aria-controls="appSidebar" aria-expanded="false">
                    <i class="bi bi-list fs-4" id="sidebarToggleIcon"></i>
                </button>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            </div>
            
            <div class="user-info">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="theme-toggle" title="Ganti Tema" aria-label="Ganti tema" aria-pressed="false">
                    <i class="bi bi-moon-fill" id="theme-icon"></i>
                </button>

                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-outline-warning position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi Stok Menipis" aria-label="Notifikasi stok menipis">
                        <i class="bi bi-bell-fill"></i>
                        @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Notifikasi Baru</span>
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 300px;">
                        <li class="dropdown-header fw-bold">Peringatan Stok Menipis</li>
                        <li><hr class="dropdown-divider"></li>
                        @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                            @foreach($lowStockProducts as $product)
                                <li>
                                    <a class="dropdown-item small text-wrap" href="{{ route('products.edit', $product->id) }}">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">{{ $product->name }}</span>
                                            <span class="text-danger fw-bold">{{ $product->stock }} {{ $product->unit }}</span>
                                        </div>
                                        <small class="text-muted">Minimal: {{ $product->min_stock }} {{ $product->unit }}</small>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a class="dropdown-item text-center small text-muted" href="#">Tidak ada notifikasi baru</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small text-primary" href="{{ route('products.index') }}">Lihat Semua Produk</a></li>
                    </ul>
                </div>

                <button type="button" class="btn profile-trigger d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#adminProfileModal" aria-label="Buka profil admin">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="rounded-circle" alt="Foto profil admin">
                    @else
                        <div class="profile-avatar rounded-circle bg-secondary d-flex align-items-center justify-content-center" aria-hidden="true">
                            <i class="bi bi-person text-white"></i>
                        </div>
                    @endif
                    <span class="profile-name fw-bold">{{ auth()->user()->name }}</span>
                </button>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SELECT2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // DataTables
            if ($('.table-datatable').length > 0) {
                $('.table-datatable').DataTable({
                    "pageLength": 10,
                    "language": {
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "paginate": { "previous": "Sebelumnya", "next": "Berikutnya" }
                    }
                });
            }

            // Select2
            if ($('.select2').length > 0) {
                $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
            }
        });

        const pageLoadingIndicator = document.getElementById('pageLoadingIndicator');
        const saveLoadingOverlay = document.getElementById('saveLoadingOverlay');
        const saveLoadingMessage = document.getElementById('saveLoadingMessage');
        let pageLoadingTimer;

        function showPageLoading() {
            window.clearTimeout(pageLoadingTimer);
            pageLoadingTimer = window.setTimeout(function() {
                pageLoadingIndicator.classList.add('active');
                pageLoadingIndicator.setAttribute('aria-hidden', 'false');
            }, 120);
        }

        function hidePageLoading() {
            window.clearTimeout(pageLoadingTimer);
            pageLoadingIndicator.classList.remove('active');
            pageLoadingIndicator.setAttribute('aria-hidden', 'true');
        }

        function showSaveLoading(message) {
            hidePageLoading();
            saveLoadingMessage.textContent = message;
            saveLoadingOverlay.classList.add('active');
            saveLoadingOverlay.setAttribute('aria-hidden', 'false');
        }

        function hideSaveLoading() {
            saveLoadingOverlay.classList.remove('active');
            saveLoadingOverlay.setAttribute('aria-hidden', 'true');
        }

        window.addEventListener('pageshow', function() {
            hidePageLoading();
            hideSaveLoading();
        });

        document.addEventListener('click', function(event) {
            if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }

            const clickedElement = event.target instanceof Element ? event.target : event.target.parentElement;
            const link = clickedElement?.closest('a[href]');

            if (
                !link
                || link.getAttribute('href').startsWith('#')
                || link.closest('form')
                || link.hasAttribute('download')
                || (link.target && link.target !== '_self')
            ) {
                return;
            }

            const destination = new URL(link.href, window.location.href);
            if (destination.origin !== window.location.origin) {
                return;
            }

            if (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash) {
                return;
            }

            showPageLoading();
        });

        const sidebar = document.getElementById('appSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarToggleIcon = document.getElementById('sidebarToggleIcon');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileSidebarQuery = window.matchMedia('(max-width: 991.98px)');

        function closeSidebar() {
            sidebar.classList.remove('is-open');
            sidebarBackdrop.classList.remove('is-visible');
            document.body.classList.remove('sidebar-open');
            sidebarToggle.setAttribute('aria-expanded', 'false');
            sidebarToggle.setAttribute('aria-label', 'Buka menu');
            sidebarToggleIcon.classList.remove('bi-x-lg');
            sidebarToggleIcon.classList.add('bi-list');
        }

        sidebarToggle.addEventListener('click', function() {
            const isOpen = sidebar.classList.toggle('is-open');
            sidebarBackdrop.classList.toggle('is-visible', isOpen);
            document.body.classList.toggle('sidebar-open', isOpen);
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
            sidebarToggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
            sidebarToggleIcon.classList.toggle('bi-list', !isOpen);
            sidebarToggleIcon.classList.toggle('bi-x-lg', isOpen);
        });

        sidebarBackdrop.addEventListener('click', closeSidebar);
        sidebar.addEventListener('click', function(event) {
            if (mobileSidebarQuery.matches && event.target instanceof Element && event.target.closest('.sidebar-menu a')) {
                closeSidebar();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        mobileSidebarQuery.addEventListener('change', function(event) {
            if (!event.matches) {
                closeSidebar();
            }
        });

        const deleteConfirmModalElement = document.getElementById('deleteConfirmModal');
        const deleteConfirmModal = bootstrap.Modal.getOrCreateInstance(deleteConfirmModalElement);
        const deleteConfirmMessage = document.getElementById('deleteConfirmMessage');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        let pendingDeleteForm = null;

        document.addEventListener('submit', function(event) {
            if (event.defaultPrevented || !(event.target instanceof HTMLFormElement)) {
                return;
            }

            const form = event.target;
            if (form.dataset.skipSaveLoading === 'true') {
                delete form.dataset.skipSaveLoading;
                return;
            }

            const methodOverride = form.querySelector('input[name="_method"]');

            if (methodOverride?.value.toUpperCase() === 'DELETE') {
                if (form.dataset.deleteConfirmed === 'true') {
                    delete form.dataset.deleteConfirmed;
                    showSaveLoading('Menghapus data...');
                    return;
                }

                event.preventDefault();
                pendingDeleteForm = form;
                const itemName = form.dataset.deleteName;
                deleteConfirmMessage.textContent = itemName
                    ? `"${itemName}" akan dihapus permanen dan tidak dapat dipulihkan.`
                    : 'Data ini akan dihapus permanen dan tidak dapat dipulihkan.';
                deleteConfirmModal.show();
                return;
            }

            if (!form.target || form.target === '_self') {
                event.preventDefault();
                showSaveLoading('Menyimpan data...');
                const submitter = event.submitter;

                window.requestAnimationFrame(function() {
                    window.requestAnimationFrame(function() {
                        form.dataset.skipSaveLoading = 'true';
                        if (submitter instanceof HTMLElement && submitter.form === form) {
                            form.requestSubmit(submitter);
                        } else {
                            form.requestSubmit();
                        }
                    });
                });
            }
        });

        confirmDeleteButton.addEventListener('click', function() {
            if (!pendingDeleteForm) {
                return;
            }

            const form = pendingDeleteForm;
            const row = form.closest('tr');
            pendingDeleteForm = null;
            deleteConfirmModal.hide();

            if (row) {
                row.classList.add('fade-out-scale');
            }

            window.setTimeout(function() {
                form.dataset.deleteConfirmed = 'true';
                form.requestSubmit();
            }, row ? 180 : 0);
        });

        deleteConfirmModalElement.addEventListener('hidden.bs.modal', function() {
            pendingDeleteForm = null;
        });

        @if($errors->any() && old('_modal') === 'adminProfileModal')
            bootstrap.Modal.getOrCreateInstance(document.getElementById('adminProfileModal')).show();
        @elseif($errors->any() && old('_modal') === 'storeProfileModal')
            bootstrap.Modal.getOrCreateInstance(document.getElementById('storeProfileModal')).show();
        @endif

        // DARK MODE
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const htmlElement = document.documentElement;

        const currentTheme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
        htmlElement.setAttribute('data-bs-theme', currentTheme);
        updateIcon(currentTheme);
        themeToggle.setAttribute('aria-pressed', String(currentTheme === 'dark'));

        themeToggle.addEventListener('click', function() {
            const newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
            themeToggle.setAttribute('aria-pressed', String(newTheme === 'dark'));
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('bi-moon-fill');
                themeIcon.classList.add('bi-sun-fill');
            } else {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-fill');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>