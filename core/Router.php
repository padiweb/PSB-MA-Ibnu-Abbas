<?php
/**
 * Router - Simple MVC Router
 */
class Router
{
    private array $routes = [];

    public function get(string $pattern, string $controller, string $method): void
    {
        $this->routes['GET'][$pattern] = [$controller, $method];
    }

    public function post(string $pattern, string $controller, string $method): void
    {
        $this->routes['POST'][$pattern] = [$controller, $method];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base   = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        $uri    = rtrim(str_replace($base, '', $uri), '/') ?: '/';

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => [$ctrl, $action]) {
            $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->run($ctrl, $action, $params);
                return;
            }
        }

        http_response_code(404);
        $this->run('ErrorController', 'notFound', []);
    }

    private function run(string $ctrl, string $action, array $params): void
    {
        // Semua file sudah di-load di index.php via require_once
        // Cukup load controller files di sini (require_once aman dari double-load)
        $ctrlFiles = [
            APP_PATH . '/controllers/BaseController.php',
            APP_PATH . '/controllers/PendaftaranController.php',
            APP_PATH . '/controllers/AdminController.php',
            APP_PATH . '/controllers/ExtraControllers.php',
        ];
        foreach ($ctrlFiles as $f) {
            if (file_exists($f)) require_once $f;
        }

        if (!class_exists($ctrl)) {
            http_response_code(500);
            die("Controller [{$ctrl}] tidak ditemukan.");
        }

        $obj = new $ctrl();
        if (!method_exists($obj, $action)) {
            http_response_code(404);
            $this->run('ErrorController', 'notFound', []);
            return;
        }
        call_user_func_array([$obj, $action], $params);
    }
}