<?php

namespace App\Controllers\Orders;

use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class PurchaseOrderController extends Controller
{
    use ResponseTrait;

    protected $model;
    protected $itemModel;

    public function __construct()
    {
        $this->model = new PurchaseOrderModel();
        $this->itemModel = new PurchaseOrderItemModel();
    }

    public function index()
    {
        $data = $this->model->select('purchase_orders.*, suppliers.company_name')
                           ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                           ->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $purchase = $this->model->select('purchase_orders.*, suppliers.name as supplier_name')
                                ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                                ->find($id);
        if (!$purchase) return $this->failNotFound('Ordem de compra não encontrada');

        $items = $this->itemModel->select('purchase_order_items.*, products.name as product_name')
                                ->join('products', 'products.id = purchase_order_items.product_id')
                                ->where('purchase_order_id', $id)
                                ->findAll();
        
        $purchase['items'] = $items;
        return $this->respond($purchase);
    }

    public function create()
    {
        $rules = [
            'supplier_id' => 'required|integer',
            'order_date' => 'required|valid_date',
            'expected_delivery_date' => 'permit_empty|valid_date',
            'status' => 'required|in_list[PENDING,CONFIRMED,DELIVERED,CANCELLED]',
            'notes' => 'permit_empty|max_length[1000]',
            'items' => 'required|is_array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_price' => 'required|decimal|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Criar ordem de compra
            $orderData = [
                'supplier_id' => $this->request->getVar('supplier_id'),
                'order_date' => $this->request->getVar('order_date'),
                'expected_delivery_date' => $this->request->getVar('expected_delivery_date'),
                'status' => $this->request->getVar('status'),
                'notes' => $this->request->getVar('notes')
            ];

            $orderId = $this->model->insert($orderData);

            // Calcular total e inserir itens
            $totalAmount = 0;
            $items = $this->request->getVar('items');

            foreach ($items as $item) {
                $itemData = [
                    'purchase_order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ];
                $this->itemModel->insert($itemData);
                $totalAmount += $itemData['total_price'];
            }

            // Atualizar total da ordem
            $this->model->update($orderId, ['total_amount' => $totalAmount]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao criar ordem de compra');
            }

            return $this->respondCreated(['message' => 'Ordem de compra criada com sucesso', 'id' => $orderId]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Ordem de compra não encontrada');

        $rules = [
            'supplier_id' => 'required|integer',
            'order_date' => 'required|valid_date',
            'expected_delivery_date' => 'permit_empty|valid_date',
            'status' => 'required|in_list[PENDING,CONFIRMED,DELIVERED,CANCELLED]',
            'notes' => 'permit_empty|max_length[1000]',
            'items' => 'required|is_array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_price' => 'required|decimal|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Atualizar ordem de compra
            $orderData = [
                'supplier_id' => $this->request->getVar('supplier_id'),
                'order_date' => $this->request->getVar('order_date'),
                'expected_delivery_date' => $this->request->getVar('expected_delivery_date'),
                'status' => $this->request->getVar('status'),
                'notes' => $this->request->getVar('notes')
            ];

            $this->model->update($id, $orderData);

            // Remover itens existentes
            $this->itemModel->where('purchase_order_id', $id)->delete();

            // Inserir novos itens
            $totalAmount = 0;
            $items = $this->request->getVar('items');

            foreach ($items as $item) {
                $itemData = [
                    'purchase_order_id' => $id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ];
                $this->itemModel->insert($itemData);
                $totalAmount += $itemData['total_price'];
            }

            // Atualizar total da ordem
            $this->model->update($id, ['total_amount' => $totalAmount]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao atualizar ordem de compra');
            }

            return $this->respond(['message' => 'Ordem de compra atualizada com sucesso']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Ordem de compra não encontrada');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->itemModel->where('purchase_order_id', $id)->delete();
            $this->model->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao excluir ordem de compra');
            }

            return $this->respondDeleted(['message' => 'Ordem de compra excluída com sucesso']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function updateStatus($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Ordem de compra não encontrada');

        $rules = ['status' => 'required|in_list[PENDING,CONFIRMED,DELIVERED,CANCELLED]'];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->model->update($id, ['status' => $this->request->getVar('status')]);
        return $this->respond(['message' => 'Status atualizado com sucesso']);
    }
}