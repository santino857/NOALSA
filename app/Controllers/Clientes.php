<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PagoModel;
use App\Models\VentaItemModel;
use App\Models\VentaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Clientes extends BaseController
{
    private array $reglas = [
        'nombre'    => 'required|min_length[2]|max_length[150]',
        'telefono'  => 'permit_empty|max_length[50]',
        'direccion' => 'permit_empty|max_length[200]',
        'notas'     => 'permit_empty|max_length[1000]',
    ];

    private function datos(): array
    {
        return [
            'nombre'    => trim((string) $this->request->getPost('nombre')),
            'telefono'  => trim((string) $this->request->getPost('telefono')) ?: null,
            'direccion' => trim((string) $this->request->getPost('direccion')) ?: null,
            'notas'     => trim((string) $this->request->getPost('notas')) ?: null,
        ];
    }

    private function buscar(int $id): array
    {
        $cliente = (new ClienteModel())->find($id);
        if (! $cliente) {
            throw PageNotFoundException::forPageNotFound('Cliente no encontrado');
        }

        return $cliente;
    }

    public function index(): string
    {
        $buscar    = trim((string) $this->request->getGet('q'));
        $soloDeuda = $this->request->getGet('deuda') === '1';
        $clientes  = (new ClienteModel())->conSaldo($buscar, $soloDeuda);

        $totalDeuda = 0.0;
        foreach ($clientes as $c) {
            $totalDeuda += max(0, (float) $c['saldo']);
        }

        return view('clientes/index', [
            'titulo'     => 'Clientes',
            'clientes'   => $clientes,
            'buscar'     => $buscar,
            'soloDeuda'  => $soloDeuda,
            'totalDeuda' => $totalDeuda,
        ]);
    }

    public function nuevo(): string
    {
        return view('clientes/form', ['titulo' => 'Nuevo cliente', 'cliente' => null]);
    }

    public function crear()
    {
        if (! $this->validate($this->reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $id = (new ClienteModel())->insert($this->datos());

        return redirect()->to(site_url('clientes/' . $id))->with('exito', 'Cliente creado.');
    }

    public function ver(int $id): string
    {
        $modelo  = new ClienteModel();
        $cliente = $this->buscar($id);
        $ventas  = (new VentaModel())->listado(['cliente_id' => $id], 500);

        $items = [];
        $pagos = [];
        if ($ventas) {
            $ids = array_column($ventas, 'id');
            foreach ((new VentaItemModel())->whereIn('venta_id', $ids)->orderBy('id')->findAll() as $it) {
                $items[$it['venta_id']][] = $it;
            }
            foreach ((new PagoModel())->whereIn('venta_id', $ids)->orderBy('fecha')->orderBy('id')->findAll() as $pg) {
                $pagos[$pg['venta_id']][] = $pg;
            }
        }

        return view('clientes/ver', [
            'titulo'  => $cliente['nombre'],
            'cliente' => $cliente,
            'resumen' => $modelo->resumen($id),
            'ventas'  => $ventas,
            'items'   => $items,
            'pagos'   => $pagos,
        ]);
    }

    public function editar(int $id): string
    {
        return view('clientes/form', ['titulo' => 'Editar cliente', 'cliente' => $this->buscar($id)]);
    }

    public function actualizar(int $id)
    {
        $this->buscar($id);

        if (! $this->validate($this->reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        (new ClienteModel())->update($id, $this->datos());

        return redirect()->to(site_url('clientes/' . $id))->with('exito', 'Datos actualizados.');
    }

    public function eliminar(int $id)
    {
        $this->buscar($id);

        $tieneVentas = (new VentaModel())->where('cliente_id', $id)->countAllResults() > 0;
        if ($tieneVentas) {
            return redirect()->to(site_url('clientes/' . $id))
                ->with('error', 'No se puede eliminar: el cliente tiene ventas registradas.');
        }

        (new ClienteModel())->delete($id);

        return redirect()->to(site_url('clientes'))->with('exito', 'Cliente eliminado.');
    }
}
