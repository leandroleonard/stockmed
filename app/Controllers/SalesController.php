<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;
use CodeIgniter\HTTP\ResponseInterface;

class SalesController extends BaseController
{
    private CustomerModel $customerModel;
    private WarehouseModel $warehouseModel;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->warehouseModel = new WarehouseModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        return view('dashboard/sales/index');
    }

    public function create()
    {
        $stockModel = new \App\Models\StockLevelModel();
        
        $builder = $stockModel->select('stock_levels.*, products.name as product_name, products.product_code, products.form, products.dosage, products.barcode, product_batches.selling_price, warehouses.name as warehouse_name, product_categories.name as category_name')
            ->join('products', 'products.id = stock_levels.product_id')
            ->join('product_batches', 'product_batches.product_id = products.id')
            ->join('product_categories', 'products.category_id = product_categories.id')
            ->join('warehouses', 'warehouses.id = stock_levels.warehouse_id')
            ->where('stock_levels.quantity_available >', 0)
            ->orderBy('products.name', 'ASC');

        // echo "<pre>";
        // exit(var_dump($builder->findAll()));

        $data = [
            'products' => $builder->findAll(),
            'customers' => $this->customerModel->findAll(),
            'warehouses' => $this->warehouseModel->findAll(),
        ];

        return view('dashboard/sales/sale', $data);
    }
}
