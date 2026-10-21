<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Models\VentaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pagos extends BaseController
{
    /** Registra un pago (total o parcial) sobre una venta. */
    public function crear(int $ventaId)
    {
        $venta = (new VentaModel())->conPagado($ventaId);
        if (! $venta) {
            throw PageNotFoundException::forPageNotFound('Venta no encontrada');
        }

        $destino = site_url('clientes/' . $venta['cliente_id']) . '#venta-' . $ventaId;

        $reglas = [
            'monto' => 'required|numeric|greater_than[0]',
            'fecha' => 'required|valid_date[Y-m-d]',
            'nota'  => 'permit_empty|max_length[255]',
        ];
        if (! $this->validate($reglas)) {
            return redirect()->to($destino)->with('errores', $this->validator->getErrors());
        }

        $saldo = round((float) $venta['total'] - (float) $venta['pagado'], 2);
        $monto = round(a_numero($this->request->getPost('monto')), 2);

        if ($monto > $saldo + 0.004) {
            return redirect()->to($destino)
                ->with('error', 'El pago supera lo que se debe de esta venta (' . dinero($saldo) . ').');
        }

        (new PagoModel())->insert([
            'venta_id' => $ventaId,
            'monto'    => $monto,
            'fecha'    => (string) $this->request->getPost('fecha'),
            'nota'     => trim((string) $this->request->getPost('nota')) ?: null,
        ]);

        return redirect()->to($destino)->with('exito', 'Pago de ' . dinero($monto) . ' registrado.');
    }

    public function eliminar(int $id)
    {
        $pago = (new PagoModel())->find($id);
        if (! $pago) {
            throw PageNotFoundException::forPageNotFound('Pago no encontrado');
        }

        $venta = (new VentaModel())->find($pago['venta_id']);
        (new PagoModel())->delete($id);

        return redirect()->to(site_url('clientes/' . $venta['cliente_id']) . '#venta-' . $venta['id'])
            ->with('exito', 'Pago eliminado.');
    }
}
