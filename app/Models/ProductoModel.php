<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table         = 'productos';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'precio', 'unidad', 'activo'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
}
