<?php
namespace App\Core;

abstract class Controller {
    protected $db;
    protected $view;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Render view - Hỗ trợ AJAX
     */
    protected function view($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        // Nếu là AJAX request, chỉ trả về content (không layout)
        if ($this->isAjaxRequest()) {
            echo $content;
            return;
        }
        
        // Load layout nếu có
        $layout = $data['layout'] ?? 'layouts/app';
        $layoutPath = __DIR__ . '/../Views/' . str_replace('.', '/', $layout) . '.php';
        
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }
    
    /**
     * Render view không cần layout (dùng cho AJAX hoặc popup)
     */
    protected function renderPartial($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        require $viewPath;
    }
    
    /**
     * Kiểm tra có phải AJAX request không
     */
    protected function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    
    /**
     * Trả về JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Trả về success response cho AJAX
     */
    protected function ajaxSuccess($message = 'Success', $data = []) {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    /**
     * Trả về error response cho AJAX
     */
    protected function ajaxError($message = 'Error', $statusCode = 400) {
        return $this->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
    
    /**
     * Redirect
     */
    protected function redirect($url) {
        // Nếu là AJAX request, trả về redirect URL
        if ($this->isAjaxRequest()) {
            return $this->json([
                'redirect' => $url
            ]);
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Quay lại trang trước
     */
    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        $this->redirect($referer);
    }
    
    /**
     * Validate dữ liệu
     */
    protected function validate($data, $rules) {
        $validator = new Validator();
        return $validator->validate($data, $rules);
    }
    
    /**
     * Lấy model
     */
    protected function model($model) {
        $modelClass = "App\\Models\\{$model}";
        return new $modelClass();
    }
    
    /**
     * Lấy dữ liệu từ request
     */
    protected function input($key = null, $default = null) {
        $input = $_REQUEST;
        
        // Nếu là JSON request
        if ($this->isJsonRequest()) {
            $json = json_decode(file_get_contents('php://input'), true);
            if ($json) {
                $input = array_merge($input, $json);
            }
        }
        
        if ($key === null) {
            return $input;
        }
        
        return $input[$key] ?? $default;
    }
    
    /**
     * Kiểm tra có phải JSON request không
     */
    protected function isJsonRequest() {
        return isset($_SERVER['CONTENT_TYPE']) && 
               strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    }
    
    /**
     * Lấy database connection
     */
    protected function db() {
        return $this->db->getConnection();
    }
    
    /**
     * Bắt đầu transaction
     */
    protected function beginTransaction() {
        return $this->db->getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    protected function commit() {
        return $this->db->getConnection()->commit();
    }
    
    /**
     * Rollback transaction
     */
    protected function rollback() {
        return $this->db->getConnection()->rollBack();
    }
}