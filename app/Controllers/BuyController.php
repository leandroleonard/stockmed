<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Orders\PurchaseOrderController;
use App\Models\PurchaseOrderItemModel;
use App\Models\PurchaseOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

class BuyController extends BaseController
{
    protected $model;
    protected $itemModel;

    public function __construct()
    {
        $this->model = new PurchaseOrderModel();
        $this->itemModel = new PurchaseOrderItemModel();
    }

    public function index()
    {
        $data = $this->model->select('purchase_orders.*, suppliers.company_name')
                           ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                           ->findAll();
        // dd($data);
        return view('dashboard/buy/index', ['data' => $data]);
    }
}
