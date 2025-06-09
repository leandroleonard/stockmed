<?php

namespace App\Database\Seeds;

use CodeIgniter\I18n\Time;

class StockmedSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $this->db->transStart();

        // User Roles
        $userRoles = [
            ['name' => 'Administrador'],
            ['name' => 'Funcionário']
        ];
        $this->db->table('user_roles')->insertBatch($userRoles);

        // Users
        $users = [
            [
                'user_role_id' => 1,
                'username' => 'admin',
                'password' => password_hash('12345', PASSWORD_DEFAULT),
                'email' => 'admin@stockmed.com',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'user_role_id' => 2,
                'username' => 'l.ventra',
                'password' => password_hash('12345', PASSWORD_DEFAULT),
                'email' => 'leandro.ventura@stockmed.com',
                'first_name' => 'Leandro',
                'last_name' => 'Ventura',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'user_role_id' => 3,
                'username' => 'e.reepson',
                'password' => password_hash('12345', PASSWORD_DEFAULT),
                'email' => 'etianete.reepson@stockmed.com',
                'first_name' => 'Etianete',
                'last_name' => 'Reepson',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // Customers
        $customers = [
            [
                'name' => 'Ana Bela',
                'address' => 'Rua A, 123',
                'phone' => '987654321',
                'email' => 'ana@cliente.com'
            ],
            [
                'name' => 'Miguel André',
                'address' => 'Rua B, 456',
                'phone' => '987654320',
                'email' => 'miguel@cliente.com'
            ],
            [
                'name' => 'Macaba Pedro',
                'address' => 'Rua B, 789',
                'phone' => '987654310',
                'email' => 'nobre@cliente.com'
            ]
        ];
        $this->db->table('customers')->insertBatch($customers);

        // Suppliers
        $suppliers = [
            [
                'name' => 'Fonseca e Filhos',
                'address' => 'Rua C, 789',
                'phone' => '922111222',
                'email' => 'fonseca@fornecedor.com'
            ],
            [
                'name' => 'Aimed',
                'address' => 'Rua D, 012',
                'phone' => '999222555',
                'email' => 'aimed@fornecedor.com'
            ]
        ];
        $this->db->table('suppliers')->insertBatch($suppliers);

        // Warehouses
        $warehouses = [
            ['name' => 'Armazém Talatona', 'address' => 'Rua E, 345'],
            ['name' => 'Armazém Kilamba', 'address' => 'Rua F, 678']
        ];
        $this->db->table('warehouses')->insertBatch($warehouses);

        // Product Categories
        $productCategories = [
            ['name' => 'Medicamentos'],
            ['name' => 'Cosméticos'],
            ['name' => 'Higiene Pessoal'],
        ];
        $this->db->table('product_categories')->insertBatch($productCategories);

        // Manufacturers
        $manufacturers = [
            ['name' => 'Fabricante 1', 'address' => 'Rua G, 901'],
            ['name' => 'Fabricante 2', 'address' => 'Rua H, 234']
        ];
        $this->db->table('manufacturers')->insertBatch($manufacturers);

        // Products
        $products = [
            [
                'category_id' => 1,
                'manufacturer_id' => 1,
                'name' => 'Paracetamol',
                'description' => 'Analgésico e antitérmico',
                'active_ingredient' => 'Paracetamol',
                'dosage' => '500mg',
                'form' => 'Comprimido',
                'barcode' => '1234567890123',
                'requires_prescription' => 0
            ],
            [
                'category_id' => 2,
                'manufacturer_id' => 2,
                'name' => 'Creme Hidratante',
                'description' => 'Hidratação para a pele',
                'active_ingredient' => 'Ureia',
                'dosage' => '10%',
                'form' => 'Creme',
                'barcode' => '3216549870321',
                'requires_prescription' => 0
            ]
        ];
        $this->db->table('products')->insertBatch($products);

        // Product Batches
        $productBatches = [
            [
                'product_id' => 1,
                'batch_number' => 'LOTE001',
                'manufacturing_date' => '2024-01-01',
                'expiry_date' => '2025-01-01',
                'quantity' => 100,
                'cost_price' => 1.50,
                'selling_price' => 3.00
            ],
            [
                'product_id' => 2,
                'batch_number' => 'LOTE002',
                'manufacturing_date' => '2024-02-01',
                'expiry_date' => '2025-02-01',
                'quantity' => 50,
                'cost_price' => 5.00,
                'selling_price' => 10.00
            ]
        ];
        $this->db->table('product_batches')->insertBatch($productBatches);

        // Stock Levels
        $stockLevels = [
            [
                'product_id' => 1,
                'warehouse_id' => 1,
                'batch_id' => 1,
                'quantity' => 50,
                'reorder_level' => 10
            ],
            [
                'product_id' => 2,
                'warehouse_id' => 2,
                'batch_id' => 2,
                'quantity' => 25,
                'reorder_level' => 5
            ]
        ];
        $this->db->table('stock_levels')->insertBatch($stockLevels);

        // Purchase Orders
        $purchaseOrders = [
            [
                'supplier_id' => 1,
                'order_date' => '2024-05-01',
                'expected_delivery_date' => '2024-05-15',
                'total_amount' => 150.00,
                'status' => 'DELIVERED',
                'notes' => 'Primeira compra',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'supplier_id' => 2,
                'order_date' => '2024-05-05',
                'expected_delivery_date' => '2024-05-20',
                'total_amount' => 500.00,
                'status' => 'PENDING',
                'notes' => 'Segunda compra',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('purchase_orders')->insertBatch($purchaseOrders);

        // Sales
        $sales = [
            [
                'customer_id' => 1,
                'user_id' => 1,
                'sale_date' => '2024-05-10',
                'subtotal' => 60.00,
                'discount_amount' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 60.00,
                'payment_method' => 'CASH',
                'notes' => 'Primeira venda',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'customer_id' => 2,
                'user_id' => 2,
                'sale_date' => '2024-05-12',
                'subtotal' => 100.00,
                'discount_amount' => 10.00,
                'tax_amount' => 5.00,
                'total_amount' => 95.00,
                'payment_method' => 'CARD',
                'notes' => 'Segunda venda',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('sales')->insertBatch($sales);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE)
        {
            echo "Erro ao popular o banco de dados";
        } else {
            echo "Banco de dados populado com sucesso";
        }
    }
}