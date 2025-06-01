<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'supplier_code',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'tax_number',
        'payment_terms',
        'credit_limit',
        'notes',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_name' => 'required|max_length[200]',
        'email' => 'permit_empty|valid_email',
        'phone' => 'permit_empty|max_length[20]',
        'supplier_code' => 'permit_empty|is_unique[suppliers.supplier_code,id,{id}]',
        'credit_limit' => 'permit_empty|decimal'
    ];

    protected $validationMessages = [
        'company_name' => [
            'required' => 'O nome da empresa é obrigatório'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateSupplierCode'];

    /**
     * Gera código do fornecedor automaticamente
     */
    protected function generateSupplierCode(array $data)
    {
        if (empty($data['data']['supplier_code'])) {
            $lastSupplier = $this->orderBy('id', 'DESC')->first();
            $nextId = $lastSupplier ? $lastSupplier['id'] + 1 : 1;
            $data['data']['supplier_code'] = 'FOR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Busca fornecedores ativos
     */
    public function getActiveSuppliers()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Busca fornecedor por código ou nome
     */
    public function searchSuppliers($term)
    {
        return $this->like('supplier_code', $term)
                    ->orLike('company_name', $term)
                    ->orLike('contact_person', $term)
                    ->where('is_active', true)
                    ->findAll();
    }
}