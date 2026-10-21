<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Carga la lista de precios inicial (precios por kilo). Solo si la tabla está vacía.
 */
class ProductosSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('productos')->countAllResults() > 0) {
            return;
        }

        $lista = [
            ['Cremoso Noal', 6900],
            ['Cremoso Makis', 6600],
            ['Barra Noal', 8900],
            ['Barra Makis', 8600],
            ['Fimbo de primera', 14900],
            ['Fimbo de segunda', 9900],
            ['Roquefort', 12500],
            ['Muzzarela de primera', 8500],
            ['Sardo', 8200],
            ['Regianito Noal', 15900],
        ];

        $ahora = date('Y-m-d H:i:s');
        foreach ($lista as [$nombre, $precio]) {
            $this->db->table('productos')->insert([
                'nombre'     => $nombre,
                'precio'     => $precio,
                'unidad'     => 'kilo',
                'activo'     => 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }
}
