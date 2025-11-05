<?php

// Carrega as configurações
require_once 'config.php';
require_once 'Database.php';

// Inicializa variáveis
$contratos = [];
$estatisticas = [
    'total_contratos' => 0,
    'valor_total' => 0,
    'valor_medio' => 0
];
$erro = null;

try {
    // Obtém a instância do banco de dados
    $db = Database::getInstance();
    
    // Query SQL com prepared statement (mesmo sem parâmetros externos, é boa prática)
    $sql = "
        SELECT 
            b.nome AS nome_banco,
            cv.verba AS verba,
            ct.codigo AS codigo_contrato,
            ct.data_inclusao AS data_inclusao,
            ct.valor AS valor,
            ct.prazo AS prazo
        FROM Tb_contrato ct
        INNER JOIN Tb_convenio_servico cs ON ct.convenio_servico = cs.codigo
        INNER JOIN Tb_convenio cv ON cs.convenio = cv.codigo
        INNER JOIN Tb_banco b ON cv.banco = b.codigo
        ORDER BY ct.data_inclusao DESC
    ";
    
    // Executa a consulta
    $contratos = $db->query($sql);
    
    // Calcula estatísticas
    $estatisticas['total_contratos'] = count($contratos);
    
    foreach ($contratos as $contrato) {
        $estatisticas['valor_total'] += $contrato['valor'];
    }
    
    if ($estatisticas['total_contratos'] > 0) {
        $estatisticas['valor_medio'] = $estatisticas['valor_total'] / $estatisticas['total_contratos'];
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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sistema de Gestão de Contratos e Convênios">
    <title>Listagem de Contratos - Versão Segura</title>
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
            max-width: 1400px;
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

        .security-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .stat-item {
            text-align: center;
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
            font-size: 14px;
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

        .valor {
            color: #28a745;
            font-weight: 600;
        }

        .banco {
            color: #667eea;
            font-weight: 600;
        }

        .codigo {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
        }

        .prazo-badge {
            background: #764ba2;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-block;
            font-size: 12px;
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

        @media (max-width: 768px) {
            .nav-button {
                position: static;
                margin: 10px auto 0 auto;
                display: flex;
                justify-content: center;
            }
            .stats {
                flex-direction: column;
                gap: 15px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="position: relative;">
            <a href="relatorio_agrupado.php" class="nav-button">
                <span>Relatório</span>
            </a>
            <h1>Listagem de Contratos</h1>
            <p>Sistema de Gestão de Contratos e Convênios</p>
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
        <?php elseif (empty($contratos)): ?>
            <!-- Nenhum contrato encontrado -->
            <div class="no-data">
                <p>Nenhum contrato encontrado no banco de dados.</p>
            </div>
        <?php else: ?>
            <!-- Estatísticas -->
            <div class="stats">
                <div class="stat-item">
                    <div class="number"><?php echo $estatisticas['total_contratos']; ?></div>
                    <div class="label">Total de Contratos</div>
                </div>
                <div class="stat-item">
                    <div class="number"><?php echo formatarMoeda($estatisticas['valor_total']); ?></div>
                    <div class="label">Valor Total</div>
                </div>
                <div class="stat-item">
                    <div class="number"><?php echo formatarMoeda($estatisticas['valor_medio']); ?></div>
                    <div class="label">Valor Médio</div>
                </div>
            </div>

            <!-- Tabela de contratos -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Banco</th>
                            <th>Verba</th>
                            <th>Data Inclusão</th>
                            <th>Valor</th>
                            <th>Prazo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contratos as $row): ?>
                            <tr>
                                <td>
                                    <span class="codigo">#<?php echo str_pad($row['codigo_contrato'], 4, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td class="banco"><?php echo escape($row['nome_banco']); ?></td>
                                <td>
                                    <?php 
                                    if (!empty($row['verba'])) {
                                        echo formatarMoeda($row['verba']);
                                    } else {
                                        echo '<span style="color: #999;">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo formatarData($row['data_inclusao']); ?></td>
                                <td class="valor"><?php echo formatarMoeda($row['valor']); ?></td>
                                <td>
                                    <span class="prazo-badge"><?php echo $row['prazo']; ?> meses</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p>© <?php echo date('Y'); ?> Fácil Tecnologia - Sistema de Gestão de Contratos</p>
            <p style="margin-top: 10px; font-size: 11px;">
                Ambiente: <strong><?php echo escape(ENVIRONMENT); ?></strong>
            </p>
        </div>
    </div>
</body>
</html>

