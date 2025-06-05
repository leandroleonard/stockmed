<?php

namespace App\Controllers\Products;

use App\Models\SaleModel;
use App\Models\SaleItemModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class SaleController extends Controller
{
    use ResponseTrait;

    protected $model;
    protected $itemModel;

    public function __construct()
    {
        $this->model = new SaleModel();
        $this->itemModel = new SaleItemModel();
    }

    public function index()
    {
        $data = $this->model->select('sales.*, customers.name as customer_name, users.username as user_name')
                           ->join('customers', 'customers.id = sales.customer_id', 'left')
                           ->join('users', 'users.id = sales.user_id')
                           ->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $sale = $this->model->select('sales.*, customers.name as customer_name, users.username as user_name')
                           ->join('customers', 'customers.id = sales.customer_id', 'left')
                           ->join('users', 'users.id = sales.user_id')
                           ->find($id);
        if (!$sale) return $this->failNotFound('Venda não encontrada');

        $items = $this->itemModel->select('sale_items.*, products.name as product_name, product_batches.batch_number')
                                ->join('products', 'products.id = sale_items.product_id')
                                ->join('product_batches', 'product_batches.id = sale_items.batch_id')
                                ->where('sale_id', $id)
                                ->findAll();
        
        $sale['items'] = $items;
        return $this->respond($sale);
    }

    public function create()
    {
        $rules = [
            'customer_id' => 'permit_empty|integer',
            'user_id' => 'required|integer',
            'sale_date' => 'required|valid_date',
            'payment_method' => 'required|in_list[CASH,CARD,TRANSFER,CHECK]',
            'discount_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'tax_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'notes' => 'permit_empty|max_length[1000]',
            'items' => 'required|is_array',
            'items.*.product_id' => 'required|integer',
            'items.*.batch_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_price' => 'required|decimal|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Criar venda
            $saleData = [
                'customer_id' => $this->request->getVar('customer_id'),
                'user_id' => $this->request->getVar('user_id'),
                'sale_date' => $this->request->getVar('sale_date'),
                'payment_method' => $this->request->getVar('payment_method'),
                'discount_amount' => $this->request->getVar('discount_amount') ?? 0,
                'tax_amount' => $this->request->getVar('tax_amount') ?? 0,
                'notes' => $this->request->getVar('notes')
            ];

            $saleId = $this->model->insert($saleData);

            // Calcular total e inserir itens
            $subtotal = 0;
            $items = $this->request->getVar('items');

            foreach ($items as $item) {
                $itemData = [
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ];
                $this->itemModel->insert($itemData);
                $subtotal += $itemData['total_price'];
            }

            // Calcular total final
            $discountAmount = $this->request->getVar('discount_amount') ?? 0;
            $taxAmount = $this->request->getVar('tax_amount') ?? 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            // Atualizar totais da venda
            $this->model->update($saleId, [
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao criar venda');
            }

            return $this->respondCreated(['message' => 'Venda criada com sucesso', 'id' => $saleId]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Venda não encontrada');

        $rules = [
            'customer_id' => 'permit_empty|integer',
            'user_id' => 'required|integer',
            'sale_date' => 'required|valid_date',
            'payment_method' => 'required|in_list[CASH,CARD,TRANSFER,CHECK]',
            'discount_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'tax_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'notes' => 'permit_empty|max_length[1000]',
            'items' => 'required|is_array',
            'items.*.product_id' => 'required|integer',
            'items.*.batch_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_price' => 'required|decimal|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Atualizar venda
            $saleData = [
                'customer_id' => $this->request->getVar('customer_id'),
                'user_id' => $this->request->getVar('user_id'),
                'sale_date' => $this->request->getVar('sale_date'),
                'payment_method' => $this->request->getVar('payment_method'),
                'discount_amount' => $this->request->getVar('discount_amount') ?? 0,
                'tax_amount' => $this->request->getVar('tax_amount') ?? 0,
                'notes' => $this->request->getVar('notes')
            ];

            $this->model->update($id, $saleData);

            // Remover itens existentes
            $this->itemModel->where('sale_id', $id)->delete();

            // Inserir novos itens
            $subtotal = 0;
            $items = $this->request->getVar('items');

            foreach ($items as $item) {
                $itemData = [
                    'sale_id' => $id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price']
                ];
                $this->itemModel->insert($itemData);
                $subtotal += $itemData['total_price'];
            }

            // Calcular total final
            $discountAmount = $this->request->getVar('discount_amount') ?? 0;
            $taxAmount = $this->request->getVar('tax_amount') ?? 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            // Atualizar totais da venda
            $this->model->update($id, [
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao atualizar venda');
            }

            return $this->respond(['message' => 'Venda atualizada com sucesso']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Venda não encontrada');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->itemModel->where('sale_id', $id)->delete();
            $this->model->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao excluir venda');
            }

            return $this->respondDeleted(['message' => 'Venda excluída com sucesso']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function dailySales()
    {
        $date = $this->request->getVar('date') ?? date('Y-m-d');
        $sales = $this->model->where('DATE(sale_date)', $date)->findAll();
        $total = array_sum(array_column($sales, 'total_amount'));
        
        return $this->respond([
            'date' => $date,
            'sales' => $sales,
            'total' => $total,
            'count' => count($sales)
        ]);
    }
}