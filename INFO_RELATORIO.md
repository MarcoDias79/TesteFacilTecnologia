# Relatório Agrupado de Contratos

## Descrição

Este relatório agrupa os contratos por **Banco** e **Verba**, exibindo informações consolidadas para cada combinação única.

## Campos Exibidos

### Campos de Agrupamento (GROUP BY):
- **Nome do Banco** - Nome da instituição bancária
- **Verba** - Valor da verba do convênio

### Campos Calculados:
- **Data Mais Antiga** - Data do contrato mais antigo deste agrupamento (MIN)
- **Data Mais Nova** - Data do contrato mais recente deste agrupamento (MAX)
- **Soma dos Valores** - Soma total dos valores de todos os contratos (SUM)
- **Quantidade de Contratos** - Total de contratos neste agrupamento (COUNT)
- **Período** - Diferença em dias entre a data mais antiga e mais nova

## Arquivos Disponíveis

### Interface HTML Completa
**Arquivo:** `relatorio_agrupado.php`

**Como acessar:**
```
http://localhost/TesteFacilTecnologia/relatorio_agrupado.php
```

**Recursos:**
- Interface visual moderna e responsiva
- Estatísticas gerais (total de agrupamentos, contratos e valores)
- Tabela formatada com cores e badges
- Cálculo automático do período entre datas
- Linha de totais ao final da tabela
- Ordenação por banco (alfabética) e verba (decrescente)

---

### API REST em JSON
**Arquivo:** `relatorio_agrupado_json.php`

**Como acessar:**
```
http://localhost/TesteFacilTecnologia/relatorio_agrupado_json.php
```

**Formato de resposta:**
```json
{
    "success": true,
    "timestamp": "2024-11-05 15:30:00",
    "totais": {
        "grupos": 10,
        "contratos": 50,
        "valor_total": 650000.00,
        "valor_total_formatado": "R$ 650.000,00"
    },
    "agrupamentos": [
        {
            "nome_banco": "Banco do Brasil",
            "verba": 1500000.00,
            "verba_formatada": "R$ 1.500.000,00",
            "data_mais_antiga": "2024-01-05 10:30:00",
            "data_mais_nova": "2024-10-05 08:50:00",
            "data_mais_antiga_formatada": "05/01/2024 10:30:00",
            "data_mais_nova_formatada": "05/10/2024 08:50:00",
            "soma_valores": 85000.00,
            "soma_valores_formatada": "R$ 85.000,00",
            "quantidade_contratos": 8,
            "periodo_dias": 273
        }
    ]
}
```

**Uso ideal:**
- Integração com aplicações frontend (React, Vue, Angular)
- Consumo em aplicativos mobile
- Integração com outras APIs
- Dashboards dinâmicos

## Query SQL Utilizada

```sql
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
```

## Exemplo de Análise

### Entendendo os Dados:

Se o relatório mostra:

| Banco | Verba | Data Mais Antiga | Data Mais Nova | Soma | Qtd |
|-------|-------|------------------|----------------|------|-----|
| Banco do Brasil | R$ 1.500.000,00 | 05/01/2024 | 05/10/2024 | R$ 85.000,00 | 8 |

**Isso significa:**
- O Banco do Brasil tem um convênio com verba de R$ 1.500.000,00
- Foram realizados **8 contratos** neste convênio
- O primeiro contrato foi em **05/01/2024**
- O último contrato foi em **05/10/2024**
- A soma de todos os valores é **R$ 85.000,00**
- O período de operação foi de **273 dias**

## Casos de Uso

### 1. Análise de Desempenho por Banco
Identificar quais bancos têm maior volume de contratos e valores.

### 2. Monitoramento de Verbas
Verificar quanto foi utilizado de cada verba disponível.

### 3. Análise Temporal
Identificar períodos de maior atividade em cada banco/verba.

### 4. Relatórios Gerenciais
Base para tomada de decisão sobre convênios mais rentáveis.

### 5. Auditoria
Rastrear e consolidar informações por instituição e verba.

## Segurança

Todos os scripts utilizam:
- **PDO** com prepared statements
- **Classe Database** com Singleton Pattern
- **XSS Protection** via htmlspecialchars
- **Tratamento de erros** apropriado
- **Configurações separadas** (config.php)

## Estatísticas Exibidas

### Totais Gerais:
- **Total de Agrupamentos** - Quantas combinações únicas de banco+verba existem
- **Total de Contratos** - Soma de todos os contratos
- **Valor Total Geral** - Soma de todos os valores de todos os contratos

## Como Testar

### Acesse a versão HTML:
```
http://localhost/TesteFacilTecnologia/relatorio_agrupado.php
```

### 2. Teste a API JSON:
```bash
# Via navegador
http://localhost/TesteFacilTecnologia/relatorio_agrupado_json.php

# Via cURL
curl http://localhost/TesteFacilTecnologia/relatorio_agrupado_json.php

```
### 3. Execute via linha de comando:
```bash
php relatorio_agrupado_simples.php

# Ou salve em arquivo
php relatorio_agrupado_simples.php > relatorio.txt
```

## Dicas

1. **Para filtrar por banco específico**, modifique a query adicionando:
   ```sql
   WHERE b.nome = 'Banco do Brasil'
   ```

2. **Para filtrar por período**, adicione:
   ```sql
   WHERE ct.data_inclusao BETWEEN '2024-01-01' AND '2024-12-31'
   ```

3. **Para ordenar por valor total**, altere:
   ```sql
   ORDER BY soma_valores DESC
  ```

4. **Para exportar em Excel**, use a API JSON e importe em ferramentas como Excel ou Google Sheets
---



