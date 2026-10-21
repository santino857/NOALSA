<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablas extends Migration
{
    private const ENGINE = ['ENGINE' => 'InnoDB'];

    private function id(): array
    {
        return ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true];
    }

    private function fk(bool $null = false): array
    {
        return ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => $null];
    }

    public function up()
    {
        // USUARIOS (acceso al sistema)
        $this->forge->addField([
            'id'         => $this->id(),
            'username'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'admin'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('users', true, self::ENGINE);

        // CLIENTES
        $this->forge->addField([
            'id'         => $this->id(),
            'nombre'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'telefono'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'direccion'  => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'notas'      => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nombre');
        $this->forge->createTable('clientes', true, self::ENGINE);

        // PRODUCTOS
        $this->forge->addField([
            'id'         => $this->id(),
            'nombre'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'precio'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'unidad'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kilo'],
            'activo'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('productos', true, self::ENGINE);

        // VENTAS
        $this->forge->addField([
            'id'         => $this->id(),
            'cliente_id' => $this->fk(),
            'fecha'      => ['type' => 'DATE'],
            'total'      => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'notas'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('fecha');
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('ventas', true, self::ENGINE);

        // ITEMS DE CADA VENTA (guarda nombre y precio al momento de vender)
        $this->forge->addField([
            'id'              => $this->id(),
            'venta_id'        => $this->fk(),
            'producto_id'     => $this->fk(true),
            'producto_nombre' => ['type' => 'VARCHAR', 'constraint' => 150],
            'unidad'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'kilo'],
            'cantidad'        => ['type' => 'DECIMAL', 'constraint' => '10,3'],
            'precio_unitario' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'subtotal'        => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('venta_id', 'ventas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('producto_id', 'productos', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('venta_items', true, self::ENGINE);

        // PAGOS (siempre asociados a una venta)
        $this->forge->addField([
            'id'         => $this->id(),
            'venta_id'   => $this->fk(),
            'monto'      => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'fecha'      => ['type' => 'DATE'],
            'nota'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('venta_id', 'ventas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pagos', true, self::ENGINE);

        // SESIONES (en Vercel no hay disco persistente, se guardan en MySQL)
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `ci_sessions` (
                `id` VARCHAR(128) NOT NULL,
                `ip_address` VARCHAR(45) NOT NULL,
                `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                `data` BLOB NOT NULL,
                PRIMARY KEY (`id`),
                KEY `ci_sessions_timestamp` (`timestamp`)
            ) ENGINE=InnoDB'
        );
    }

    public function down()
    {
        $this->forge->dropTable('ci_sessions', true);
        $this->forge->dropTable('pagos', true);
        $this->forge->dropTable('venta_items', true);
        $this->forge->dropTable('ventas', true);
        $this->forge->dropTable('productos', true);
        $this->forge->dropTable('clientes', true);
        $this->forge->dropTable('users', true);
    }
}
