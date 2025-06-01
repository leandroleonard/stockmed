<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'order_number',
        'supplier_id',
        'warehouse_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
        'created_by',
        'approved_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_number' => 'required|is_unique[purchase_orders.order_number,id,{id}]',
        'supplier_id' => 'required|integer',
        'warehouse_id' => 'required|integer',
        'order_date' => 'required|valid_date',
        'status' => 'required|in_list[pendente,aprovado,enviado,recebido,cancelado]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'order_number' => [
            'required' => 'O número do pedido é obrigatório',
            'is_unique' => 'Este número de pedido já existe'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateOrderNumber'];

    /**
     * Gera número do pedido automaticamente
     */
    protected function generateOrderNumber(array $data)
    {
        if (empty($data['data']['order_number'])) {
            $lastOrder = $this->orderBy('id', 'DESC')->first();
            $nextId = $lastOrder ? $lastOrder['id'] + 1 : 1;
            $data['data']['order_number'] = 'PO' . date('Y') . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Busca pedidos com detalhes
     */
    public function getOrdersWithDetails($status = null)
    {
        $builder = $this->select('purchase_orders.*, suppliers.company_name as supplier_name, warehouses.name as warehouse_name, CONCAT(users.first_name, " ", users.last_name) as created_by_name')
                        ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                        ->join('warehouses', 'warehouses.id = purchase_orders.warehouse_id')
                        ->join('users', 'users.id = purchase_orders.created_by');

        if ($status) {
            $builder->where('purchase_orders.status', $status);
        }

        return $builder->orderBy('purchase_orders.order_date', 'DESC')->findAll();
    }

    /**
     * Busca pedido com itens
     */
    public function getOrderWithItems($orderId)
    {
        $order = $this->select('purchase_orders.*, suppliers.company_name as supplier_name, warehouses.name as warehouse_name')
                      ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                      ->join('warehouses', 'warehouses.id = purchase_orders.warehouse_id')
                      ->find($orderId);

        if ($order) {
            $itemModel = new PurchaseOrderItemModel();
            $order['items'] = $itemModel->getOrderItems($orderId);
        }

        return $order;
    }

    /**
     * Atualiza status do pedido
     */
    public function updateStatus($orderId, $status, $userId = null)
    {
        $data = ['status' => $status];
        
        if ($status === 'aprovado' && $userId) {
            $data['approved_by'] = $userId;
        }

        return $this->update($orderId, $data);
    }

    /**
     * Calcula totais do pedido
     */
    public function calculateTotals($orderId)
    {
        $itemModel = new PurchaseOrderItemModel();
        $items = $itemModel->where('purchase_order_id', $orderId)->findAll();

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['total_cost'];
        }

        $taxAmount = $subtotal * 0.1; // 10% de imposto (ajustar conforme necessário)
        $totalAmount = $subtotal + $taxAmount;

        return $this->update($orderId, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);
    }

    /**
     * Busca pedidos pendentes de entrega
     */
    public function getPendingDeliveries()
    {
        return $this->select('purchase_orders.*, suppliers.company_name as supplier_name')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->whereIn('purchase_orders.status', ['aprovado', 'enviado'])
                    ->orderBy('purchase_orders.expected_delivery_date', 'ASC')
                    ->findAll();
    }
}