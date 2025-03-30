<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link rel="stylesheet" href="<?= base_url("bootstrap-5.3.3-dist/css/bootstrap.css") ?>">
    <script src="<?= base_url("bootstrap-5.3.3-dist/js/bootstrap.js") ?>"></script>
    <script src="<?= base_url("jquery-3.7.1.js") ?>"></script>
    <script src="<?= base_url("jquery-datatable.js") ?>"></script>
    <script src="<?= base_url("jquery.mask.min.js") ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fontawesome/css/all.min.css') ?>">
    <!-- biblioteca Toastr -->
    <link href="<?= base_url('toastr/toastr.min.css') ?>" rel="stylesheet" />
    <script src="<?= base_url('toastr/toastr.min.js') ?>"></script>
    <link href="<?= base_url('datatable/datatables.min.css') ?>" rel="stylesheet" />
    <script src="<?= base_url('datatable/datatables.min.js') ?>"></script>

    <script>
        $(document).ready(function() {
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
        });
        let spinner = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';

        function muda_status_botao(id_botao, texto, desabilitar) {
            var botao = $("#" + id_botao);
            botao.attr("disabled", desabilitar);
            botao.html(desabilitar ? spinner : texto);
        }

        function setBotaoStatus(botao, texto, desabilitar) {
            $(botao).attr("disabled", desabilitar);
            $(botao).html(desabilitar ? spinner : texto);
        }

        function logout() {
            if (!confirm("Deseja sair da conta?")) return;

            $.ajax({
                url: "<?= base_url("logout") ?>",
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    window.location.href = '/';
                },
                error: function(xhr, statu, error) {
                    console.error(error);
                    toastr.error(error);
                }
            })
        }
    </script>
    <style>
        body {
            background-color: #EBE6DF;
        }
    </style>
</head>

<body>
    <button class="btn btn-outline-dark float-end me-2 mt-2" onclick="logout()">Sair</button>
    <a href="<?= base_url("sistema/adm/visualizar_usuarios") ?>" class="btn btn-info float-end me-2 mt-2">Visualizar <i class="fa-solid fa-users"></i></a>
    <div class="container" style="height: 100vh;">
        <?= $this->renderSection("servico"); ?>
    </div>
</body>

</html>