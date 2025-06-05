<?php

namespace App\Controllers;

use App\Models\StockLevelModel;
use CodeIgniter\API\ResponseTrait;

class StockLevelController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new StockLevelModel();
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Nível de stock não encontrado');
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'product_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'batch_id' => 'required|integer',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'reorder_level' => 'permit_empty|integer|greater_than_equal_to[0]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'product_id' => $this->request->getVar('product_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'batch_id' => $this->request->getVar('batch_id'),
            'quantity' => $this->request->getVar('quantity'),
            'reorder_level' => $this->request->getVar('reorder_level')
        ];
        $this->model->insert($data);
        return $this->respondCreated(['message' => 'Nível de stock criado com sucesso']);
    }

    public function update($id = null)
    {
        $rules = [
            'product_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'batch_id' => 'required|integer',
            'quantity' => 'required|integer|greater_than_equal_to[0]',
            'reorder_level' => 'permit_empty|integer|greater_than_equal_to[0]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'product_id' => $this->request->getVar('product_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'batch_id' => $this->request->getVar('batch_id'),
            'quantity' => $this->request->getVar('quantity'),
            'reorder_level' => $this->request->getVar('reorder_level')
        ];
        $this->model->update($id, $data);
        return $this->respond(['message' => 'Nível de stock atualizado com sucesso']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Nível de stock não encontrado');
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Nível de stock excluído com sucesso']);
    }
}