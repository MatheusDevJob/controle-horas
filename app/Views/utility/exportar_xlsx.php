<button id="btnExportar" class="btn btn-sm btn-dark">Exportar Planilha</button>

<script>
  // parser BR -> número (remove R$, pontos de milhar e troca vírgula)
  const parseBR = (s) => {
    if (s == null) return 0;
    const t = String(s).replace(/[^\d,,-.]/g,'').replace(/\./g,'').replace(',', '.');
    const n = parseFloat(t);
    return isNaN(n) ? 0 : n;
  };

  $('#btnExportar').on('click', function (e) {
    e.preventDefault();
    muda_status_botao("btnExportar", "", true);

    // NUNCA mande elementos DOM em data: {...}
    const filtros = {
      dataI:   $('#dataI').val()   || '',
      dataF:   $('#dataF').val()   || '',
      projeto: $('#projeto').val() || ''
    };

    $.ajax({
      url: '/sistema/getAtividadesUsuariosAjax',
      type: 'POST',
      dataType: 'json',
      data: filtros,                // <- só strings simples
      success: function (response) {
        const linhas = Array.isArray(response?.data) ? response.data : [];
        if (!linhas.length) {
          toastr.info('Nenhum registro para exportar com os filtros atuais.');
          return;
        }

        const aoa = [[
          'Projeto',
          'Atividade Realizada',
          'Início da Atividade',
          'Fim da Atividade',
          'Horas Trabalhadas',
          'Valor Atividade',
          'R$/Hora',
          'Total'
        ]];

        // monta linhas (Valor Atividade numérico -> formato moeda na planilha)
        linhas.forEach(l => {
          const valorAtv = parseBR(l.valor_atividade ?? l.valorAtividade ?? l.valor); // cobre variações
          aoa.push([
            l.projeto ?? '',
            l.descricao ?? '',
            l.inicio_atividade ?? '',
            l.fim_atividade ?? '',
            l.horas_trabalhadas ?? '',
            { v: valorAtv, t: 'n', z: '"R$"#,##0.00' }  // número com formato
          ]);
        });

        // valor/hora (pego do primeiro registro)
        const vHora = parseBR(linhas[0].valor_hora ?? linhas[0].valorHora);
        // adiciona R$/Hora e Total só na primeira linha de dados
        // (coluna G e H), mantendo cabeçalho com 8 colunas
        aoa[1].push(
          { v: vHora, t: 'n', z: '"R$"#,##0.00' },
          { f: `SUM(F2:F${aoa.length})`, t: 'n', z: '"R$"#,##0.00' }
        );
        // as demais linhas ficam com G/H em branco (ok)

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(aoa);

        // Larguras de coluna (opcional)
        ws['!cols'] = [
          { wch: 22 }, // Projeto
          { wch: 40 }, // Atividade
          { wch: 19 }, // Início Atv
          { wch: 19 }, // Fim Atv
          { wch: 15 }, // Horas trab.
          { wch: 14 }, // Valor Atv
          { wch: 10 }, // R$/Hora
          { wch: 12 }  // Total
        ];

        XLSX.utils.book_append_sheet(wb, ws, 'Relatório');
        XLSX.writeFile(wb, 'relatorio_atividades.xlsx');
      },
      error: function (xhr, status, error) {
        toastr.error("Erro ao buscar dados: " + error);
        console.error(error);
      },
      complete: function () {
        muda_status_botao("btnExportar", "Exportar Planilha", false);
      }
    });
  });
</script>
