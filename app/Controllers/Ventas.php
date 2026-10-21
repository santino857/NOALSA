<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\ProductoModel;
use App\Models\VentaItemModel;
use App\Models\VentaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Ventas extends BaseController
{
    public function index(): string
    {
        $estado = (string) $this->request->getGet('estado');
        if (! in_array($estado, ['deuda', 'pagadas'], true)) {
            $estado = '';
        }

        return view('ventas/index', [
            'titulo' => 'Ventas',
            'ventas' => (new VentaModel())->listado(['estado' => $estado], 200),
            'estado' => $estado,
        ]);
    }

    public function nueva(): string
    {
        $productos = (new ProductoModel())->where('activo', 1)->orderBy('nombre')->findAll();

        return view('ventas/nueva', [
            'titulo'     => 'Nueva venta',
            'clientes'   => (new ClienteModel())->orderBy('nombre')->findAll(),
            'productos'  => $productos,
            'clienteId'  => (int) $this->request->getGet('cliente'),
        ]);
    }

    public function crear()
    {
        $volver = static fn (array $errores) => redirect()->back()->withInput()->with('errores', $errores);

        $reglas = [
            'cliente_id'   => 'required|is_natural_no_zero',
            'fecha'        => 'required|valid_date[Y-m-d]',
            'pago_inicial' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'notas'        => 'permit_empty|max_length[255]',
        ];
        if (! $this->validate($reglas)) {
            return $volver($this->validator->getErrors());
        }

        $clienteId = (int) $this->request->getPost('cliente_id');
        if (! (new ClienteModel())->find($clienteId)) {
            return $volver(['cliente' => 'El cliente elegido no existe.']);
        }

        // Armar los renglones de la venta
        $pids    = (array) $this->request->getPost('producto_id');
        $cants   = (array) $this->request->getPost('cantidad');
        $precios = (array) $this->request->getPost('precio');

        $productos = new ProductoModel();
        $items     = [];
        $total     = 0.0;

        foreach ($pids as $i => $pid) {
            $pid = (int) $pid;
            if ($pid === 0) {
                continue; // renglón vacío
            }

            $cant   = round(a_numero($cants[$i] ?? 0), 3);
            $precio = round(a_numero($precios[$i] ?? 0), 2);
            $prod   = $productos->find($pid);

            if (! $prod || ! $prod['activo']) {
                return $volver(['producto' => 'Uno de los productos no está disponible.']);
            }
            if ($cant <= 0 || $precio < 0) {
                return $volver(['cantidad' => 'Revisá las cantidades y los precios cargados.']);
            }

            $subtotal = round($cant * $precio, 2);
            $total   += $subtotal;
            $items[]  = [
                'producto_id'     => $pid,
                'producto_nombre' => $prod['nombre'],
                'unidad'          => $prod['unidad'],
                'cantidad'        => $cant,
                'precio_unitario' => $precio,
                'subtotal'        => $subtotal,
            ];
        }

        if (! $items) {
            return $volver(['items' => 'Agregá al menos un producto a la venta.']);
        }

        $total = round($total, 2);
        $pago  = round(a_numero($this->request->getPost('pago_inicial')), 2);
        if ($pago > $total + 0.004) {
            return $volver(['pago_inicial' => 'El pago no puede ser mayor al total de la venta (' . dinero($total) . ').']);
        }

        $fecha = (string) $this->request->getPost('fecha');
        $db    = db_connect();
        $db->transStart();

        $ventaModel = new VentaModel();
        $ventaId    = $ventaModel->insert([
            'cliente_id' => $clienteId,
            'fecha'      => $fecha,
            'total'      => $total,
            'notas'      => trim((string) $this->request->getPost('notas')) ?: null,
        ]);

        foreach ($items as &$it) {
            $it['venta_id'] = $ventaId;
        }
        unset($it);
        (new VentaItemModel())->insertBatch($items);

        if ($pago > 0) {
            (new PagoModel())->insert([
                'venta_id' => $ventaId,
                'monto'    => $pago,
                'fecha'    => $fecha,
                'nota'     => 'Pago al momento de la venta',
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return $volver(['general' => 'No se pudo guardar la venta. Probá de nuevo.']);
        }

        return redirect()->to(site_url('clientes/' . $clienteId) . '#venta-' . $ventaId)
            ->with('exito', 'Venta registrada por ' . dinero($total) . '.');
    }

    public function eliminar(int $id)
    {
        $venta = (new VentaModel())->find($id);
        if (! $venta) {
            throw PageNotFoundException::forPageNotFound('Venta no encontrada');
        }

        // Los renglones y pagos de la venta se borran en cascada
        (new VentaModel())->delete($id);

        return redirect()->to(site_url('clientes/' . $venta['cliente_id']))
            ->with('exito', 'Venta eliminada.');
    }
}
