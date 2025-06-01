
-- =============================================
-- Stockmed
-- Esquema do Banco de Dados 
-- =============================================

-- Criação da bd
CREATE DATABASE IF NOT EXISTS stockmed_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockmed_db;

-- =============================================
-- TABELAS DE CONFIGURAÇÃO E USUÁRIOS
-- =============================================

-- Tabela de perfis/roles de usuários
CREATE TABLE user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de usuários do sistema
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES user_roles(id)
);

-- =============================================
-- TABELAS DE ENTIDADES PRINCIPAIS
-- =============================================

-- Tabela de clientes
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(20) UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    birth_date DATE,
    tax_number VARCHAR(50),
    insurance_number VARCHAR(50),
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de fornecedores
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_code VARCHAR(20) UNIQUE,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    tax_number VARCHAR(50),
    payment_terms VARCHAR(100),
    credit_limit DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de armazéns
CREATE TABLE warehouses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    warehouse_code VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    manager_id INT,
    capacity_limit INT,
    temperature_controlled BOOLEAN DEFAULT FALSE,
    min_temperature DECIMAL(5,2),
    max_temperature DECIMAL(5,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES users(id)
);

-- =============================================
-- TABELAS DE PRODUTOS FARMACÊUTICOS
-- =============================================

-- Tabela de categorias de produtos
CREATE TABLE product_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id)
);

-- Tabela de fabricantes
CREATE TABLE manufacturers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    country VARCHAR(100),
    license_number VARCHAR(100),
    contact_info JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela principal de produtos
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    barcode VARCHAR(100) UNIQUE,
    name VARCHAR(200) NOT NULL,
    generic_name VARCHAR(200),
    description TEXT,
    category_id INT,
    manufacturer_id INT,
    dosage VARCHAR(100),
    form VARCHAR(100), -- comprimido, xarope, injeção, etc.
    active_ingredient TEXT,
    concentration VARCHAR(100),
    pack_size INT DEFAULT 1,
    unit_of_measure VARCHAR(20) DEFAULT 'unidade',
    requires_prescription BOOLEAN DEFAULT FALSE,
    controlled_substance BOOLEAN DEFAULT FALSE,
    min_stock_level INT DEFAULT 0,
    max_stock_level INT DEFAULT 1000,
    reorder_point INT DEFAULT 10,
    storage_conditions TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(id)
);

-- Tabela de lotes de produtos
CREATE TABLE product_batches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    batch_number VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    manufacture_date DATE,
    expiry_date DATE NOT NULL,
    quantity_received INT NOT NULL,
    quantity_remaining INT NOT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    warehouse_id INT NOT NULL,
    location_in_warehouse VARCHAR(100),
    quality_status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    UNIQUE KEY unique_batch_product (batch_number, product_id)
);

-- =============================================
-- TABELAS DE GESTÃO DE STOCK
-- =============================================

-- Tabela de stock atual por armazém
CREATE TABLE stock_levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity_available INT NOT NULL DEFAULT 0,
    quantity_reserved INT NOT NULL DEFAULT 0,
    quantity_on_order INT NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    UNIQUE KEY unique_product_warehouse (product_id, warehouse_id)
);

-- Tabela de movimentações de stock
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    batch_id INT,
    warehouse_id INT NOT NULL,
    movement_type ENUM('entrada', 'saida', 'transferencia', 'ajuste', 'vencimento') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('compra', 'venda', 'transferencia', 'ajuste', 'devolucao', 'vencimento') NOT NULL,
    reference_id INT,
    cost_price DECIMAL(10,2),
    selling_price DECIMAL(10,2),
    notes TEXT,
    user_id INT NOT NULL,
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =============================================
-- TABELAS DE COMPRAS
-- =============================================

-- Tabela de pedidos de compra
CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    status ENUM('pendente', 'aprovado', 'enviado', 'recebido', 'cancelado') DEFAULT 'pendente',
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT,
    created_by INT NOT NULL,
    approved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Tabela de itens do pedido de compra
CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_cost DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(15,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =============================================
-- TABELAS DE VENDAS
-- =============================================

-- Tabela de vendas
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    warehouse_id INT NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('dinheiro', 'cartao', 'transferencia', 'cheque', 'credito') NOT NULL,
    payment_status ENUM('pago', 'pendente', 'parcial') DEFAULT 'pago',
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    prescription_number VARCHAR(100),
    doctor_name VARCHAR(200),
    notes TEXT,
    cashier_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id)
);

-- Tabela de itens da venda
CREATE TABLE sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    total_price DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id)
);

-- =============================================
-- TABELAS DE INVENTÁRIO
-- =============================================

-- Tabela de inventários
CREATE TABLE inventories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inventory_number VARCHAR(50) UNIQUE NOT NULL,
    warehouse_id INT NOT NULL,
    inventory_date DATE NOT NULL,
    status ENUM('planejado', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'planejado',
    notes TEXT,
    created_by INT NOT NULL,
    completed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (completed_by) REFERENCES users(id)
);

-- Tabela de itens do inventário
CREATE TABLE inventory_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inventory_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT,
    expected_quantity INT NOT NULL,
    counted_quantity INT,
    variance INT GENERATED ALWAYS AS (counted_quantity - expected_quantity) STORED,
    notes TEXT,
    counted_by INT,
    counted_at TIMESTAMP NULL,
    FOREIGN KEY (inventory_id) REFERENCES inventories(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES product_batches(id),
    FOREIGN KEY (counted_by) REFERENCES users(id)
);

-- =============================================
-- TABELAS DE AUDITORIA E LOGS
-- =============================================

-- Tabela de logs de atividades
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =============================================
-- ÍNDICES PARA PERFORMANCE
-- =============================================

-- Índices para produtos
CREATE INDEX idx_products_code ON products(product_code);
CREATE INDEX idx_products_barcode ON products(barcode);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_active ON products(is_active);

-- Índices para lotes
CREATE INDEX idx_batches_expiry ON product_batches(expiry_date);
CREATE INDEX idx_batches_product ON product_batches(product_id);
CREATE INDEX idx_batches_warehouse ON product_batches(warehouse_id);

-- Índices para movimentações
CREATE INDEX idx_movements_date ON stock_movements(movement_date);
CREATE INDEX idx_movements_product ON stock_movements(product_id);
CREATE INDEX idx_movements_type ON stock_movements(movement_type);

-- Índices para vendas
CREATE INDEX idx_sales_date ON sales(sale_date);
CREATE INDEX idx_sales_customer ON sales(customer_id);
CREATE INDEX idx_sales_number ON sales(sale_number);

-- Índices para compras
CREATE INDEX idx_purchases_date ON purchase_orders(order_date);
CREATE INDEX idx_purchases_supplier ON purchase_orders(supplier_id);
CREATE INDEX idx_purchases_status ON purchase_orders(status);

-- =============================================
-- DADOS INICIAIS
-- =============================================

-- Inserir roles padrão
INSERT INTO user_roles (name, description, permissions) VALUES
('Administrador', 'Acesso total ao sistema', '["all"]'),
('Gerente', 'Gestão de stock e relatórios', '["stock", "reports", "purchases", "sales"]'),
('Farmacêutico', 'Vendas e consulta de stock', '["sales", "stock_view", "customers"]'),
('Operador', 'Operações básicas de stock', '["stock_basic", "sales_basic"]');

-- Inserir usuário administrador padrão
INSERT INTO users (username, email, password_hash, first_name, last_name, role_id) VALUES
('admin', 'admin@stockmed.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 1);

-- Inserir categorias padrão
INSERT INTO product_categories (name, description) VALUES
('Medicamentos', 'Produtos farmacêuticos'),
('Genéricos', 'Medicamentos genéricos'),
('Cosméticos', 'Produtos de beleza e higiene'),
('Suplementos', 'Vitaminas e suplementos alimentares'),
('Material Médico', 'Equipamentos e materiais médicos');

-- Inserir armazém padrão
INSERT INTO warehouses (warehouse_code, name, description, is_active) VALUES
('ARM001', 'Armazém Principal', 'Armazém principal da farmácia', TRUE);

