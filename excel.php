<button onclick="gerarRelatorioExpedicao()">Gerar Relatório de Expedição</button>

<script src="https://cdn.jsdelivr.net/npm/@sheetjs/xlsx-js-style@1.2.0/dist/xlsx.full.min.js"></script>

<script>
function gerarRelatorioExpedicao() {
  // 1. Defina os dados, incluindo o cabeçalho e o rodapé
  const data = [
    ["RELATÓRIO EXPEDIÇÃO"], // Linha do título (será mesclada)
    [],                      // Linha em branco
    ["ID", "Data", "Produto", "Quantidade", "Placa Tanque 1", "Placa Tanque 2"], // Cabeçalho da tabela
    ["1", "2025-09-21", "BIODIESEL B100", "20000", "ABC-1234", "DEF-5678"], // Primeira linha de dados
    ["2", "2025-09-21", "BIODIESEL B100", "15000", "GHI-9012", "JKL-3456"], // Segunda linha de dados
    [],                      // Linha em branco
    ["Total de volume expedido:", "35000"], // Linha do rodapé
  ];

  // 2. Converte o array de dados em uma planilha
  const ws = XLSX.utils.aoa_to_sheet(data);

  // 3. Aplica os estilos (cores, negrito e mesclagem)
  
  // Título: Mescla as células A1 a F1 e aplica negrito
  ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 5 } }];
  ws['A1'].s = {
    font: { bold: true, sz: 14 },
    alignment: { horizontal: "center" }
  };
  
  // Cabeçalhos (Linha 3): Negrito e cor de fundo
  const headerStyle = { font: { bold: true }, fill: { fgColor: { rgb: "FFDDDDDD" } } };
  const headers = ["A3", "B3", "C3", "D3", "E3", "F3"];
  headers.forEach(cell => { ws[cell].s = headerStyle; });

  // Dados (Linhas 4 e 5): Cores de fundo alternadas
  const dataStyle1 = { fill: { fgColor: { rgb: "FFFFFFFF" } } };
  const dataStyle2 = { fill: { fgColor: { rgb: "FFEFEFEF" } } };
  for (let row = 3; row <= 4; row++) {
    const style = (row % 2 === 0) ? dataStyle2 : dataStyle1;
    for (let col = 0; col <= 5; col++) {
      const cellRef = XLSX.utils.encode_cell({ r: row, c: col });
      if (ws[cellRef]) ws[cellRef].s = style;
    }
  }

  // Rodapé (Linha 7): Negrito e alinhamento
  const footerStyle = { font: { bold: true } };
  const footerCells = ["A7", "B7"];
  footerCells.forEach(cell => { ws[cell].s = footerStyle; });

  // 4. Cria o workbook e salva o arquivo
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Relatório");
  XLSX.writeFile(wb, "Relatorio Expedicao.xlsx");
}
</script>