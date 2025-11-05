# Sistema de Gestão de Contratos e Convênios

Sistema web completo para gerenciamento de contratos bancários e convênios, desenvolvido em PHP puro com MySQL. Oferece listagem detalhada de contratos, relatórios agrupados com exportação PDF e APIs REST em JSON.

## Índice

- [Características](#características)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Funcionalidades](#funcionalidades)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso](#uso)
- [APIs Disponíveis](#apis-disponíveis)
- [Segurança](#segurança)
- [Instruções Git](#instruções-git)
- [Contribuindo](#contribuindo)
- [Licença](#licença)

## Características

- Interface web moderna e responsiva
- Sistema de relatórios com agrupamento de dados
- Exportação de relatórios em PDF
- APIs REST em JSON
- Busca e filtragem em tempo real
- Design profissional com gradientes e animações
- Arquitetura MVC simplificada
- Padrão Singleton para conexão com banco de dados
- Proteção contra SQL Injection e XSS

## Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Bibliotecas JavaScript**:
  - jsPDF (geração de PDF)
  - jsPDF-AutoTable (tabelas em PDF)
- **Servidor Web**: Apache (via XAMPP)

## Funcionalidades

### 1. Listagem de Contratos (`listar_contratos.php`)
- Visualização completa de todos os contratos
- Estatísticas gerais (total, valor total, valor médio)
- Ordenação por data de inclusão
- Design responsivo com badges e cores
- Navegação para relatórios

### 2. Relatório Agrupado (`relatorio_agrupado.php`)
- Agrupamento por banco e verba
- Cálculo de datas (mais antiga e mais nova)
- Soma de valores por agrupamento
- Contagem de contratos
- Cálculo de período em dias
- **Busca em tempo real** por qualquer campo
- **Exportação para PDF** com formatação profissional
- Estatísticas consolidadas

### 3. APIs REST
- `listar_contratos_json.php` - Lista de contratos em JSON
- `relatorio_agrupado_json.php` - Relatório agrupado em JSON
- Formato padronizado com timestamp e metadados
- Ideal para integração com frontends modernos

### 4. Versões de Terminal
- `listar_contratos_texto.php` - Saída em texto puro
- `relatorio_agrupado_texto.php` - Relatório em texto puro
- Útil para logs e automação

## Estrutura do Projeto

```
TesteFacilTecnologia/
├── config.php                      # Configurações do banco (NÃO versionar)
├── env.example                     # Exemplo de configurações
├── Database.php                    # Classe de conexão (Singleton Pattern)
├── create_tables.sql               # Script de criação das tabelas
├── insert_dados_ficticios.sql      # Dados de exemplo para testes
├── listar_contratos.php            # Interface web - Lista de contratos
├── listar_contratos_json.php       # API REST - Lista em JSON
├── listar_contratos_texto.php      # Versão texto - Lista
├── relatorio_agrupado.php          # Interface web - Relatório agrupado
├── relatorio_agrupado_json.php     # API REST - Relatório em JSON
├── relatorio_agrupado_texto.php    # Versão texto - Relatório
├── INFO_RELATORIO.md               # Documentação detalhada
└── README.md                       # Este arquivo
```

## Requisitos

### Servidor
- PHP 7.4 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Apache 2.4+ (ou Nginx)
- Extensões PHP:
  - PDO
  - PDO_MySQL
  - mbstring

### Desenvolvimento Local
- XAMPP 7.4+ (Windows/Mac/Linux)
- OU WAMP/LAMP/MAMP

## Instalação

### 1. Clone o Repositório

```bash
git clone https://github.com/seu-usuario/TesteFacilTecnologia.git
cd TesteFacilTecnologia
```

### 2. Configure o Servidor Web

#### XAMPP (Windows/Mac/Linux)

Coloque os arquivos em:
```
Windows: C:\xampp\htdocs\TesteFacilTecnologia
Mac: /Applications/XAMPP/htdocs/TesteFacilTecnologia
Linux: /opt/lampp/htdocs/TesteFacilTecnologia
```

### 3. Crie o Banco de Dados

Acesse o phpMyAdmin (http://localhost/phpmyadmin) e execute:

```sql
CREATE DATABASE faciltecnologia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'faciltecnologia'@'localhost' IDENTIFIED BY 'sua_senha_segura';
GRANT ALL PRIVILEGES ON faciltecnologia.* TO 'faciltecnologia'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Execute os Scripts SQL

```bash
# Criar tabelas
mysql -u faciltecnologia -p faciltecnologia < create_tables.sql

# Inserir dados de teste (opcional)
mysql -u faciltecnologia -p faciltecnologia < insert_dados_ficticios.sql
```

Ou via phpMyAdmin:
1. Selecione o banco `faciltecnologia`
2. Vá em "SQL" ou "Importar"
3. Execute `create_tables.sql`
4. Execute `insert_dados_ficticios.sql`

## Configuração

### 1. Configure o Arquivo de Conexão

Copie o arquivo de exemplo:

```bash
cp env.example config.php
```

### 2. Edite `config.php`

```php
<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'faciltecnologia');
define('DB_USER', 'faciltecnologia');
define('DB_PASS', 'sua_senha_aqui');
define('DB_CHARSET', 'utf8mb4');

// Configurações de Ambiente
define('ENVIRONMENT', 'development'); // ou 'production'

// Timezone
date_default_timezone_set('America/Sao_Paulo');
?>
```

### 3. Configurações de Segurança

**IMPORTANTE para Produção:**

```bash
# Restrinja permissões do arquivo de configuração
chmod 600 config.php

# Certifique-se que config.php está no .gitignore
echo "config.php" >> .gitignore
```

## Uso

### Acesso via Navegador

#### Listagem de Contratos
```
http://localhost/TesteFacilTecnologia/listar_contratos.php
```

#### Relatório Agrupado
```
http://localhost/TesteFacilTecnologia/relatorio_agrupado.php
```

### Recursos Disponíveis

#### Busca em Tempo Real (Relatório Agrupado)
- Digite qualquer termo no campo de busca
- Filtra por: banco, verba, data, valor, quantidade
- Atualização instantânea sem recarregar a página

#### Exportar para PDF
1. Acesse o relatório agrupado
2. Use a busca para filtrar (opcional)
3. Clique em "Exportar PDF"
4. O arquivo será baixado automaticamente

### Navegação
- Na **listagem de contratos**: botão "Relatório" (canto superior direito)
- No **relatório agrupado**: botão "Contratos" (canto superior direito)

## APIs Disponíveis

### API de Contratos

**Endpoint**: `/listar_contratos_json.php`

**Método**: GET

**Resposta**:
```json
{
  "success": true,
  "timestamp": "2024-11-05 15:30:00",
  "estatisticas": {
    "total_contratos": 50,
    "valor_total": 1500000.00,
    "valor_medio": 30000.00
  },
  "contratos": [
    {
      "codigo_contrato": 1,
      "nome_banco": "Banco do Brasil",
      "verba": 1500000.00,
      "data_inclusao": "2024-11-05 10:30:00",
      "valor": 50000.00,
      "prazo": 36
    }
  ]
}
```

### API de Relatório Agrupado

**Endpoint**: `/relatorio_agrupado_json.php`

**Método**: GET

**Resposta**:
```json
{
  "success": true,
  "timestamp": "2024-11-05 15:30:00",
  "totais": {
    "grupos": 10,
    "contratos": 50,
    "valor_total": 1500000.00
  },
  "agrupamentos": [
    {
      "nome_banco": "Banco do Brasil",
      "verba": 1500000.00,
      "data_mais_antiga": "2024-01-05 10:30:00",
      "data_mais_nova": "2024-10-05 08:50:00",
      "soma_valores": 85000.00,
      "quantidade_contratos": 8,
      "periodo_dias": 273
    }
  ]
}
```

## Segurança

### Recursos Implementados

- **PDO com Prepared Statements**: Prevenção de SQL Injection
- **htmlspecialchars()**: Proteção contra XSS
- **Singleton Pattern**: Controle de conexões com banco
- **Separação de Configurações**: Credenciais em arquivo separado
- **Tratamento de Erros**: Mensagens genéricas em produção
- **Validação de Dados**: Sanitização de inputs

### Boas Práticas

1. **NUNCA** versione o arquivo `config.php`
2. Use senhas fortes para o banco de dados
3. Em produção, defina `ENVIRONMENT` como `production`
4. Configure permissões adequadas nos arquivos (644 para PHP, 600 para config)
5. Use HTTPS em produção
6. Mantenha o PHP e MySQL atualizados

## Instruções Git

### Configuração Inicial do Repositório

```bash
# Entre no diretório do projeto
cd TesteFacilTecnologia

# Inicialize o repositório Git
git init

# Crie o arquivo .gitignore
cat > .gitignore << EOF
# Arquivos de configuração sensíveis
config.php
.env

# Logs e temporários
*.log
tmp/
temp/

# IDEs
.vscode/
.idea/
*.sublime-project
*.sublime-workspace

# Sistema operacional
.DS_Store
Thumbs.db
desktop.ini

# Dependências (se usar Composer no futuro)
vendor/
composer.lock
EOF

# Adicione todos os arquivos
git add .

# Faça o commit inicial
git commit -m "Primeiro commit: Sistema de Gestão de Contratos"
```

### Criar Repositório no GitHub

```bash
# Crie um repositório no GitHub (via interface web)
# Depois, adicione o remote:
git remote add origin https://github.com/seu-usuario/TesteFacilTecnologia.git

# Envie o código
git branch -M main
git push -u origin main
```

### Workflow de Desenvolvimento

```bash
# Crie uma branch para nova funcionalidade
git checkout -b feature/nome-da-feature

# Faça suas alterações e commits
git add .
git commit -m "Descrição clara da alteração"

# Envie para o GitHub
git push origin feature/nome-da-feature

# No GitHub, crie um Pull Request
# Após revisão e aprovação, faça merge na main
```

### Comandos Úteis Git

```bash
# Ver status dos arquivos
git status

# Ver histórico de commits
git log --oneline --graph

# Desfazer alterações não commitadas
git checkout -- arquivo.php

# Voltar para um commit anterior (cuidado!)
git revert <hash-do-commit>

# Atualizar repositório local
git pull origin main

# Ver diferenças
git diff
```

### Boas Práticas de Commit

```bash
# Commits descritivos e organizados
git commit -m "feat: Adiciona busca em tempo real no relatório"
git commit -m "fix: Corrige formatação de data no PDF"
git commit -m "docs: Atualiza README com instruções de instalação"
git commit -m "style: Remove emoticons para visual mais profissional"
git commit -m "refactor: Melhora estrutura da classe Database"
```

**Prefixos recomendados:**
- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação, estilo
- `refactor:` Refatoração de código
- `test:` Testes
- `chore:` Tarefas gerais

## Contribuindo

Contribuições são bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'feat: Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## Estrutura do Banco de Dados

### Diagrama ER Simplificado

```
Tb_banco (1) ----< (N) Tb_convenio (1) ----< (N) Tb_convenio_servico (1) ----< (N) Tb_contrato
```

### Tabelas

- **Tb_banco**: Instituições bancárias
- **Tb_convenio**: Convênios com verbas
- **Tb_convenio_servico**: Serviços disponíveis por convênio
- **Tb_contrato**: Contratos realizados

## Suporte

Para reportar bugs ou solicitar funcionalidades:

1. Abra uma [Issue no GitHub](https://github.com/seu-usuario/TesteFacilTecnologia/issues)
2. Descreva detalhadamente o problema ou sugestão
3. Inclua prints se possível

## Autor

**Fácil Tecnologia**
- Sistema desenvolvido para gestão de contratos bancários
- Data: 2024

## Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

**Desenvolvido com dedicação pela equipe Fácil Tecnologia** ✨

