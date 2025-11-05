<?php
/**
 * API JSON para relatório agrupado de contratos
 * Agrupa por: nome do banco e verba
 * Retorna em formato JSON
 */

// Definir header como JSON
header('Content-Type: application/json; charset=utf-8');

// Carrega as configurações
require_once 'config.php';
require_once 'Database.php';

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
            COUNT(ct.codigo) AS quantidade_contratos,
            DATEDIFF(MAX(ct.data_inclusao), MIN(ct.data_inclusao)) AS periodo_dias
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
    $totais = [
        'grupos' => count($relatorio),
        'contratos' => 0,
        'valor_total' => 0
    ];
    
    foreach ($relatorio as &$grupo) {
        $totais['contratos'] += $grupo['quantidade_contratos'];
        $totais['valor_total'] += $grupo['soma_valores'];
        
        // Formatar valores para exibição
        $grupo['verba_formatada'] = $grupo['verba'] 
            ? 'R$ ' . number_format($grupo['verba'], 2, ',', '.') 
            : null;
        $grupo['soma_valores_formatada'] = 'R$ ' . number_format($grupo['soma_valores'], 2, ',', '.');
        $grupo['data_mais_antiga_formatada'] = date('d/m/Y H:i:s', strtotime($grupo['data_mais_antiga']));
        $grupo['data_mais_nova_formatada'] = date('d/m/Y H:i:s', strtotime($grupo['data_mais_nova']));
    }
    
    // Formatar totais
    $totais['valor_total_formatado'] = 'R$ ' . number_format($totais['valor_total'], 2, ',', '.');
    
    // Montar resposta JSON
    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'totais' => $totais,
        'agrupamentos' => $relatorio
    ];
    
    // Retornar JSON
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Erro na conexão ou consulta
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar solicitação',
        'message' => SHOW_ERRORS ? $e->getMessage() : 'Erro interno do servidor',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>


