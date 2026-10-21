<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Productos extends BaseController
{
    private array $reglas = [
        'nombre' => 'required|min_length[2]|max_length[150]',
        'precio' => 'required|numeric|greater_than_equal_to[0]',
        'unidad' => 'required|in_list[kilo,unidad]',
    ];

    private function buscar(int $id): array
    {
        $producto = (new ProductoModel())->find($id);
        if (! $producto) {
            throw PageNotFoundException::forPageNotFound('Producto no encontrado');
        }

        return $producto;
    }

    private function datos(): array
    {
        return [
            'nombre' => trim((string) $this->request->getPost('nombre')),
            'precio' => round(a_numero($this->request->getPost('precio')), 2),
            'unidad' => (string) $this->request->getPost('unidad'),
            'activo' => $this->request->getPost('activo') ? 1 : 0,
        ];
    }

    public function index(): string
    {
        return view('productos/index', [
            'titulo'    => 'Productos y precios',
            'productos' => (new ProductoModel())->orderBy('activo', 'DESC')->orderBy('nombre')->findAll(),
        ]);
    }

    public function nuevo(): string
    {
        return view('productos/form', ['titulo' => 'Nuevo producto', 'producto' => null]);
    }

    public function crear()
    {
        if (! $this->validate($this->reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        (new ProductoModel())->insert($this->datos());

        return redirect()->to(site_url('productos'))->with('exito', 'Producto agregado.');
    }

    public function editar(int $id): string
    {
        return view('productos/form', ['titulo' => 'Editar producto', 'producto' => $this->buscar($id)]);
    }

    public function actualizar(int $id)
    {
        $this->buscar($id);

        if (! $this->validate($this->reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        (new ProductoModel())->update($id, $this->datos());

        return redirect()->to(site_url('productos'))->with('exito', 'Producto actualizado.');
    }

    /** Pausa o reactiva un producto (no aparece al cargar ventas mientras está pausado). */
    public function alternar(int $id)
    {
        $p = $this->buscar($id);
        (new ProductoModel())->update($id, ['activo' => $p['activo'] ? 0 : 1]);

        return redirect()->to(site_url('productos'));
    }

    /** Las ventas anteriores conservan el nombre y el precio con que se vendió. */
    public function eliminar(int $id)
    {
        $this->buscar($id);
        (new ProductoModel())->delete($id);

        return redirect()->to(site_url('productos'))->with('exito', 'Producto eliminado.');
    }
}
