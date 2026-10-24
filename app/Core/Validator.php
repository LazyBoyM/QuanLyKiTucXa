<?php
namespace App\Core;

class Validator {
    private $errors = [];
    private $data = [];
    
    public function validate($data, $rules) {
        $this->data = $data;
        $this->errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule, $data[$field] ?? null);
            }
        }
        
        return $this->errors;
    }
    
    private function applyRule($field, $rule, $value) {
        // Handle rule with parameters
        if (strpos($rule, ':') !== false) {
            list($ruleName, $param) = explode(':', $rule);
        } else {
            $ruleName = $rule;
            $param = null;
        }
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "Trường này là bắt buộc");
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Email không hợp lệ");
                }
                break;
                
            case 'min':
                if (strlen($value) < $param) {
                    $this->addError($field, "Phải có ít nhất {$param} ký tự");
                }
                break;
                
            case 'max':
                if (strlen($value) > $param) {
                    $this->addError($field, "Không được vượt quá {$param} ký tự");
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "Phải là số");
                }
                break;
                
            case 'phone':
                if (!empty($value) && !preg_match('/^[0-9]{10,11}$/', $value)) {
                    $this->addError($field, "Số điện thoại không hợp lệ");
                }
                break;
                
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, "Ngày không hợp lệ");
                }
                break;
                
            case 'confirmed':
                $confirmationField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmationField] ?? null)) {
                    $this->addError($field, "Xác nhận không khớp");
                }
                break;
                
            case 'unique':
                $this->validateUnique($field, $value, $param);
                break;
        }
    }
    
    private function validateUnique($field, $value, $table) {
        if (empty($value)) return;
        
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$field} = :value";
        $result = $db->fetch($sql, [':value' => $value]);
        
        if ($result && $result->count > 0) {
            $this->addError($field, "Giá trị này đã tồn tại");
        }
    }
    
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    public function passes() {
        return empty($this->errors);
    }
    
    public function fails() {
        return !empty($this->errors);
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public function first($field) {
        return $this->errors[$field][0] ?? null;
    }
}