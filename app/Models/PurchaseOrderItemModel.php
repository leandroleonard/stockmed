<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderItemModel extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'purchase_order_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'notes'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'purchase_order_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity_ordered' => 'required|integer|greater_than[0]',
        'quantity_received' => 'permit_empty|integer|greater_than_equal_to[0]',
        'unit_cost' => 'required|decimal|greater_than[0]',
        'total_cost' => 'required|decimal|greater_than[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca itens do pedido com detalhes do produto
     */
    public function getOrderItems($orderId)
    {
        return $this->select('purchase_order_items.*, products.name as product_name, products.product_code')
                    ->join('products', 'products.id = purchase_order_items.product_id')
                    ->where('purchase_order_items.purchase_order_id', $orderId)
                    ->findAll();
    }

    /**
     * Atualiza quantidade recebida
     */
    public function updateReceivedQuantity($itemId, $quantityReceived)
    {
        return $this->update($itemId, ['quantity_received' => $quantityReceived]);
    }

    /**
     * Verifica se todos os itens foram recebidos
     */
    public function isOrderFullyReceived($orderId)
    {
        $items = $this->where('purchase_order_id', $orderId)->findAll();
        
        foreach ($items as $item) {
            if ($item['quantity_received'] < $item['quantity_ordered']) {
                return false;
            }
        }
        
        return true;
    }
}