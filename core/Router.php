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
        $uri    = rtrim(str_replace(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '', $uri), '/') ?: '/';

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
        // Load semua file controller (order penting: Base dulu, lalu yang lain)
        $ctrlFiles = [
            APP_PATH . '/models/Models.php',
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
            die("Controller class {$ctrl} tidak ditemukan.");
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

/**
 * Autoloader
 */
spl_autoload_register(function (string $class): void {
    $dirs = [
        ROOT_PATH . '/core/',
        ROOT_PATH . '/app/controllers/',
        ROOT_PATH . '/app/models/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
