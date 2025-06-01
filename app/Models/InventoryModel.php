<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'inventory_number',
        'warehouse_id',
        'inventory_date',
        'status',
        'notes',
        'created_by',
        'completed_by',
        'completed_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    // Validation
    protected $validationRules = [
        'inventory_number' => 'required|is_unique[inventories.inventory_number,id,{id}]',
        'warehouse_id' => 'required|integer',
        'inventory_date' => 'required|valid_date',
        'status' => 'required|in_list[planejado,em_andamento,concluido,cancelado]',
        'created_by' => 'required|integer'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateInventoryNumber'];

    /**
     * Gera número do inventário automaticamente
     */
    protected function generateInventoryNumber(array $data)
    {
        if (empty($data['data']['inventory_number'])) {
            $lastInventory = $this->orderBy('id', 'DESC')->first();
            $nextId = $lastInventory ? $lastInventory['id'] + 1 : 1;
            $data['data']['inventory_number'] = 'INV' . date('Y') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Busca inventários com detalhes
     */
    public function getInventoriesWithDetails($status = null)
    {
        $builder = $this->select('inventories.*, warehouses.name as warehouse_name, CONCAT(users.first_name, " ", users.last_name) as created_by_name')
                        ->join('warehouses', 'warehouses.id = inventories.warehouse_id')
                        ->join('users', 'users.id = inventories.created_by');

        if ($status) {
            $builder->where('inventories.status', $status);
        }

        return $builder->orderBy('inventories.inventory_date', 'DESC')->findAll();
    }

    /**
     * Inicia inventário
     */
    public function startInventory($inventoryId, $userId)
    {
        $inventory = $this->find($inventoryId);
        if (!$inventory || $inventory['status'] !== 'planejado') {
            return false;
        }

        // Atualizar status
        $this->update($inventoryId, ['status' => 'em_andamento']);

        // Criar itens do inventário baseado no stock atual
        $this->createInventoryItems($inventoryId, $inventory['warehouse_id']);

        return true;
    }

    /**
     * Cria itens do inventário
     */
    private function createInventoryItems($inventoryId, $warehouseId)
    {
        $stockModel = new StockLevelModel();
        $itemModel = new InventoryItemModel();

        $stockLevels = $stockModel->where('warehouse_id', $warehouseId)->findAll();

        foreach ($stockLevels as $stock) {
            $itemModel->insert([
                'inventory_id' => $inventoryId,
                'product_id' => $stock['product_id'],
                'expected_quantity' => $stock['quantity_available']
            ]);
        }
    }

    /**
     * Completa inventário
     */
    public function completeInventory($inventoryId, $userId)
    {
        $inventory = $this->find($inventoryId);
        if (!$inventory || $inventory['status'] !== 'em_andamento') {
            return false;
        }

        // Processar ajustes de stock
        $this->processStockAdjustments($inventoryId, $inventory['warehouse_id'], $userId);

        // Atualizar status
        return $this->update($inventoryId, [
            'status' => 'concluido',
            'completed_by' => $userId,
            'completed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Processa ajustes de stock
     */
    private function processStockAdjustments($inventoryId, $warehouseId, $userId)
    {
        $itemModel = new InventoryItemModel();
        $movementModel = new StockMovementModel();

        $items = $itemModel->where('inventory_id', $inventoryId)
                          ->where('counted_quantity IS NOT NULL')
                          ->findAll();

        foreach ($items as $item) {
            $variance = $item['counted_quantity'] - $item['expected_quantity'];
            
            if ($variance != 0) {
                $movementData = [
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $warehouseId,
                    'movement_type' => $variance > 0 ? 'entrada' : 'saida',
                    'quantity' => abs($variance),
                    'reference_type' => 'ajuste',
                    'reference_id' => $inventoryId,
                    'notes' => 'Ajuste de inventário',
                    'user_id' => $userId
                ];

                $movementModel->recordMovement($movementData);
            }
        }
    }
}