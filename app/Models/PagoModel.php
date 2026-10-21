<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table         = 'pagos';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['venta_id', 'monto', 'fecha', 'nota'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $updatedField  = '';
}
