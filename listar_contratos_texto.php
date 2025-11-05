<?php
/**
 * Script PHP Simples para listagem de contratos
 * Versão sem HTML - Saída em formato texto/tabela simples
 */

// Configurações de conexão
$host = 'localhost';
$database = 'faciltecnologia';
$usuario = 'faciltecnologia';
$senha = '1q2w!Q@W';

// Conexão com o banco de dados usando MySQLi
$conexao = new mysqli($host, $usuario, $senha, $database);

// Verificar se houve erro na conexão
if ($conexao->connect_error) {
    die("ERRO: Não foi possível conectar ao banco de dados.\n" . $conexao->connect_error);
}

// Definir charset para UTF-8
$conexao->set_charset("utf8");

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
$resultado = $conexao->query($sql);

// Verificar se a consulta foi executada com sucesso
if (!$resultado) {
    die("ERRO na consulta: " . $conexao->error . "\n");
}

// Exibir cabeçalho
echo str_repeat("=", 120) . "\n";
echo "LISTAGEM DE CONTRATOS\n";
echo str_repeat("=", 120) . "\n\n";

// Verificar se há resultados
if ($resultado->num_rows > 0) {
    // Exibir cabeçalho da tabela
    printf("%-8s | %-30s | %-15s | %-19s | %-15s | %-10s\n",
        "CÓDIGO",
        "NOME DO BANCO",
        "VERBA",
        "DATA INCLUSÃO",
        "VALOR",
        "PRAZO"
    );
    echo str_repeat("-", 120) . "\n";
    
    // Exibir cada registro
    while ($row = $resultado->fetch_assoc()) {
        printf("%-8s | %-30s | %-15s | %-19s | %-15s | %-10s\n",
            str_pad($row['codigo_contrato'], 4, '0', STR_PAD_LEFT),
            substr($row['nome_banco'], 0, 30),
            $row['verba'] ? 'R$ ' . number_format($row['verba'], 2, ',', '.') : 'N/A',
            date('d/m/Y H:i:s', strtotime($row['data_inclusao'])),
            'R$ ' . number_format($row['valor'], 2, ',', '.'),
            $row['prazo'] . ' meses'
        );
    }
    
    echo str_repeat("-", 120) . "\n";
    echo "\nTotal de registros: " . $resultado->num_rows . "\n";
    
} else {
    echo "Nenhum contrato encontrado no banco de dados.\n";
}

echo str_repeat("=", 120) . "\n";

// Fechar a conexão
$conexao->close();
?>

