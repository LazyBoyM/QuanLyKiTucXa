<?php
namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    public function create($data) {
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $data);
    }
    
    public function update($id, $data) {
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->update($this->table, $data, [$this->primaryKey => $id]);
    }
    
    public function delete($id) {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }
    
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        return $this->db->fetchAll($sql, [':value' => $value]);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result->count ?? 0;
    }
    
    public function paginate($perPage = 15, $page = 1) {
        $offset = ($page - 1) * $perPage;
        $total = $this->count();
        
        $sql = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
}