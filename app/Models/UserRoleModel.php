<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'description',
        'permissions'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[50]|is_unique[user_roles.name,id,{id}]',
        'description' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'O nome do perfil é obrigatório',
            'is_unique' => 'Este nome de perfil já existe'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = ['decodePermissions'];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Decodifica as permissões JSON
     */
    protected function decodePermissions(array $data)
    {
        if (isset($data['data'])) {
            if (is_array($data['data'])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['permissions']) && is_string($row['permissions'])) {
                        $row['permissions'] = json_decode($row['permissions'], true);
                    }
                }
            } else {
                if (isset($data['data']['permissions']) && is_string($data['data']['permissions'])) {
                    $data['data']['permissions'] = json_decode($data['data']['permissions'], true);
                }
            }
        }
        return $data;
    }

    /**
     * Busca roles ativos
     */
    public function getActiveRoles()
    {
        return $this->findAll();
    }

    /**
     * Verifica se um role tem uma permissão específica
     */
    public function hasPermission($roleId, $permission)
    {
        $role = $this->find($roleId);
        if (!$role) return false;

        $permissions = is_string($role['permissions']) ? 
            json_decode($role['permissions'], true) : 
            $role['permissions'];

        return in_array('all', $permissions) || in_array($permission, $permissions);
    }
}