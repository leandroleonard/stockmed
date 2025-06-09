<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WarehouseModel;
use CodeIgniter\HTTP\ResponseInterface;

class StorageController extends BaseController
{
    public function index()
    {
        $warehouseModel = new WarehouseModel();
        $warehouses = $warehouseModel->findAll();

        return view('dashboard/storage/index', ['warehouses' => $warehouses]);
    }
}
