<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    public function index()
    {
        $stockModel = new \App\Models\StockLevelModel();
        $productModel = new \App\Models\ProductModel();
        $warehouseModel = new \App\Models\WarehouseModel();

        // Consulta: todos os produtos com quantidade disponível > 0
        $builder = $stockModel->select('stock_levels.*, products.name as product_name, products.product_code, product_batches.selling_price, warehouses.name as warehouse_name, product_categories.name as category_name')
            ->join('products', 'products.id = stock_levels.product_id')
            ->join('product_batches', 'product_batches.product_id = products.id')
            ->join('product_categories', 'products.category_id = product_categories.id')
            ->join('warehouses', 'warehouses.id = stock_levels.warehouse_id')
            ->where('stock_levels.quantity_available >', 0)
            ->orderBy('products.name', 'ASC');

        $stockList = $builder->findAll();

        // echo "<pre>";
        // var_dump($stockList);
        // exit;

        return view('dashboard/product/index',['stockList' => $stockList]);
    }
}
