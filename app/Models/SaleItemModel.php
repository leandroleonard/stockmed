<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sale_id',
        'product_id',
        'batch_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'total_price'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'sale_id' => 'required|integer',
        'product_id' => 'required|integer',
        'batch_id' => 'required|integer',
        'quantity' => 'required|integer|greater_than[0]',
        'unit_price' => 'required|decimal|greater_than[0]',
        'discount_percentage' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'total_price' => 'required|decimal|greater_than[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca itens da venda com detalhes
     */
    public function getSaleItems($saleId)
    {
        return $this->select('sale_items.*, products.name as product_name, products.product_code, product_batches.batch_number, product_batches.expiry_date')
                    ->join('products', 'products.id = sale_items.product_id')
                    ->join('product_batches', 'product_batches.id = sale_items.batch_id')
                    ->where('sale_items.sale_id', $saleId)
                    ->findAll();
    }

    /**
     * Processa venda completa
     */
    public function processSale($saleData, $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Criar venda
        $saleModel = new SaleModel();
        $saleId = $saleModel->insert($saleData);

        if ($saleId) {
            $stockModel = new StockLevelModel();
            $movementModel = new StockMovementModel();

            foreach ($items as $item) {
                $item['sale_id'] = $saleId;
                
                // Inserir item da venda
                $this->insert($item);

                // Registrar movimentação de stock
                $movementData = [
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'warehouse_id' => $saleData['warehouse_id'],
                    'movement_type' => 'saida',
                    'quantity' => $item['quantity'],
                    'reference_type' => 'venda',
                    'reference_id' => $saleId,
                    'selling_price' => $item['unit_price'],
                    'user_id' => $saleData['cashier_id']
                ];

                $movementModel->recordMovement($movementData);

                // Confirmar venda (remover da reserva se houver)
                $stockModel->confirmSale($item['product_id'], $saleData['warehouse_id'], $item['quantity']);
            }

            // Calcular totais
            $saleModel->calculateTotals($saleId);
        }

        $db->transComplete();
        return $db->transStatus() ? $saleId : false;
    }
}