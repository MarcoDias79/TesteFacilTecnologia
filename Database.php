<?php
/**
 * Classe Database - Gerenciamento Seguro de Conexão
 * 
 * Usa PDO (PHP Data Objects) para melhor segurança e portabilidade
 * Implementa Singleton Pattern para garantir uma única conexão
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    /**
     * Construtor privado (Singleton)
     */
    private function __construct() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                // Força o uso de prepared statements reais
                PDO::ATTR_EMULATE_PREPARES   => false,
                
                // Lança exceções em caso de erro
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                
                // Retorna resultados como array associativo
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Desabilita prepared statements persistentes
                PDO::ATTR_PERSISTENT         => false,
                
                // Define timeout de conexão
                PDO::ATTR_TIMEOUT            => 5
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    /**
     * Obtém a instância única da classe (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtém a conexão PDO
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Executa uma consulta SELECT com prepared statements
     * 
     * @param string $sql Query SQL com placeholders
     * @param array $params Parâmetros para bind
     * @return array Resultados da consulta
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError($e);
            return [];
        }
    }
    
    /**
     * Executa uma consulta que retorna apenas uma linha
     * 
     * @param string $sql Query SQL com placeholders
     * @param array $params Parâmetros para bind
     * @return array|false Resultado ou false se não encontrar
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }
    
    /**
     * Executa INSERT, UPDATE ou DELETE
     * 
     * @param string $sql Query SQL com placeholders
     * @param array $params Parâmetros para bind
     * @return int Número de linhas afetadas
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e);
            return 0;
        }
    }
    
    /**
     * Retorna o ID do último registro inserido
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Cancela uma transação
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }
    
    /**
     * Trata erros de banco de dados
     */
    private function handleError($e) {
        // Log do erro real (em produção, salvar em arquivo)
        error_log("Erro no banco de dados: " . $e->getMessage());
        
        // Em desenvolvimento, mostra o erro
        if (SHOW_ERRORS) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        } else {
            // Em produção, mostra mensagem genérica
            throw new Exception("Erro ao processar sua solicitação. Tente novamente mais tarde.");
        }
    }
    
    /**
     * Previne clonagem da instância (Singleton)
     */
    private function __clone() {}
    
    /**
     * Previne deserialização da instância (Singleton)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>


