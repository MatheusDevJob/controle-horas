<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>
<style>
    tbody tr td:not(:nth-child(2)) {
        white-space: nowrap;
        text-align: center !important;
    }
</style>
<div class="pt-3">
    <table class="table table-hover" id="tabelaAtividades">
        <thead>
            <tr>
                <th>Projeto</th>
                <th>Descrição</th>
                <th>Início Atv.</th>
                <th>Fim Atv.</th>
                <th>Início Turno</th>
                <th>Fim Turno</th>
                <th>Trabalhadas</th>
                <th>R$/hora</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        carregarTabelaAtividades();
    });

    function carregarTabelaAtividades() {
        $('#tabelaAtividades').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [
                [4, 'desc']
            ],
            ajax: {
                url: '/sistema/getAtividadesUsuariosAjax',
                type: 'POST',
            },
            columns: [{
                data: 'projeto'
            }, {
                data: 'descricao'
            }, {
                data: 'inicio_atividade'
            }, {
                data: 'fim_atividade'
            }, {
                data: 'inicio_turno'
            }, {
                data: 'fim_turno'
            }, {
                data: 'horas_trabalhadas'
            }, {
                data: 'valor_hora'
            }, {
                data: 'valor_atividade'
            }]
        });
    }

    let userID;

    function selecionarUsuario(botao, _userID) {
        userID = _userID;

        if (!$.fn.dataTable.isDataTable('#tabelaAtividades')) carregarTabelaAtividades()
        else $('#tabelaAtividades').DataTable().search('').draw();

        $("#tabelaAtividades").show()
    }
</script>
<?= $this->endSection(); ?>