<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SeederUser extends Seeder
{
    public function run()
    {

        try {
            $this->db->transBegin(); 
            $users = [
                [
                    'role_id' => 1,
                    'username' => 'admin',
                    'password_hash' => password_hash('12345', PASSWORD_DEFAULT),
                    'email' => 'admin@stockmed.com',
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'created_at' => Time::now(),
                    'updated_at' => Time::now()
                ],
                [
                    'role_id' => 2,
                    'username' => 'l.ventra',
                    'password_hash' => password_hash('12345', PASSWORD_DEFAULT),
                    'email' => 'leandro.ventura@stockmed.com',
                    'first_name' => 'Leandro',
                    'last_name' => 'Ventura',
                    'created_at' => Time::now(),
                    'updated_at' => Time::now()
                ],
                [
                    'role_id' => 2,
                    'username' => 'e.reepson',
                    'password_hash' => password_hash('12345', PASSWORD_DEFAULT),
                    'email' => 'etianete.reepson@stockmed.com',
                    'first_name' => 'Etianete',
                    'last_name' => 'Reepson',
                    'created_at' => Time::now(),
                    'updated_at' => Time::now()
                ],
            ];

            $builder = $this->db->table('users');
            $builder->insertBatch($users);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                $error = $this->db->error();
                echo "Erro ao inserir usuários: " . $error['message'] . "\n";
                echo "Última query: " . $this->db->getLastQuery() . "\n";
                return false;
            }

            $this->db->transCommit();
            echo "Tabela usuários populada com sucesso!\n";
            return true;
        } catch (DatabaseException $e) {
            echo "Exceção capturada: " . $e->getMessage() . "\n";
            return false;
        }
    }
}
