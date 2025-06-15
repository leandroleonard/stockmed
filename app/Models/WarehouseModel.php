<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'warehouse_code',
        'name',
        'description',
        'address',
        'city',
        'postal_code',
        'manager_id',
        'capacity_limit',
        'temperature_controlled',
        'min_temperature',
        'max_temperature',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'warehouse_code' => 'permit_empty|is_unique[warehouses.warehouse_code,id,{id}]',
        'name' => 'required|max_length[100]',
        'manager_id' => 'permit_empty|integer',
        'capacity_limit' => 'permit_empty|integer',
        'min_temperature' => 'permit_empty|decimal',
        'max_temperature' => 'permit_empty|decimal'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'O nome do armazém é obrigatório'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Busca armazéns ativos
     */
    public function getActiveWarehouses()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Busca armazém com gerente
     */
    public function getWarehouseWithManager($warehouseCode)
    {
        return $this->select('warehouses.*, CONCAT(users.first_name, " ", users.last_name) as manager_name')
                    ->join('users', 'users.id = warehouses.manager_id', 'left')
                    ->where(['warehouse_code' => $warehouseCode])
                    ->first();
    }
}