<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>
<style>
    tbody tr td:not(:nth-child(2)) {
        white-space: nowrap;
        text-align: center !important;
    }

    #dataI,
    #dataF {
        width: 40% !important;
    }
</style>
<div class="pt-3">
    <div class="row">
        <div class="col-2">
            <?= view("utility/exportar_xlsx") ?>
        </div>
        <div class="col-5">
            <label for="dataI" class="form-label">De</label>
            <input type="date" class="form-control form-control-sm d-inline" id="dataI">
            <label for="dataF" class="form-label">até</label>
            <input type="date" class="form-control form-control-sm d-inline" id="dataF">
        </div>
        <div class="col-3">
            <label for="projeto" class="form-label">Projeto</label>
            <select id="projeto" class="form-select form-select-sm d-inline w-50">
                <option value="">Selecione</option>
                <?php
                foreach ($projetos as $value) {
                    echo "<option value=\"{$value["projeto_id"]}\">{$value["projeto"]}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-1">
            <button class="btn btn-sm btn-primary" onclick="reload()">Buscar</button>
        </div>
    </div>
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
<h3>Horas Trabalhadas: <span id="campoTotalHoras" class="fw-bold text-primary"></span></h3>


<script>
    $(document).ready(function() {
        carregarTabelaAtividades();
        $("#dataI").on("change", function() {
            dataI = this.value;
        });
        $("#dataF").on("change", function() {
            dataF = this.value;
        });
        $("#projeto").on("change", function() {
            projeto = this.value;
        });
    });

    let dataI, dataF, projeto, tabelaAtividades;

    function reload() {
        tabelaAtividades.search($("#dt-search-0").val()).draw();
    }

    function carregarTabelaAtividades() {
        tabelaAtividades = $('#tabelaAtividades').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [
                [4, 'desc']
            ],
            ajax: {
                url: '/sistema/getAtividadesUsuariosAjax',
                type: 'POST',
                data: function(d) {
                    d.dataI = $("#dataI").val();
                    d.dataF = $("#dataF").val();
                    d.projeto = $("#projeto").val();
                },
                dataSrc: function(json) {
                    $('#campoTotalHoras').text(json.totalHoras.total_geral_horas || '00:00:00');
                    return json.data;
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

    }
</script>
<?= $this->endSection(); ?>