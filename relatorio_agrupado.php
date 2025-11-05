<?php
/**
 * Script PHP para relatório agrupado de contratos
 * Agrupa por: nome do banco e verba
 * Exibe: banco, verba, data mais antiga, data mais nova, soma dos valores
 */

// Carrega as configurações
require_once 'config.php';
require_once 'Database.php';

// Inicializa variáveis
$relatorio = [];
$totais = [
    'grupos' => 0,
    'contratos' => 0,
    'valor_total' => 0
];
$erro = null;

try {
    // Obtém a instância do banco de dados
    $db = Database::getInstance();
    
    // Query SQL com agrupamento por banco e verba
    $sql = "
        SELECT 
            b.nome AS nome_banco,
            cv.verba AS verba,
            MIN(ct.data_inclusao) AS data_mais_antiga,
            MAX(ct.data_inclusao) AS data_mais_nova,
            SUM(ct.valor) AS soma_valores,
            COUNT(ct.codigo) AS quantidade_contratos
        FROM Tb_contrato ct
        INNER JOIN Tb_convenio_servico cs ON ct.convenio_servico = cs.codigo
        INNER JOIN Tb_convenio cv ON cs.convenio = cv.codigo
        INNER JOIN Tb_banco b ON cv.banco = b.codigo
        GROUP BY b.nome, cv.verba
        ORDER BY b.nome ASC, cv.verba DESC
    ";
    
    // Executa a consulta
    $relatorio = $db->query($sql);
    
    // Calcula totais
    $totais['grupos'] = count($relatorio);
    
    foreach ($relatorio as $grupo) {
        $totais['contratos'] += $grupo['quantidade_contratos'];
        $totais['valor_total'] += $grupo['soma_valores'];
    }
    
} catch (Exception $e) {
    $erro = $e->getMessage();
    
    // Em produção, log o erro mas não mostre detalhes
    if (!SHOW_ERRORS) {
        $erro = "Erro ao carregar os dados. Por favor, tente novamente mais tarde.";
    }
}

/**
 * Função helper para sanitizar output (previne XSS)
 */
function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Função helper para formatar valores monetários
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Função helper para formatar data
 */
function formatarData($data) {
    return date('d/m/Y H:i', strtotime($data));
}

/**
 * Função helper para calcular dias entre datas
 */
