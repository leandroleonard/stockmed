<?php

namespace App\Controllers;

use App\Models\ManufacturerModel;
use CodeIgniter\API\ResponseTrait;

class ManufacturerController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new ManufacturerModel();
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) return $this->failNotFound('Fabricante não encontrado');
        return $this->respond($data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]|is_unique[manufacturers.name]',
            'address' => 'permit_empty|max_length[255]',
            'contact_person' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'name' => $this->request->getVar('name'),
            'address' => $this->request->getVar('address'),
            'contact_person' => $this->request->getVar('contact_person'),
            'phone' => $this->request->getVar('phone'),
            'email' => $this->request->getVar('email')
        ];
        $this->model->insert($data);
        return $this->respondCreated(['message' => 'Fabricante criado com sucesso']);
    }

    public function update($id = null)
    {
        $rules = [
            'name' => "required|min_length[3]|max_length[255]|is_unique[manufacturers.name,id,{$id}]",
            'address' => 'permit_empty|max_length[255]',
            'contact_person' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        $data = [
            'name' => $this->request->getVar('name'),
            'address' => $this->request->getVar('address'),
            'contact_person' => $this->request->getVar('contact_person'),
            'phone' => $this->request->getVar('phone'),
            'email' => $this->request->getVar('email')
        ];
        $this->model->update($id, $data);
        return $this->respond(['message' => 'Fabricante atualizado com sucesso']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Fabricante não encontrado');
        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Fabricante excluído com sucesso']);
    }
}