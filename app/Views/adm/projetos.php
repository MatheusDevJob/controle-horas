<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>

<button type="button" class="btn btn-sm btn-primary float-end mt-2" data-bs-toggle="modal" data-bs-target="#modalProjeto">
    Cadastrar Projeto
</button>

<!-- MODAL -->
<div class="modal fade" id="modalProjeto" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalProjetoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalProjetoLabel">Cadastro Projeto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="projeto" class="form-label">Projeto:</label>
                <input type="text" class="form-control" id="projeto" autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnCadastrarProjeto" onclick="cadastrarProjeto($(this))">Cadastrar</button>
            </div>
        </div>
    </div>
</div>

<div class="pt-5">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaProjetos">
                    <thead>
                        <tr>
                            <th class="text-start">Projeto</th>
                            <th class="col-2">Início Projeto</th>
                            <th class="col-1 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover visível (BS + DataTables), com dark-mode */
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

<script>
    let tabelaProjetos;

    $(document).ready(function() {
        // UX: focar input ao abrir modal e submit no Enter
        $('#modalProjeto').on('shown.bs.modal', function() {
            $('#projeto').trigger('focus');
        });
        $('#projeto').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#btnCadastrarProjeto').trigger('click');
            }
        });

        carregarTabelaProjetos();
    });

    function carregarTabelaProjetos() {
        tabelaProjetos = $('#tabelaProjetos').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, 'desc']
            ], // ordenar por data_registro (coluna 1)
            ajax: {
                url: '/sistema/adm/getProjetosAjax',
                type: 'POST'
            },
            // >>> SEMPRE especifique columns para objeto JSON
            columns: [{
                    data: 'projeto',
                    title: 'Projeto',
                    defaultContent: '—',
                    className: 'text-start'
                },
                {
                    data: 'data_registro',
                    title: 'Início Projeto',
                    defaultContent: '—',
                    className: 'text-center',
                    render: renderDataHoraBR
                },
                {
                    data: 'acoes',
                    title: 'Ações',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    function renderDataHoraBR(data, type, row) {
        // Formata apenas para display/filter
        if ((type === 'display' || type === 'filter') && data) {
            // tenta 'YYYY-MM-DD HH:MM:SS' | 'YYYY-MM-DDTHH:MM:SS'
            const m = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/.exec(data);
            if (m) {
                const D = m[3],
                    M = m[2],
                    Y = m[1],
                    h = m[4] || '00',
                    mi = m[5] || '00';
                return `${D}/${M}/${Y} ${h}:${mi}`;
            }
        }
        return data || '—';
    }

    function cadastrarProjeto(botao) {
        const projeto = ($("#projeto").val() || '').trim();
        const html = botao.html();
        if (!projeto) {
            $("#projeto").focus();
            return;
        }

        setBotaoStatus(botao, "", true);
        $.ajax({
            url: "<?= base_url("sistema/adm/cadastrar_projeto") ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                projeto
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.msg);
                    // limpar e fechar modal
                    $("#projeto").val('');
                    $('#modalProjeto').modal('hide');
                    // recarrega mantendo busca/página
                    if ($.fn.dataTable.isDataTable('#tabelaProjetos')) {
                        $('#tabelaProjetos').DataTable().ajax.reload(null, false);
                    }
                } else {
                    toastr.warning(response.msg);
                }
            },
            error: function(_, __, error) {
                toastr.error(error);
                console.error(error);
            },
            complete: function() {
                setBotaoStatus(botao, html, false);
            }
        });
    }

    function mudaStatusProjeto(botao, projetoID, status) {
        const html = botao.html();
        setBotaoStatus(botao, "", true);
        const pergunta = status === 1 ? "Deseja inativar o projeto?" : "Deseja reativar o projeto?";
        if (!confirm(pergunta)) {
            setBotaoStatus(botao, html, false);
            return;
        }

        $.ajax({
            url: "<?= base_url("sistema/adm/muda_status_projeto") ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                projetoID,
                status
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.msg);
                    if ($.fn.dataTable.isDataTable('#tabelaProjetos')) {
                        $('#tabelaProjetos').DataTable().ajax.reload(null, false);
                    }
                } else {
                    toastr.warning(response.msg);
                }
            },
            error: function(_, __, error) {
                toastr.error(error);
                console.error(error);
            },
            complete: function() {
                setBotaoStatus(botao, html, false);
            }
        });
    }
</script>
<?= $this->endSection(); ?>