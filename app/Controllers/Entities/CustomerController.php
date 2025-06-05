<?php

namespace App\Controllers\Entities;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\ActivityLogModel;

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $activityModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
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
     * Lista todos os clientes
     */
    public function index()
    {
        if (!$this->hasPermission('customers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para visualizar clientes'
            ]);
        }

        // Filtros
        $filters = [];
        
        if ($this->request->getGet('status')) {
            $filters['is_active'] = $this->request->getGet('status') === 'active';
        }
        
        if ($this->request->getGet('type')) {
            $filters['customer_type'] = $this->request->getGet('type');
        }

        // Paginação
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;

        $customers = $this->customerModel->getCustomersWithFilters($filters, $page, $perPage);
        $total = $this->customerModel->getCustomersCount($filters);

        return $this->response->setJSON([
            'success' => true,
            'data' => $customers,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Busca cliente por ID
     */
    public function show($id)
    {
        if (!$this->hasPermission('customers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $customer
        ]);
    }

    /**
     * Cria novo cliente
     */
    public function create()
    {
        if (!$this->hasPermission('customers_create')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para criar clientes'
            ]);
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'customer_type' => 'required|in_list[individual,company]',
            'document_number' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'mobile' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|string',
            'city' => 'permit_empty|max_length[100]',
            'state' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'country' => 'permit_empty|max_length[100]',
            'birth_date' => 'permit_empty|valid_date',
            'gender' => 'permit_empty|in_list[M,F,Other]',
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
            $existing = $this->customerModel->where('document_number', $documentNumber)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Já existe um cliente com este documento'
                ]);
            }
        }

        $data = [
            'customer_code' => $this->customerModel->generateCustomerCode(),
            'name' => $this->request->getPost('name'),
            'customer_type' => $this->request->getPost('customer_type'),
            'document_number' => $documentNumber,
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'mobile' => $this->request->getPost('mobile'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country') ?: 'Brasil',
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender' => $this->request->getPost('gender'),
            'notes' => $this->request->getPost('notes'),
            'is_active' => true
        ];

        $customerId = $this->customerModel->insert($data);

        if ($customerId) {
            // Log criação
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Cliente criado: ' . $data['name'],
                'customers',
                $customerId,
                null,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente criado com sucesso',
                'data' => ['id' => $customerId, 'customer_code' => $data['customer_code']]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar cliente'
        ]);
    }

    /**
     * Atualiza cliente
     */
    public function update($id)
    {
        if (!$this->hasPermission('customers_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para editar clientes'
            ]);
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ]);
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'customer_type' => 'required|in_list[individual,company]',
            'document_number' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'mobile' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|string',
            'city' => 'permit_empty|max_length[100]',
            'state' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'country' => 'permit_empty|max_length[100]',
            'birth_date' => 'permit_empty|valid_date',
            'gender' => 'permit_empty|in_list[M,F,Other]',
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
            $existing = $this->customerModel->where('document_number', $documentNumber)
                                          ->where('id !=', $id)
                                          ->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Já existe outro cliente com este documento'
                ]);
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'customer_type' => $this->request->getPost('customer_type'),
            'document_number' => $documentNumber,
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'mobile' => $this->request->getPost('mobile'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country'),
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender' => $this->request->getPost('gender'),
            'notes' => $this->request->getPost('notes')
        ];

        $updated = $this->customerModel->update($id, $data);

        if ($updated) {
            // Log atualização
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Cliente atualizado: ' . $data['name'],
                'customers',
                $id,
                $customer,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente atualizado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar cliente'
        ]);
    }

    /**
     * Ativa/Desativa cliente
     */
    public function toggleStatus($id)
    {
        if (!$this->hasPermission('customers_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ]);
        }

        $newStatus = !$customer['is_active'];
        $updated = $this->customerModel->update($id, ['is_active' => $newStatus]);

        if ($updated) {
            $action = $newStatus ? 'ativado' : 'desativado';
            
            // Log alteração
            $this->activityModel->logActivity(
                session()->get('user_id'),
                "Cliente {$action}: " . $customer['name'],
                'customers',
                $id
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Cliente {$action} com sucesso"
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alterar status do cliente'
        ]);
    }

    /**
     * Remove cliente (soft delete)
     */
    public function delete($id)
    {
        if (!$this->hasPermission('customers_delete')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para excluir clientes'
            ]);
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ]);
        }

        // Verificar se cliente tem vendas
        $salesModel = new \App\Models\SaleModel();
        $salesCount = $salesModel->where('customer_id', $id)->countAllResults();

        if ($salesCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Não é possível excluir. Cliente possui {$salesCount} venda(s) registrada(s)."
            ]);
        }

        $deleted = $this->customerModel->delete($id);

        if ($deleted) {
            // Log exclusão
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Cliente excluído: ' . $customer['name'],
                'customers',
                $id,
                $customer,
                null
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente excluído com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir cliente'
        ]);
    }

    /**
     * Busca clientes
     */
    public function search()
    {
        if (!$this->hasPermission('customers_view')) {
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

        $customers = $this->customerModel->searchCustomers($term);

        return $this->response->setJSON([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Histórico de compras do cliente
     */
    public function purchaseHistory($id)
    {
        if (!$this->hasPermission('customers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ]);
        }

        $salesModel = new \App\Models\SaleModel();
        $sales = $salesModel->getCustomerSales($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'customer' => $customer,
                'sales' => $sales
            ]
        ]);
    }

    /**
     * Estatísticas do cliente
     */
    public function getStats($id)
    {
        if (!$this->hasPermission('customers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $stats = $this->customerModel->getCustomerStats($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Exportar clientes
     */
    public function export()
    {
        if (!$this->hasPermission('customers_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $format = $this->request->getGet('format') ?? 'csv';
        $customers = $this->customerModel->where('is_active', true)->findAll();

        if ($format === 'csv') {
            return $this->exportToCsv($customers);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Formato não suportado'
        ]);
    }

    /**
     * Exporta para CSV
     */
    private function exportToCsv($customers)
    {
        $filename = 'clientes_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalhos
        fputcsv($output, [
            'Código',
            'Nome',
            'Tipo',
            'Documento',
            'Email',
            'Telefone',
            'Celular',
            'Cidade',
            'Estado',
            'Data Cadastro'
        ], ';');
        
        // Dados
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer['customer_code'],
                $customer['name'],
                $customer['customer_type'] === 'individual' ? 'Pessoa Física' : 'Pessoa Jurídica',
                $customer['document_number'],
                $customer['email'],
                $customer['phone'],
                $customer['mobile'],
                $customer['city'],
                $customer['state'],
                date('d/m/Y', strtotime($customer['created_at']))
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