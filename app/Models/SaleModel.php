<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sale_number',
        'customer_id',
        'warehouse_id',
        'sale_date',
        'payment_method',
        'payment_status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'prescription_number',
        'doctor_name',
        'notes',
        'cashier_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'sale_number' => 'required|is_unique[sales.sale_number,id,{id}]',
        'warehouse_id' => 'required|integer',
        'payment_method' => 'required|in_list[dinheiro,cartao,transferencia,cheque,credito]',
        'payment_status' => 'required|in_list[pago,pendente,parcial]',
        'cashier_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'sale_number' => [
            'required' => 'O número da venda é obrigatório',
            'is_unique' => 'Este número de venda já existe'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateSaleNumber'];

    /**
     * Gera número da venda automaticamente
     */
    protected function generateSaleNumber(array $data)
    {
        if (empty($data['data']['sale_number'])) {
            $lastSale = $this->orderBy('id', 'DESC')->first();
            $nextId = $lastSale ? $lastSale['id'] + 1 : 1;
            $data['data']['sale_number'] = 'VD' . date('Ymd') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Busca vendas com detalhes
     */
    public function getSalesWithDetails($filters = [])
    {
        $builder = $this->select('sales.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, warehouses.name as warehouse_name, CONCAT(users.first_name, " ", users.last_name) as cashier_name')
                        ->join('customers', 'customers.id = sales.customer_id', 'left')
                        ->join('warehouses', 'warehouses.id = sales.warehouse_id')
                        ->join('users', 'users.id = sales.cashier_id');

        // Aplicar filtros
        if (isset($filters['customer_id'])) {
            $builder->where('sales.customer_id', $filters['customer_id']);
        }

        if (isset($filters['warehouse_id'])) {
            $builder->where('sales.warehouse_id', $filters['warehouse_id']);
        }

        if (isset($filters['payment_status'])) {
            $builder->where('sales.payment_status', $filters['payment_status']);
        }

        if (isset($filters['date_from'])) {
            $builder->where('sales.sale_date >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $builder->where('sales.sale_date <=', $filters['date_to']);
        }

        return $builder->orderBy('sales.sale_date', 'DESC')->findAll();
    }

    /**
     * Busca venda com itens
     */
    public function getSaleWithItems($saleId)
    {
        $sale = $this->select('sales.*, CONCAT(customers.first_name, " ", customers.last_name) as customer_name, warehouses.name as warehouse_name')
                     ->join('customers', 'customers.id = sales.customer_id', 'left')
                     ->join('warehouses', 'warehouses.id = sales.warehouse_id')
                     ->find($saleId);

        if ($sale) {
            $itemModel = new SaleItemModel();
            $sale['items'] = $itemModel->getSaleItems($saleId);
        }

        return $sale;
    }

    /**
     * Calcula totais da venda
     */
    public function calculateTotals($saleId)
    {
        $itemModel = new SaleItemModel();
        $items = $itemModel->where('sale_id', $saleId)->findAll();

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['total_price'];
        }

        $taxAmount = $subtotal * 0.1; // 10% de imposto (ajustar conforme necessário)
        $totalAmount = $subtotal + $taxAmount;

        return $this->update($saleId, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);
    }

    /**
     * Relatório de vendas por período
     */
    public function getSalesReport($startDate, $endDate, $warehouseId = null)
    {
        $builder = $this->select('DATE(sales.sale_date) as date, COUNT(*) as total_sales, SUM(sales.total_amount) as total_revenue')
                        ->where('sales.sale_date >=', $startDate)
                        ->where('sales.sale_date <=', $endDate);

        if ($warehouseId) {
            $builder->where('sales.warehouse_id', $warehouseId);
        }

        return $builder->groupBy('DATE(sales.sale_date)')
                       ->orderBy('date', 'DESC')
                       ->findAll();
    }

    /**
     * Top produtos vendidos
     */
    public function getTopSellingProducts($startDate, $endDate, $limit = 10, $warehouseId = null)
    {
        $builder = $this->db->table('sales s')
                           ->select('p.name as product_name, p.product_code, SUM(si.quantity) as total_quantity, SUM(si.total_price) as total_revenue')
                           ->join('sale_items si', 'si.sale_id = s.id')
                           ->join('products p', 'p.id = si.product_id')
                           ->where('s.sale_date >=', $startDate)
                           ->where('s.sale_date <=', $endDate);

        if ($warehouseId) {
            $builder->where('s.warehouse_id', $warehouseId);
        }

        return $builder->groupBy('si.product_id')
                       ->orderBy('total_quantity', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }
}