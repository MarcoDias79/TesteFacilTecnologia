<?php
/**
 * Script PHP Simples para relatório agrupado
 * Versão em formato texto/tabela para linha de comando
 */

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
    
    // Exibir cabeçalho
    echo str_repeat("=", 150) . "\n";
    echo "RELATÓRIO AGRUPADO DE CONTRATOS\n";
    echo "Agrupado por: Nome do Banco e Verba\n";
    echo str_repeat("=", 150) . "\n\n";
    
    // Verificar se há resultados
    if (!empty($relatorio)) {
        // Exibir cabeçalho da tabela
        printf("%-35s | %-15s | %-19s | %-19s | %-15s | %s\n",
            "BANCO",
            "VERBA",
            "DATA MAIS ANTIGA",
            "DATA MAIS NOVA",
            "SOMA VALORES",
            "QTD"
        );
        echo str_repeat("-", 150) . "\n";
        
        // Variáveis para totais
        $totalContratos = 0;
        $totalValores = 0;
        
        // Exibir cada agrupamento
        foreach ($relatorio as $row) {
            $verba = $row['verba'] ? 'R$ ' . number_format($row['verba'], 2, ',', '.') : 'N/A';
            
            printf("%-35s | %-15s | %-19s | %-19s | %-15s | %s\n",
                substr($row['nome_banco'], 0, 35),
                $verba,
                date('d/m/Y H:i:s', strtotime($row['data_mais_antiga'])),
                date('d/m/Y H:i:s', strtotime($row['data_mais_nova'])),
                'R$ ' . number_format($row['soma_valores'], 2, ',', '.'),
                $row['quantidade_contratos']
            );
            
            $totalContratos += $row['quantidade_contratos'];
            $totalValores += $row['soma_valores'];
        }
        
        echo str_repeat("=", 150) . "\n";
        printf("%-73s | %-15s | %s\n",
            "TOTAL GERAL:",
            'R$ ' . number_format($totalValores, 2, ',', '.'),
            $totalContratos
        );
        echo str_repeat("=", 150) . "\n\n";
        
        echo "Total de Agrupamentos: " . count($relatorio) . "\n";
        echo "Total de Contratos: " . $totalContratos . "\n";
        echo "Valor Total: R$ " . number_format($totalValores, 2, ',', '.') . "\n";
        
    } else {
        echo "Nenhum dado encontrado no banco de dados.\n";
    }
    
    echo "\n" . str_repeat("=", 150) . "\n";
    echo "Relatório gerado em: " . date('d/m/Y H:i:s') . "\n";
    echo str_repeat("=", 150) . "\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
?>


