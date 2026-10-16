<?php
/**
 * Customer Controller
 */
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class CustomerController extends Controller
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function index(array $params = []): void
    {
        Auth::requireAuth();
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $per    = 20;
        $offset = ($page - 1) * $per;

        $where = ['c.is_active = 1'];
        $binds = [];
        if ($search !== '') {
            $where[] = '(c.name LIKE ? OR c.cedula LIKE ? OR c.phone LIKE ?)';
            $binds = ["%$search%", "%$search%", "%$search%"];
        }
        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $total = (int)$this->db->queryOne(
            "SELECT COUNT(*) as c FROM customers c $whereSQL", $binds
        )['c'];

        $customers = $this->db->query(
            "SELECT c.*, COUNT(s.id) as total_purchases, COALESCE(SUM(s.total),0) as total_spent
             FROM customers c LEFT JOIN sales s ON c.id = s.customer_id AND s.status='completada'
             $whereSQL GROUP BY c.id ORDER BY c.name LIMIT $per OFFSET $offset",
            $binds
        );

        $this->render('customers.index', [
            'title'      => 'Clientes',
            'customers'  => $customers,
            'search'     => $search,
            'page'       => $page,
            'total'      => $total,
            'totalPages' => (int)ceil($total / $per),
        ]);
    }

    public function store(array $params = []): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();
        $data = $this->extract();
        if (empty($data['name'])) { Session::setError('El nombre es obligatorio.'); $this->redirect('/customers'); }
        $this->db->execute(
            "INSERT INTO customers (name, phone, email, address, cedula) VALUES (?,?,?,?,?)",
            array_values($data)
        );
        Session::setSuccess("Cliente '{$data['name']}' registrado.");
        $this->redirect('/customers');
    }

    public function update(array $params = []): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();
        $id   = (int)($params['id'] ?? 0);
        $data = $this->extract();
        $this->db->execute(
            "UPDATE customers SET name=?,phone=?,email=?,address=?,cedula=?,updated_at=NOW() WHERE id=?",
            [...array_values($data), $id]
        );
        Session::setSuccess('Cliente actualizado.');
        $this->redirect('/customers');
    }

    public function delete(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);
        $this->db->execute('UPDATE customers SET is_active=0 WHERE id=?', [$id]);
        Session::setSuccess('Cliente eliminado.');
        $this->redirect('/customers');
    }

    private function extract(): array
    {
        return [
            'name'    => trim($_POST['name'] ?? ''),
            'phone'   => trim($_POST['phone'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'cedula'  => trim($_POST['cedula'] ?? ''),
        ];
    }
}
