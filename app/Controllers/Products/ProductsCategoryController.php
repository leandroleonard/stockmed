<?php

namespace App\Controllers\Products;

use App\Models\ProductCategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class ProductCategoryController extends Controller
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new ProductCategoryModel();
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Categoria não encontrada');
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]|is_unique[product_categories.name]',
            'description' => 'permit_empty|max_length[1000]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description')
        ];
        $this->model->insert($data);
        return $this->respondCreated(['message' => 'Categoria criada com sucesso']);
    }

    public function update($id = null)
    {
        $rules = [
            'name' => "required|min_length[3]|max_length[255]|is_unique[product_categories.name,id,{$id}]",
            'description' => 'permit_empty|max_length[1000]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description')
        ];
        $this->model->update($id, $data);
        return $this->respond(['message' => 'Categoria atualizada com sucesso']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Categoria não encontrada');
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Categoria excluída com sucesso']);
    }
}