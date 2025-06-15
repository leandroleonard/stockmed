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

        echo "<pre>";
        exit(var_dump($data));
    }
}
