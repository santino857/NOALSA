<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'clientes';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'telefono', 'direccion', 'notas'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    /**
     * Clientes con total vendido, total pagado y saldo (lo que deben).
     */
    public function conSaldo(string $buscar = '', bool $soloDeuda = false): array
    {
        $sql = "SELECT c.*,
                       COALESCE(v.total, 0) AS total_vendido,
                       COALESCE(p.total, 0) AS total_pagado,
                       (COALESCE(v.total, 0) - COALESCE(p.total, 0)) AS saldo
                FROM clientes c
                LEFT JOIN (SELECT cliente_id, SUM(total) AS total FROM ventas GROUP BY cliente_id) v
                       ON v.cliente_id = c.id
                LEFT JOIN (SELECT ve.cliente_id, SUM(pa.monto) AS total
                           FROM pagos pa JOIN ventas ve ON ve.id = pa.venta_id
                           GROUP BY ve.cliente_id) p
                       ON p.cliente_id = c.id";

        $where = [];
        $binds = [];

        if ($buscar !== '') {
            $where[] = '(c.nombre LIKE ? OR c.telefono LIKE ?)';
            $binds[] = '%' . $buscar . '%';
            $binds[] = '%' . $buscar . '%';
        }
        if ($soloDeuda) {
            $where[] = '(COALESCE(v.total, 0) - COALESCE(p.total, 0)) > 0.004';
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY c.nombre ASC';

        return $this->db->query($sql, $binds)->getResultArray();
    }

    /**
     * Resumen de cuenta de un cliente.
     */
    public function resumen(int $id): array
    {
        $r = $this->db->query(
            "SELECT COALESCE(SUM(v.total), 0) AS total_vendido,
                    (SELECT COALESCE(SUM(p.monto), 0)
                       FROM pagos p JOIN ventas v2 ON v2.id = p.venta_id
                      WHERE v2.cliente_id = ?) AS total_pagado
               FROM ventas v WHERE v.cliente_id = ?",
            [$id, $id]
        )->getRowArray();

        $r['saldo'] = round((float) $r['total_vendido'] - (float) $r['total_pagado'], 2);

        return $r;
    }
}
