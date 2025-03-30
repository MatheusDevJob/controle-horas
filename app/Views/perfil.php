<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow rounded-4">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Perfil do Usuário</h4>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome:</label>
                        <input class="form-control" id="user_nome">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuário:</label>
                        <input class="form-control" id="usuario" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Valor Hora:</label>
                        <input class="form-control maskMoney" id="valor_hora">
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary" onclick="atualizarUser($(this))">
                            <i class="fas fa-user-edit me-2"></i> Editar Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        buscaUserByID();
    })

    function buscaUserByID() {
        $.ajax({
            url: "<?= base_url("sistema/buscaUserByID") ?>",
            type: "POST",
            dataType: "JSON",
            success: function(response) {
                $("#user_nome").val(response.user_nome)
                $("#usuario").val(response.usuario)
                $("#valor_hora").val(response.valor_hora)
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function atualizarUser(botao) {
        const user_nome = $("#user_nome").val()
        const valor_hora = $("#valor_hora").val()
        const html = botao.html();
        if (!user_nome) {
            $("#user_nome").focus();
            return;
        }

        if (!valor_hora) {
            $("#valor_hora").focus();
            return;
        }
        setBotaoStatus(botao, "", true);
        $.ajax({
            url: "<?= base_url("sistema/atualizar_usuario") ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                user_nome,
                valor_hora
            },
            success: function(response) {
                if (response) {
                    toastr.success("Usuário atualizado.");
                } else {
                    toastr.warning("Erro ao atualizar usuário.");
                }
            },
            error: function(xhr, status, error) {
                toastr.error(error);
                console.error(error);
            },
            complete: function(xhr) {
                setBotaoStatus(botao, html, false);
            }
        });
    }
</script>
<?= $this->endSection(); ?>