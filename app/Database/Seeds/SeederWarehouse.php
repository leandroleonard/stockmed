<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SeederWarehouse extends Seeder
{
    public function run()
    {
        $warehouses = [
            [
                'name' => 'Depósito Principal',
                'address' => 'Rua do Depósito, 100 - Galpão A',
                'description' => 'Armazém principal para medicamentos gerais',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
                'city' => 'Luanda',
                'manager_id' => 1,
                'warehouse_code' => ''
            ],
            [
                'name' => 'Câmara Fria',
                'address' => 'Rua do Depósito, 100 - Galpão B',
                'description' => 'Armazém refrigerado para medicamentos termolábeis',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
                'manager_id' => 3,
                'warehouse_code' => ''
            ],
            [
                'name' => 'Loja - Balcão',
                'address' => 'Rua Principal, 50 - Loja',
                'description' => 'Estoque da área de vendas',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
                'city' => 'Luanda',
                'manager_id' => 2,
                'warehouse_code' => ''
            ]
        ];
        $this->db->table('warehouses')->insertBatch($warehouses);
    }
}
