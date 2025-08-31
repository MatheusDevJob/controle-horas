<!DOCTYPE html>
<html lang="pt-br"> <!-- data-theme será definido pelo script pré-CSS -->

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $titulo ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('icon.png?v=1') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('icon.png?v=1') ?>">
    <link rel="shortcut icon" href="<?= base_url('icon.png?v=1') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('icon.png?v=1') ?>">

    <script>
        (function() {
            var KEY = 'ui.theme';
            var saved = null;
            try {
                saved = localStorage.getItem(KEY);
            } catch (e) {}
            // Default se não existir valor salvo:
            var theme = saved || 'dark'; // <--- mude para 'light' se preferir
            var root = document.documentElement;
            root.setAttribute('data-theme', theme);

            // Previne flash: define fundo e color-scheme imediatamente
            var bg = theme === 'dark' ? '#0e1014' : '#f6f7f9';
            var cs = theme === 'dark' ? 'dark' : 'light';
            var s = document.createElement('style');
            s.textContent = 'html{background:' + bg + ';color-scheme:' + cs + '}';
            document.head.appendChild(s);

            // Garante persistência
            try {
                localStorage.setItem(KEY, theme);
            } catch (e) {}
        })();
    </script>

    <!-- Bootstrap / Libs -->
    <link rel="stylesheet" href="<?= base_url('bootstrap-5.3.3-dist/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= base_url('fontawesome/css/all.min.css') ?>">
    <link href="<?= base_url('toastr/toastr.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('datatable/datatables.min.css') ?>" rel="stylesheet" />

    <script src="<?= base_url('jquery-3.7.1.js') ?>"></script>
    <script src="<?= base_url('bootstrap-5.3.3-dist/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('jquery-datatable.js') ?>"></script>
    <script src="<?= base_url('jquery.mask.min.js') ?>"></script>
    <script src="<?= base_url('toastr/toastr.min.js') ?>"></script>
    <script src="<?= base_url('datatable/datatables.min.js') ?>"></script>
    <script src="<?= base_url('sheet-js/xlsx.full.min.js') ?>"></script>
    <script src="<?= base_url('js/helper.js') ?>"></script>

    <style>
        /* ========== THEME TOKENS ========== */
        :root {
            --sidebar-w: 240px;
            --radius: 12px;
            --shadow-1: 0 4px 18px rgba(0, 0, 0, .06);

            /* Light */
            --bg: #f6f7f9;
            --fg: #191c22;
            --fg-muted: #606575;
            --surface-1: #ffffff;
            --surface-2: #f0f2f5;
            --border: #e6e8ee;

            --brand: #0d6efd;
        }

        [data-theme="dark"] {
            --bg: #0e1014;
            --fg: #e9ecf1;
            --fg-muted: #a6adbb;
            --surface-1: #161a21;
            --surface-2: #1b2029;
            --border: #2a2f3a;
        }

        /* ========== APP LAYOUT ========== */
        html,
        body {
            height: 100%;
        }

        body {
            background: var(--bg);
            color: var(--fg);
        }

        .app-shell {
            display: grid;
            grid-template-columns: var(--sidebar-w) 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .app-sidebar {
            position: sticky;
            top: 0;
            height: 100dvh;
            padding: 20px 16px;
            background: var(--surface-1);
            border-right: 1px solid var(--border);
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            letter-spacing: .2px;
            margin-bottom: 16px;
            color: var(--fg);
            text-decoration: none;
        }

        .app-brand i {
            color: var(--brand);
        }

        .app-nav .nav-link {
            border-radius: 10px;
            color: var(--fg-muted);
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .app-nav .nav-link i {
            width: 18px;
            text-align: center;
        }

        .app-nav .nav-link:hover,
        .app-nav .nav-link.active {
            background: var(--surface-2);
            color: var(--fg);
        }

        /* Header */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--surface-1);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-1);
        }

        .app-header .navbar {
            padding-block: .5rem;
        }

        /* Content */
        .app-content {
            padding: 24px;
        }

        /* Cards / Tables */
        .card {
            background: var(--surface-1);
            border-color: var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-1);
        }

        .card-header {
            background: var(--surface-2);
            border-bottom-color: var(--border);
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
        }

        .btn {
            border-radius: 10px;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: color-mix(in srgb, var(--surface-2) 75%, transparent);
            --bs-table-hover-bg: color-mix(in srgb, var(--surface-2) 85%, transparent);
        }

        .table thead th {
            color: var(--fg-muted);
            text-transform: uppercase;
            font-size: .8rem;
            letter-spacing: .04em;
        }

        /* Forms */
        .form-control,
        .form-select {
            border-radius: 10px;
            background: var(--surface-1);
            border-color: var(--border);
            color: var(--fg);
        }

        .form-control::placeholder {
            color: color-mix(in srgb, var(--fg) 40%, transparent);
        }

        .input-group-text {
            background: var(--surface-2);
            border-color: var(--border);
            color: var(--fg-muted);
        }

        /* Toastr */
        #toast-container>div {
            border-radius: 10px !important;
            box-shadow: var(--shadow-1);
            border: 1px solid var(--border);
            background-color: var(--surface-1);
            color: var(--fg);
            opacity: .98;
        }

        #toast-container>.toast-success {
            border-left: 4px solid var(--bs-success);
        }

        #toast-container>.toast-warning {
            border-left: 4px solid var(--bs-warning);
        }

        #toast-container>.toast-error {
            border-left: 4px solid var(--bs-danger);
        }

        .muted {
            color: var(--fg-muted);
        }

        .shadow-none {
            box-shadow: none !important;
        }

        @media (max-width: 992px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .app-sidebar {
                position: static;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }
        }
    </style>

    <style>
        /* ===== Correções de tema escuro ===== */
        [data-theme="dark"] {
            /* Alinha Bootstrap às tuas tokens */
            --bs-body-bg: var(--bg);
            --bs-body-color: var(--fg);
            --bs-border-color: var(--border);
            --bs-tertiary-bg: var(--surface-2);
            --bs-secondary-color: var(--fg-muted);

            /* Subtle variants (badges, alerts, bg-*-subtle) */
            --bs-primary-bg-subtle: color-mix(in srgb, var(--brand) 18%, transparent);
            --bs-primary-text-emphasis: color-mix(in srgb, var(--brand) 82%, white 10%);
            --bs-success-bg-subtle: color-mix(in srgb, #198754 18%, transparent);
            --bs-success-text-emphasis: color-mix(in srgb, #198754 80%, white 10%);
            --bs-warning-bg-subtle: color-mix(in srgb, #ffc107 22%, transparent);
            --bs-warning-text-emphasis: color-mix(in srgb, #ffc107 75%, black 10%);
            --bs-danger-bg-subtle: color-mix(in srgb, #dc3545 18%, transparent);
            --bs-danger-text-emphasis: color-mix(in srgb, #dc3545 82%, white 10%);
        }

        /* Sidebar / header: contraste e separação */
        [data-theme="dark"] .app-sidebar {
            background: var(--surface-1);
            border-right: 1px solid var(--border);
        }

        [data-theme="dark"] .app-header {
            background: var(--surface-1);
            border-bottom: 1px solid var(--border);
        }

        /* Navegação: estados de hover/active bem visíveis */
        .app-nav .nav-link {
            color: var(--fg-muted);
        }

        .app-nav .nav-link:hover,
        .app-nav .nav-link.active {
            background: var(--surface-2);
            color: var(--fg);
        }

        /* Cards e headers */
        .card {
            background: var(--surface-1);
            border-color: var(--border);
        }

        .card-header {
            background: var(--surface-2);
            color: var(--fg);
            border-bottom-color: var(--border);
        }

        /* Form controls (inputs/selects): */
        .form-control,
        .form-select {
            background: var(--surface-2);
            border-color: var(--border);
            color: var(--fg);
        }

        .form-control::placeholder {
            color: color-mix(in srgb, var(--fg) 55%, transparent);
        }

        .form-control:disabled,
        .form-select:disabled {
            background: color-mix(in srgb, var(--surface-2) 70%, black 30%);
            color: var(--fg-muted);
        }

        /* Focus ring visível no dark */
        :root {
            --ring: 0 0 0 .2rem rgba(13, 110, 253, .35);
        }

        /* azul do BS */
        .form-control:focus,
        .form-select:focus,
        .btn:focus {
            border-color: color-mix(in srgb, var(--brand) 70%, var(--border));
            box-shadow: var(--ring);
            outline: 0;
        }

        /* Dropdowns e modais */
        .dropdown-menu {
            background: var(--surface-1);
            border-color: var(--border);
            color: var(--fg);
        }

        .dropdown-item {
            color: var(--fg);
        }

        .dropdown-item:hover {
            background: var(--surface-2);
        }

        .modal-content {
            background: var(--surface-1);
            color: var(--fg);
            border-color: var(--border);
        }

        .modal-backdrop.show {
            opacity: .65;
        }

        /* Tabelas: header e listras com contraste melhor */
        .table {
            --bs-table-color: var(--fg);
            --bs-table-border-color: var(--border);
            --bs-table-striped-bg: color-mix(in srgb, var(--surface-2) 85%, transparent);
            --bs-table-hover-bg: color-mix(in srgb, var(--surface-2) 92%, transparent);
        }

        .table thead th {
            color: var(--fg);
            background: var(--surface-2);
            border-bottom-color: var(--border);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .table> :not(caption)>*>* {
            border-bottom-color: var(--border);
        }

        /* Botões outline ganham contraste no dark */
        .btn-outline-secondary,
        .btn-outline-light,
        .btn-outline-dark {
            color: var(--fg);
            border-color: var(--border);
            background: transparent;
        }

        .btn-outline-secondary:hover,
        .btn-outline-light:hover,
        .btn-outline-dark:hover {
            background: var(--surface-2);
            border-color: var(--border);
        }

        /* Badges “*-subtle” legíveis no dark */
        .bg-primary-subtle {
            background-color: var(--bs-primary-bg-subtle) !important;
            color: var(--bs-primary-text-emphasis) !important;
        }

        .bg-success-subtle {
            background-color: var(--bs-success-bg-subtle) !important;
            color: var(--bs-success-text-emphasis) !important;
        }

        .bg-warning-subtle {
            background-color: var(--bs-warning-bg-subtle) !important;
            color: var(--bs-warning-text-emphasis) !important;
        }

        .bg-danger-subtle {
            background-color: var(--bs-danger-bg-subtle) !important;
            color: var(--bs-danger-text-emphasis) !important;
        }

        /* Toastr já segue o tema; reforço só o título/texto */
        #toast-container>div {
            background: var(--surface-1);
            color: var(--fg);
            border-color: var(--border);
        }

        /* Scrollbar discreta no dark (Chromium/Opera) */
        [data-theme="dark"] * {
            scrollbar-width: thin;
            scrollbar-color: color-mix(in srgb, var(--fg) 25%, transparent) transparent;
        }

        [data-theme="dark"] *::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        [data-theme="dark"] *::-webkit-scrollbar-thumb {
            background: color-mix(in srgb, var(--fg) 20%, transparent);
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        /* DataTables (se usar) */
        [data-theme="dark"] .dataTables_wrapper .dataTables_length select,
        [data-theme="dark"] .dataTables_wrapper .dataTables_filter input {
            background: var(--surface-2);
            color: var(--fg);
            border-color: var(--border);
        }

        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--fg) !important;
            border: 1px solid var(--border) !important;
            background: var(--surface-1) !important;
        }

        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--surface-2) !important;
        }

        /* Hover visível na tabela (BS + DataTables) */
        .table.table-hover tbody tr:hover>*,
        table.dataTable.hover tbody tr:hover>*,
        table.dataTable.display tbody tr:hover>* {
            background-color: rgba(255, 255, 255, .06) !important;
            /* fallback para dark */
            transition: background-color .12s ease-in-out;
        }

        /* Bootstrap usa --bs-table-hover-color; no dark, garanta texto claro */
        [data-theme="dark"] .table {
            --bs-table-hover-color: var(--fg);
        }

        /* Garante que a cor do texto não escureça no hover (BS + DataTables) */
        .table.table-hover tbody tr:hover>*,
        table.dataTable.hover tbody tr:hover>*,
        table.dataTable.display tbody tr:hover>* {
            color: var(--fg) !important;
        }

        /* Se você usa .table-active ou linhas “sutis”, mantenha contraste no dark */
        [data-theme="dark"] .table-active>* {
            background-color: color-mix(in srgb, var(--surface-2) 88%, transparent) !important;
            color: var(--fg) !important;
        }


        /* Ajuste fino quando não estiver no dark */
        :root .table.table-hover tbody tr:hover>*,
        :root table.dataTable.hover tbody tr:hover>*,
        :root table.dataTable.display tbody tr:hover>* {
            background-color: rgba(13, 110, 253, .06) !important;
            /* leve azul no tema claro */
        }

        /* Se você usa as tokens do template novo, ativa isso também: */
        [data-theme="dark"] .table.table-hover tbody tr:hover>*,
        [data-theme="dark"] table.dataTable.hover tbody tr:hover>*,
        [data-theme="dark"] table.dataTable.display tbody tr:hover>* {
            background-color: color-mix(in srgb, var(--surface-2) 92%, transparent) !important;
        }

        /* === Toastr clássico, independente do tema === */
        #toast-container>div {
            /* volta pro layout padrão do toastr */
            margin: 0 0 6px !important;
            padding: 15px 15px 15px 50px !important;
            width: 320px !important;
            border-radius: 8px !important;
            box-shadow: 0 0 12px rgba(0, 0, 0, .3) !important;
            color: #fff !important;

            /* impede o “xadrez” (ícone repetido) e qualquer herança do tema */
            background-image: none !important;
            background-repeat: no-repeat !important;
            background-position: 15px center !important;
            border: 0 !important;
            background-color: #333 !important;
            /* será sobrescrito por tipo abaixo */
        }

        /* Cores sólidas por tipo (sem imagem de fundo) */
        #toast-container>.toast-success {
            background-color: #28a745 !important;
        }

        #toast-container>.toast-error {
            background-color: #dc3545 !important;
        }

        #toast-container>.toast-info {
            background-color: #0dcaf0 !important;
            color: #0b2e3b !important;
        }

        #toast-container>.toast-warning {
            background-color: #ffc107 !important;
            color: #111 !important;
        }

        /* Barra de progresso e botão fechar visíveis em qualquer cor */
        #toast-container .toast-progress {
            background: rgba(255, 255, 255, .85) !important;
        }

        #toast-container>.toast-info .toast-progress,
        #toast-container>.toast-warning .toast-progress {
            background: rgba(0, 0, 0, .35) !important;
        }

        #toast-container .toast-close-button {
            text-shadow: none !important;
            opacity: .9 !important;
            color: #fff !important;
        }

        #toast-container>.toast-info .toast-close-button,
        #toast-container>.toast-warning .toast-close-button {
            color: #111 !important;
        }

        /* Opcional: largura fluida em telas pequenas */
        @media (max-width: 480px) {
            #toast-container>div {
                width: calc(100vw - 32px) !important;
            }
        }
    </style>


    <script>
        // Máscaras + relógio + toggle
        $(function() {
            $('.number_only').mask('0#');
            $('.decimal_only').mask('#0.00', {
                reverse: true
            });
            $('.maskTel').mask('(00) 0 0000-0000');
            $('.maskCNPJ').mask('00.000.000/0000-00');
            $('.maskCPF').mask('000.000.000-00');
            $('.maskCNH').mask('00000000000-0');
            $('.maskPlacaVeiculo').mask('AAA-0A00');
            $('.maskMoney').mask('#.##0,00', {
                reverse: true
            });

            atualizarDataHora();
            setInterval(atualizarDataHora, 1000);

            const KEY = 'ui.theme';
            const root = document.documentElement;

            // Sincroniza ícone com o tema atual
            (function syncIcon() {
                const t = root.getAttribute('data-theme');
                $('#themeToggle i').toggleClass('fa-sun', t === 'dark').toggleClass('fa-moon', t !== 'dark');
            })();

            $('#themeToggle').on('click', function() {
                const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-theme', next);
                try {
                    localStorage.setItem(KEY, next);
                } catch (e) {}

                // Ajusta imediato para evitar flashes em navegações
                document.documentElement.style.background = (next === 'dark' ? '#0e1014' : '#f6f7f9');
                document.documentElement.style.colorScheme = (next === 'dark' ? 'dark' : 'light');

                $(this).find('i').toggleClass('fa-moon fa-sun');
            });
        });

        // Helpers existentes
        let spinner = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';

        function muda_status_botao(id_botao, texto, desabilitar) {
            var botao = $("#" + id_botao);
            botao.prop("disabled", desabilitar);
            botao.html(desabilitar ? spinner : texto);
        }

        function setBotaoStatus(botao, texto, desabilitar) {
            $(botao).prop("disabled", desabilitar);
            $(botao).html(desabilitar ? spinner : texto);
        }

        function logout() {
            if (!confirm("Deseja sair da conta?")) return;
            $.ajax({
                url: "<?= base_url('logout') ?>",
                type: "POST",
                dataType: "JSON",
                success: function() {
                    window.location.href = '/';
                },
                error: function(_, __, error) {
                    console.error(error);
                    toastr.error(error);
                }
            });
        }

        function atualizarDataHora() {
            const dataHoraAtual = obterDataHoraFormatada();
            $('#elementoDataHora').text(dataHoraAtual);
        }
    </script>
