<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductBatchModel extends Model
{
    protected $table = 'product_batches';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'batch_number',
        'product_id',
        'supplier_id',
        'manufacture_date',
        'expiry_date',
        'quantity_received',
        'quantity_remaining',
        'cost_price',
        'selling_price',
        'warehouse_id',
        'location_in_warehouse',
        'quality_status',
        'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'batch_number' => 'required|max_length[100]',
        'product_id' => 'required|integer',
        'supplier_id' => 'required|integer',
        'expiry_date' => 'required|valid_date',
        'quantity_received' => 'required|integer|greater_than[0]',
        'quantity_remaining' => 'required|integer|greater_than_equal_to[0]',
        'cost_price' => 'required|decimal|greater_than[0]',
        'selling_price' => 'required|decimal|greater_than[0]',
        'warehouse_id' => 'required|integer',
        'quality_status' => 'required|in_list[approved,pending,rejected]'
    ];

    protected $validationMessages = [
        'batch_number' => [
            'required' => 'O número do lote é obrigatório'
        ],
        'expiry_date' => [
            'required' => 'A data de validade é obrigatória'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca lotes com detalhes
     */
    public function getBatchesWithDetails($productId = null, $warehouseId = null)
    {
        $builder = $this->select('product_batches.*, products.name as product_name, products.product_code, suppliers.company_name as supplier_name, warehouses.name as warehouse_name')
                        ->join('products', 'products.id = product_batches.product_id')
                        ->join('suppliers', 'suppliers.id = product_batches.supplier_id')
                        ->join('warehouses', 'warehouses.id = product_batches.warehouse_id');

        if ($productId) {
            $builder->where('product_batches.product_id', $productId);
        }

        if ($warehouseId) {
            $builder->where('product_batches.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('product_batches.expiry_date', 'ASC')->findAll();
    }

    /**
     * Busca lotes disponíveis para venda
     */
    public function getAvailableBatches($productId, $warehouseId)
    {
        return $this->where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('quantity_remaining >', 0)
                    ->where('quality_status', 'approved')
                    ->where('expiry_date >', date('Y-m-d'))
                    ->orderBy('expiry_date', 'ASC')
                    ->findAll();
    }

    /**
     * Busca lotes próximos ao vencimento
     */
    public function getExpiringBatches($days = 30, $warehouseId = null)
    {
        $builder = $this->select('product_batches.*, products.name as product_name, products.product_code')
                        ->join('products', 'products.id = product_batches.product_id')
                        ->where('product_batches.quantity_remaining >', 0)
                        ->where('product_batches.expiry_date <=', date('Y-m-d', strtotime("+{$days} days")))
                        ->where('product_batches.expiry_date >', date('Y-m-d'))
                        ->orderBy('product_batches.expiry_date', 'ASC');

        if ($warehouseId) {
            $builder->where('product_batches.warehouse_id', $warehouseId);
        }

        return $builder->findAll();
    }

    /**
     * Atualiza quantidade do lote
     */
    public function updateQuantity($batchId, $quantityChange, $operation = 'subtract')
    {
        $batch = $this->find($batchId);
        if (!$batch) return false;

        $newQuantity = $operation === 'add' ? 
            $batch['quantity_remaining'] + $quantityChange : 
            $batch['quantity_remaining'] - $quantityChange;

        if ($newQuantity < 0) return false;

        return $this->update($batchId, ['quantity_remaining' => $newQuantity]);
    }

    /**
     * Busca lotes vencidos
     */
    public function getExpiredBatches($warehouseId = null)
    {
        $builder = $this->select('product_batches.*, products.name as product_name, products.product_code')
                        ->join('products', 'products.id = product_batches.product_id')
                        ->where('product_batches.quantity_remaining >', 0)
                        ->where('product_batches.expiry_date <', date('Y-m-d'));

        if ($warehouseId) {
            $builder->where('product_batches.warehouse_id', $warehouseId);
        }

        return $builder->findAll();
    }
}