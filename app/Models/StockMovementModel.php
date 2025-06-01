<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_id',
        'batch_id',
        'warehouse_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'cost_price',
        'selling_price',
        'notes',
        'user_id',
        'movement_date'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'movement_type' => 'required|in_list[entrada,saida,transferencia,ajuste,vencimento]',
        'quantity' => 'required|integer|greater_than[0]',
        'reference_type' => 'required|in_list[compra,venda,transferencia,ajuste,devolucao,vencimento]',
        'user_id' => 'required|integer'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Registra movimentação de stock
     */
    public function recordMovement($data)
    {
        $data['movement_date'] = $data['movement_date'] ?? date('Y-m-d H:i:s');
        
        $db = \Config\Database::connect();
        $db->transStart();

        // Inserir movimentação
        $movementId = $this->insert($data);

        if ($movementId) {
            // Atualizar nível de stock
            $stockModel = new StockLevelModel();
            $quantityChange = $data['movement_type'] === 'entrada' ? $data['quantity'] : -$data['quantity'];
            
            $stockModel->updateStockLevel($data['product_id'], $data['warehouse_id'], $quantityChange);

            // Atualizar quantidade do lote se especificado
            if (isset($data['batch_id']) && $data['batch_id']) {
                $batchModel = new ProductBatchModel();
                $operation = $data['movement_type'] === 'entrada' ? 'add' : 'subtract';
                $batchModel->updateQuantity($data['batch_id'], $data['quantity'], $operation);
            }
        }

        $db->transComplete();
        return $db->transStatus() ? $movementId : false;
    }

    /**
     * Busca movimentações com detalhes
     */
    public function getMovementsWithDetails($filters = [])
    {
        $builder = $this->select('stock_movements.*, products.name as product_name, products.product_code, warehouses.name as warehouse_name, CONCAT(users.first_name, " ", users.last_name) as user_name')
                        ->join('products', 'products.id = stock_movements.product_id')
                        ->join('warehouses', 'warehouses.id = stock_movements.warehouse_id')
                        ->join('users', 'users.id = stock_movements.user_id');

        // Aplicar filtros
        if (isset($filters['product_id'])) {
            $builder->where('stock_movements.product_id', $filters['product_id']);
        }

        if (isset($filters['warehouse_id'])) {
            $builder->where('stock_movements.warehouse_id', $filters['warehouse_id']);
        }

        if (isset($filters['movement_type'])) {
            $builder->where('stock_movements.movement_type', $filters['movement_type']);
        }

        if (isset($filters['date_from'])) {
            $builder->where('stock_movements.movement_date >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $builder->where('stock_movements.movement_date <=', $filters['date_to']);
        }

        return $builder->orderBy('stock_movements.movement_date', 'DESC')->findAll();
    }

    /**
     * Busca movimentações por período
     */
    public function getMovementsByPeriod($startDate, $endDate, $warehouseId = null)
    {
        $builder = $this->select('stock_movements.*, products.name as product_name, products.product_code')
                        ->join('products', 'products.id = stock_movements.product_id')
                        ->where('stock_movements.movement_date >=', $startDate)
                        ->where('stock_movements.movement_date <=', $endDate);

        if ($warehouseId) {
            $builder->where('stock_movements.warehouse_id', $warehouseId);
        }

        return $builder->orderBy('stock_movements.movement_date', 'DESC')->findAll();
    }

    /**
     * Relatório de movimentações por produto
     */
    public function getProductMovementReport($productId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('stock_movements.*, warehouses.name as warehouse_name, CONCAT(users.first_name, " ", users.last_name) as user_name')
                        ->join('warehouses', 'warehouses.id = stock_movements.warehouse_id')
                        ->join('users', 'users.id = stock_movements.user_id')
                        ->where('stock_movements.product_id', $productId);

        if ($startDate) {
            $builder->where('stock_movements.movement_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('stock_movements.movement_date <=', $endDate);
        }

        return $builder->orderBy('stock_movements.movement_date', 'DESC')->findAll();
    }
}