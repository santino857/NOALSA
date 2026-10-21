<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaItemModel extends Model
{
    protected $table         = 'venta_items';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['venta_id', 'producto_id', 'producto_nombre', 'unidad', 'cantidad', 'precio_unitario', 'subtotal'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;
}
