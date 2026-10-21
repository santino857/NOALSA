<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    // 1. FORMULARIO DE LOGIN
    public function login()
    {
        // Si ya hay sesión activa, va directo al inicio
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'));
        }

        return view('auth/login');
    }

    // 2. PROCESO DE LOGIN
    public function processLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $user = $username !== '' ? (new UserModel())->where('username', $username)->first() : null;

        if ($user && password_verify($password, $user['password'])) {
            // Nuevo ID de sesión al loguearse (evita fijación de sesión)
            session()->regenerate();
            session()->set([
                'id'         => $user['id'],
                'name'       => $user['name'],
                'username'   => $user['username'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ]);

            return redirect()->to(site_url('/'));
        }

        return redirect()->to(site_url('login'))
            ->with('error', 'Usuario o contraseña incorrectos.')
            ->with('ultimo_usuario', $username);
    }

    // 3. CIERRE DE SESIÓN
    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }
}
