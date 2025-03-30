<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>
<div class="pt-3">
    <table class="table table-hover" id="tabelaAtividades">
        <thead>
            <tr>
                <th class="col-2 text-start">Projeto</th>
                <th class="col">Descrição</th>
                <th class="col-2">Início Atv.</th>
                <th class="col-2">Fim Atv.</th>
                <th class="col-2">Início Turno</th>
                <th class="col-2">Fim Turno</th>
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