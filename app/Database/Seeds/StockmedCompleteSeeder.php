<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class StockmedCompleteSeeder extends Seeder
{
    public function run()
    {
        $this->db->transStart();

        // 1. User Roles
        $userRoles = [
            ['name' => 'Administrador', 'description' => 'Acesso total ao sistema'],
            ['name' => 'Funcionário', 'description' => 'Acesso limitado às operações básicas'],
            ['name' => 'Farmacêutico', 'description' => 'Acesso às prescrições e medicamentos controlados']
        ];
        $this->db->table('user_roles')->insertBatch($userRoles);

        // 2. Users
        $users = [
            [
                'user_role_id' => 1,
                'username' => 'admin',
                'password_hash' => password_hash('12345', PASSWORD_DEFAULT),
                'email' => 'admin@stockmed.com',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'user_role_id' => 2,
                'username' => 'l.ventra',
                'password_hash' => password_hash('12345', PASSWORD_DEFAULT),
                'email' => 'leandro.ventura@stockmed.com',
                'first_name' => 'Leandro',
                'last_name' => 'Ventura',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'user_role_id' => 3,
                'username' => 'e.reepson',
                'password_hash' => password_hash('12345', PASSWORD_DEFAULT),
                'email' => 'etianete.reepson@stockmed.com',
                'first_name' => 'Etianete',
                'last_name' => 'Reepson',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // 3. Customers
        $customers = [
            [
                'name' => 'Ana Paula Costa',
                'cpf' => '987654321',
                'address' => 'Rua das Flores, 123 - Centro',
                'phone' => '11987654321',
                'email' => 'ana.costa@email.com',
                'birth_date' => '1985-03-15',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Roberto Ferreira',
                'cpf' => '98765432109',
                'address' => 'Av. Paulista, 456 - Bela Vista',
                'phone' => '11876543210',
                'email' => 'roberto.ferreira@email.com',
                'birth_date' => '1978-07-22',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Lucia Mendes',
                'cpf' => '45678912345',
                'address' => 'Rua Augusta, 789 - Consolação',
                'phone' => '11765432109',
                'email' => 'lucia.mendes@email.com',
                'birth_date' => '1992-11-08',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('customers')->insertBatch($customers);

        // 4. Suppliers
        $suppliers = [
            [
                'name' => 'Distribuidora Farmacêutica ABC Ltda',
                'cnpj' => '12345678000195',
                'address' => 'Rua Industrial, 1000 - Distrito Industrial',
                'contact_person' => 'Pedro Almeida',
                'phone' => '1133334444',
                'email' => 'vendas@distribuidoraabc.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'MedSupply Distribuidora',
                'cnpj' => '98765432000187',
                'address' => 'Av. dos Medicamentos, 500 - Vila Farmacêutica',
                'contact_person' => 'Sandra Lima',
                'phone' => '1155556666',
                'email' => 'compras@medsupply.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'PharmaDistrib S.A.',
                'cnpj' => '11223344000156',
                'address' => 'Rodovia SP-100, Km 25 - Cotia',
                'contact_person' => 'Ricardo Souza',
                'phone' => '1177778888',
                'email' => 'atendimento@pharmadistrib.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('suppliers')->insertBatch($suppliers);

        // 5. Warehouses
        $warehouses = [
            [
                'name' => 'Depósito Principal',
                'address' => 'Rua do Depósito, 100 - Galpão A',
                'description' => 'Armazém principal para medicamentos gerais',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Câmara Fria',
                'address' => 'Rua do Depósito, 100 - Galpão B',
                'description' => 'Armazém refrigerado para medicamentos termolábeis',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Loja - Balcão',
                'address' => 'Rua Principal, 50 - Loja',
                'description' => 'Estoque da área de vendas',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('warehouses')->insertBatch($warehouses);

        // 6. Product Categories
        $productCategories = [
            [
                'name' => 'Analgésicos e Antipiréticos',
                'description' => 'Medicamentos para dor e febre',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Antibióticos',
                'description' => 'Medicamentos para infecções bacterianas',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Anti-inflamatórios',
                'description' => 'Medicamentos anti-inflamatórios',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Cardiovasculares',
                'description' => 'Medicamentos para o coração e circulação',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Dermatológicos',
                'description' => 'Medicamentos para a pele',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Vitaminas e Suplementos',
                'description' => 'Vitaminas e suplementos alimentares',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Cosméticos',
                'description' => 'Produtos de beleza e cuidados pessoais',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Higiene Pessoal',
                'description' => 'Produtos de higiene e cuidados pessoais',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('product_categories')->insertBatch($productCategories);

        // 7. Manufacturers
        $manufacturers = [
            [
                'name' => 'EMS S.A.',
                'address' => 'Rod. Jornalista F. A. Proença, Km 08 - Hortolândia/SP',
                'contact_person' => 'Departamento Comercial',
                'phone' => '1934958800',
                'email' => 'comercial@ems.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Medley Farmacêutica Ltda.',
                'address' => 'Rua Macedo Costa, 55 - Campinas/SP',
                'contact_person' => 'Vendas',
                'phone' => '1932076000',
                'email' => 'vendas@medley.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Eurofarma Laboratórios S.A.',
                'address' => 'Av. Vereador José Diniz, 3465 - São Paulo/SP',
                'contact_person' => 'Atendimento Comercial',
                'phone' => '1150908600',
                'email' => 'comercial@eurofarma.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Hypera Pharma',
                'address' => 'Rua Jequitibá, 400 - São Paulo/SP',
                'contact_person' => 'Relacionamento Comercial',
                'phone' => '1133838000',
                'email' => 'comercial@hypera.com.br',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Sanofi-Aventis Farmacêutica Ltda.',
                'address' => 'Av. Mj. Sylvio de M. Padilha, 5200 - São Paulo/SP',
                'contact_person' => 'Vendas Brasil',
                'phone' => '1137044000',
                'email' => 'brasil@sanofi.com',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('manufacturers')->insertBatch($manufacturers);

        // 8. Products (15 produtos)
        $products = [
            [
                'category_id' => 1,
                'manufacturer_id' => 1,
                'name' => 'Paracetamol 500mg',
                'description' => 'Analgésico e antitérmico para dores leves a moderadas',
                'active_ingredient' => 'Paracetamol',
                'dosage' => '500mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567890',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 2,
                'manufacturer_id' => 2,
                'name' => 'Amoxicilina 500mg',
                'description' => 'Antibiótico de amplo espectro',
                'active_ingredient' => 'Amoxicilina',
                'dosage' => '500mg',
                'form' => 'Cápsula',
                'barcode' => '7891234567891',
                'requires_prescription' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 3,
                'manufacturer_id' => 3,
                'name' => 'Ibuprofeno 600mg',
                'description' => 'Anti-inflamatório não esteroidal',
                'active_ingredient' => 'Ibuprofeno',
                'dosage' => '600mg',
                'form' => 'Comprimido revestido',
                'barcode' => '7891234567892',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 4,
                'manufacturer_id' => 4,
                'name' => 'Losartana 50mg',
                'description' => 'Anti-hipertensivo',
                'active_ingredient' => 'Losartana Potássica',
                'dosage' => '50mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567893',
                'requires_prescription' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 5,
                'manufacturer_id' => 5,
                'name' => 'Betametasona Creme',
                'description' => 'Corticosteroide tópico para dermatites',
                'active_ingredient' => 'Betametasona',
                'dosage' => '1mg/g',
                'form' => 'Creme',
                'barcode' => '7891234567894',
                'requires_prescription' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 6,
                'manufacturer_id' => 1,
                'name' => 'Complexo B',
                'description' => 'Suplemento vitamínico do complexo B',
                'active_ingredient' => 'Vitaminas do Complexo B',
                'dosage' => 'Variado',
                'form' => 'Comprimido',
                'barcode' => '7891234567895',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 1,
                'manufacturer_id' => 2,
                'name' => 'Dipirona 500mg',
                'description' => 'Analgésico e antitérmico potente',
                'active_ingredient' => 'Dipirona Sódica',
                'dosage' => '500mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567896',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 2,
                'manufacturer_id' => 3,
                'name' => 'Azitromicina 500mg',
                'description' => 'Antibiótico macrolídeo',
                'active_ingredient' => 'Azitromicina',
                'dosage' => '500mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567897',
                'requires_prescription' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 3,
                'manufacturer_id' => 4,
                'name' => 'Diclofenaco 50mg',
                'description' => 'Anti-inflamatório potente',
                'active_ingredient' => 'Diclofenaco Sódico',
                'dosage' => '50mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567898',
                'requires_prescription' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 4,
                'manufacturer_id' => 5,
                'name' => 'Enalapril 10mg',
                'description' => 'Inibidor da ECA para hipertensão',
                'active_ingredient' => 'Enalapril',
                'dosage' => '10mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567899',
                'requires_prescription' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 7,
                'manufacturer_id' => 1,
                'name' => 'Protetor Solar FPS 60',
                'description' => 'Proteção solar de alta proteção',
                'active_ingredient' => 'Filtros Solares',
                'dosage' => 'FPS 60',
                'form' => 'Loção',
                'barcode' => '7891234567800',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 8,
                'manufacturer_id' => 2,
                'name' => 'Shampoo Anticaspa',
                'description' => 'Shampoo medicinal para caspa',
                'active_ingredient' => 'Cetoconazol',
                'dosage' => '2%',
                'form' => 'Shampoo',
                'barcode' => '7891234567801',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 6,
                'manufacturer_id' => 3,
                'name' => 'Vitamina C 1g',
                'description' => 'Suplemento de vitamina C',
                'active_ingredient' => 'Ácido Ascórbico',
                'dosage' => '1g',
                'form' => 'Comprimido efervescente',
                'barcode' => '7891234567802',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 1,
                'manufacturer_id' => 4,
                'name' => 'Aspirina 500mg',
                'description' => 'Ácido acetilsalicílico para dor e febre',
                'active_ingredient' => 'Ácido Acetilsalicílico',
                'dosage' => '500mg',
                'form' => 'Comprimido',
                'barcode' => '7891234567803',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'category_id' => 5,
                'manufacturer_id' => 5,
                'name' => 'Hidrocortisona Pomada',
                'description' => 'Corticosteroide para inflamações da pele',
                'active_ingredient' => 'Hidrocortisona',
                'dosage' => '10mg/g',
                'form' => 'Pomada',
                'barcode' => '7891234567804',
                'requires_prescription' => 0,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('products')->insertBatch($products);

        // 9. Product Batches
        $productBatches = [
            // Paracetamol
            [
                'product_id' => 1,
                'batch_number' => 'PAR001-2024',
                'manufacturing_date' => '2024-01-15',
                'expiry_date' => '2026-01-15',
                'quantity' => 1000,
                'cost_price' => 0.15,
                'selling_price' => 0.35,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Amoxicilina
            [
                'product_id' => 2,
                'batch_number' => 'AMX002-2024',
                'manufacturing_date' => '2024-02-01',
                'expiry_date' => '2025-02-01',
                'quantity' => 500,
                'cost_price' => 0.80,
                'selling_price' => 1.50,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Ibuprofeno
            [
                'product_id' => 3,
                'batch_number' => 'IBU003-2024',
                'manufacturing_date' => '2024-01-20',
                'expiry_date' => '2026-01-20',
                'quantity' => 800,
                'cost_price' => 0.25,
                'selling_price' => 0.55,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Losartana
            [
                'product_id' => 4,
                'batch_number' => 'LOS004-2024',
                'manufacturing_date' => '2024-03-01',
                'expiry_date' => '2026-03-01',
                'quantity' => 600,
                'cost_price' => 0.45,
                'selling_price' => 0.85,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Betametasona
            [
                'product_id' => 5,
                'batch_number' => 'BET005-2024',
                'manufacturing_date' => '2024-02-15',
                'expiry_date' => '2025-02-15',
                'quantity' => 200,
                'cost_price' => 8.50,
                'selling_price' => 15.90,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Complexo B
            [
                'product_id' => 6,
                'batch_number' => 'CPB006-2024',
                'manufacturing_date' => '2024-01-10',
                'expiry_date' => '2025-01-10',
                'quantity' => 400,
                'cost_price' => 2.20,
                'selling_price' => 4.50,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Dipirona
            [
                'product_id' => 7,
                'batch_number' => 'DIP007-2024',
                'manufacturing_date' => '2024-02-20',
                'expiry_date' => '2026-02-20',
                'quantity' => 1200,
                'cost_price' => 0.12,
                'selling_price' => 0.28,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Azitromicina
            [
                'product_id' => 8,
                'batch_number' => 'AZI008-2024',
                'manufacturing_date' => '2024-03-10',
                'expiry_date' => '2025-03-10',
                'quantity' => 300,
                'cost_price' => 1.80,
                'selling_price' => 3.20,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Diclofenaco
            [
                'product_id' => 9,
                'batch_number' => 'DIC009-2024',
                'manufacturing_date' => '2024-01-25',
                'expiry_date' => '2025-01-25',
                'quantity' => 700,
                'cost_price' => 0.35,
                'selling_price' => 0.70,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Enalapril
            [
                'product_id' => 10,
                'batch_number' => 'ENA010-2024',
                'manufacturing_date' => '2024-02-05',
                'expiry_date' => '2026-02-05',
                'quantity' => 500,
                'cost_price' => 0.30,
                'selling_price' => 0.65,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Protetor Solar
            [
                'product_id' => 11,
                'batch_number' => 'PRO011-2024',
                'manufacturing_date' => '2024-01-05',
                'expiry_date' => '2025-01-05',
                'quantity' => 150,
                'cost_price' => 12.50,
                'selling_price' => 24.90,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Shampoo Anticaspa
            [
                'product_id' => 12,
                'batch_number' => 'SHA012-2024',
                'manufacturing_date' => '2024-03-15',
                'expiry_date' => '2025-03-15',
                'quantity' => 100,
                'cost_price' => 8.90,
                'selling_price' => 16.50,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Vitamina C
            [
                'product_id' => 13,
                'batch_number' => 'VTC013-2024',
                'manufacturing_date' => '2024-02-10',
                'expiry_date' => '2025-02-10',
                'quantity' => 250,
                'cost_price' => 1.50,
                'selling_price' => 3.20,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Aspirina
            [
                'product_id' => 14,
                'batch_number' => 'ASP014-2024',
                'manufacturing_date' => '2024-01-30',
                'expiry_date' => '2026-01-30',
                'quantity' => 900,
                'cost_price' => 0.18,
                'selling_price' => 0.40,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            // Hidrocortisona
            [
                'product_id' => 15,
                'batch_number' => 'HID015-2024',
                'manufacturing_date' => '2024-03-05',
                'expiry_date' => '2025-03-05',
                'quantity' => 180,
                'cost_price' => 4.20,
                'selling_price' => 8.90,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('product_batches')->insertBatch($productBatches);

        // 10. Stock Levels
        $stockLevels = [
            // Depósito Principal
            ['product_id' => 1, 'warehouse_id' => 1, 'batch_id' => 1, 'quantity' => 800, 'reorder_level' => 100],
            ['product_id' => 2, 'warehouse_id' => 1, 'batch_id' => 2, 'quantity' => 400, 'reorder_level' => 50],
            ['product_id' => 3, 'warehouse_id' => 1, 'batch_id' => 3, 'quantity' => 600, 'reorder_level' => 80],
            ['product_id' => 4, 'warehouse_id' => 1, 'batch_id' => 4, 'quantity' => 450, 'reorder_level' => 60],
            ['product_id' => 5, 'warehouse_id' => 1, 'batch_id' => 5, 'quantity' => 150, 'reorder_level' => 20],
            ['product_id' => 6, 'warehouse_id' => 1, 'batch_id' => 6, 'quantity' => 300, 'reorder_level' => 40],
            ['product_id' => 7, 'warehouse_id' => 1, 'batch_id' => 7, 'quantity' => 1000, 'reorder_level' => 120],
            ['product_id' => 8, 'warehouse_id' => 1, 'batch_id' => 8, 'quantity' => 250, 'reorder_level' => 30],
            ['product_id' => 9, 'warehouse_id' => 1, 'batch_id' => 9, 'quantity' => 550, 'reorder_level' => 70],
            ['product_id' => 10, 'warehouse_id' => 1, 'batch_id' => 10, 'quantity' => 400, 'reorder_level' => 50],
            
            // Loja - Balcão
            ['product_id' => 1, 'warehouse_id' => 3, 'batch_id' => 1, 'quantity' => 150, 'reorder_level' => 20],
            ['product_id' => 3, 'warehouse_id' => 3, 'batch_id' => 3, 'quantity' => 120, 'reorder_level' => 15],
            ['product_id' => 6, 'warehouse_id' => 3, 'batch_id' => 6, 'quantity' => 80, 'reorder_level' => 10],
            ['product_id' => 7, 'warehouse_id' => 3, 'batch_id' => 7, 'quantity' => 180, 'reorder_level' => 25],
            ['product_id' => 11, 'warehouse_id' => 3, 'batch_id' => 11, 'quantity' => 120, 'reorder_level' => 15],
            ['product_id' => 12, 'warehouse_id' => 3, 'batch_id' => 12, 'quantity' => 80, 'reorder_level' => 10],
            ['product_id' => 13, 'warehouse_id' => 3, 'batch_id' => 13, 'quantity' => 200, 'reorder_level' => 25],
            ['product_id' => 14, 'warehouse_id' => 3, 'batch_id' => 14, 'quantity' => 160, 'reorder_level' => 20],
            ['product_id' => 15, 'warehouse_id' => 3, 'batch_id' => 15, 'quantity' => 140, 'reorder_level' => 18]
        ];
        $this->db->table('stock_levels')->insertBatch($stockLevels);

        // 11. Stock Movements
        $stockMovements = [
            [
                'product_id' => 1,
                'warehouse_id' => 1,
                'batch_id' => 1,
                'type' => 'IN',
                'quantity' => 1000,
                'description' => 'Entrada inicial - compra',
                'movement_date' => '2024-01-20',
                'created_at' => Time::now()
            ],
            [
                'product_id' => 1,
                'warehouse_id' => 3,
                'batch_id' => 1,
                'type' => 'TRANSFER',
                'quantity' => 200,
                'description' => 'Transferência para loja',
                'source_warehouse_id' => 1,
                'destination_warehouse_id' => 3,
                'movement_date' => '2024-01-25',
                'created_at' => Time::now()
            ],
            [
                'product_id' => 1,
                'warehouse_id' => 3,
                'batch_id' => 1,
                'type' => 'OUT',
                'quantity' => 50,
                'description' => 'Venda',
                'movement_date' => '2024-02-01',
                'created_at' => Time::now()
            ]
        ];
        $this->db->table('stock_movements')->insertBatch($stockMovements);

        // 12. Purchase Orders
        $purchaseOrders = [
            [
                'supplier_id' => 1,
                'order_date' => '2024-01-15',
                'expected_delivery_date' => '2024-01-25',
                'total_amount' => 2500.00,
                'status' => 'DELIVERED',
                'notes' => 'Pedido de reposição mensal',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'supplier_id' => 2,
                'order_date' => '2024-02-01',
                'expected_delivery_date' => '2024-02-10',
                'total_amount' => 1800.00,
                'status' => 'DELIVERED',
                'notes' => 'Antibióticos e anti-inflamatórios',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'supplier_id' => 3,
                'order_date' => '2024-03-01',
                'expected_delivery_date' => '2024-03-15',
                'total_amount' => 3200.00,
                'status' => 'PENDING',
                'notes' => 'Pedido de cosméticos e higiene',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('purchase_orders')->insertBatch($purchaseOrders);

        // 13. Purchase Order Items
        $purchaseOrderItems = [
            // Pedido 1
            ['purchase_order_id' => 1, 'product_id' => 1, 'quantity' => 1000, 'unit_price' => 0.15, 'total_price' => 150.00],
            ['purchase_order_id' => 1, 'product_id' => 3, 'quantity' => 800, 'unit_price' => 0.25, 'total_price' => 200.00],
            ['purchase_order_id' => 1, 'product_id' => 7, 'quantity' => 1200, 'unit_price' => 0.12, 'total_price' => 144.00],
            ['purchase_order_id' => 1, 'product_id' => 14, 'quantity' => 900, 'unit_price' => 0.18, 'total_price' => 162.00],
            
            // Pedido 2
            ['purchase_order_id' => 2, 'product_id' => 2, 'quantity' => 500, 'unit_price' => 0.80, 'total_price' => 400.00],
            ['purchase_order_id' => 2, 'product_id' => 8, 'quantity' => 300, 'unit_price' => 1.80, 'total_price' => 540.00],
            ['purchase_order_id' => 2, 'product_id' => 9, 'quantity' => 700, 'unit_price' => 0.35, 'total_price' => 245.00],
            
            // Pedido 3
            ['purchase_order_id' => 3, 'product_id' => 11, 'quantity' => 150, 'unit_price' => 12.50, 'total_price' => 1875.00],
            ['purchase_order_id' => 3, 'product_id' => 12, 'quantity' => 100, 'unit_price' => 8.90, 'total_price' => 890.00]
        ];
        $this->db->table('purchase_order_items')->insertBatch($purchaseOrderItems);

        // 14. Sales
        $sales = [
            [
                'customer_id' => 1,
                'user_id' => 2,
                'sale_date' => '2024-02-01',
                'subtotal' => 17.50,
                'discount_amount' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 17.50,
                'payment_method' => 'CASH',
                'notes' => 'Venda balcão',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'customer_id' => 2,
                'user_id' => 3,
                'sale_date' => '2024-02-05',
                'subtotal' => 45.60,
                'discount_amount' => 2.30,
                'tax_amount' => 0.00,
                'total_amount' => 43.30,
                'payment_method' => 'CARD',
                'notes' => 'Cliente fidelidade - desconto 5%',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'customer_id' => 3,
                'user_id' => 2,
                'sale_date' => '2024-02-10',
                'subtotal' => 28.90,
                'discount_amount' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => 28.90,
                'payment_method' => 'TRANSFER',
                'notes' => 'Pagamento PIX',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('sales')->insertBatch($sales);

        // 15. Sale Items
        $saleItems = [
            // Venda 1
            ['sale_id' => 1, 'product_id' => 1, 'batch_id' => 1, 'quantity' => 20, 'unit_price' => 0.35, 'total_price' => 7.00],
            ['sale_id' => 1, 'product_id' => 3, 'batch_id' => 3, 'quantity' => 10, 'unit_price' => 0.55, 'total_price' => 5.50],
            ['sale_id' => 1, 'product_id' => 6, 'batch_id' => 6, 'quantity' => 1, 'unit_price' => 4.50, 'total_price' => 4.50],
            
            // Venda 2
            ['sale_id' => 2, 'product_id' => 11, 'batch_id' => 11, 'quantity' => 1, 'unit_price' => 24.90, 'total_price' => 24.90],
            ['sale_id' => 2, 'product_id' => 12, 'batch_id' => 12, 'quantity' => 1, 'unit_price' => 16.50, 'total_price' => 16.50],
            ['sale_id' => 2, 'product_id' => 13, 'batch_id' => 13, 'quantity' => 1, 'unit_price' => 3.20, 'total_price' => 3.20],
            
            // Venda 3
            ['sale_id' => 3, 'product_id' => 7, 'batch_id' => 7, 'quantity' => 30, 'unit_price' => 0.28, 'total_price' => 8.40],
            ['sale_id' => 3, 'product_id' => 14, 'batch_id' => 14, 'quantity' => 25, 'unit_price' => 0.40, 'total_price' => 10.00],
            ['sale_id' => 3, 'product_id' => 15, 'batch_id' => 15, 'quantity' => 1, 'unit_price' => 8.90, 'total_price' => 8.90]
        ];
        $this->db->table('sale_items')->insertBatch($saleItems);

        // 16. Inventories
        $inventories = [
            [
                'warehouse_id' => 1,
                'user_id' => 1,
                'inventory_date' => '2024-01-31',
                'notes' => 'Inventário mensal - Depósito Principal',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'warehouse_id' => 3,
                'user_id' => 2,
                'inventory_date' => '2024-02-15',
                'notes' => 'Inventário quinzenal - Loja',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];
        $this->db->table('inventories')->insertBatch($inventories);

        // 17. Inventory Items
        $inventoryItems = [
            // Inventário 1 - Depósito Principal
            ['inventory_id' => 1, 'product_id' => 1, 'batch_id' => 1, 'expected_quantity' => 800, 'counted_quantity' => 798, 'difference' => -2],
            ['inventory_id' => 1, 'product_id' => 3, 'batch_id' => 3, 'expected_quantity' => 600, 'counted_quantity' => 602, 'difference' => 2],
            ['inventory_id' => 1, 'product_id' => 7, 'batch_id' => 7, 'expected_quantity' => 1000, 'counted_quantity' => 995, 'difference' => -5],
            
            // Inventário 2 - Loja
            ['inventory_id' => 2, 'product_id' => 1, 'batch_id' => 1, 'expected_quantity' => 150, 'counted_quantity' => 148, 'difference' => -2],
            ['inventory_id' => 2, 'product_id' => 11, 'batch_id' => 11, 'expected_quantity' => 120, 'counted_quantity' => 119, 'difference' => -1],
            ['inventory_id' => 2, 'product_id' => 13, 'batch_id' => 13, 'expected_quantity' => 200, 'counted_quantity' => 199, 'difference' => -1]
        ];
        $this->db->table('inventory_items')->insertBatch($inventoryItems);

        // 18. Activity Logs
        $activityLogs = [
            [
                'user_id' => 1,
                'action' => 'CREATE',
                'table_name' => 'purchase_orders',
                'record_id' => 1,
                'description' => 'Criou ordem de compra #1',
                'ip_address' => '192.168.1.100',
                'created_at' => Time::now()
            ],
            [
                'user_id' => 2,
                'action' => 'CREATE',
                'table_name' => 'sales',
                'record_id' => 1,
                'description' => 'Registrou venda #1',
                'ip_address' => '192.168.1.101',
                'created_at' => Time::now()
            ],
            [
                'user_id' => 1,
                'action' => 'CREATE',
                'table_name' => 'inventories',
                'record_id' => 1,
                'description' => 'Criou inventário #1',
                'ip_address' => '192.168.1.100',
                'created_at' => Time::now()
            ]
        ];
        $this->db->table('activity_logs')->insertBatch($activityLogs);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            echo "Erro ao popular o banco de dados\n";
            return false;
        } else {
            echo "Banco de dados populado com sucesso!\n";
            echo "Dados inseridos:\n";
            echo "- 3 Roles de usuário\n";
            echo "- 3 Usuários\n";
            echo "- 3 Clientes\n";
            echo "- 3 Fornecedores\n";
            echo "- 3 Armazéns\n";
            echo "- 8 Categorias de produtos\n";
            echo "- 5 Fabricantes\n";
            echo "- 15 Produtos\n";
            echo "- 15 Lotes de produtos\n";
            echo "- 19 Níveis de stock\n";
            echo "- 3 Movimentações de stock\n";
            echo "- 3 Ordens de compra\n";
            echo "- 9 Itens de compra\n";
            echo "- 3 Vendas\n";
            echo "- 9 Itens de venda\n";
            echo "- 2 Inventários\n";
            echo "- 6 Itens de inventário\n";
            echo "- 3 Logs de atividade\n";
            return true;
        }
    }
}