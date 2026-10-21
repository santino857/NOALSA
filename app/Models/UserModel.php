<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['username', 'name', 'password', 'role'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
}
