<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>
<div class="pt-3">
    <table class="table table-hover" id="tabelaUsuarios">
        <thead>
            <tr>
                <th class="col text-start">Usuário</th>
                <th class="col-1">Turno</th>
                <th class="col-1">Botão</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <table class="table table-hover" id="tabelaAtividades" style="display: none;">
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
        carregarTabelaUsuarios();
    });

    function carregarTabelaUsuarios() {
        $('#tabelaUsuarios').DataTable({
            processing: true,
            serverSide: true,
            columnDefs: [{
                targets: 0,
                className: 'text-start'
            }, {
                targets: 2,
                orderable: false,
            }],
            ajax: {
                url: '/sistema/adm/getUsuariosAjax',
                type: 'POST'
            },
            columns: [{
                data: 'user_nome'
            }, {
                data: 'turno'
            }, {
                data: 'acoes'
            }]
        });
    }

    function carregarTabelaAtividades() {
        $('#tabelaAtividades').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/sistema/adm/getAtividadesUsuariosAjax',
                type: 'POST',
                data: function(d) {
                    d.userID = userID;
                }
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