<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login — Controle de Horas</title>

    <link rel="stylesheet" href="<?= base_url('bootstrap-5.3.3-dist/css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= base_url('fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('toastr/toastr.min.css') ?>">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        .auth-bg {
            background: radial-gradient(1200px 500px at 80% -10%, rgba(13, 110, 253, .12), transparent 60%),
                radial-gradient(800px 400px at -10% 110%, rgba(32, 201, 151, .12), transparent 60%),
                linear-gradient(180deg, #f8f9fa, #ffffff);
        }

        .auth-card {
            max-width: 420px;
            width: 100%
        }

        .brand-dot {
            width: .6rem;
            height: .6rem;
            border-radius: 50%;
            background: #0d6efd;
            display: inline-block;
            margin-right: .4rem
        }

        footer a {
            text-decoration: none
        }
    </style>
</head>

<body class="auth-bg">
    <main class="container flex-grow-1 d-flex align-items-center justify-content-center py-4">
        <div class="card shadow-sm border-0 auth-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="fa-regular fa-clock fa-lg me-2 text-primary"></i>
                    <h1 class="h5 mb-0">Controle de Horas</h1>
                </div>

                <?php if (isset($_GET['token_invalido'])): ?>
                    <div class="alert alert-warning py-2">
                        Sua conta foi acessada em outro navegador. Você foi desconectado por segurança.
                    </div>
                <?php endif; ?>

                <div class="mb-2 d-flex justify-content-between align-items-center">
                    <label for="cnpj" class="form-label mb-0">CNPJ</label>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="removeEmpresa()">
                        <i class="fa-solid fa-eraser me-1"></i> Limpar
                    </button>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                    <input type="text" class="form-control maskCNPJ" id="cnpj" inputmode="numeric" autocomplete="off" placeholder="00.000.000/0000-00">
                </div>

                <label for="usuario" class="form-label">Usuário</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" class="form-control" id="usuario" autocomplete="username" placeholder="Seu usuário">
                </div>

                <label for="senha" class="form-label">Senha</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control" id="senha" autocomplete="current-password" placeholder="••••••••">
                </div>

                <!-- Removido: "Não possui conta?" (apenas ADM cadastra) -->

                <button class="btn btn-primary w-100" onclick="login()" id="botaoEntrar">
                    Entrar
                </button>
            </div>
        </div>
    </main>

    <footer class="py-3 text-center text-muted small">
        <div class="container">
            <span class="brand-dot"></span> The Bots • <?= date('Y') ?> •
            <a href="mailto:mhop.developer@gmail.com"><i class="fa-regular fa-envelope"></i> mhop.developer@gmail.com</a>
        </div>
    </footer>

    <!-- Scripts no final (ordem correta) -->
    <script src="<?= base_url('jquery-3.7.1.js') ?>"></script>
    <script src="<?= base_url('jquery.mask.min.js') ?>"></script>
    <script src="<?= base_url('toastr/toastr.min.js') ?>"></script>
    <script src="<?= base_url('bootstrap-5.3.3-dist/js/bootstrap.bundle.js') ?>"></script>
    <script src="<?= base_url('js/helper.js') ?>"></script>

    <script>
        const spinner = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';
        const cnpjCookie = "<?= get_cookie('cnpj') ?>";

        $(function() {
            if (cnpjCookie) $("#cnpj").val(cnpjCookie).prop("disabled", true);
            $('.maskCNPJ').mask('00.000.000/0000-00');
            // Enter envia
            $('#senha,#usuario,#cnpj').on('keydown', e => {
                if (e.key === 'Enter') login();
            });
        });

        function muda_status_botao(id, texto, desabilitar) {
            const b = $("#" + id);
            b.prop("disabled", desabilitar).html(desabilitar ? spinner : texto || 'Entrar');
        }

        function login() {
            const cnpj = $("#cnpj").val();
            const usuario = $("#usuario").val();
            const senha = $("#senha").val();

            if ($('#cnpj').length && !$("#cnpj").prop('disabled') && !cnpj) return $("#cnpj").focus();
            if (!usuario) return $("#usuario").focus();
            if (!senha) return $("#senha").focus();

            muda_status_botao("botaoEntrar", "", true);
            $.ajax({
                url: "<?= base_url('login') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    usuario,
                    cnpj,
                    senha
                },
                success: function(r) {
                    if (r.status) {
                        location.href = "/sistema";
                    } else {
                        muda_status_botao("botaoEntrar", "Entrar", false);
                        toastr.warning(r.msg || 'Não foi possível entrar.');
                    }
                },
                error: function() {
                    muda_status_botao("botaoEntrar", "Entrar", false);
                    toastr.error('Falha na comunicação.');
                }
            });
        }

        function removeEmpresa() {
            apagarCookie('cnpj');
            location.href = "/";
        }
    </script>
</body>

</html>