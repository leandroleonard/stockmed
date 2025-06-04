<?php

namespace App\Controllers;

use App\Models\ProductBatchModel;
use CodeIgniter\API\ResponseTrait;

class ProductBatchController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new ProductBatchModel();
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Lote não encontrado');
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'product_id' => 'required|integer',
            'batch_number' => 'required|min_length[3]|max_length[255]|is_unique[product_batches.batch_number]',
            'manufacturing_date' => 'permit_empty|valid_date',
            'expiry_date' => 'required|valid_date',
            'quantity' => 'required|integer|greater_than[0]',
            'cost_price' => 'required|decimal',
            'selling_price' => 'required|decimal'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'product_id' => $this->request->getVar('product_id'),
            'batch_number' => $this->request->getVar('batch_number'),
            'manufacturing_date' => $this->request->getVar('manufacturing_date'),
            'expiry_date' => $this->request->getVar('expiry_date'),
            'quantity' => $this->request->getVar('quantity'),
            'cost_price' => $this->request->getVar('cost_price'),
            'selling_price' => $this->request->getVar('selling_price')
        ];
        $this->model->insert($data);
        return $this->respondCreated(['message' => 'Lote criado com sucesso']);
    }

    public function update($id = null)
    {
        $rules = [
            'product_id' => 'required|integer',
            'batch_number' => "required|min_length[3]|max_length[255]|is_unique[product_batches.batch_number,id,{$id}]",
            'manufacturing_date' => 'permit_empty|valid_date',
            'expiry_date' => 'required|valid_date',
            'quantity' => 'required|integer|greater_than[0]',
            'cost_price' => 'required|decimal',
            'selling_price' => 'required|decimal'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'product_id' => $this->request->getVar('product_id'),
            'batch_number' => $this->request->getVar('batch_number'),
            'manufacturing_date' => $this->request->getVar('manufacturing_date'),
            'expiry_date' => $this->request->getVar('expiry_date'),
            'quantity' => $this->request->getVar('quantity'),
            'cost_price' => $this->request->getVar('cost_price'),
            'selling_price' => $this->request->getVar('selling_price')
        ];
        $this->model->update($id, $data);
        return $this->respond(['message' => 'Lote atualizado com sucesso']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Lote não encontrado');
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Lote excluído com sucesso']);
    }
}