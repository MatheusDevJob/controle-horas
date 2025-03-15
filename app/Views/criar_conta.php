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
        const spinner = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';

        function muda_status_botao(id_botao, texto, desabilitar) {
            var botao = $("#" + id_botao);
            botao.attr("disabled", desabilitar);
            botao.html(desabilitar ? spinner : texto);
        }

        function cadastrar() {
            const usuario = $("#usuario").val();
            const senha = $("#senha").val();
            const userNome = $("#userNome").val();

            if (!usuario) {
                $("#usuario").focus()
                return;
            }

            if (!senha) {
                $("#senha").focus()
                return;
            }

            if (!userNome) {
                $("#userNome").focus()
                return;
            }

            muda_status_botao("botaoRegistrar", "", true)

            $.ajax({
                url: "<?= base_url("cadastrar_usuario") ?>",
                type: "POST",
                dataType: "JSON",
                data: {
                    usuario,
                    senha,
                    userNome
                },
                success: function(response) {
                    muda_status_botao("botaoRegistrar", "Registrar", false)

                    if (response.status)
                        window.location.href = "/";
                    else
                        toastr.warning(response.msg)

                },
                error: function(xhr, status, error) {
                    console.error(error);
                    muda_status_botao("botaoRegistrar", "Registrar", false)
                }
            });
        }
    </script>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="container">
            <div class="row">
                <div class="col">
                    <label for="usuario" class="form-label">Usuário:</label>
                    <input type="text" class="form-control" id="usuario">
                </div>
                <div class="col">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" class="form-control" id="senha">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="userNome" class="form-label">Nome Usuário:</label>
                    <input type="text" class="form-control" id="userNome">
                </div>
            </div>
            <button class="btn btn-primary mt-2 float-end" onclick="cadastrar()" id="botaoRegistrar">Registrar</button>
            <a class="btn btn-outline-primary mt-2 me-2 float-end" href="<?= base_url("/") ?>">Voltar</a>
        </div>
    </div>
</body>

</html>