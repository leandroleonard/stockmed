<?php

namespace App\Inventory;

use App\Models\InventoryModel;
use App\Models\InventoryItemModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class InventoryController extends Controller
{
    use ResponseTrait;

    protected $model;
    protected $itemModel;

    public function __construct()
    {
        $this->model = new InventoryModel();
        $this->itemModel = new InventoryItemModel();
    }

    public function index()
    {
        $data = $this->model->select('inventories.*, warehouses.name as warehouse_name, users.username as user_name')
                           ->join('warehouses', 'warehouses.id = inventories.warehouse_id')
                           ->join('users', 'users.id = inventories.user_id')
                           ->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $inventory = $this->model->select('inventories.*, warehouses.name as warehouse_name, users.username as user_name')
                                ->join('warehouses', 'warehouses.id = inventories.warehouse_id')
                                ->join('users', 'users.id = inventories.user_id')
                                ->find($id);
        if (!$inventory) return $this->failNotFound('Inventário não encontrado');

        $items = $this->itemModel->select('inventory_items.*, products.name as product_name, product_batches.batch_number')
                                ->join('products', 'products.id = inventory_items.product_id')
                                ->join('product_batches', 'product_batches.id = inventory_items.batch_id')
                                ->where('inventory_id', $id)
                                ->findAll();
        
        $inventory['items'] = $items;
        return $this->respond($inventory);
    }

    public function create()
    {
        $rules = [
            'warehouse_id' => 'required|integer',
            'user_id' => 'required|integer',
            'inventory_date' => 'required|valid_date',
            'notes' => 'permit_empty|max_length[1000]',
            'items' => 'required|is_array',
            'items.*.product_id' => 'required|integer',
            'items.*.batch_id' => 'required|integer',
            'items.*.counted_quantity' => 'required|integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Criar inventário
            $inventoryData = [
                'warehouse_id' => $this->request->getVar('warehouse_id'),
                'user_id' => $this->request->getVar('user_id'),
                'inventory_date' => $this->request->getVar('inventory_date'),
                'notes' => $this->request->getVar('notes')
            ];

            $inventoryId = $this->model->insert($inventoryData);

            // Inserir itens do inventário
            $items = $this->request->getVar('items');

            foreach ($items as $item) {
                // Obter a quantidade esperada do stock
                $stockLevelModel = new \App\Models\StockLevelModel();
                $stockLevel = $stockLevelModel->where('product_id', $item['product_id'])
                                              ->where('warehouse_id', $this->request->getVar('warehouse_id'))
                                              ->where('batch_id', $item['batch_id'])
                                              ->first();

                $expectedQuantity = $stockLevel ? $stockLevel['quantity'] : 0;

                $itemData = [
                    'inventory_id' => $inventoryId,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'expected_quantity' => $expectedQuantity,
                    'counted_quantity' => $item['counted_quantity'],
                    'difference' => $item['counted_quantity'] - $expectedQuantity
                ];
                $this->itemModel->insert($itemData);

                // Atualizar stock (opcional)
                // $stockLevelModel->update($stockLevel['id'], ['quantity' => $item['counted_quantity']]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao criar inventário');
            }

            return $this->respondCreated(['message' => 'Inventário criado com sucesso', 'id' => $inventoryId]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Inventário não encontrado');

        $rules = [
            'warehouse_id' => 'required|integer',
            'user_id' => 'required|integer',
            'inventory_date' => 'required|valid_date',
            'notes' => 'permit_empty|max_length[1000]',
            'items' => 'required|is_array',
            'items.*.product_id' => 'required|integer',
            'items.*.batch_id' => 'required|integer',
            'items.*.counted_quantity' => 'required|integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Atualizar inventário
            $inventoryData = [
                'warehouse_id' => $this->request->getVar('warehouse_id'),
                'user_id' => $this->request->getVar('user_id'),
                'inventory_date' => $this->request->getVar('inventory_date'),
                'notes' => $this->request->getVar('notes')
            ];

            $this->model->update($id, $inventoryData);

            // Remover itens existentes
            $this->itemModel->where('inventory_id', $id)->delete();

            // Inserir novos itens
            $items = $this->request->getVar('items');

            foreach ($items as $item) {
                // Obter a quantidade esperada do stock
                $stockLevelModel = new \App\Models\StockLevelModel();
                $stockLevel = $stockLevelModel->where('product_id', $item['product_id'])
                                              ->where('warehouse_id', $this->request->getVar('warehouse_id'))
                                              ->where('batch_id', $item['batch_id'])
                                              ->first();

                $expectedQuantity = $stockLevel ? $stockLevel['quantity'] : 0;

                $itemData = [
                    'inventory_id' => $id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'expected_quantity' => $expectedQuantity,
                    'counted_quantity' => $item['counted_quantity'],
                    'difference' => $item['counted_quantity'] - $expectedQuantity
                ];
                $this->itemModel->insert($itemData);

                // Atualizar stock (opcional)
                // $stockLevelModel->update($stockLevel['id'], ['quantity' => $item['counted_quantity']]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao atualizar inventário');
            }

            return $this->respond(['message' => 'Inventário atualizado com sucesso']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Inventário não encontrado');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->itemModel->where('inventory_id', $id)->delete();
            $this->model->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao excluir inventário');
            }

            return $this->respondDeleted(['message' => 'Inventário excluído com sucesso']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }
}