<?= $this->extend("template/template"); ?>
<?= $this->section("servico"); ?>

<style>
    /* Hover padronizado (tema claro/escuro + DataTables) */
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

    <!-- ==== CARD: Usuários ==== -->
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-users text-primary"></i>
                <strong>Usuários</strong>
            </div>

            <!-- Split button: cadastrar (modal) + página completa -->
            <div class="btn-group">
                <button class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCadastrarUsuario">
                    <i class="fa-solid fa-user-plus me-1"></i> Cadastrar
                </button>

            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaUsuarios">
                    <thead>
                        <tr>
                            <th class="text-start">Usuário</th>
                            <th class="col-1">Turno</th>
                            <th class="col-2 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==== CARD: Atividades ==== -->
    <div class="card" id="cardAtividades" style="display:none;">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fa-solid fa-clipboard-check text-primary"></i>
            <strong>Atividades — <span id="tituloAtividadesNome">Selecionado</span></strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaAtividades">
                    <thead>
                        <tr>
                            <th class="col-1 text-start">Projeto</th>
                            <th class="col">Descrição</th>
                            <th class="col-1 text-center">Início Atv.</th>
                            <th class="col-1 text-center">Fim Atv.</th>
                            <th class="col-1 text-center">Início Turno</th>
                            <th class="col-1 text-center">Fim Turno</th>
                            <th class="col-1 text-end">R$/hora</th>
                            <th class="col-1 text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ==== MODAL: Editar usuário ==== -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalEditarUsuarioLabel">Editar usuário</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_user_id">

                <div class="mb-3">
                    <label class="form-label" for="edit_nome">Nome do usuário</label>
                    <input type="text" class="form-control" id="edit_nome" autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit_login">Usuário (login)</label>
                    <input type="text" class="form-control" id="edit_login" autocomplete="off" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit_senha">Senha (opcional)</label>
                    <input type="password" class="form-control" id="edit_senha" minlength="8" placeholder="Deixe em branco para manter">
                    <div class="form-text">Se informado, mínimo de 8 caracteres.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit_tipo_usuario">Tipo de usuário</label>
                    <select id="edit_tipo_usuario" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach (($tiposUsuarios ?? []) as $t): ?>
                            <option value="<?= esc($t['tipo_id']) ?>"><?= esc($t['tipo_nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit_valor_hora">Valor da hora</label>
                    <input type="text" class="form-control" id="edit_valor_hora" readonly>
                    <div class="form-text">Campo somente leitura. Alterar em “Meus preços”.</div>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="edit_status">Status</label>
                    <select id="edit_status" class="form-select">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnSalvarUsuario" onclick="salvarUsuario($(this))">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==== MODAL: Cadastrar usuário (na mesma tela) ==== -->
<div class="modal fade" id="modalCadastrarUsuario" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalCadastrarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalCadastrarUsuarioLabel">Cadastrar usuário</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="cad_nome">Nome do usuário</label>
                    <input type="text" class="form-control" id="cad_nome" autocomplete="off" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="cad_login">Usuário (login)</label>
                    <input type="text" class="form-control" id="cad_login" autocomplete="off" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="cad_senha">Senha</label>
                    <input type="password" class="form-control" id="cad_senha" minlength="8" required>
                    <div class="form-text">Mínimo de 8 caracteres.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="cad_valor_hora">Valor da hora</label>
                    <input type="text" class="form-control number_only" id="cad_valor_hora" required>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="cad_tipo_usuario">Tipo de usuário</label>
                    <select id="cad_tipo_usuario" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach (($tiposUsuarios ?? []) as $t): ?>
                            <option value="<?= esc($t['tipo_id']) ?>"><?= esc($t['tipo_nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnCadastrarUsuario" onclick="cadastrarUsuario($(this))">
                    Cadastrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let tabelaUsuarios, tabelaAtividades, userID;

    $(document).ready(function() {
        carregarTabelaUsuarios();
        // Enter para enviar
        $('#modalCadastrarUsuario input, #modalCadastrarUsuario select').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#btnCadastrarUsuario').trigger('click');
            }
        });
    });

    /* ---------- Helpers ---------- */
    function renderDataHoraBR(data, type) {
        if ((type === 'display' || type === 'filter') && data) {
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
        return data ?? '—';
    }

    function fmtMoneyBR(raw, type) {
        if (type === 'display' || type === 'filter') {
            const n = Number(String(raw ?? '').replace(/\./g, '').replace(',', '.'));
            if (!isNaN(n)) {
                try {
                    return new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(n);
                } catch {
                    return `R$ ${n.toFixed(2).replace('.', ',')}`;
                }
            }
        }
        return raw ?? '—';
    }

    // Converte "1.234,56" -> 1234.56 (Number)
    function brToFloat(str) {
        const n = Number(String(str || '').replace(/\./g, '').replace(',', '.'));
        return isNaN(n) ? null : n;
    }

    function getUserId(row) {
        return row.user_id ?? row.id ?? row.usuario_id;
    }

    /* ---------- Tabela: Usuários ---------- */
    function carregarTabelaUsuarios() {
        tabelaUsuarios = $('#tabelaUsuarios').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/sistema/adm/getUsuariosAjax',
                type: 'POST'
            },
            order: [
                [0, 'asc']
            ],
            columnDefs: [{
                    targets: 0,
                    className: 'text-start'
                },
                {
                    targets: 2,
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            columns: [{
                    data: 'user_nome',
                    defaultContent: '—',
                    title: 'Usuário',
                    className: 'text-start'
                },
                {
                    data: 'turno',
                    defaultContent: '—',
                    title: 'Turno'
                },
                {
                    data: null,
                    title: 'Ações',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const id = getUserId(row);
                        const nome = row.user_nome ?? '';
                        return `
              <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-primary"
                        onclick="selecionarUsuario(this, '${id}')"
                        title="Ver atividades de ${nome}">
                  <i class="fa-solid fa-list-check"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary"
                        onclick="abrirModalEditarUsuario('${id}')"
                        title="Editar ${nome}">
                  <i class="fa-regular fa-pen-to-square"></i>
                </button>
              </div>`;
                    }
                }
            ]
        });
    }

    /* ---------- Tabela: Atividades ---------- */
    function carregarTabelaAtividades() {
        tabelaAtividades = $('#tabelaAtividades').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [4, 'desc']
            ], // Início Turno
            ajax: {
                url: '/sistema/adm/getAtividadesUsuariosAjax',
                type: 'POST',
                data: function(d) {
                    d.userID = userID;
                }
            },
            columnDefs: [{
                    targets: [2, 3, 4, 5],
                    className: 'text-center'
                },
                {
                    targets: [6, 7],
                    className: 'text-end'
                }
            ],
            columns: [{
                    data: 'projeto',
                    defaultContent: '—',
                    title: 'Projeto',
                    className: 'text-start'
                },
                {
                    data: 'descricao',
                    defaultContent: '—',
                    title: 'Descrição'
                },
                {
                    data: 'inicio_atividade',
                    title: 'Início Atv.',
                    render: renderDataHoraBR
                },
                {
                    data: 'fim_atividade',
                    title: 'Fim Atv.',
                    render: renderDataHoraBR
                },
                {
                    data: 'inicio_turno',
                    title: 'Início Turno',
                    render: renderDataHoraBR
                },
                {
                    data: 'fim_turno',
                    title: 'Fim Turno',
                    render: renderDataHoraBR
                },
                {
                    data: 'valor_hora',
                    title: 'R$/hora',
                    render: fmtMoneyBR
                },
                {
                    data: 'valor_atividade',
                    title: 'Total',
                    render: fmtMoneyBR
                }
            ]
        });
    }

    /* ---------- Selecionar usuário ---------- */
    function selecionarUsuario(botao, _userID) {
        userID = _userID;
        const nome = $(botao).closest('tr').find('td').eq(0).text().trim();
        $('#tituloAtividadesNome').text(nome || 'Selecionado');

        $("#cardAtividades").show();

        if ($.fn.dataTable.isDataTable('#tabelaAtividades')) {
            $('#tabelaAtividades').DataTable().ajax.reload(null, false);
        } else {
            carregarTabelaAtividades();
        }

        const off = $('#cardAtividades').offset();
        if (off) $('html, body').animate({
            scrollTop: off.top - 80
        }, 300);
    }

    /* ---------- Editar: buscar/abrir (usa tua função/rota atualizada) ---------- */
    function abrirModalEditarUsuario(id) {
        $.ajax({
            url: "<?= base_url('sistema/adm/buscaUserByID') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                userID: id
            },
            success: function(u) {
                if (u) {
                    $('#edit_user_id').val(u.id ?? id);
                    $('#edit_nome').val(u.nome ?? u.user_nome ?? '');
                    $('#edit_login').val(u.usuario ?? u.username ?? '');
                    $('#edit_status').val(String(u.status ?? 1));
                    const tipo = String(u.tipo_usuario_id ?? u.tipo_usuario_fk ?? u.tipo ?? '');
                    $('#edit_tipo_usuario').val(tipo);

                    const vh = u.valor_hora ?? u.hourly ?? u.hourly_cents;
                    if (vh != null) {
                        // Se vier em centavos: const reais = Number(vh)/100;
                        const reais = Number(String(vh).replace(/\./g, '').replace(',', '.'));
                        $('#edit_valor_hora').val(isNaN(reais) ? String(vh) : new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }).format(reais));
                    } else {
                        $('#edit_valor_hora').val('—');
                    }

                    $('#edit_senha').val('');
                    $('#modalEditarUsuario').modal('show');
                } else {
                    toastr.warning('Usuário não encontrado.');
                }
            },
            error: function(_, __, err) {
                toastr.error(err);
            }
        });
    }

    /* ---------- Editar: salvar ---------- */
    function salvarUsuario($btn) {
        const html = $btn.html();
        setBotaoStatus($btn, "", true);

        const payload = {
            userID: $('#edit_user_id').val(),
            nome: ($('#edit_nome').val() || '').trim(),
            tipo_usuario_id: $('#edit_tipo_usuario').val(),
            status: $('#edit_status').val()
        };

        const senha = $('#edit_senha').val();
        if (senha) {
            if (senha.length < 8) {
                toastr.info('A senha deve ter ao menos 8 caracteres.');
                $('#edit_senha').focus();
                setBotaoStatus($btn, html, false);
                return;
            }
            payload.senha = senha;
        }

        if (!payload.nome) {
            $('#edit_nome').focus();
            setBotaoStatus($btn, html, false);
            return;
        }
        if (!payload.tipo_usuario_id) {
            $('#edit_tipo_usuario').focus();
            setBotaoStatus($btn, html, false);
            return;
        }

        $.ajax({
            url: "<?= base_url('sistema/adm/atualizar_usuario') ?>",
            type: "POST",
            dataType: "JSON",
            data: payload,
            success: function(resp) {
                if (resp?.status) {
                    toastr.success(resp.msg || 'Usuário atualizado.');
                    $('#modalEditarUsuario').modal('hide');
                    if ($.fn.dataTable.isDataTable('#tabelaUsuarios')) {
                        $('#tabelaUsuarios').DataTable().ajax.reload(null, false);
                    }
                } else {
                    toastr.warning(resp?.msg || 'Não foi possível atualizar.');
                }
            },
            error: function(_, __, err) {
                toastr.error(err);
            },
            complete: function() {
                setBotaoStatus($btn, html, false);
            }
        });
    }

    /* ---------- Cadastrar (mesma tela) ---------- */
    function cadastrarUsuario($btn) {
        const html = $btn.html();
        setBotaoStatus($btn, "", true);

        const nome = ($('#cad_nome').val() || '').trim();
        const login = ($('#cad_login').val() || '').trim();
        const senha = $('#cad_senha').val();
        const tipo = $('#cad_tipo_usuario').val();
        const valorHoraStr = $('#cad_valor_hora').val();
        const valor_hora_num = brToFloat(valorHoraStr);

        if (!nome) {
            $('#cad_nome').focus();
            setBotaoStatus($btn, html, false);
            return;
        }
        if (!login) {
            $('#cad_login').focus();
            setBotaoStatus($btn, html, false);
            return;
        }
        if (!senha || senha.length < 8) {
            $('#cad_senha').focus();
            toastr.info('Senha deve ter ao menos 8 caracteres.');
            setBotaoStatus($btn, html, false);
            return;
        }
        if (!tipo) {
            $('#cad_tipo_usuario').focus();
            setBotaoStatus($btn, html, false);
            return;
        }
        if (valor_hora_num == null || valor_hora_num <= 0) {
            $('#cad_valor_hora').focus();
            toastr.info('Informe um valor/hora válido.');
            setBotaoStatus($btn, html, false);
            return;
        }

        const payload = {
            nome,
            login,
            senha,
            tipo_usuario_id: tipo,
            // Se o backend espera CENTAVOS, envie Math.round(valor_hora_num*100)
            valor_hora: valor_hora_num.toFixed(2)
        };

        $.ajax({
            url: "<?= base_url('sistema/adm/cadastrar_usuario') ?>", // ajuste a rota se necessário
            type: "POST",
            dataType: "JSON",
            data: payload,
            success: function(resp) {
                if (resp?.status) {
                    toastr.success(resp.msg || 'Usuário cadastrado.');
                    // limpa e fecha
                    $('#cad_nome, #cad_login, #cad_senha, #cad_valor_hora').val('');
                    $('#cad_tipo_usuario').val('');
                    $('#modalCadastrarUsuario').modal('hide');

                    if ($.fn.dataTable.isDataTable('#tabelaUsuarios')) {
                        $('#tabelaUsuarios').DataTable().ajax.reload(null, false);
                    }
                } else {
                    toastr.warning(resp?.msg || 'Não foi possível cadastrar.');
                }
            },
            error: function(_, __, err) {
                toastr.error(err);
            },
            complete: function() {
                setBotaoStatus($btn, html, false);
            }
        });
    }
</script>

<?= $this->endSection(); ?>