<?php
/**
 * Supplier Controller
 */
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class SupplierController extends Controller
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function index(array $params = []): void
    {
        Auth::requireAuth();
        $suppliers = $this->db->query(
            "SELECT s.*, COUNT(p.id) as product_count
             FROM suppliers s LEFT JOIN products p ON s.id = p.supplier_id AND p.is_active=1
             WHERE s.is_active = 1 GROUP BY s.id ORDER BY s.name"
        );
        $this->render('suppliers.index', ['title' => 'Proveedores', 'suppliers' => $suppliers]);
    }

    public function create(array $params = []): void
    {
        Auth::requireAdmin();
        $this->render('suppliers.create', ['title' => 'Nuevo Proveedor']);
    }

    public function store(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $data = $this->extract();
        if (empty($data['name'])) { Session::setError('El nombre es obligatorio.'); $this->redirect('/suppliers/create'); }
        $this->db->execute(
            "INSERT INTO suppliers (name, contact_name, phone, email, address, rnc) VALUES (?,?,?,?,?,?)",
            array_values($data)
        );
        Session::setSuccess("Proveedor '{$data['name']}' creado.");
        $this->redirect('/suppliers');
    }

    public function edit(array $params = []): void
    {
        Auth::requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $supplier = $this->db->queryOne('SELECT * FROM suppliers WHERE id=?', [$id]);
        if (!$supplier) { $this->redirect('/suppliers'); }
        $this->render('suppliers.edit', ['title' => 'Editar Proveedor', 'supplier' => $supplier]);
    }

    public function update(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id   = (int)($params['id'] ?? 0);
        $data = $this->extract();
        if (empty($data['name'])) { Session::setError('El nombre es obligatorio.'); $this->redirect("/suppliers/{$id}/edit"); }
        $this->db->execute(
            "UPDATE suppliers SET name=?,contact_name=?,phone=?,email=?,address=?,rnc=?,updated_at=NOW() WHERE id=?",
            [...array_values($data), $id]
        );
        Session::setSuccess('Proveedor actualizado.');
        $this->redirect('/suppliers');
    }

    public function delete(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);
        $this->db->execute('UPDATE suppliers SET is_active=0 WHERE id=?', [$id]);
        Session::setSuccess('Proveedor eliminado.');
        $this->redirect('/suppliers');
    }

    private function extract(): array
    {
        return [
            'name'         => trim($_POST['name'] ?? ''),
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'phone'        => trim($_POST['phone'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'address'      => trim($_POST['address'] ?? ''),
            'rnc'          => trim($_POST['rnc'] ?? ''),
        ];
    }
}
