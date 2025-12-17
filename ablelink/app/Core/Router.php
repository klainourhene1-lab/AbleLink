<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $basePath = '/projetweb/ablelink/';
    
    public function __construct() {
        $this->parseRequest();
    }
    
    private function parseRequest(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = strpos($uri, $this->basePath) === 0 
            ? trim(substr($uri, strlen($this->basePath)), '/') 
            : trim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'];
        
        $this->path = $path ?: 'index';
        $this->method = $method;
    }
    
    public function get(string $path, $handler): void {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, $handler): void {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute(string $method, string $path, $handler): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch(): void {
        foreach ($this->routes as $route) {
            if ($route['method'] === $this->method && $this->matchRoute($route['path'], $this->path)) {
                $handler = $route['handler'];
                
                if (is_callable($handler)) {
                    call_user_func($handler);
                    return;
                }
                
                // Support pour tableau [ControllerClass, 'method']
                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $method] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $method)) {
                            $controller->$method();
                            return;
                        }
                    }
                }
                
                // Support pour string "Controller@method"
                if (is_string($handler) && strpos($handler, '@') !== false) {
                    [$controllerClass, $method] = explode('@', $handler);
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $method)) {
                            $controller->$method();
                            return;
                        }
                    }
                }
            }
        }
        
        http_response_code(404);
        echo 'Not Found';
    }
    
    private function matchRoute(string $route, string $path): bool {
        // Exact match pour les routes simples
        if ($route === $path) {
            return true;
        }
        
        // Support pour les paramètres dynamiques (ex: {id})
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        return preg_match($routePattern, $path);
    }
    
    public function getPath(): string {
        return $this->path;
    }
    
    public function getMethod(): string {
        return $this->method;
    }
}

