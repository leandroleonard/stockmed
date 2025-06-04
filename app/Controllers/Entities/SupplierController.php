<?php

namespace App\Controllers\Entities;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $activityModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->activityModel = new ActivityLogModel();
    }

    /**
     * Middleware para verificar autenticação
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        if (!session()->get('isLoggedIn')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acesso negado');
        }
    }

    /**
     * Lista todos os fornecedores
     */
    public function index()
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para visualizar fornecedores'
            ]);
        }

        // Filtros
        $filters = [];
        
        if ($this->request->getGet('status')) {
            $filters['is_active'] = $this->request->getGet('status') === 'active';
        }

        // Paginação
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;

        $suppliers = $this->supplierModel->getSuppliersWithFilters($filters, $page, $perPage);
        $total = $this->supplierModel->getSuppliersCount($filters);

        return $this->response->setJSON([
            'success' => true,
            'data' => $suppliers,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Busca fornecedor por ID
     */
    public function show($id)
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $supplier = $this->supplierModel->find($id);

        if (!$supplier) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $supplier
        ]);
    }

    /**
     * Cria novo fornecedor
     */
    public function create()
    {
        if (!$this->hasPermission('suppliers_create')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para criar fornecedores'
            ]);
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'company_name' => 'permit_empty|max_length[255]',
            'document_number' => 'permit_empty|max_length[20]',
            'tax_id' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'mobile' => 'permit_empty|max_length[20]',
            'website' => 'permit_empty|valid_url|max_length[255]',
            'address' => 'permit_empty|string',
            'city' => 'permit_empty|max_length[100]',
            'state' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'country' => 'permit_empty|max_length[100]',
            'contact_person' => 'permit_empty|max_length[255]',
            'contact_phone' => 'permit_empty|max_length[20]',
            'contact_email' => 'permit_empty|valid_email|max_length[255]',
            'payment_terms' => 'permit_empty|string',
            'credit_limit' => 'permit_empty|decimal',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Verificar se documento já existe (se fornecido)
        $documentNumber = $this->request->getPost('document_number');
        if (!empty($documentNumber)) {
            $existing = $this->supplierModel->where('document_number', $documentNumber)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Já existe um fornecedor com este documento'
                ]);
            }
        }

        $data = [
            'supplier_code' => $this->supplierModel->generateSupplierCode(),
            'name' => $this->request->getPost('name'),
            'company_name' => $this->request->getPost('company_name'),
            'document_number' => $documentNumber,
            'tax_id' => $this->request->getPost('tax_id'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'mobile' => $this->request->getPost('mobile'),
            'website' => $this->request->getPost('website'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country') ?: 'Brasil',
            'contact_person' => $this->request->getPost('contact_person'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'contact_email' => $this->request->getPost('contact_email'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'credit_limit' => $this->request->getPost('credit_limit') ?: 0,
            'notes' => $this->request->getPost('notes'),
            'is_active' => true
        ];

        $supplierId = $this->supplierModel->insert($data);

        if ($supplierId) {
            // Log criação
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Fornecedor criado: ' . $data['name'],
                'suppliers',
                $supplierId,
                null,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fornecedor criado com sucesso',
                'data' => ['id' => $supplierId, 'supplier_code' => $data['supplier_code']]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar fornecedor'
        ]);
    }

    /**
     * Atualiza fornecedor
     */
    public function update($id)
    {
        if (!$this->hasPermission('suppliers_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para editar fornecedores'
            ]);
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ]);
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'company_name' => 'permit_empty|max_length[255]',
            'document_number' => 'permit_empty|max_length[20]',
            'tax_id' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'mobile' => 'permit_empty|max_length[20]',
            'website' => 'permit_empty|valid_url|max_length[255]',
            'address' => 'permit_empty|string',
            'city' => 'permit_empty|max_length[100]',
            'state' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'country' => 'permit_empty|max_length[100]',
            'contact_person' => 'permit_empty|max_length[255]',
            'contact_phone' => 'permit_empty|max_length[20]',
            'contact_email' => 'permit_empty|valid_email|max_length[255]',
            'payment_terms' => 'permit_empty|string',
            'credit_limit' => 'permit_empty|decimal',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Verificar documento duplicado
        $documentNumber = $this->request->getPost('document_number');
        if (!empty($documentNumber)) {
            $existing = $this->supplierModel->where('document_number', $documentNumber)
                                          ->where('id !=', $id)
                                          ->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Já existe outro fornecedor com este documento'
                ]);
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'company_name' => $this->request->getPost('company_name'),
            'document_number' => $documentNumber,
            'tax_id' => $this->request->getPost('tax_id'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'mobile' => $this->request->getPost('mobile'),
            'website' => $this->request->getPost('website'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country'),
            'contact_person' => $this->request->getPost('contact_person'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'contact_email' => $this->request->getPost('contact_email'),
            'payment_terms' => $this->request->getPost('payment_terms'),
            'credit_limit' => $this->request->getPost('credit_limit') ?: 0,
            'notes' => $this->request->getPost('notes')
        ];

        $updated = $this->supplierModel->update($id, $data);

        if ($updated) {
            // Log atualização
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Fornecedor atualizado: ' . $data['name'],
                'suppliers',
                $id,
                $supplier,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fornecedor atualizado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar fornecedor'
        ]);
    }

    /**
     * Ativa/Desativa fornecedor
     */
    public function toggleStatus($id)
    {
        if (!$this->hasPermission('suppliers_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ]);
        }

        $newStatus = !$supplier['is_active'];
        $updated = $this->supplierModel->update($id, ['is_active' => $newStatus]);

        if ($updated) {
            $action = $newStatus ? 'ativado' : 'desativado';
            
            // Log alteração
            $this->activityModel->logActivity(
                session()->get('user_id'),
                "Fornecedor {$action}: " . $supplier['name'],
                'suppliers',
                $id
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Fornecedor {$action} com sucesso"
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alterar status do fornecedor'
        ]);
    }

    /**
     * Remove fornecedor (soft delete)
     */
    public function delete($id)
    {
        if (!$this->hasPermission('suppliers_delete')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para excluir fornecedores'
            ]);
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ]);
        }

        // Verificar se fornecedor tem pedidos de compra
        $purchaseModel = new \App\Models\PurchaseOrderModel();
        $purchasesCount = $purchaseModel->where('supplier_id', $id)->countAllResults();

        if ($purchasesCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Não é possível excluir. Fornecedor possui {$purchasesCount} pedido(s) de compra."
            ]);
        }

        // Verificar se tem produtos vinculados
        $productModel = new \App\Models\ProductModel();
        $productsCount = $productModel->where('supplier_id', $id)->countAllResults();

        if ($productsCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Não é possível excluir. Fornecedor possui {$productsCount} produto(s) vinculado(s)."
            ]);
        }

        $deleted = $this->supplierModel->delete($id);

        if ($deleted) {
            // Log exclusão
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Fornecedor excluído: ' . $supplier['name'],
                'suppliers',
                $id,
                $supplier,
                null
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fornecedor excluído com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir fornecedor'
        ]);
    }

    /**
     * Busca fornecedores
     */
    public function search()
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $term = $this->request->getGet('term');
        
        if (empty($term)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Termo de busca é obrigatório'
            ]);
        }

        $suppliers = $this->supplierModel->searchSuppliers($term);

        return $this->response->setJSON([
            'success' => true,
            'data' => $suppliers
        ]);
    }

    /**
     * Histórico de compras do fornecedor
     */
    public function purchaseHistory($id)
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ]);
        }

        $purchaseModel = new \App\Models\PurchaseOrderModel();
        $purchases = $purchaseModel->getSupplierPurchases($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'supplier' => $supplier,
                'purchases' => $purchases
            ]
        ]);
    }

    /**
     * Produtos do fornecedor
     */
    public function getProducts($id)
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ]);
        }

        $productModel = new \App\Models\ProductModel();
        $products = $productModel->getSupplierProducts($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'supplier' => $supplier,
                'products' => $products
            ]
        ]);
    }

    /**
     * Estatísticas do fornecedor
     */
    public function getStats($id)
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $stats = $this->supplierModel->getSupplierStats($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Exportar fornecedores
     */
    public function export()
    {
        if (!$this->hasPermission('suppliers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $format = $this->request->getGet('format') ?? 'csv';
        $suppliers = $this->supplierModel->where('is_active', true)->findAll();

        if ($format === 'csv') {
            return $this->exportToCsv($suppliers);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Formato não suportado'
        ]);
    }

    /**
     * Exporta para CSV
     */
    private function exportToCsv($suppliers)
    {
        $filename = 'fornecedores_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalhos
        fputcsv($output, [
            'Código',
            'Nome',
            'Empresa',
            'Documento',
            'Email',
            'Telefone',
            'Pessoa de Contato',
            'Cidade',
            'Estado',
            'Limite de Crédito',
            'Data Cadastro'
        ], ';');
        
        // Dados
        foreach ($suppliers as $supplier) {
            fputcsv($output, [
                $supplier['supplier_code'],
                $supplier['name'],
                $supplier['company_name'],
                $supplier['document_number'],
                $supplier['email'],
                $supplier['phone'],
                $supplier['contact_person'],
                $supplier['city'],
                $supplier['state'],
                number_format($supplier['credit_limit'], 2, ',', '.'),
                date('d/m/Y', strtotime($supplier['created_at']))
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Verifica se usuário tem permissão
     */
    private function hasPermission($permission)
    {
        $permissions = session()->get('permissions');
        
        if (!$permissions) {
            return false;
        }

        return in_array('all', $permissions) || in_array($permission, $permissions);
    }
}