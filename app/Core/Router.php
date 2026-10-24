<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function get($path, $handler, $middlewares = []) {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }
    
    public function post($path, $handler, $middlewares = []) {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }
    
    public function put($path, $handler, $middlewares = []) {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }
    
    public function delete($path, $handler, $middlewares = []) {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }
    
    private function addRoute($method, $path, $handler, $middlewares) {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->normalizePath($path),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
        return $this;
    }
    
    private function normalizePath($path) {
        return '/' . trim($path, '/');
    }
    
    public function dispatch($method, $uri) {
        $uri = $this->normalizePath($uri);
        if (empty($uri) || $uri == '/') {
            $uri = '/';
        }
        
        // Bỏ qua query string
        $uri = strtok($uri, '?');
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            
            $pattern = $this->convertPattern($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $params = $this->extractParams($matches);
                
                // Run middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareClass = "App\\Middleware\\{$middleware}";
                    if (class_exists($middlewareClass)) {
                        if (!$middlewareClass::handle()) {
                            return false;
                        }
                    }
                }
                
                return $this->handle($route['handler'], $params);
            }
        }
        
        $this->notFound();
    }
    
    private function convertPattern($path) {
    $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $path);
    return '#^' . $pattern . '$#';
}
    
    private function extractParams($matches) {
        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }
    
    private function handle($handler, $params) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        }
        
        throw new \Exception("Invalid route handler");
    }
    
    private function notFound() {
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>404 - Không tìm thấy</title>
            <style>
                body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; }
                .error { text-align: center; }
                h1 { font-size: 6rem; margin: 0; color: #2563eb; }
                p { color: #64748b; }
                a { color: #2563eb; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="error">
                <h1>404</h1>
                <p>Không tìm thấy trang</p>
                <a href="' . BASE_URL . '">← Về trang chủ</a>
            </div>
        </body>
        </html>';
        exit;
    }
}