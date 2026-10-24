<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        $this->config = require __DIR__ . '/../../config/database.php';
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        $config = $this->config['connections'][$this->config['default']];
        
        try {
            $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $this->connection = new PDO($dsn, $config['username'], $config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Lấy connection để dùng trực tiếp
     */
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->query($sql, array_combine($placeholders, $values));
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where) {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :set_$key";
            $params[":set_$key"] = $value;
        }
        
        $whereClause = [];
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :where_$key";
            $params[":where_$key"] = $value;
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $fields) . 
               " WHERE " . implode(' AND ', $whereClause);
        
        return $this->query($sql, $params)->rowCount();
    }
    
    public function delete($table, $where) {
        $whereClause = [];
        $params = [];
        
        foreach ($where as $key => $value) {
            $whereClause[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereClause);
        return $this->query($sql, $params)->rowCount();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollBack();
    }
}