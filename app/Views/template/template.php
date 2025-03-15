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
    </script>
</head>

<body>

</body>

</html>