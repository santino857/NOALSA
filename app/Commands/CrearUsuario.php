<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * php spark usuario:crear
 * Crea el usuario de acceso (o cambia la contraseña si el usuario ya existe).
 */
class CrearUsuario extends BaseCommand
{
    protected $group       = 'Distribuidora';
    protected $name        = 'usuario:crear';
    protected $description = 'Crea el usuario de acceso o actualiza su contraseña.';
    protected $usage       = 'usuario:crear [usuario] [nombre] [contraseña]';

    public function run(array $params)
    {
        $username = $params[0] ?? CLI::prompt('Usuario');
        $nombre   = $params[1] ?? CLI::prompt('Nombre para mostrar', $username);
        $password = $params[2] ?? CLI::prompt('Contraseña (mínimo 8 caracteres)');

        if (strlen($password) < 8) {
            CLI::error('La contraseña debe tener al menos 8 caracteres.');

            return;
        }

        $model    = new UserModel();
        $existing = $model->where('username', $username)->first();
        $datos    = [
            'username' => $username,
            'name'     => $nombre,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'admin',
        ];

        if ($existing) {
            $model->update($existing['id'], $datos);
            CLI::write("Contraseña de '{$username}' actualizada.", 'green');
        } else {
            $model->insert($datos);
            CLI::write("Usuario '{$username}' creado.", 'green');
        }
    }
}
