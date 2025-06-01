<?php

namespace App\Models;

use CodeIgniter\Model;

class ManufacturerModel extends Model
{
    protected $table = 'manufacturers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'country',
        'license_number',
        'contact_info',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[200]',
        'country' => 'permit_empty|max_length[100]',
        'license_number' => 'permit_empty|max_length[100]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'O nome do fabricante é obrigatório'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $afterFind = ['decodeContactInfo'];

    /**
     * Decodifica informações de contato JSON
     */
    protected function decodeContactInfo(array $data)
    {
        if (isset($data['data'])) {
            if (is_array($data['data'])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['contact_info']) && is_string($row['contact_info'])) {
                        $row['contact_info'] = json_decode($row['contact_info'], true);
                    }
                }
            } else {
                if (isset($data['data']['contact_info']) && is_string($data['data']['contact_info'])) {
                    $data['data']['contact_info'] = json_decode($data['data']['contact_info'], true);
                }
            }
        }
        return $data;
    }

    /**
     * Busca fabricantes ativos
     */
    public function getActiveManufacturers()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Busca fabricante por nome
     */
    public function searchManufacturers($term)
    {
        return $this->like('name', $term)
                    ->orLike('country', $term)
                    ->where('is_active', true)
                    ->findAll();
    }
}