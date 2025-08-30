<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>

<style>
    /* Colunas: tudo central menos Descrição */
    tbody tr td:not(:nth-child(2)) {
        white-space: nowrap;
        text-align: center !important;
    }

    /* Inputs de data mais compactos lado a lado */
    #dataI,
    #dataF {
        width: 40% !important;
        display: inline-block;
    }

    /* Hover visível (BS + DataTables) */
    .table.table-hover tbody tr:hover>*,
    table.dataTable.hover tbody tr:hover>*,
    table.dataTable.display tbody tr:hover>* {
        background-color: rgba(255, 255, 255, .06) !important;
        transition: background-color .12s ease-in-out;
    }

    :root .table.table-hover tbody tr:hover>*,
    :root table.dataTable.hover tbody tr:hover>*,
    :root table.dataTable.display tbody tr:hover>* {
        background-color: rgba(13, 110, 253, .06) !important;
    }

    [data-theme="dark"] .table.table-hover tbody tr:hover>*,
    [data-theme="dark"] table.dataTable.hover tbody tr:hover>*,
    [data-theme="dark"] table.dataTable.display tbody tr:hover>* {
        background-color: color-mix(in srgb, var(--surface-2) 92%, transparent) !important;
    }
</style>

<div class="pt-3">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
            <?= view("utility/exportar_xlsx") ?>
        </div>

        <div class="col-12 col-md-5">
            <label class="form-label d-block">Período</label>
            <input type="date" class="form-control form-control-sm d-inline" id="dataI">
            <span class="mx-2">até</span>
            <input type="date" class="form-control form-control-sm d-inline" id="dataF">
        </div>

        <div class="col-12 col-md-3">
            <label for="projeto" class="form-label">Projeto</label>
            <select id="projeto" class="form-select form-select-sm d-inline w-75">
                <option value="">Selecione</option>
                <?php foreach ($projetos as $value): ?>
                    <option value="<?= $value['projeto_id'] ?>"><?= esc($value['projeto']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-1">
            <button class="btn btn-sm btn-primary w-100" onclick="reload()">Buscar</button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaAtividades">
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
        </div>
    </div>
</div>

<h3 class="mt-3">
    Horas Trabalhadas:
    <span id="campoTotalHoras" class="fw-bold text-primary"></span>
</h3>

<script>
    let tabelaAtividades;

    $(document).ready(function() {
        setDefaultDateRange(); // datas: 1º do mês -> hoje
        bindFilterChanges(); // recarrega ao mudar filtros (opcional)
        carregarTabelaAtividades(); // inicia DataTable
    });

    function pad2(n) {
        return String(n).padStart(2, '0');
    }

    function setDefaultDateRange() {
        const now = new Date();
        const y = now.getFullYear();
        const m = now.getMonth() + 1;
        const first = `${y}-${pad2(m)}-01`;
        const today = `${y}-${pad2(m)}-${pad2(now.getDate())}`;

        // só seta se estiver vazio (permite navegação voltar manter valor)
        if (!$('#dataI').val()) $('#dataI').val(first);
        if (!$('#dataF').val()) $('#dataF').val(today);
    }

    function bindFilterChanges() {
        $('#dataI, #dataF, #projeto').on('change', function() {
            if ($.fn.dataTable.isDataTable('#tabelaAtividades')) {
                tabelaAtividades.ajax.reload(null, false);
            }
        });
    }

    function reload() {
        if ($.fn.dataTable.isDataTable('#tabelaAtividades')) {
            tabelaAtividades.ajax.reload(null, false); // mantém página atual
        }
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
                    d.dataI = $('#dataI').val();
                    d.dataF = $('#dataF').val();
                    d.projeto = $('#projeto').val();
                },
                dataSrc: function(json) {
                    $('#campoTotalHoras').text(json?.totalHoras?.total_geral_horas || '00:00:00');
                    return json.data || [];
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
            }],
            columnDefs: [{
                    targets: [7, 8],
                    className: 'text-end'
                }, // R$/hora e Total à direita
                {
                    targets: [2, 3, 4, 5, 6],
                    className: 'text-center'
                } // datas/horas centralizadas
            ]
        });
    }

    // Se você usa seleção por usuário (mantive tua função)
    let userID;

    function selecionarUsuario(botao, _userID) {
        userID = _userID;
        if (!$.fn.dataTable.isDataTable('#tabelaAtividades')) carregarTabelaAtividades();
        else $('#tabelaAtividades').DataTable().search('').draw();
    }
</script>

<?= $this->endSection(); ?>