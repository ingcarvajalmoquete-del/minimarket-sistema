<?php
/**
 * Application Entry Point
 * Mini Market Management System
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Autoload simple PSR-4 compliant
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load .env if present
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($val);
            putenv(trim($key) . '=' . trim($val));
        }
    }
}

// Support Railway MYSQL_URL or direct env variables
if (isset($_ENV['MYSQL_URL']) || getenv('MYSQL_URL')) {
    $dbUrl = parse_url($_ENV['MYSQL_URL'] ?? getenv('MYSQL_URL'));
    $_ENV['DB_HOST'] = $dbUrl['host'] ?? 'localhost';
    $_ENV['DB_PORT'] = (string)($dbUrl['port'] ?? 3306);
    $_ENV['DB_USER'] = $dbUrl['user'] ?? 'root';
    $_ENV['DB_PASSWORD'] = $dbUrl['pass'] ?? '';
    // Si schema.sql creó minimarket_db, usamos esa como prioridad
    $_ENV['DB_NAME'] = 'minimarket_db';
} elseif (isset($_ENV['MYSQLHOST']) || getenv('MYSQLHOST')) {
    $_ENV['DB_HOST'] = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST');
    $_ENV['DB_PORT'] = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? '3306';
    $_ENV['DB_USER'] = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER');
    $_ENV['DB_PASSWORD'] = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD');
    $_ENV['DB_NAME'] = 'minimarket_db';
}

use App\Core\Router;

$router = new Router();

// Auth Routes
$router->get('/', 'DashboardController', 'index');
$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/logout', 'AuthController', 'logout');

// Dashboard
$router->get('/dashboard', 'DashboardController', 'index');

// Products
$router->get('/products', 'ProductController', 'index');
$router->get('/products/create', 'ProductController', 'create');
$router->post('/products', 'ProductController', 'store');
$router->get('/products/{id}', 'ProductController', 'show');
$router->get('/products/{id}/edit', 'ProductController', 'edit');
$router->post('/products/{id}/update', 'ProductController', 'update');
$router->post('/products/{id}/delete', 'ProductController', 'delete');
$router->get('/products/search', 'ProductController', 'search');

// POS & Sales
$router->get('/pos', 'SaleController', 'pos');
$router->post('/pos/store', 'SaleController', 'store');
$router->get('/sales', 'SaleController', 'index');
$router->get('/sales/{id}', 'SaleController', 'show');
$router->post('/sales/{id}/cancel', 'SaleController', 'cancel');

// Inventory
$router->get('/inventory', 'InventoryController', 'index');
$router->get('/inventory/adjust', 'InventoryController', 'adjust');
$router->post('/inventory/adjust', 'InventoryController', 'storeAdjust');

// Categories
$router->get('/categories', 'CategoryController', 'index');
$router->post('/categories', 'CategoryController', 'store');
$router->post('/categories/{id}/delete', 'CategoryController', 'delete');

// Suppliers
$router->get('/suppliers', 'SupplierController', 'index');
$router->get('/suppliers/create', 'SupplierController', 'create');
$router->post('/suppliers', 'SupplierController', 'store');
$router->get('/suppliers/{id}/edit', 'SupplierController', 'edit');
$router->post('/suppliers/{id}/update', 'SupplierController', 'update');
$router->post('/suppliers/{id}/delete', 'SupplierController', 'delete');

// Customers
$router->get('/customers', 'CustomerController', 'index');
$router->post('/customers', 'CustomerController', 'store');
$router->post('/customers/{id}/update', 'CustomerController', 'update');
$router->post('/customers/{id}/delete', 'CustomerController', 'delete');

// Users
$router->get('/users', 'UserController', 'index');
$router->get('/users/create', 'UserController', 'create');
$router->post('/users', 'UserController', 'store');
$router->post('/users/{id}/toggle', 'UserController', 'toggleActive');
$router->post('/users/{id}/reset-password', 'UserController', 'resetPassword');

// Reports
$router->get('/reports/sales', 'ReportController', 'sales');
$router->get('/reports/inventory', 'ReportController', 'inventory');

// Dispatch
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($method, $uri);