function calcularDias($dataInicio, $dataFim) {
    $inicio = new DateTime($dataInicio);
    $fim = new DateTime($dataFim);
    $intervalo = $inicio->diff($fim);
    return $intervalo->days;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Relatório Agrupado - Contratos por Banco e Verba</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 13px;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-item .number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-item .label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        .table-container {
            overflow-x: auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .banco {
            color: #667eea;
            font-weight: 600;
            font-size: 15px;
        }

        .verba {
            color: #28a745;
            font-weight: 600;
            font-size: 15px;
        }

        .valor-total {
            color: #dc3545;
            font-weight: 700;
            font-size: 15px;
        }

        .data {
            color: #6c757d;
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .periodo-badge {
            background: #e7e7ff;
            color: #5568d3;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 5px;
        }

        .error-container {
            padding: 40px;
            text-align: center;
        }

        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            display: inline-block;
        }

        .error-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #6c757d;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 12px;
            border-top: 2px solid #e9ecef;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nav-button {
            position: absolute;
            top: 30px;
            right: 30px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-button:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        /* Estilos para busca e exportação */
        .toolbar {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-export {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-export:active {
            transform: translateY(0);
        }

        .search-info {
            width: 100%;
            padding: 10px;
            background: #d1ecf1;
            color: #0c5460;
            border-radius: 6px;
            font-size: 13px;
            text-align: center;
            display: none;
        }

        tr.hidden {
            display: none !important;
        }

        @media (max-width: 768px) {
            .nav-button {
                position: static;
                margin: 10px auto 0 auto;
                display: flex;
                justify-content: center;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }

            .toolbar {
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
            }

            .btn-export {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header" style="position: relative;">
            <a href="listar_contratos.php" class="nav-button">
                <span>Contratos</span>
            </a>
            <h1>Relatório Agrupado de Contratos</h1>
            <p>Agrupamento por Banco e Verba</p>
            <p>Data de geração: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>

        <?php if ($erro): ?>
            <!-- Exibir erro se houver -->
            <div class="error-container">
                <div class="error-box">
                    <div class="error-icon">!</div>
                    <h3>Erro ao Carregar Dados</h3>
                    <p><?php echo escape($erro); ?></p>
                </div>
            </div>
        <?php elseif (empty($relatorio)): ?>
            <!-- Nenhum dado encontrado -->
            <div class="no-data">
                <p>Nenhum dado encontrado no banco de dados.</p>
            </div>
        <?php else: ?>
            <!-- Estatísticas Gerais -->
            <div class="stats">
                <div class="stat-item">
                    <div class="number"><?php echo $totais['grupos']; ?></div>
                    <div class="label">Total de Agrupamentos</div>
                </div>
                <div class="stat-item">
                    <div class="number"><?php echo $totais['contratos']; ?></div>
                    <div class="label">Total de Contratos</div>
                </div>
                <div class="stat-item">
                    <div class="number"><?php echo formatarMoeda($totais['valor_total']); ?></div>
                    <div class="label">Valor Total Geral</div>
                </div>
            </div>

            <!-- Barra de Ferramentas: Busca e Exportação -->
            <div class="toolbar">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar por banco, verba, data, valor ou quantidade...">
                </div>
                <button class="btn-export" onclick="exportarPDF()">
                    <span>Exportar PDF</span>
                </button>
                <div class="search-info" id="searchInfo"></div>
            </div>

            <!-- Tabela de Relatório -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Banco</th>
                            <th class="text-right">Verba</th>
                            <th>Data Mais Antiga</th>
                            <th>Data Mais Nova</th>
                            <th>Período</th>
                            <th class="text-right">Soma dos Valores</th>
                            <th class="text-center">Qtd. Contratos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorio as $row): ?>
                            <?php $dias = calcularDias($row['data_mais_antiga'], $row['data_mais_nova']); ?>
                            <tr>
                                <td class="banco"><?php echo escape($row['nome_banco']); ?></td>
                                <td class="verba text-right">
                                    <?php 
                                    if (!empty($row['verba'])) {
                                        echo formatarMoeda($row['verba']);
                                    } else {
                                        echo '<span style="color: #999;">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td class="data">
                                    <?php echo formatarData($row['data_mais_antiga']); ?>
                                </td>
                                <td class="data">
                                    <?php echo formatarData($row['data_mais_nova']); ?>
                                </td>
                                <td class="text-center">
                                    <span class="periodo-badge">
                                        <?php echo $dias; ?> <?php echo $dias == 1 ? 'dia' : 'dias'; ?>
                                    </span>
                                </td>
                                <td class="valor-total text-right">
                                    <?php echo formatarMoeda($row['soma_valores']); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">
                                        <?php echo $row['quantidade_contratos']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8f9fa; font-weight: bold;">
                            <td colspan="5" style="text-align: right; padding-right: 20px;">
                                TOTAL GERAL:
                            </td>
                            <td class="text-right" style="font-size: 16px; color: #dc3545;">
                                <?php echo formatarMoeda($totais['valor_total']); ?>
                            </td>
                            <td class="text-center" style="font-size: 16px; color: #667eea;">
                                <?php echo $totais['contratos']; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p>© <?php echo date('Y'); ?> Fácil Tecnologia - Sistema de Gestão de Contratos</p>
            <p>Relatório gerado em: <?php echo date('d/m/Y H:i:s'); ?></p>
            <p style="margin-top: 10px; font-size: 11px;">
                Ambiente: <strong><?php echo escape(ENVIRONMENT); ?></strong>
            </p>
        </div>
    </div>

    <script>
        // Função de busca em tempo real
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const tbody = document.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            const searchInfo = document.getElementById('searchInfo');
            
            let visibleCount = 0;
            
            rows.forEach(row => {
                // Pega todo o texto da linha
                const rowText = row.textContent.toLowerCase();
                
                if (rowText.includes(searchTerm)) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            // Mostra informação sobre a busca
            if (searchTerm) {
                searchInfo.style.display = 'block';
                searchInfo.textContent = `Mostrando ${visibleCount} de ${rows.length} registros`;
            } else {
                searchInfo.style.display = 'none';
            }
        });

        // Função para exportar para PDF
        function exportarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // landscape, milímetros, A4
            
            // Configurações
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            
            // Título
            doc.setFontSize(18);
            doc.setTextColor(102, 126, 234);
            doc.text('Relatório Agrupado de Contratos', pageWidth / 2, 15, { align: 'center' });
            
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text('Agrupamento por Banco e Verba', pageWidth / 2, 22, { align: 'center' });
            doc.text('Data de geração: ' + new Date().toLocaleString('pt-BR'), pageWidth / 2, 27, { align: 'center' });
            
            // Estatísticas
            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            const statsY = 35;
            
            <?php if (isset($totais)): ?>
            doc.text('Total de Agrupamentos: <?php echo $totais["grupos"]; ?>', 20, statsY);
            doc.text('Total de Contratos: <?php echo $totais["contratos"]; ?>', 100, statsY);
            doc.text('Valor Total: <?php echo formatarMoeda($totais["valor_total"]); ?>', 180, statsY);
            <?php endif; ?>
            
            // Prepara dados da tabela
            const tbody = document.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr:not(.hidden)'); // Apenas linhas visíveis
            const tableData = [];
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const rowData = [
                    cells[0]?.textContent.trim() || '', // Banco
                    cells[1]?.textContent.trim() || '', // Verba
                    cells[2]?.textContent.trim() || '', // Data Antiga
                    cells[3]?.textContent.trim() || '', // Data Nova
                    cells[4]?.textContent.trim() || '', // Período
                    cells[5]?.textContent.trim() || '', // Soma Valores
                    cells[6]?.textContent.trim() || ''  // Qtd Contratos
                ];
                tableData.push(rowData);
            });
            
            // Adiciona rodapé com totais
            tableData.push([
                { content: 'TOTAL GERAL:', colSpan: 5, styles: { fontStyle: 'bold', halign: 'right' } },
                { content: '<?php echo formatarMoeda($totais["valor_total"]); ?>', styles: { fontStyle: 'bold', textColor: [220, 53, 69] } },
                { content: '<?php echo $totais["contratos"]; ?>', styles: { fontStyle: 'bold', textColor: [102, 126, 234] } }
            ]);
            
            // Configuração da tabela
            doc.autoTable({
                head: [['Banco', 'Verba', 'Data Mais Antiga', 'Data Mais Nova', 'Período', 'Soma dos Valores', 'Qtd. Contratos']],
                body: tableData,
                startY: 42,
                theme: 'striped',
                headStyles: {
                    fillColor: [102, 126, 234],
                    textColor: 255,
                    fontStyle: 'bold',
                    halign: 'center'
                },
                styles: {
                    fontSize: 8,
                    cellPadding: 3,
                    overflow: 'linebreak',
                    valign: 'middle'
                },
                columnStyles: {
                    0: { cellWidth: 45 },  // Banco
                    1: { cellWidth: 30, halign: 'right' },  // Verba
                    2: { cellWidth: 35 },  // Data Antiga
                    3: { cellWidth: 35 },  // Data Nova
                    4: { cellWidth: 25, halign: 'center' },  // Período
                    5: { cellWidth: 35, halign: 'right' },  // Soma
                    6: { cellWidth: 25, halign: 'center' }   // Qtd
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                margin: { top: 42, left: 10, right: 10 },
                didDrawPage: function(data) {
                    // Rodapé da página
                    doc.setFontSize(8);
                    doc.setTextColor(150);
                    doc.text(
                        'Página ' + doc.internal.getNumberOfPages(),
                        pageWidth / 2,
                        pageHeight - 10,
                        { align: 'center' }
                    );
                }
            });
            
            // Salva o PDF
            const dataHora = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            doc.save(`relatorio_agrupado_${dataHora}.pdf`);
        }
    </script>
</body>
</html>


