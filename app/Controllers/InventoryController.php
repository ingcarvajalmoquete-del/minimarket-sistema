<?php
/**
 * Inventory Controller - Movimientos de inventario
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class InventoryController extends Controller
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(array $params = []): void
    {
        Auth::requireAuth();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;
        $type    = $_GET['type'] ?? '';
        $search  = trim($_GET['search'] ?? '');

        $where = [];
        $binds = [];

        if (!empty($type)) {
            $where[] = 'im.type = ?';
            $binds[] = $type;
        }
        if (!empty($search)) {
            $where[] = '(p.name LIKE ? OR im.reference LIKE ?)';
            $binds[] = "%{$search}%";
            $binds[] = "%{$search}%";
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = (int)$this->db->queryOne(
            "SELECT COUNT(*) as count FROM inventory_movements im
             JOIN products p ON im.product_id = p.id {$whereSQL}",
            $binds
        )['count'];

        $movements = $this->db->query(
            "SELECT im.*, p.name as product_name, p.code as product_code, u.name as user_name
             FROM inventory_movements im
             JOIN products p ON im.product_id = p.id
             JOIN users u ON im.user_id = u.id
             {$whereSQL}
             ORDER BY im.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $binds
        );

        $this->render('inventory.index', [
            'title'      => 'Movimientos de Inventario',
            'movements'  => $movements,
            'type'       => $type,
            'search'     => $search,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => (int)ceil($total / $perPage),
        ]);
    }

    public function adjust(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');
        $products = $this->db->query(
            'SELECT id, code, name, stock FROM products WHERE is_active = 1 ORDER BY name'
        );
        $this->render('inventory.adjust', [
            'title'    => 'Ajuste de Inventario',
            'products' => $products,
        ]);
    }

    public function storeAdjust(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');
        $this->verifyCsrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        $type      = $_POST['type'] ?? '';
        $quantity  = (int)($_POST['quantity'] ?? 0);
        $notes     = trim($_POST['notes'] ?? '');
        $reference = trim($_POST['reference'] ?? '');

        $errors = [];
        if ($productId <= 0)            $errors[] = 'Seleccione un producto.';
        if (!in_array($type, ['entrada','salida','ajuste'])) $errors[] = 'Tipo de movimiento inválido.';
        if ($quantity <= 0)             $errors[] = 'La cantidad debe ser mayor a 0.';

        if (!empty($errors)) {
            Session::setErrors($errors);
            $this->redirect('/inventory/adjust');
        }

        $product = $this->db->queryOne('SELECT * FROM products WHERE id = ? AND is_active = 1', [$productId]);
        if (!$product) {
            Session::setError('Producto no encontrado.');
            $this->redirect('/inventory/adjust');
        }

        $stockBefore = $product['stock'];
        if ($type === 'salida') {
            if ($quantity > $stockBefore) {
                Session::setError("Stock insuficiente. Disponible: {$stockBefore}");
                $this->redirect('/inventory/adjust');
            }
            $stockAfter = $stockBefore - $quantity;
        } elseif ($type === 'entrada') {
            $stockAfter = $stockBefore + $quantity;
        } else {
            // ajuste: set to quantity
            $stockAfter = $quantity;
        }

        $this->db->beginTransaction();
        try {
            $this->db->execute('UPDATE products SET stock = ? WHERE id = ?', [$stockAfter, $productId]);
            $this->db->execute(
                "INSERT INTO inventory_movements (product_id, user_id, type, quantity, stock_before, stock_after, reference, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$productId, Auth::id(), $type,
                 $type === 'ajuste' ? ($stockAfter - $stockBefore) : $quantity,
                 $stockBefore, $stockAfter, $reference ?: null, $notes ?: null]
            );
            $this->db->commit();
            Session::setSuccess('Movimiento de inventario registrado correctamente.');
        } catch (\Exception $e) {
            $this->db->rollback();
            Session::setError('Error al registrar el movimiento: ' . $e->getMessage());
        }

        $this->redirect('/inventory');
    }
}
