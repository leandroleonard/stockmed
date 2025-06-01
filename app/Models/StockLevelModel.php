<?php

namespace App\Models;

use CodeIgniter\Model;

class StockLevelModel extends Model
{
    protected $table = 'stock_levels';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_id',
        'warehouse_id',
        'quantity_available',
        'quantity_reserved',
        'quantity_on_order'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'quantity_available' => 'required|integer|greater_than_equal_to[0]',
        'quantity_reserved' => 'required|integer|greater_than_equal_to[0]',
        'quantity_on_order' => 'required|integer|greater_than_equal_to[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca níveis de stock com detalhes do produto
     */
    public function getStockLevelsWithDetails($warehouseId = null)
    {
        $builder = $this->select('stock_levels.*, products.name as product_name, products.product_code, products.min_stock_level, products.reorder_point, warehouses.name as warehouse_name')
                        ->join('products', 'products.id = stock_levels.product_id')
                        ->join('warehouses', 'warehouses.id = stock_levels.warehouse_id');

        if ($warehouseId) {
            $builder->where('stock_levels.warehouse_id', $warehouseId);
        }

        return $builder->findAll();
    }

    /**
     * Busca stock de um produto específico
     */
    public function getProductStock($productId, $warehouseId = null)
    {
        $builder = $this->where('product_id', $productId);

        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->findAll();
    }

    /**
     * Atualiza ou cria nível de stock
     */
    public function updateStockLevel($productId, $warehouseId, $quantityChange, $type = 'available')
    {
        $stock = $this->where('product_id', $productId)
                      ->where('warehouse_id', $warehouseId)
                      ->first();

        $field = 'quantity_' . $type;

        if ($stock) {
            $newQuantity = $stock[$field] + $quantityChange;
            if ($newQuantity < 0) return false;

            return $this->update($stock['id'], [$field => $newQuantity]);
        } else {
            $data = [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity_available' => $type === 'available' ? max(0, $quantityChange) : 0,
                'quantity_reserved' => $type === 'reserved' ? max(0, $quantityChange) : 0,
                'quantity_on_order' => $type === 'on_order' ? max(0, $quantityChange) : 0
            ];
            return $this->insert($data);
        }
    }

    /**
     * Busca produtos com baixo stock
     */
    public function getLowStockProducts($warehouseId = null)
    {
        $builder = $this->select('stock_levels.*, products.name as product_name, products.product_code, products.min_stock_level, products.reorder_point')
                        ->join('products', 'products.id = stock_levels.product_id')
                        ->where('stock_levels.quantity_available <=', 'products.reorder_point', false);

        if ($warehouseId) {
            $builder->where('stock_levels.warehouse_id', $warehouseId);
        }

        return $builder->findAll();
    }

    /**
     * Reserva quantidade para venda
     */
    public function reserveStock($productId, $warehouseId, $quantity)
    {
        $stock = $this->where('product_id', $productId)
                      ->where('warehouse_id', $warehouseId)
                      ->first();

        if (!$stock || $stock['quantity_available'] < $quantity) {
            return false;
        }

        return $this->update($stock['id'], [
            'quantity_available' => $stock['quantity_available'] - $quantity,
            'quantity_reserved' => $stock['quantity_reserved'] + $quantity
        ]);
    }

    /**
     * Confirma venda (remove da reserva)
     */
    public function confirmSale($productId, $warehouseId, $quantity)
    {
        $stock = $this->where('product_id', $productId)
                      ->where('warehouse_id', $warehouseId)
                      ->first();

        if (!$stock || $stock['quantity_reserved'] < $quantity) {
            return false;
        }

        return $this->update($stock['id'], [
            'quantity_reserved' => $stock['quantity_reserved'] - $quantity
        ]);
    }

    /**
     * Cancela reserva
     */
    public function cancelReservation($productId, $warehouseId, $quantity)
    {
        $stock = $this->where('product_id', $productId)
                      ->where('warehouse_id', $warehouseId)
                      ->first();

        if (!$stock || $stock['quantity_reserved'] < $quantity) {
            return false;
        }

        return $this->update($stock['id'], [
            'quantity_available' => $stock['quantity_available'] + $quantity,
            'quantity_reserved' => $stock['quantity_reserved'] - $quantity
        ]);
    }
}