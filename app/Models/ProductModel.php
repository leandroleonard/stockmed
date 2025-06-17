<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_code',
        'barcode',
        'name',
        'generic_name',
        'description',
        'category_id',
        'manufacturer_id',
        'dosage',
        'form',
        'active_ingredient',
        'concentration',
        'pack_size',
        'unit_of_measure',
        'requires_prescription',
        'controlled_substance',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'storage_conditions',
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
        'category_id' => 'permit_empty|integer',
        'manufacturer_id' => 'permit_empty|integer',
        'pack_size' => 'permit_empty|integer',
        'min_stock_level' => 'permit_empty|integer',
        'max_stock_level' => 'permit_empty|integer',
        'reorder_point' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'product_code' => [
            'required' => 'O código do produto é obrigatório',
            'is_unique' => 'Este código de produto já existe'
        ],
        'name' => [
            'required' => 'O nome do produto é obrigatório'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateProductCode', 'generateBarcode'];

    /**
     * Gera código do produto automaticamente
     */
    protected function generateProductCode(array $data)
    {
        if (empty($data['data']['product_code'])) {
            $lastProduct = $this->orderBy('id', 'DESC')->first();
            $nextId = $lastProduct ? $lastProduct['id'] + 1 : 1;
            $data['data']['product_code'] = 'PROD' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    protected function generateBarcode(array $data)
    {
        if (empty($data['data']['barcode'])) {            
            $data['data']['barcode'] = rand(1111111111111, 9999999999999);
        }
        return $data;
    }

    /**
     * Busca produtos ativos
     */
    public function getActiveProducts()
    {
        return $this->select('products.*, product_categories.name as category_name, manufacturers.name as manufacturer_name')
                    ->join('product_categories', 'product_categories.id = products.category_id', 'left')
                    ->join('manufacturers', 'manufacturers.id = products.manufacturer_id', 'left')
                    ->where('products.is_active', true)
                    ->findAll();
    }

    /**
     * Busca produto com detalhes completos
     */
    public function getProductWithDetails($code)
    {
        return $this->select('products.*, product_categories.id as category_id, product_categories.name as category_name, manufacturers.id as manufacturer_id, manufacturers.name as manufacturer_name, product_batches.batch_number, product_batches.manufacture_date, product_batches.expiry_date, product_batches.quantity_received, product_batches.cost_price, product_batches.selling_price, suppliers.id as supplier_id, suppliers.company_name as supplier_name')
                    ->join('product_categories', 'product_categories.id = products.category_id', 'left')
                    ->join('product_batches', 'product_batches.product_id = products.id')
                    ->join('suppliers', 'product_batches.supplier_id = suppliers.id')
                    ->join('manufacturers', 'manufacturers.id = products.manufacturer_id', 'left')
                    ->where(['product_code' => $code])
                    ->first();
    }

    /**
     * Busca produtos por termo
     */
    public function searchProducts($term)
    {
        return $this->select('products.*, product_categories.name as category_name')
                    ->join('product_categories', 'product_categories.id = products.category_id', 'left')
                    ->like('products.product_code', $term)
                    ->orLike('products.name', $term)
                    ->orLike('products.generic_name', $term)
                    ->orLike('products.barcode', $term)
                    ->where('products.is_active', true)
                    ->findAll();
    }

    /**
     * Busca produtos com baixo stock
     */
    public function getLowStockProducts($warehouseId = null)
    {
        $builder = $this->db->table('products p')
                           ->select('p.*, sl.quantity_available, pc.name as category_name')
                           ->join('stock_levels sl', 'sl.product_id = p.id')
                           ->join('product_categories pc', 'pc.id = p.category_id', 'left')
                           ->where('p.is_active', true)
                           ->where('sl.quantity_available <=', 'p.reorder_point', false);

        if ($warehouseId) {
            $builder->where('sl.warehouse_id', $warehouseId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Busca produtos próximos ao vencimento
     */
    public function getExpiringProducts($days = 30, $warehouseId = null)
    {
        $builder = $this->db->table('products p')
                           ->select('p.*, pb.batch_number, pb.expiry_date, pb.quantity_remaining, w.name as warehouse_name')
                           ->join('product_batches pb', 'pb.product_id = p.id')
                           ->join('warehouses w', 'w.id = pb.warehouse_id')
                           ->where('p.is_active', true)
                           ->where('pb.quantity_remaining >', 0)
                           ->where('pb.expiry_date <=', date('Y-m-d', strtotime("+{$days} days")))
                           ->orderBy('pb.expiry_date', 'ASC');

        if ($warehouseId) {
            $builder->where('pb.warehouse_id', $warehouseId);
        }

        return $builder->get()->getResultArray();
    }
}