<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?= base_url("bootstrap-5.3.3-dist/css/bootstrap.css") ?>">
    <script src="<?= base_url("bootstrap-5.3.3-dist/js/bootstrap.js") ?>"></script>
    <script src="<?= base_url("jquery-3.7.1.js") ?>"></script>
    <script src="<?= base_url("jquery-datatable.js") ?>"></script>
    <script src="<?= base_url("jquery.mask.min.js") ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fontawesome/css/all.min.css') ?>">
    <!-- biblioteca Toastr -->
    <link href="<?= base_url('toastr/toastr.min.css') ?>" rel="stylesheet" />
    <script src="<?= base_url('toastr/toastr.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
            $('.maskCNPJ').mask('00.000.000/0000-00');
        });
        const spinner = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';

        function muda_status_botao(id_botao, texto, desabilitar) {
            var botao = $("#" + id_botao);
            botao.attr("disabled", desabilitar);
            botao.html(desabilitar ? spinner : texto);
        }

        function login() {
            const cnpj = $("#cnpj").val();
            const usuario = $("#usuario").val();
            const senha = $("#senha").val();

            if (!cnpj && $('#cnpj').length) {
                $("#cnpj").focus()
                return;
            }

            if (!usuario) {
                $("#usuario").focus()
                return;
            }
            if (!senha) {
                $("#senha").focus()
                return;
            }

            muda_status_botao("botaoEntrar", "", true)
            $.ajax({
                url: "<?= base_url("login") ?>",
                type: "POST",
                dataType: "JSON",
                data: {
                    usuario,
                    cnpj,
                    senha
                },
                success: function(response) {
                    if (response.status) {
                        window.location.href = "/sistema";
                    } else {
                        muda_status_botao("botaoEntrar", "Entrar", false)
                        toastr.warning(response.msg)
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    muda_status_botao("botaoEntrar", "Entrar", false)
                }
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="border p-4 rounded" style="width: 400px">
                <?php if (isset($_GET['token_invalido'])): ?>
                    <div class="alert alert-warning">
                        Sua conta foi acessada em outro navegador. Você foi desconectado por segurança.
                    </div>
                <?php endif; ?>
                <?php
                if (!get_cookie('cnpj')) {
                    echo '<label for="cnpj" class="form-label">Cnpj:</label>';
                    echo '<input type="text" class="form-control maskCNPJ" id="cnpj">';
                } ?>
                <label for="usuario" class="form-label">Usuário:</label>
                <input type="text" class="form-control" id="usuario">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" class="form-control" id="senha">
                <a href="<?= base_url("criar_conta") ?>">Não possui conta?</a>
                <button class="btn btn-sm btn-primary mt-2 float-end" onclick="login()" id="botaoEntrar">Entrar</button>
            </div>
        </div>
    </div>
</body>

</html>