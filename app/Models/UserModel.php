<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'role_id',
        'is_active',
        'last_login'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password_hash' => 'required|min_length[8]',
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'role_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'O nome de usuário é obrigatório',
            'is_unique' => 'Este nome de usuário já existe'
        ],
        'email' => [
            'required' => 'O email é obrigatório',
            'valid_email' => 'Digite um email válido',
            'is_unique' => 'Este email já está cadastrado'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash da senha antes de inserir/atualizar
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password_hash'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Busca usuário com role
     */
    public function getUserWithRole($id)
    {
        return $this->select('users.*, user_roles.name as role_name, user_roles.permissions')
                    ->join('user_roles', 'user_roles.id = users.role_id')
                    ->find($id);
    }

    /**
     * Busca usuários ativos
     */
    public function getActiveUsers()
    {
        return $this->select('users.*, user_roles.name as role_name')
                    ->join('user_roles', 'user_roles.id = users.role_id')
                    ->where('users.is_active', true)
                    ->findAll();
    }

    /**
     * Atualiza último login
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Verifica credenciais
     */
    public function verifyCredentials($username, $password)
    {
        $user = $this->where('username', $username)
                     ->orWhere('email', $username)
                     ->where('is_active', true)
                     ->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }
}