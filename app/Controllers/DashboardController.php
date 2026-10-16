<?php
/**
 * Dashboard Controller - KPIs y estadísticas
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class DashboardController extends Controller
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(array $params = []): void
    {
        Auth::requireAuth();

        $today = date('Y-m-d');

        // KPI: Ventas de hoy
        $todaySales = $this->db->queryOne(
            "SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as total
             FROM sales WHERE DATE(created_at) = ? AND status = 'completada'",
            [$today]
        );

        // KPI: Ventas del mes
        $monthSales = $this->db->queryOne(
            "SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as total
             FROM sales
             WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW()) AND status = 'completada'"
        );

        // KPI: Productos activos
        $totalProducts = $this->db->queryOne(
            "SELECT COUNT(*) as count FROM products WHERE is_active = 1"
        );

        // KPI: Productos con stock bajo
        $lowStockCount = $this->db->queryOne(
            "SELECT COUNT(*) as count FROM products WHERE stock <= min_stock AND is_active = 1"
        );

        // KPI: Clientes registrados
        $totalCustomers = $this->db->queryOne(
            "SELECT COUNT(*) as count FROM customers WHERE is_active = 1"
        );

        // Ventas últimos 7 días (para gráfica)
        $salesLast7Days = $this->db->query(
            "SELECT DATE(created_at) as date, COUNT(*) as sales_count, COALESCE(SUM(total), 0) as total
             FROM sales
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status = 'completada'
             GROUP BY DATE(created_at)
             ORDER BY date ASC"
        );

        // Últimas 10 ventas
        $recentSales = $this->db->query(
            "SELECT s.*, u.name as cashier, c.name as customer_name
             FROM sales s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN customers c ON s.customer_id = c.id
             ORDER BY s.created_at DESC
             LIMIT 10"
        );

        // Top 5 productos más vendidos
        $topProducts = $this->db->query(
            "SELECT p.name, SUM(sd.quantity) as total_sold, SUM(sd.subtotal) as revenue
             FROM sale_details sd
             JOIN products p ON sd.product_id = p.id
             JOIN sales s ON sd.sale_id = s.id
             WHERE s.status = 'completada'
             GROUP BY p.id, p.name
             ORDER BY total_sold DESC
             LIMIT 5"
        );

        // Productos con stock bajo
        $lowStockProducts = $this->db->query(
            "SELECT p.*, c.name as category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.stock <= p.min_stock AND p.is_active = 1
             ORDER BY p.stock ASC
             LIMIT 10"
        );

        $this->render('dashboard.index', [
            'title'            => 'Dashboard',
            'todaySales'       => $todaySales,
            'monthSales'       => $monthSales,
            'totalProducts'    => $totalProducts,
            'lowStockCount'    => $lowStockCount,
            'totalCustomers'   => $totalCustomers,
            'salesLast7Days'   => $salesLast7Days,
            'recentSales'      => $recentSales,
            'topProducts'      => $topProducts,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
