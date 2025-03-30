<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>

<button type="button" class="btn btn-sm btn-primary float-end mt-2" data-bs-toggle="modal" data-bs-target="#modalProjeto">
    Cadastrar Projeto
</button>


<div class="modal fade" id="modalProjeto" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalProjetoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalProjetoLabel">Cadastro Projeto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="projeto" class="form-label">Projeto:</label>
                <input type="text" class="form-control" id="projeto">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="cadastrarProjeto($(this))">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<div class="pt-5">
    <table class="table table-hover" id="tabelaProjetos">
        <thead>
            <tr>
                <th class="col text-start">Projeto</th>
                <th class="col-2">Início Projeto</th>
                <th class="col-1">Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        carregarTabelaProjetos();
    });

    function cadastrarProjeto(botao) {
        const projeto = $("#projeto").val();
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
                    $('#tabelaProjetos').DataTable().search($("#dt-search-0").val()).draw();
                    toastr.success(response.msg)
                } else {
                    toastr.warning(response.msg)
                }
            },
            error: function(xhr, status, error) {
                toastr.error(error)
                console.log(error);
            },
            complete: function(xhr) {
                setBotaoStatus(botao, html, false);
            }
        });
    }

    function carregarTabelaProjetos() {
        $('#tabelaProjetos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/sistema/adm/getProjetosAjax',
                type: 'POST',
            },
            columns: [{
                data: 'projeto'
            }, {
                data: 'data_registro'
            }, {
                data: 'acoes'
            }]
        });
    }

    function mudaStatusProjeto(botao, projetoID, status) {
        const html = botao.html();
        setBotaoStatus(botao, "", true);
        if (!confirm("Deseja inativar o projeto?")) return;

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
                    $('#tabelaProjetos').DataTable().search($("#dt-search-0").val()).draw();
                    toastr.success(response.msg)
                } else {
                    toastr.warning(response.msg)
                }
            },
            error: function(xhr, status, error) {
                toastr.error(error)
                console.log(error);
            },
            complete: function(xhr) {
                setBotaoStatus(botao, html, false);
            }
        });
    }
</script>
<?= $this->endSection(); ?>