<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\VentaModel;

class Home extends BaseController
{
    public function index(): string
    {
        $mes = date('Y-m-01');

        $t = db_connect()->query(
            'SELECT (SELECT COALESCE(SUM(total), 0) FROM ventas) AS vendido,
                    (SELECT COALESCE(SUM(monto), 0) FROM pagos) AS cobrado,
                    (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE fecha >= ?) AS vendido_mes,
                    (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE fecha >= ?) AS cobrado_mes',
            [$mes, $mes]
        )->getRowArray();

        $deudores = (new ClienteModel())->conSaldo('', true);
        usort($deudores, static fn ($a, $b) => (float) $b['saldo'] <=> (float) $a['saldo']);

        return view('home', [
            'titulo'          => 'Inicio',
            'deuda_total'     => round((float) $t['vendido'] - (float) $t['cobrado'], 2),
            'vendido_mes'     => $t['vendido_mes'],
            'cobrado_mes'     => $t['cobrado_mes'],
            'cant_deudores'   => count($deudores),
            'top_deudores'    => array_slice($deudores, 0, 5),
            'ultimas_ventas'  => (new VentaModel())->listado([], 8),
        ]);
    }
}
