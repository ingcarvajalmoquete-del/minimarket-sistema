<?php
/**
 * Product Controller - CRUD Completo con búsqueda y paginación
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class ProductController extends Controller
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(array $params = []): void
    {
        Auth::requireAuth();

        $search   = trim($_GET['search'] ?? '');
        $category = (int)($_GET['category'] ?? 0);
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 20;
        $offset   = ($page - 1) * $perPage;

        $where  = ['p.is_active = 1'];
        $binds  = [];

        if ($search !== '') {
            $where[] = '(p.name LIKE ? OR p.code LIKE ?)';
            $binds[] = "%{$search}%";
            $binds[] = "%{$search}%";
        }

        if ($category > 0) {
            $where[] = 'p.category_id = ?';
            $binds[] = $category;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $total = (int)$this->db->queryOne(
            "SELECT COUNT(*) as count FROM products p {$whereSQL}",
            $binds
        )['count'];

        $products = $this->db->query(
            "SELECT p.*, c.name as category_name, s.name as supplier_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             LEFT JOIN suppliers s ON p.supplier_id = s.id
             {$whereSQL}
             ORDER BY p.name ASC
             LIMIT {$perPage} OFFSET {$offset}",
            $binds
        );

        $categories = $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name');

        $this->render('products.index', [
            'title'      => 'Productos',
            'products'   => $products,
            'categories' => $categories,
            'search'     => $search,
            'category'   => $category,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => (int)ceil($total / $perPage),
        ]);
    }

    public function create(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');
        $categories = $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name');
        $suppliers  = $this->db->query('SELECT * FROM suppliers WHERE is_active = 1 ORDER BY name');
        $this->render('products.create', [
            'title'      => 'Nuevo Producto',
            'categories' => $categories,
            'suppliers'  => $suppliers,
        ]);
    }

    public function store(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');
        $this->verifyCsrf();

        $data   = $this->extractProductData();
        $errors = $this->validateProduct($data);

        if (!empty($errors)) {
            Session::setErrors($errors);
            Session::flash('old', $data);
            $this->redirect('/products/create');
        }

        // Check duplicate code
        if (!empty($data['code'])) {
            $exists = $this->db->queryOne('SELECT id FROM products WHERE code = ?', [$data['code']]);
            if ($exists) {
                Session::setError('El código de producto ya existe.');
                Session::flash('old', $data);
                $this->redirect('/products/create');
            }
        }

        $this->db->execute(
            "INSERT INTO products (category_id, supplier_id, code, name, description, unit, cost_price, sale_price, stock, min_stock)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['category_id'], $data['supplier_id'] ?: null, $data['code'] ?: null,
                $data['name'], $data['description'], $data['unit'],
                $data['cost_price'], $data['sale_price'], $data['stock'], $data['min_stock'],
            ]
        );

        $productId = (int)$this->db->lastInsertId();

        // Register initial inventory movement
        if ($data['stock'] > 0) {
            $this->db->execute(
                "INSERT INTO inventory_movements (product_id, user_id, type, quantity, stock_before, stock_after, notes)
                 VALUES (?, ?, 'entrada', ?, 0, ?, 'Stock inicial del producto')",
                [$productId, Auth::id(), $data['stock'], $data['stock']]
            );
        }

        Session::setSuccess("Producto '{$data['name']}' creado correctamente.");
        $this->redirect('/products');
    }

    public function edit(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');
        $id = (int)($params['id'] ?? 0);
        $product = $this->getProductOrFail($id);
        $categories = $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name');
        $suppliers  = $this->db->query('SELECT * FROM suppliers WHERE is_active = 1 ORDER BY name');

        $this->render('products.edit', [
            'title'      => 'Editar Producto',
            'product'    => $product,
            'categories' => $categories,
            'suppliers'  => $suppliers,
        ]);
    }

    public function update(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);
        $product = $this->getProductOrFail($id);

        $data   = $this->extractProductData();
        $errors = $this->validateProduct($data);

        if (!empty($errors)) {
            Session::setErrors($errors);
            Session::flash('old', $data);
            $this->redirect("/products/{$id}/edit");
        }

        // Check duplicate code (excluding self)
        if (!empty($data['code'])) {
            $exists = $this->db->queryOne(
                'SELECT id FROM products WHERE code = ? AND id != ?',
                [$data['code'], $id]
            );
            if ($exists) {
                Session::setError('El código ya existe en otro producto.');
                $this->redirect("/products/{$id}/edit");
            }
        }

        $this->db->execute(
            "UPDATE products SET category_id=?, supplier_id=?, code=?, name=?, description=?,
             unit=?, cost_price=?, sale_price=?, min_stock=?, updated_at=NOW()
             WHERE id=?",
            [
                $data['category_id'], $data['supplier_id'] ?: null, $data['code'] ?: null,
                $data['name'], $data['description'], $data['unit'],
                $data['cost_price'], $data['sale_price'], $data['min_stock'], $id,
            ]
        );

        Session::setSuccess("Producto '{$data['name']}' actualizado correctamente.");
        $this->redirect('/products');
    }

    public function delete(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);
        $product = $this->getProductOrFail($id);

        // Soft delete
        $this->db->execute('UPDATE products SET is_active = 0 WHERE id = ?', [$id]);

        Session::setSuccess("Producto '{$product['name']}' eliminado correctamente.");
        $this->redirect('/products');
    }

    public function show(array $params = []): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);
        $product = $this->getProductOrFail($id);

        $movements = $this->db->query(
            "SELECT im.*, u.name as user_name
             FROM inventory_movements im
             JOIN users u ON im.user_id = u.id
             WHERE im.product_id = ?
             ORDER BY im.created_at DESC
             LIMIT 30",
            [$id]
        );

        $this->render('products.show', [
            'title'     => $product['name'],
            'product'   => $product,
            'movements' => $movements,
        ]);
    }

    // AJAX: Search products for POS
    public function search(array $params = []): void
    {
        Auth::requireAuth();
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            $this->json([]);
        }

        $results = $this->db->query(
            "SELECT id, code, name, sale_price, stock, unit
             FROM products
             WHERE is_active = 1 AND stock > 0 AND (name LIKE ? OR code LIKE ?)
             ORDER BY name ASC
             LIMIT 15",
            ["%{$q}%", "%{$q}%"]
        );

        $this->json($results);
    }

    // ----------------------------------------------------------------
    // Private helpers
    // ----------------------------------------------------------------

    private function extractProductData(): array
    {
        return [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'supplier_id' => (int)($_POST['supplier_id'] ?? 0),
            'code'        => trim($_POST['code'] ?? ''),
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'unit'        => trim($_POST['unit'] ?? 'unidad'),
            'cost_price'  => (float)($_POST['cost_price'] ?? 0),
            'sale_price'  => (float)($_POST['sale_price'] ?? 0),
            'stock'       => (int)($_POST['stock'] ?? 0),
            'min_stock'   => (int)($_POST['min_stock'] ?? 5),
        ];
    }

    private function validateProduct(array $data): array
    {
        $errors = [];
        if (empty($data['name']))          $errors['name']        = 'El nombre es obligatorio.';
        if ($data['category_id'] <= 0)     $errors['category_id'] = 'Seleccione una categoría.';
        if ($data['sale_price'] <= 0)      $errors['sale_price']  = 'El precio de venta debe ser mayor a 0.';
        if ($data['cost_price'] < 0)       $errors['cost_price']  = 'El costo no puede ser negativo.';
        if ($data['min_stock'] < 0)        $errors['min_stock']   = 'El stock mínimo no puede ser negativo.';
        return $errors;
    }

    private function getProductOrFail(int $id): array
    {
        $product = $this->db->queryOne(
            "SELECT p.*, c.name as category_name FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.id = ?",
            [$id]
        );
        if (!$product) {
            http_response_code(404);
            require APP_PATH . '/Views/errors/404.php';
            exit;
        }
        return $product;
    }
}
