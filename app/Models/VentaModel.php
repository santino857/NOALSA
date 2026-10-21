<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table         = 'ventas';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['cliente_id', 'fecha', 'total', 'notas'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    /**
     * Ventas con lo pagado hasta el momento.
     * Filtros: cliente_id, estado ('deuda' | 'pagadas').
     */
    public function listado(array $filtros = [], int $limite = 200): array
    {
        $where = [];
        $binds = [];

        if (! empty($filtros['cliente_id'])) {
            $where[] = 'v.cliente_id = ?';
            $binds[] = (int) $filtros['cliente_id'];
        }

        $having = '';
        if (($filtros['estado'] ?? '') === 'deuda') {
            $having = 'HAVING (v.total - COALESCE(SUM(p.monto), 0)) > 0.004';
        } elseif (($filtros['estado'] ?? '') === 'pagadas') {
            $having = 'HAVING (v.total - COALESCE(SUM(p.monto), 0)) <= 0.004';
        }

        $sql = 'SELECT v.*, c.nombre AS cliente_nombre, COALESCE(SUM(p.monto), 0) AS pagado
                FROM ventas v
                JOIN clientes c ON c.id = v.cliente_id
                LEFT JOIN pagos p ON p.venta_id = v.id '
             . ($where ? 'WHERE ' . implode(' AND ', $where) : '')
             . " GROUP BY v.id, c.nombre {$having}
                ORDER BY v.fecha DESC, v.id DESC
                LIMIT " . (int) $limite;

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function conPagado(int $id): ?array
    {
        return $this->db->query(
            'SELECT v.*, COALESCE(SUM(p.monto), 0) AS pagado
               FROM ventas v LEFT JOIN pagos p ON p.venta_id = v.id
              WHERE v.id = ? GROUP BY v.id',
            [$id]
        )->getRowArray();
    }
}