</head>

<body>
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <a class="app-brand" href="/sistema">
                <i class="fa-solid fa-clipboard-list"></i>
                <span><?= $titulo ?></span>
            </a>

            <nav class="app-nav nav flex-column">
                <a class="nav-link" href="/sistema"><i class="fa-solid fa-house"></i> Home</a>

                <?php if ($_SESSION['tipo_usuario_fk'] == 1): ?>
                    <a class="nav-link" href="<?= base_url('sistema/adm/visualizar_usuarios') ?>"><i class="fa-solid fa-users"></i> Colaboradores</a>
                    <a class="nav-link" href="<?= base_url('sistema/adm/visualizar_projetos') ?>"><i class="fa-solid fa-folder-open"></i> Projetos</a>
                <?php endif; ?>

                <a class="nav-link" href="<?= base_url('sistema/visualizar_atividades') ?>"><i class="fa-solid fa-clipboard-check"></i> Atividades</a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="d-flex flex-column">
            <!-- Header -->
            <header class="app-header">
                <nav class="navbar navbar-expand px-3">
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <span id="elementoDataHora" class="small text-body-secondary"></span>
                        <button id="themeToggle" class="btn btn-sm btn-outline-secondary" title="Tema">
                            <i class="fa-solid fa-moon"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="logout()">
                            <i class="fa-solid fa-right-from-bracket"></i> Sair
                        </button>
                    </div>
                </nav>
            </header>

            <!-- Content -->
            <main class="app-content">
                <div class="container-fluid">
                    <?= $this->renderSection('servico'); ?>
                </div>
            </main>
        </div>
    </div>
</body>

</html>