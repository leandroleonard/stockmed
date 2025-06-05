<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\API\ResponseTrait;

class ProductController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Produto não encontrado');
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'category_id' => 'required|integer',
            'manufacturer_id' => 'required|integer',
            'name' => 'required|min_length[3]|max_length[255]|is_unique[products.name]',
            'description' => 'permit_empty|max_length[1000]',
            'active_ingredient' => 'permit_empty|max_length[255]',
            'dosage' => 'permit_empty|max_length[255]',
            'form' => 'permit_empty|max_length[255]',
            'barcode' => 'permit_empty|max_length[255]|is_unique[products.barcode]',
            'requires_prescription' => 'in_list[0,1]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'category_id' => $this->request->getVar('category_id'),
            'manufacturer_id' => $this->request->getVar('manufacturer_id'),
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'active_ingredient' => $this->request->getVar('active_ingredient'),
            'dosage' => $this->request->getVar('dosage'),
            'form' => $this->request->getVar('form'),
            'barcode' => $this->request->getVar('barcode'),
            'requires_prescription' => $this->request->getVar('requires_prescription')
        ];
        $this->model->insert($data);
        return $this->respondCreated(['message' => 'Produto criado com sucesso']);
    }

    public function update($id = null)
    {
        $rules = [
            'category_id' => 'required|integer',
            'manufacturer_id' => 'required|integer',
            'name' => "required|min_length[3]|max_length[255]|is_unique[products.name,id,{$id}]",
            'description' => 'permit_empty|max_length[1000]',
            'active_ingredient' => 'permit_empty|max_length[255]',
            'dosage' => 'permit_empty|max_length[255]',
            'form' => 'permit_empty|max_length[255]',
            'barcode' => "permit_empty|max_length[255]|is_unique[products.barcode,id,{$id}]",
            'requires_prescription' => 'in_list[0,1]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'category_id' => $this->request->getVar('category_id'),
            'manufacturer_id' => $this->request->getVar('manufacturer_id'),
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'active_ingredient' => $this->request->getVar('active_ingredient'),
            'dosage' => $this->request->getVar('dosage'),
            'form' => $this->request->getVar('form'),
            'barcode' => $this->request->getVar('barcode'),
            'requires_prescription' => $this->request->getVar('requires_prescription')
        ];
        $this->model->update($id, $data);
        return $this->respond(['message' => 'Produto atualizado com sucesso']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Produto não encontrado');
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Produto excluído com sucesso']);
    }
}