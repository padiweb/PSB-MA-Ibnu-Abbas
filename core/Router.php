<?php
/**
 * Router — Query String based
 * Tidak butuh mod_rewrite / .htaccess
 * URL: index.php?page=login&id=5
 */
class Router
{
    private array $routes = [];

    public function get(string $pattern, string $ctrl, string $action): void
    {
        $this->routes['GET'][$pattern] = [$ctrl, $action];
    }

    public function post(string $pattern, string $ctrl, string $action): void
    {
        $this->routes['POST'][$pattern] = [$ctrl, $action];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Ambil path dari query string ?page=xxx/yyy
        $page = trim($_GET['page'] ?? '', '/');
        $uri  = '/' . $page;
        if ($uri === '/') $uri = '/';

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => [$ctrl, $action]) {
            $regex  = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->run($ctrl, $action, $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        $this->run('ErrorController', 'notFound', []);
    }

    private function run(string $ctrl, string $action, array $params): void
    {
        $files = [
            APP_PATH . '/controllers/BaseController.php',
            APP_PATH . '/controllers/PendaftaranController.php',
            APP_PATH . '/controllers/AdminController.php',
            APP_PATH . '/controllers/ExtraControllers.php',
        ];
        foreach ($files as $f) {
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
