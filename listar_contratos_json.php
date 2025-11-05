<?php
/**
 * Script PHP para listagem de contratos em formato JSON
 * Ideal para APIs e integração com frontend JavaScript
 */

// Definir header como JSON
header('Content-Type: application/json; charset=utf-8');

// Configurações de conexão
$host = 'localhost';
$database = 'faciltecnologia';
$usuario = 'faciltecnologia';
$senha = '1q2w!Q@W';

try {
    // Conexão com o banco de dados usando PDO
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Query SQL com JOINs entre as tabelas
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
    
    // Executar a consulta
    $stmt = $pdo->query($sql);
    $contratos = $stmt->fetchAll();
    
    // Calcular estatísticas
    $total_contratos = count($contratos);
    $valor_total = 0;
    $verba_total = 0;
    
    foreach ($contratos as &$contrato) {
        $valor_total += $contrato['valor'];
        if (!empty($contrato['verba'])) {
            $verba_total += $contrato['verba'];
        }
        
        // Formatar valores para exibição
        $contrato['valor_formatado'] = 'R$ ' . number_format($contrato['valor'], 2, ',', '.');
        $contrato['verba_formatada'] = $contrato['verba'] 
            ? 'R$ ' . number_format($contrato['verba'], 2, ',', '.') 
            : null;
        $contrato['data_formatada'] = date('d/m/Y H:i:s', strtotime($contrato['data_inclusao']));
        $contrato['prazo_formatado'] = $contrato['prazo'] . ' meses';
    }
    
    $valor_medio = $total_contratos > 0 ? $valor_total / $total_contratos : 0;
    
    // Montar resposta JSON
    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'estatisticas' => [
            'total_contratos' => $total_contratos,
            'valor_total' => $valor_total,
            'valor_total_formatado' => 'R$ ' . number_format($valor_total, 2, ',', '.'),
            'valor_medio' => $valor_medio,
            'valor_medio_formatado' => 'R$ ' . number_format($valor_medio, 2, ',', '.'),
            'verba_total' => $verba_total,
            'verba_total_formatada' => 'R$ ' . number_format($verba_total, 2, ',', '.')
        ],
        'data' => $contratos
    ];
    
    // Retornar JSON
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Erro na conexão ou consulta
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao conectar com o banco de dados',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>


