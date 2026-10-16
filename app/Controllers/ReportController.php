<?php
/**
 * Report Controller - Reportes de ventas e inventario
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class ReportController extends Controller
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function sales(array $params = []): void
    {
        Auth::requireRole('admin');

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo   = $_GET['date_to']   ?? date('Y-m-d');

        // Daily sales for chart
        $dailySales = $this->db->query(
            "SELECT DATE(created_at) as date,
                    COUNT(*) as count,
                    SUM(subtotal) as subtotal,
                    SUM(tax) as tax,
                    SUM(total) as total
             FROM sales
             WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completada'
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$dateFrom, $dateTo]
        );

        // Top products sold
        $topProducts = $this->db->query(
            "SELECT p.name, p.code, c.name as category_name,
                    SUM(sd.quantity) as total_qty,
                    SUM(sd.subtotal) as revenue,
                    AVG(sd.unit_price) as avg_price
             FROM sale_details sd
             JOIN products p ON sd.product_id = p.id
             JOIN categories c ON p.category_id = c.id
             JOIN sales s ON sd.sale_id = s.id
             WHERE DATE(s.created_at) BETWEEN ? AND ? AND s.status = 'completada'
             GROUP BY p.id, p.name, p.code, c.name
             ORDER BY revenue DESC
             LIMIT 20",
            [$dateFrom, $dateTo]
        );

        // Sales by payment method
        $byPayment = $this->db->query(
            "SELECT payment_method, COUNT(*) as count, SUM(total) as total
             FROM sales
             WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completada'
             GROUP BY payment_method",
            [$dateFrom, $dateTo]
        );

        // Summary totals
        $summary = $this->db->queryOne(
            "SELECT COUNT(*) as transactions,
                    COALESCE(SUM(subtotal),0) as subtotal,
                    COALESCE(SUM(tax),0) as tax,
                    COALESCE(SUM(discount),0) as discount,
                    COALESCE(SUM(total),0) as total
             FROM sales
             WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completada'",
            [$dateFrom, $dateTo]
        );

        $this->render('reports.sales', [
            'title'       => 'Reporte de Ventas',
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'dailySales'  => $dailySales,
            'topProducts' => $topProducts,
            'byPayment'   => $byPayment,
            'summary'     => $summary,
        ]);
    }

    public function inventory(array $params = []): void
    {
        Auth::requireRole('admin', 'almacen');

        $category = (int)($_GET['category'] ?? 0);

        $where = ['p.is_active = 1'];
        $binds = [];
        if ($category > 0) {
            $where[] = 'p.category_id = ?';
            $binds[] = $category;
        }
        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $products = $this->db->query(
            "SELECT p.*, c.name as category_name,
                    (p.cost_price * p.stock) as stock_value,
                    (p.sale_price * p.stock) as sale_value
             FROM products p
             JOIN categories c ON p.category_id = c.id
             {$whereSQL}
             ORDER BY p.stock ASC",
            $binds
        );

        $summary = $this->db->queryOne(
            "SELECT COUNT(*) as total_products,
                    SUM(stock) as total_units,
                    SUM(cost_price * stock) as total_cost_value,
                    SUM(sale_price * stock) as total_sale_value,
                    SUM(CASE WHEN stock <= min_stock THEN 1 ELSE 0 END) as low_stock_count
             FROM products p {$whereSQL}",
            $binds
        );

        $categories = $this->db->query('SELECT * FROM categories WHERE is_active = 1 ORDER BY name');

        $this->render('reports.inventory', [
            'title'      => 'Reporte de Inventario',
            'products'   => $products,
            'summary'    => $summary,
            'categories' => $categories,
            'category'   => $category,
        ]);
    }
}
