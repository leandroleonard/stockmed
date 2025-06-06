<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SeederSuppliers extends Seeder
{
    public function run()
    {
        $suppliers = [
            [
                'company_name' => 'Distribuidora Farmacêutica ABC Ltda',
                'address' => 'Rua Industrial, 1000 - Distrito Industrial',
                'contact_person' => 'Pedro Almeida',
                'phone' => '244222222',
                'email' => 'vendas@distribuidoraabc.com',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'company_name' => 'MedSupply Distribuidora',
                'address' => 'Av. dos Medicamentos, 500 - Vila Farmacêutica',
                'contact_person' => 'Sandra Lima',
                'phone' => '244222224',
                'email' => 'compras@medsupply.com',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'company_name' => 'PharmaDistrib S.A.',
                'address' => 'Rodovia SP-100, Km 25 - Cotia',
                'contact_person' => 'Ricardo Souza',
                'phone' => '244222220',
                'email' => 'atendimento@pharmadistrib.com',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('suppliers')->insertBatch($suppliers);
    }
}
