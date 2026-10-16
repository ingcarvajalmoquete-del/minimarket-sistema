<?php
/**
 * Auth Controller - Login / Logout
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

class AuthController extends Controller
{
    public function showLogin(array $params = []): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->render('auth.login', ['layout' => 'auth', 'title' => 'Iniciar Sesión']);
    }

    public function login(array $params = []): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if (!$this->isPost()) {
            $this->redirect('/login');
        }

        $this->verifyCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        if (empty($email)) $errors['email'] = 'El correo es obligatorio.';
        if (empty($password)) $errors['password'] = 'La contraseña es obligatoria.';

        if (!empty($errors)) {
            Session::setErrors($errors);
            Session::flash('old', ['email' => $email]);
            $this->redirect('/login');
        }

        $db   = Database::getInstance();
        $user = $db->queryOne(
            'SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1',
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setError('Credenciales incorrectas. Verifique su correo y contraseña.');
            Session::flash('old', ['email' => $email]);
            $this->redirect('/login');
        }

        Auth::login($user);
        Session::setSuccess('Bienvenido, ' . $user['name'] . '!');
        $this->redirect('/dashboard');
    }

    public function logout(array $params = []): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
