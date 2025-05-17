<button id="btnExportar" class="btn btn-sm btn-dark">Exportar Planilha</button>

<script>
    $('#btnExportar').on('click', function() {
        muda_status_botao("btnExportar", "", true);
        $.ajax({
            url: '/sistema/getAtividadesUsuariosAjax',
            type: 'POST',
            dataType: 'json',
            data: {
                dataI,
                dataF,
                projeto
            },
            success: function(response) {
                const dados = [];

                // Cabeçalhos da planilha
                dados.push([
                    "Projeto",
                    "Atividade Realizada",
                    "Início da Atividade",
                    "Fim da Atividade",
                    "Horas Trabalhadas",
                    "Valor Atividade",
                    "R$/Hora",
                    "Total"
                ]);


                // Montagem das linhas com os campos corretos
                response.data.forEach(linha => {
                    const valorAtividade = parseFloat(linha.valor_atividade.replace("R$", "").trim().replace(",", "."));

                    dados.push([
                        linha.projeto,
                        linha.descricao,
                        linha.inicio_atividade,
                        linha.fim_atividade,
                        linha.horas_trabalhadas,
                        valorAtividade
                    ]);
                });

                const priLinha = response.data[0];
                const valorHora = parseFloat(priLinha.valor_hora.replace("R$", "").trim().replace(",", "."));

                dados[1].push({
                    v: valorHora,
                    t: 'n',
                    z: '"R$"#,##0.00'
                }, {
                    f: `SUM(F2:F${dados.length})`,
                    t: 'n',
                    z: '"R$"#,##0.00'
                });


                // Cria e exporta a planilha
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(dados);

                ws['!cols'] = [];
                ws['!cols'][5] = {
                    numFmt: '"R$"#,##0.00'
                };
                ws['!cols'][6] = {
                    numFmt: '"R$"#,##0.00'
                };
                ws['!cols'][7] = {
                    numFmt: '"R$"#,##0.00'
                };

                XLSX.utils.book_append_sheet(wb, ws, "Relatório");
                XLSX.writeFile(wb, "relatorio_atividades.xlsx");
            },
            error: function(xhr, status, error) {
                alert("Erro ao buscar dados: " + error);
            },
            complete: function(xhr, status) {
                muda_status_botao("btnExportar", "Exportar Planilha", false);
            }
        });
    });
</script>