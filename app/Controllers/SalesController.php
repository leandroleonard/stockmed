<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\StockLevelModel;
use App\Models\StockMovementModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

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
        $saleModel = new \App\Models\SaleModel();

        $builder = $saleModel->select('sales.*, customers.id as customer_id, customers.first_name, customers.last_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->orderBy('sales.sale_date', 'DESC');


        return view('dashboard/sales/index', ['sales' => $builder->findAll()]);
    }

    public function create()
    {
        $stockModel = new \App\Models\StockLevelModel();

        $builder = $stockModel->select('stock_levels.*,products.id as product_id, products.name as product_name, products.product_code, products.form, products.dosage, products.barcode, product_batches.selling_price, warehouses.name as warehouse_name, product_categories.name as category_name')
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

    public function store()
    {
        $warehouseId = 1;
        $rules = [
            'customer_id' => 'required|integer',
            'items' => 'required',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|greater_than[0]',
            'items.*.unit_price' => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // dd($data);

        $saleModel = new SaleModel();
        $saleItemModel = new SaleItemModel();
        $stockLevelModel = new StockLevelModel();
        $stockMovementModel = new StockMovementModel();
        $db = \Config\Database::connect();

        $db->transStart();

        try {
            $customerId = $this->request->getPost('customer_id');
            $warehouseId = $this->request->getPost('warehouse_id');
            $items = $this->request->getPost('items');

            // Verifica estoque disponível para cada item
            foreach ($items as $item) {
                $stock = $stockLevelModel->where(['product_id' => $item['product_id']])->first();
                if (!$stock || $stock['quantity_available'] < $item['quantity']) {
                    throw new \Exception('Estoque insuficiente para o produto ID ' . $item['product_id']);
                }
            }

            // Cria registro da venda
            $saleId = $saleModel->insert([
                'customer_id' => $customerId,
                'warehouse_id' => 1,
                'sale_date' => date('Y-m-d H:i:s'),
                'payment_status' => 'pago',
                'payment_method' => $data['payment_method'],
                'subtotal' => 0,
                'total_amount' => 0,
                'notes' => $data['notes'],
                'cashier_id' => session('user_id'),
            ]);

            if (!$saleId) {
                $errors = $saleModel->errors();
                throw new \Exception('Erro ao criar a venda: ' . json_encode($errors));
            }


            $totalAmount = 0;

            // Insere itens da venda e atualiza estoque
            foreach ($items as $item) {
                $quantity = (int)$item['quantity'];
                $unitPrice = (float)$item['unit_price'];
                $totalPrice = $quantity * $unitPrice;

                // Inserir item da venda
                $saleItemModel->insert([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'batch_id' => 1,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ]);

                // Atualizar estoque
                $stock = $stockLevelModel->where(['product_id' => $item['product_id']])->first();
                $newQuantity = $stock['quantity_available'] - $quantity;
                if ($newQuantity < 0) {
                    throw new \Exception('Estoque insuficiente para o produto ID ' . $item['product_id']);
                }
                $stockLevelModel->update($stock['id'], ['quantity_available' => $newQuantity]);

                // Registrar movimentação
                $stockMovementModel->insert([
                    'product_id' => $item['product_id'],
                    'batch_id' => 1,
                    'warehouse_id' => 1,
                    'movement_type' => 'saida',
                    'quantity' => $quantity,
                    'reference_type' => 'venda',
                    'reference_id' => $saleId,
                    'cost_price' => null,
                    'selling_price' => $unitPrice,
                    'notes' => $data['notes'],
                    'user_id' => session('user_id')
                ]);

                $totalAmount += $totalPrice;
            }

            $saleModel->update($saleId, ['total_amount' => $totalAmount, 'subtotal' => $totalAmount]);

            $db->transComplete();

            if (! $db->transStatus()) {
                throw new \Exception('Erro ao salvar a venda');
            }

            return redirect()->to(base_url('dashboard/sales'))->with('success', 'Venda registrada com sucesso!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function details($id)
    {
        $saleModel = new \App\Models\SaleModel();
        $saleItemModel = new \App\Models\SaleItemModel();

        $sale = $saleModel->select('sales.*, customers.first_name, customers.last_name, warehouses.name as warehouse_name, users.first_name as cashier_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->join('warehouses', 'warehouses.id = sales.warehouse_id')
            ->join('users', 'users.id = sales.cashier_id')
            ->where('sales.sale_number', $id)
            ->first();

        if (!$sale) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Venda não encontrada');
        }

        $items = $saleItemModel->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $sale['id'])
            ->findAll();

        return view('dashboard/sales/details', [
            'sale' => $sale,
            'items' => $items
        ]);
    }

    public function download($id)
    {
        $saleModel = new \App\Models\SaleModel();
        $saleItemModel = new \App\Models\SaleItemModel();

        $sale = $saleModel->select('sales.*, customers.name as customer_name, warehouses.name as warehouse_name, users.username as cashier_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->join('warehouses', 'warehouses.id = sales.warehouse_id')
            ->join('users', 'users.id = sales.cashier_id')
            ->where('sales.id', $id)
            ->first();

        if (!$sale) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Venda não encontrada');
        }

        $items = $saleItemModel->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $id)
            ->findAll();

        $html = view('sales/invoice_pdf', [
            'sale' => $sale,
            'items' => $items
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("fatura_venda_{$id}.pdf", ['Attachment' => true]);
    }
}
