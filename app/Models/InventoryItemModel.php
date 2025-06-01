<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryItemModel extends Model
{
    protected $table = 'inventory_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'inventory_id',
        'product_id',
        'batch_id',
        'expected_quantity',
        'counted_quantity',
        'notes',
        'counted_by',
        'counted_at'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'inventory_id' => 'required|integer',
        'product_id' => 'required|integer',
        'expected_quantity' => 'required|integer|greater_than_equal_to[0]',
        'counted_quantity' => 'permit_empty|integer|greater_than_equal_to[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca itens do inventário com detalhes
     */
    public function getInventoryItems($inventoryId)
    {
        return $this->select('inventory_items.*, products.name as product_name, products.product_code')
                    ->join('products', 'products.id = inventory_items.product_id')
                    ->where('inventory_items.inventory_id', $inventoryId)
                    ->findAll();
    }

    /**
     * Atualiza contagem
     */
    public function updateCount($itemId, $countedQuantity, $userId, $notes = null)
    {
        return $this->update($itemId, [
            'counted_quantity' => $countedQuantity,
            'counted_by' => $userId,
            'counted_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
    }

    /**
     * Busca itens com variação
     */
    public function getItemsWithVariance($inventoryId)
    {
        return $this->select('inventory_items.*, products.name as product_name, products.product_code, (inventory_items.counted_quantity - inventory_items.expected_quantity) as variance')
                    ->join('products', 'products.id = inventory_items.product_id')
                    ->where('inventory_items.inventory_id', $inventoryId)
                    ->where('inventory_items.counted_quantity IS NOT NULL')
                    ->having('variance !=', 0)
                    ->findAll();
    }
}