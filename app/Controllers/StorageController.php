<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WarehouseModel;
use CodeIgniter\HTTP\ResponseInterface;

class StorageController extends BaseController
{
    private WarehouseModel $warehouseModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->userModel = new UserModel();
    }
    public function index()
    {
        $warehouseModel = new WarehouseModel();
        $warehouses = $warehouseModel->findAll();

        return view('dashboard/storage/index', ['warehouses' => $warehouses]);
    }

    public function form($warehouseCode = null)
    {
        $warehouse = null;
        $users = $this->userModel->findAll();

        if ($warehouseCode)
            $warehouse = $this->warehouseModel->getWarehouseWithManager($warehouseCode);

        return view('dashboard/storage/form', ['warehouse' => $warehouse, 'users' => $users]);
    }

    public function submit()
    {
        $data = $this->request->getPost();

        if (isset($data['warehouse_code'])) {
            $warehouse = $this->warehouseModel->where(['warehouse_code' => $data['warehouse_code']])->first();

            if (!$warehouse) {
                return redirect()->to(base_url('dashboard/storage/create'))->with('error', 'Armazem não actualizado');
            }

            $data['id'] = $warehouse['id'];

            if (!$this->warehouseModel->update($warehouse['id'], $data)) {
                return redirect()->to(base_url('dashboard/storage/create'))
                    ->with('error', $this->warehouseModel->errors())
                    ->withInput();
            }

            return redirect()->to(base_url('dashboard/storage/update/' . $data['warehouse_code']))
                ->with('success', 'Armazem actualizado');
        }



        if ($warehouse = $this->warehouseModel->createWithKey($data))
            return redirect()->to(base_url('dashboard/storage/update/' . $warehouse['warehouse_code']))->with('success', 'Armazem cadastrado');

        return redirect()->to(base_url('dashboard/storage/create'))->with('error', $this->warehouseModel->errors())->withInput();
    }
}
