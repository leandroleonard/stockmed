<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'customer_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'birth_date',
        'tax_number',
        'insurance_number',
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
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'email' => 'permit_empty|valid_email',
        'phone' => 'permit_empty|max_length[20]',
        'customer_code' => 'permit_empty|is_unique[customers.customer_code,id,{id}]'
    ];

    protected $validationMessages = [
        'first_name' => [
            'required' => 'O primeiro nome é obrigatório'
        ],
        'last_name' => [
            'required' => 'O último nome é obrigatório'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;


    /**
     * Busca clientes ativos
     */
    public function getActiveCustomers()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Busca cliente por código ou nome
     */
    public function searchCustomers($term)
    {
        return $this->like('customer_code', $term)
                    ->orLike('first_name', $term)
                    ->orLike('last_name', $term)
                    ->orLike('email', $term)
                    ->orLike('phone', $term)
                    ->where('is_active', true)
                    ->findAll();
    }

    /**
     * Busca histórico de compras do cliente
     */
    public function getCustomerPurchaseHistory($customerId)
    {
        $db = \Config\Database::connect();
        return $db->table('sales')
                  ->select('sales.*, COUNT(sale_items.id) as total_items, SUM(sale_items.quantity) as total_quantity')
                  ->join('sale_items', 'sale_items.sale_id = sales.id')
                  ->where('sales.customer_id', $customerId)
                  ->groupBy('sales.id')
                  ->orderBy('sales.sale_date', 'DESC')
                  ->get()
                  ->getResultArray();
    }
}