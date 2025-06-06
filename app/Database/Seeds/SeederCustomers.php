<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SeederCustomers extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'first_name' => 'Ana Paula',
                'last_name' => 'Costa',
                'address' => 'Morro Bento, 123 - Centro',
                'phone' => '987653324',
                'email' => 'ana.costa@email.com',
                'birth_date' => '1985-03-15',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
                'city' => 'Luanda',
                'postal_code' => '00000'
            ],
            [
                'first_name' => 'Roberto',
                'last_name' => 'Ferreira',
                'address' => 'Av. Fidel De Castro, 456 - Bela Vista',
                'phone' => '987654322',
                'email' => 'roberto.ferreira@email.com',
                'birth_date' => '1978-07-22',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
                'city' => 'Luanda',
                'postal_code' => '00000'
            ],
            [
                'first_name' => 'Lucia',
                'last_name' => 'Mendes',
                'address' => 'Rua Augusto, 789',
                'phone' => '987654321',
                'email' => 'lucia.mendes@email.com',
                'birth_date' => '1992-11-08',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
                'city' => 'Luanda',
                'postal_code' => '00000'
            ]
        ];
        $this->db->table('customers')->insertBatch($customers);
    }
}
