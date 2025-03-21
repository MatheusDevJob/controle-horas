<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>
<div class="h-100 pt-3">
    <div class="filtros">
        <div class="row">
            <div class="col-2">
                <label for="clienteSelect" class="form-label">Cliente:</label>
                <span id="spinner"></span>
                <select style="display: none;" id="cliente" class="form-select mb-2">

                </select>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
    <div class="card p-4">
        <div class="d-flex justify-content-end">
            <button
                class="btn btn-dark me-2"
                id="botaoConcluirTurno"
                style="display: none;"
                onclick="concluirTurno()">Concluir Turno</button>
            <button
                class="btn btn-primary"
                id="botaoCriarAtividade"
                onclick="criarAtividade($(this))">Iniciar Turno</button>
        </div>
        <div class="row">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Iníco Atividade</th>
                        <th>Descrição</th>
                        <th>Conclusão Atividade</th>
                    </tr>
                </thead>
                <tbody id="tabelaRegistros"></tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?= base_url("js/helper.js") ?>"></script>
<script>
    $(document).ready(function() {
        buscarClientes();
    });

    async function criarAtividade(botao) {
        if (!confirm("Deseja iniciar turno?")) return;
        const idUnico = Date.now();

        try {
            const data = obterDataHoraFormatada();
            const JQueryXHR = await iniciarTurno(data);
            if (!JQueryXHR.status) throw new Error("Não foi possível iniciar o turno");

            const inicioAtividade = await iniciarAtividade(data);
            if (!inicioAtividade.status) throw new Error("Erro a registrar atividade");

            montaAtividade(idUnico, data);
            $("#botaoConcluirTurno").show()
        } catch (error) {
            toastr.error(`Erro ${error}.`);
            console.error(error);
        }
    }

    function montaAtividade(idUnico, data) {
        const row = `
                <tr class="classe${idUnico}">
                    <td class="align-middle">${data}</td>
                    <td><input id="id${idUnico}" placeholder="Descrição da atividade" class="form-control align-middle"></td>
                    <td class="align-middle">
                        <button class="btn btn-sm btn-success botoesNovaAtividade" onclick="novaAtividade($(this), ${idUnico}, true)">Nova atividade</button>
                        <button class="btn btn-sm btn-outline-success botoesNovaAtividade" onclick="novaAtividade($(this), ${idUnico}, false)">Apenas concluir</button>
                    </td>
                </tr>
            `;
        $("#tabelaRegistros").prepend(row);
    }

    async function novaAtividade(botao, id, novaAtividade) {
        try {
            const idUnico = Date.now();
            const data = obterDataHoraFormatada();
            const desc = $("#id" + id).val();
            const td = botao.parent();


            if (!desc) {
                toastr.info("Informe o que foi realizado na atividade.")
                return;
            }
            setBotaoStatus(botao, "", true);

            const atividade = await concluirAtividade(data, desc);
            if (!atividade.status) throw new Error("Erro ao concluir atividade");

            if (novaAtividade) {
                const inicioAtividade = await iniciarAtividade(data);
                if (!inicioAtividade.status) throw new Error("Erro ao registrar nova atividade");
                montaAtividade(idUnico, data);
            }

            td.html(data);
            $("#id" + id).attr("disabled", true);


        } catch (error) {
            toastr.error(`Erro ${error}.`);
            console.error(error);
        }
    }

    function iniciarTurno(data) {
        const cliente = $("#cliente").val();
        muda_status_botao("botaoCriarAtividade", "", true);
        return $.ajax({
            url: "<?= base_url("sistema/iniciar_turno") ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                cliente
            },
            success: function(response) {
                if (response.status) {
                    $("#botaoCriarAtividade").html("Iniciar Turno")
                    toastr.success(response.msg + data)
                } else {
                    muda_status_botao("botaoCriarAtividade", "Iniciar Turno", false)
                    toastr.warning(response.msg)
                }

            },
            error: function(xhr, status, error) {
                console.error(error);
                muda_status_botao("botaoCriarAtividade", "Iniciar Turno", false)
            }
        });
    }

    function iniciarAtividade(data) {
        const dataHoraAtual = obterDataHora();
        return $.ajax({
            url: "<?= base_url("sistema/iniciar_atividade") ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                dataHoraAtual,
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.msg + data)
                } else {
                    toastr.warning(response.msg)
                }
            },
            error: function(xhr, status, error) {
                toastr.error(error)
                console.error(error);
            }
        });
    }

    function concluirAtividade(data, desc) {
        const dataHoraAtual = obterDataHora();
        return $.ajax({
            url: "<?= base_url("sistema/concluir_atividade") ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                dataHoraAtual,
                desc
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.msg + data)
                } else {
                    toastr.warning(response.msg)
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function concluirTurno() {
        if (!confirm("Deseja finalizar o turno?")) return;
        if ($(".botoesNovaAtividade").length) {
            toastr.info("Conclua a atividade aberta primeiro.");
            return;
        };

        muda_status_botao("botaoConcluirTurno", "", true)

        $.ajax({
            url: "<?= base_url("sistema/concluir_turno") ?>",
            type: "POST",
            dataType: "JSON",
            success: function(response) {
                muda_status_botao("botaoConcluirTurno", "Concluir Turno", false)
                if (response.status) {
                    toastr.success(response.msg)
                    $("#botaoCriarAtividade").attr("disabled", false)
                    $("#botaoConcluirTurno").hide()
                } else {
                    toastr.warning(response.msg)
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                muda_status_botao("botaoConcluirTurno", "Concluir Turno", false)
            }
        });
    }

    function buscarClientes() {
        $("#spinner").html(spinner)
        $("#spinner").show()
        $("#cliente").hide()
        $.ajax({
            url: "<?= base_url("sistema/adm/getClientes") ?>",
            type: "POST",
            dataType: "JSON",
            success: function(response) {
                $("#cliente").show()
                $("#spinner").hide()
                if (response.status) {
                    let linha = '';
                    $.each(response.data, function(i, val) {
                        linha += `<option value="${val.cliente_id}">${val.cliente}</option>`;
                    });
                    $("#cliente").html(linha);
                } else {
                    toastr.warning(response.msg)
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $("#cliente").show()
                $("#spinner").hide()
            }
        });
    }
</script>
<?= $this->endSection(); ?>