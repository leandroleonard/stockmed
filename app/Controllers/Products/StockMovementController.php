<?php

namespace App\Controllers\Products;

use App\Models\StockMovementModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class StockMovementController extends Controller
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new StockMovementModel();
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Movimento de stock não encontrado');
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'product_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'batch_id' => 'required|integer',
            'type' => 'required|in_list[IN,OUT,TRANSFER]',
            'quantity' => 'required|integer',
            'description' => 'permit_empty|max_length[1000]',
            'source_warehouse_id' => 'permit_empty|integer',
            'destination_warehouse_id' => 'permit_empty|integer'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'product_id' => $this->request->getVar('product_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'batch_id' => $this->request->getVar('batch_id'),
            'type' => $this->request->getVar('type'),
            'quantity' => $this->request->getVar('quantity'),
            'description' => $this->request->getVar('description'),
            'source_warehouse_id' => $this->request->getVar('source_warehouse_id'),
            'destination_warehouse_id' => $this->request->getVar('destination_warehouse_id')
        ];
        $this->model->insert($data);
        return $this->respondCreated(['message' => 'Movimento de stock criado com sucesso']);
    }

    public function update($id = null)
    {
        $rules = [
            'product_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'batch_id' => 'required|integer',
            'type' => 'required|in_list[IN,OUT,TRANSFER]',
            'quantity' => 'required|integer',
            'description' => 'permit_empty|max_length[1000]',
            'source_warehouse_id' => 'permit_empty|integer',
            'destination_warehouse_id' => 'permit_empty|integer'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'product_id' => $this->request->getVar('product_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'batch_id' => $this->request->getVar('batch_id'),
            'type' => $this->request->getVar('type'),
            'quantity' => $this->request->getVar('quantity'),
            'description' => $this->request->getVar('description'),
            'source_warehouse_id' => $this->request->getVar('source_warehouse_id'),
            'destination_warehouse_id' => $this->request->getVar('destination_warehouse_id')
        ];
        $this->model->update($id, $data);
        return $this->respond(['message' => 'Movimento de stock atualizado com sucesso']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Movimento de stock não encontrado');
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Movimento de stock excluído com sucesso']);
    }
}