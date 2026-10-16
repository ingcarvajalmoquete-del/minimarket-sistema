<?php
/**
 * Sale Controller - Punto de Venta (POS) y historial
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class SaleController extends Controller
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ----------------------------------------------------------------
    // POS - Punto de Venta
    // ----------------------------------------------------------------
    public function pos(array $params = []): void
    {
        Auth::requireAuth();
        $customers = $this->db->query('SELECT id, name, cedula FROM customers WHERE is_active = 1 ORDER BY name');
        $this->render('sales.pos', [
            'title'     => 'Punto de Venta',
            'customers' => $customers,
        ]);
    }

    // AJAX: Process sale from POS
    public function store(array $params = []): void
    {
        Auth::requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input) || empty($input['items'])) {
            $this->json(['success' => false, 'message' => 'El carrito está vacío.'], 400);
        }

        $customerId    = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
        $paymentMethod = $input['payment_method'] ?? 'efectivo';
        $discount      = (float)($input['discount'] ?? 0);
        $amountPaid    = (float)($input['amount_paid'] ?? 0);
        $items         = $input['items'];

        // Validate items and calculate totals
        $subtotal = 0;
        $validItems = [];

        $this->db->beginTransaction();

        try {
            foreach ($items as $item) {
                $productId = (int)$item['product_id'];
                $quantity  = (int)$item['quantity'];

                $product = $this->db->queryOne(
                    'SELECT id, name, sale_price, stock FROM products WHERE id = ? AND is_active = 1',
                    [$productId]
                );

                if (!$product) {
                    throw new \RuntimeException("Producto ID {$productId} no encontrado.");
                }
                if ($product['stock'] < $quantity) {
                    throw new \RuntimeException("Stock insuficiente para: {$product['name']}. Disponible: {$product['stock']}");
                }

                $unitPrice = (float)$product['sale_price'];
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $validItems[] = [
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $lineTotal,
                    'stock'      => $product['stock'],
                ];
            }

            $tax          = round($subtotal * 0.18, 2); // ITBIS 18%
            $total        = round($subtotal + $tax - $discount, 2);
            $changeAmount = max(0, $amountPaid - $total);

            // Generate invoice number: MM-YYYYMMDD-NNNN
            $lastInvoice = $this->db->queryOne(
                "SELECT invoice_number FROM sales ORDER BY id DESC LIMIT 1"
            );
            $invoiceNumber = $this->generateInvoiceNumber($lastInvoice['invoice_number'] ?? null);

            // Insert sale
            $this->db->execute(
                "INSERT INTO sales (invoice_number, customer_id, user_id, subtotal, tax, discount, total,
                                    payment_method, amount_paid, change_amount, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completada')",
                [
                    $invoiceNumber, $customerId, Auth::id(),
                    $subtotal, $tax, $discount, $total,
                    $paymentMethod, $amountPaid, $changeAmount,
                ]
            );

            $saleId = (int)$this->db->lastInsertId();

            // Insert details + update stock
            foreach ($validItems as $item) {
                $this->db->execute(
                    "INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, subtotal)
                     VALUES (?, ?, ?, ?, ?)",
                    [$saleId, $item['product_id'], $item['quantity'], $item['unit_price'], $item['subtotal']]
                );

                $newStock = $item['stock'] - $item['quantity'];
                $this->db->execute(
                    'UPDATE products SET stock = ? WHERE id = ?',
                    [$newStock, $item['product_id']]
                );

                // Inventory movement
                $this->db->execute(
                    "INSERT INTO inventory_movements (product_id, user_id, type, quantity, stock_before, stock_after, reference, notes)
                     VALUES (?, ?, 'salida', ?, ?, ?, ?, 'Venta')",
                    [
                        $item['product_id'], Auth::id(), $item['quantity'],
                        $item['stock'], $newStock, $invoiceNumber,
                    ]
                );
            }

            $this->db->commit();

            $this->json([
                'success'        => true,
                'invoice_number' => $invoiceNumber,
                'sale_id'        => $saleId,
                'total'          => $total,
                'change_amount'  => $changeAmount,
                'message'        => 'Venta procesada correctamente.',
            ]);

        } catch (\Exception $e) {
            $this->db->rollback();
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // ----------------------------------------------------------------
    // Sales List
    // ----------------------------------------------------------------
    public function index(array $params = []): void
    {
        Auth::requireAuth();

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo   = $_GET['date_to']   ?? date('Y-m-d');
        $status   = $_GET['status']    ?? '';
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 20;
        $offset   = ($page - 1) * $perPage;

        $where = ['DATE(s.created_at) BETWEEN ? AND ?'];
        $binds = [$dateFrom, $dateTo];

        if (!empty($status)) {
            $where[] = 's.status = ?';
            $binds[] = $status;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $total = (int)$this->db->queryOne(
            "SELECT COUNT(*) as count FROM sales s {$whereSQL}", $binds
        )['count'];

        $sales = $this->db->query(
            "SELECT s.*, u.name as cashier, c.name as customer_name
             FROM sales s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN customers c ON s.customer_id = c.id
             {$whereSQL}
             ORDER BY s.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $binds
        );

        // Totals summary
        $summary = $this->db->queryOne(
            "SELECT COUNT(*) as count, COALESCE(SUM(total),0) as total
             FROM sales s {$whereSQL} AND s.status = 'completada'",
            [...$binds]
        );

        $this->render('sales.index', [
            'title'      => 'Historial de Ventas',
            'sales'      => $sales,
            'summary'    => $summary,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
            'status'     => $status,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => (int)ceil($total / $perPage),
        ]);
    }

    public function show(array $params = []): void
    {
        Auth::requireAuth();
        $id = (int)($params['id'] ?? 0);

        $sale = $this->db->queryOne(
            "SELECT s.*, u.name as cashier, c.name as customer_name, c.cedula as customer_cedula
             FROM sales s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN customers c ON s.customer_id = c.id
             WHERE s.id = ?",
            [$id]
        );

        if (!$sale) {
            http_response_code(404);
            require APP_PATH . '/Views/errors/404.php';
            exit;
        }

        $details = $this->db->query(
            "SELECT sd.*, p.name as product_name, p.code as product_code, p.unit
             FROM sale_details sd
             JOIN products p ON sd.product_id = p.id
             WHERE sd.sale_id = ?",
            [$id]
        );

        $this->render('sales.show', [
            'title'   => 'Factura ' . $sale['invoice_number'],
            'sale'    => $sale,
            'details' => $details,
        ]);
    }

    public function cancel(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);

        $sale = $this->db->queryOne('SELECT * FROM sales WHERE id = ?', [$id]);
        if (!$sale || $sale['status'] !== 'completada') {
            Session::setError('No se puede anular esta venta.');
            $this->redirect('/sales');
        }

        $this->db->beginTransaction();
        try {
            // Restore stock
            $details = $this->db->query('SELECT * FROM sale_details WHERE sale_id = ?', [$id]);
            foreach ($details as $d) {
                $product = $this->db->queryOne('SELECT stock FROM products WHERE id = ?', [$d['product_id']]);
                $newStock = $product['stock'] + $d['quantity'];
                $this->db->execute('UPDATE products SET stock = ? WHERE id = ?', [$newStock, $d['product_id']]);
                $this->db->execute(
                    "INSERT INTO inventory_movements (product_id, user_id, type, quantity, stock_before, stock_after, reference, notes)
                     VALUES (?, ?, 'entrada', ?, ?, ?, ?, 'Anulación de venta')",
                    [$d['product_id'], Auth::id(), $d['quantity'], $product['stock'], $newStock, $sale['invoice_number']]
                );
            }

            $this->db->execute('UPDATE sales SET status = ? WHERE id = ?', ['anulada', $id]);
            $this->db->commit();

            Session::setSuccess("Venta {$sale['invoice_number']} anulada correctamente. Stock restaurado.");
        } catch (\Exception $e) {
            $this->db->rollback();
            Session::setError('Error al anular la venta: ' . $e->getMessage());
        }

        $this->redirect('/sales');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function generateInvoiceNumber(?string $lastInvoice): string
    {
        $prefix = 'MM-' . date('Ymd') . '-';
        if ($lastInvoice && str_starts_with($lastInvoice, 'MM-' . date('Ymd'))) {
            $seq = (int)substr($lastInvoice, -4) + 1;
        } else {
            $seq = 1;
        }
        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }
}
