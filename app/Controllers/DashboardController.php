<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\StockLevelModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $saleModel = new SaleModel();
        $customerModel = new CustomerModel();
        $productModel = new ProductModel();
        $stockLevel = new StockLevelModel();

        $totalStock = $stockLevel->selectSum('quantity_available')->first()['quantity_available'] ?? 0;

        $totalClients = $customerModel->countAllResults();

        $currentMonth = date('m');
        $currentYear = date('Y');
        $salesThisMonth = $saleModel
            ->where('MONTH(created_at)', $currentMonth)
            ->where('YEAR(created_at)', $currentYear)
            ->countAllResults();

        $totalSalesValue = $saleModel->selectSum('total_amount')->first()['total_amount'] ?? 0;

        // Últimas vendas (exemplo: 5 últimas)
        $lastSales = $saleModel
            ->select('sales.id, sales.total_amount, sales.created_at, customers.first_name, customers.last_name, products.name as product, sale_items.quantity')
            ->join('customers', 'customers.id = sales.customer_id')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->orderBy('sales.created_at', 'DESC')
            ->findAll(5);

        return view('dashboard/index', [
            'totalStock' => $totalStock,
            'totalClients' => $totalClients,
            'salesThisMonth' => $salesThisMonth,
            'totalSalesValue' => $totalSalesValue,
            'lastSales' => $lastSales
        ]);
    }
}