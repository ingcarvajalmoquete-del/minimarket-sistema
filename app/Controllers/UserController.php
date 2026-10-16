<?php
/**
 * User Controller - Admin only
 */
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class UserController extends Controller
{
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function index(array $params = []): void
    {
        Auth::requireAdmin();
        $users = $this->db->query('SELECT * FROM users ORDER BY name');
        $this->render('users.index', ['title' => 'Usuarios del Sistema', 'users' => $users]);
    }

    public function create(array $params = []): void
    {
        Auth::requireAdmin();
        $this->render('users.create', ['title' => 'Nuevo Usuario']);
    }

    public function store(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'cajero';

        $errors = [];
        if (empty($name))     $errors[] = 'El nombre es obligatorio.';
        if (empty($email))    $errors[] = 'El correo es obligatorio.';
        if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        if (!in_array($role, ['admin', 'cajero', 'almacen'])) $errors[] = 'Rol inválido.';

        if ($errors) { Session::setErrors($errors); $this->redirect('/users/create'); }

        $exists = $this->db->queryOne('SELECT id FROM users WHERE email=?', [$email]);
        if ($exists) { Session::setError('El correo ya está registrado.'); $this->redirect('/users/create'); }

        $this->db->execute(
            'INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)',
            [$name, $email, password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $role]
        );
        Session::setSuccess("Usuario '{$name}' creado.");
        $this->redirect('/users');
    }

    public function toggleActive(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id = (int)($params['id'] ?? 0);
        if ($id === Auth::id()) { Session::setError('No puede desactivar su propio usuario.'); $this->redirect('/users'); }
        $user = $this->db->queryOne('SELECT * FROM users WHERE id=?', [$id]);
        if (!$user) { $this->redirect('/users'); }
        $newStatus = $user['is_active'] ? 0 : 1;
        $this->db->execute('UPDATE users SET is_active=? WHERE id=?', [$newStatus, $id]);
        Session::setSuccess($newStatus ? 'Usuario activado.' : 'Usuario desactivado.');
        $this->redirect('/users');
    }

    public function resetPassword(array $params = []): void
    {
        Auth::requireAdmin();
        $this->verifyCsrf();
        $id       = (int)($params['id'] ?? 0);
        $password = $_POST['password'] ?? '';
        if (strlen($password) < 8) { Session::setError('Mínimo 8 caracteres.'); $this->redirect('/users'); }
        $this->db->execute(
            'UPDATE users SET password=? WHERE id=?',
            [password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $id]
        );
        Session::setSuccess('Contraseña actualizada.');
        $this->redirect('/users');
    }
}
