<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ManufacturerModel;
use App\Models\ProductBatchModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\PurchaseOrderModel;
use App\Models\StockLevelModel;
use App\Models\StockMovementModel;
use App\Models\SupplierModel;
use App\Models\WarehouseModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    private WarehouseModel $warehouseModel;
    private ManufacturerModel $manufacturerModel;
    private SupplierModel $supplierModel;
    private ProductModel $productModel;
    private ProductBatchModel $productBatchModel;
    private StockLevelModel $stockLevelModel;
    private StockMovementModel $stockMovementModel;
    private ProductCategoryModel $productCategoryModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->manufacturerModel = new ManufacturerModel();
        $this->supplierModel = new SupplierModel();
        $this->productModel = new ProductModel();
        $this->productBatchModel = new ProductBatchModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->stockLevelModel = new StockLevelModel();
        $this->productCategoryModel = new ProductCategoryModel();
    }
    public function index()
    {
        $stockModel = new \App\Models\StockLevelModel();

        $builder = $stockModel->select('stock_levels.*, products.name as product_name, products.product_code, product_batches.selling_price, warehouses.name as warehouse_name, product_categories.name as category_name')
            ->join('products', 'products.id = stock_levels.product_id')
            ->join('product_batches', 'product_batches.product_id = products.id')
            ->join('product_categories', 'products.category_id = product_categories.id')
            ->join('warehouses', 'warehouses.id = stock_levels.warehouse_id')
            ->where('stock_levels.quantity_available >', 0)
            ->orderBy('products.name', 'ASC');

        $stockList = $builder->findAll();

        return view('dashboard/product/index', ['stockList' => $stockList]);
    }

    public function form($productCode = null)
    {

        $warehouses = $this->warehouseModel->findAll();
        $manufacturers = $this->manufacturerModel->findAll();
        $suppliers = $this->supplierModel->findAll();
        $categories = $this->productCategoryModel->findAll();

        $product = null;

        if ($productCode)
            $product = $this->productModel->getProductWithDetails($productCode);

        // dd($product);

        return view('dashboard/product/form', ['warehouses' => $warehouses, 'manufacturers' => $manufacturers, 'suppliers' => $suppliers, 'product' => $product, 'categories' => $categories]);
    }

    public function submit()
    {
        $rules = [
            'name'               => 'required|max_length[200]',
            'manufacturer_id'    => 'permit_empty|integer',
            'batch_number'       => 'required|max_length[100]',
            'supplier_id'        => 'required|integer',
            'manufacture_date'   => 'permit_empty|valid_date',
            'expiry_date'        => 'required|valid_date',
            'quantity_received'  => 'required|integer|greater_than[0]',
            'cost_price'         => 'required|decimal',
            'selling_price'      => 'required|decimal',
            'warehouse_id'       => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $productM   = new ProductModel();
        $batchM     = new ProductBatchModel();
        $stockM     = new StockLevelModel();
        $movementM  = new StockMovementModel();
        $purchaseM  = new PurchaseOrderModel();
        $db         = \Config\Database::connect();

        $data = $this->request->getPost();

        $db->transStart();
        try {

            $product = null;

            if ($this->request->getPost('product_code'))
                $product = $productM->where('product_code', $this->request->getPost('product_code'))
                    ->first();


            if (! $product) {
                $productId = $productM->insert([
                    'name'            => $this->request->getPost('name'),
                    'manufacturer_id' => $this->request->getPost('manufacturer_id'),
                    'is_active'       => true,
                    'generic_name'    => $this->request->getPost('generic_name'),
                    'category_id'     => $this->request->getPost('category_id'),
                    'description'     => $this->request->getPost('description'),

                ]);
            } else {
                $productId = $product['id'];
            }

            $batchId = $batchM->insert([
                'batch_number'         => $this->request->getPost('batch_number'),
                'product_id'           => $productId,
                'supplier_id'          => $this->request->getPost('supplier_id'),
                'manufacture_date'     => $this->request->getPost('manufacture_date'),
                'expiry_date'          => $this->request->getPost('expiry_date'),
                'quantity_received'    => $this->request->getPost('quantity_received'),
                'quantity_remaining'   => $this->request->getPost('quantity_received'),
                'cost_price'           => $this->request->getPost('cost_price'),
                'selling_price'        => $this->request->getPost('selling_price'),
                'warehouse_id'         => $this->request->getPost('warehouse_id'),
                'quality_status'       => 'approved'
            ]);

            if (!isset($data['product_code'])) {
                $purchaseId = $purchaseM->insert([
                    'supplier_id'           => $this->request->getPost('supplier_id'),
                    'warehouse_id'          => $this->request->getPost('warehouse_id'),
                    'order_date'            => date('Y-m-d'),
                    'expected_delivery_at'  => date('Y-m-d'),
                    'status'                => 'aprovada',
                    'subtotal'              => $this->request->getPost('cost_price') * $this->request->getPost('quantity_received'),
                    'total'                 => $this->request->getPost('cost_price') * $this->request->getPost('quantity_received'),
                    'tax_amount'            => 0,
                    'discount_amount'       => 0,
                    'notes'                 => $this->request->getPost('description'),
                ]);
            }

            $stockRow = $stockM->where([
                'product_id'   => $productId,
                'warehouse_id' => $this->request->getPost('warehouse_id')
            ])->first();

            if ($stockRow) {
                $stockM->update(
                    $stockRow['id'],
                    ['quantity_available' => $stockRow['quantity_available']
                        + $this->request->getPost('quantity_received')]
                );
            } else {
                $stockM->insert([
                    'product_id'        => $productId,
                    'warehouse_id'      => $this->request->getPost('warehouse_id'),
                    'quantity_available' => $this->request->getPost('quantity_received'),
                    'quantity_reserved' => 0,
                    'quantity_on_order' => 0
                ]);
            }

            $movementM->insert([
                'product_id'    => $productId,
                'batch_id'      => $batchId,
                'warehouse_id'  => $this->request->getPost('warehouse_id'),
                'movement_type' => 'entrada',
                'quantity'      => $this->request->getPost('quantity_received'),
                'reference_type' => 'compra',
                'reference_id'  => null,
                'cost_price'    => $this->request->getPost('cost_price'),
                'selling_price' => $this->request->getPost('selling_price'),
                'notes'         => 'Entrada inicial de stock',
                'user_id'       => session('user_id')
            ]);
        } catch (DatabaseException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Erro DB: ' . $e->getMessage());
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()
                ->with('error', 'Não foi possível salvar o produto.');
        }

        $product = $this->productModel->where(['id' => $productId])->first();


        return redirect()->to(base_url('dashboard/stock/' . $product['product_code']))
            ->with('success', 'Estoque atualizado!');
    }

    public function getProductByBarcode($barcode)
    {
        $product = $this->productModel->where('barcode', $barcode)->where('is_active', true)->first();

        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Produto não encontrado']);
        }

        $stockModel = new \App\Models\StockLevelModel();
        
        $builder = $stockModel->select('stock_levels.*, products.name as product_name, products.product_code, products.form, products.dosage, products.barcode, product_batches.selling_price, warehouses.name as warehouse_name, product_categories.name as category_name')
            ->join('products', 'products.id = stock_levels.product_id')
            ->join('product_batches', 'product_batches.product_id = products.id')
            ->join('product_categories', 'products.category_id = product_categories.id')
            ->join('warehouses', 'warehouses.id = stock_levels.warehouse_id')
            ->where('stock_levels.quantity_available >', 0)
            ->where(['stock_levels.product_id' => $product['id']])->first();
            
        return $this->response->setJSON([
            'id' => $product['id'],
            'name' => $product['name'],
            'selling_price' => $builder['selling_price'],
            'stock_available' => $builder['quantity_available'] ?? 0
        ]);
    }
}
