<?php
/**
 * Category Controller
 */
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class CategoryController extends Controller
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function index(array $params = []): void
    {
        Auth::requireAuth();
        $categories = $this->db->query(
            "SELECT c.*, COUNT(p.id) as product_count
             FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.is_active=1
             WHERE c.is_active = 1 GROUP BY c.id ORDER BY c.name"
        );
        $this->render('categories.index', ['title' => 'Categorías', 'categories' => $categories]);
    }

    public function store(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if (empty($name)) { Session::setError('El nombre es obligatorio.'); $this->redirect('/categories'); }
        $exists = $this->db->queryOne('SELECT id FROM categories WHERE name = ?', [$name]);
        if ($exists) { Session::setError('Ya existe una categoría con ese nombre.'); $this->redirect('/categories'); }
        $this->db->execute('INSERT INTO categories (name, description) VALUES (?, ?)', [$name, $desc]);
        Session::setSuccess("Categoría '{$name}' creada.");
        $this->redirect('/categories');
    }

    public function update(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id   = (int)($params['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if (empty($name)) { Session::setError('El nombre es obligatorio.'); $this->redirect('/categories'); }
        $this->db->execute('UPDATE categories SET name=?, description=?, updated_at=NOW() WHERE id=?', [$name, $desc, $id]);
        Session::setSuccess("Categoría actualizada.");
        $this->redirect('/categories');
    }

    public function delete(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);
        $count = $this->db->queryOne('SELECT COUNT(*) as c FROM products WHERE category_id=? AND is_active=1', [$id])['c'];
        if ($count > 0) { Session::setError("No se puede eliminar: tiene {$count} producto(s) activo(s)."); $this->redirect('/categories'); }
        $this->db->execute('UPDATE categories SET is_active=0 WHERE id=?', [$id]);
        Session::setSuccess('Categoría eliminada.');
        $this->redirect('/categories');
    }
}
