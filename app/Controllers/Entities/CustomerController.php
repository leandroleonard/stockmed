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
        $request = service('request');
        $wantsJson = strpos($request->getHeaderLine('Accept'), 'application/json') !== false;

        if (!$this->hasPermission('customers_create')) {
            if ($wantsJson) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Sem permissão para criar clientes'
                ]);
            } else {
                // Redireciona com mensagem de erro
                return redirect()->back()->with('error', 'Sem permissão para criar clientes');
            }
        }


        $rules = [
            'first_name' => 'required|max_length[255]',
            'last_name' => 'required|max_length[255]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[9]',
            'address' => 'permit_empty|string',
            'city' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            if ($wantsJson) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $this->validator->getErrors()
                ]);
            } else {
                echo "<pre>";

                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }



        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'notes' => $this->request->getPost('notes'),
            'is_active' => true
        ];

        $customerId = $this->customerModel->insert($data);

        // exit(var_dump($this->customerModel->errors()));

        if ($customerId) {
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Cliente criado: ' . $data['first_name'] . '' . $data['last_name'],
                'customers',
                $customerId,
                null,
                $data
            );

            if ($wantsJson) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Cliente criado com sucesso',
                    'data' => ['id' => $customerId, 'customer_code' => $data['customer_code']]
                ]);
            } else {

                return redirect()->to(base_url('dashboard/clients/') . $data['customer_code'])->withInput()->with('success', 'Cliente criado com sucesso!');
            }
        }

        if ($wantsJson) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar cliente'
            ]);
        } else {
            // Redireciona com mensagem de erro
            return redirect()->back()->withInput()->with('error', 'Erro ao criar cliente');
        }
    }

    /**
     * Atualiza cliente
     */
    public function update()
    {
        $request = service('request');
        $wantsJson = strpos($request->getHeaderLine('Accept'), 'application/json') !== false;

        if (!$this->hasPermission('customers_edit')) {
            if ($wantsJson) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Sem permissão para editar clientes'
                ]);
            } else {
                // Redireciona com mensagem de erro
                return redirect()->back()->with('error', 'Sem permissão para editar clientes');
            }
        }

        $id = $request->getPost('id');
        // echo "<pre>";
        // exit(var_dump($id));
        $customer = $this->customerModel->where('customer_code', $id)->first();
        if (!$customer) {
            if ($wantsJson) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ]);
            } else {
                return redirect()->back()->with('error', 'Cliente não encontrado');
            }
        }

        $rules = [
            'first_name' => 'required|max_length[255]',
            'last_name' => 'required|max_length[255]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[9]',
            'address' => 'permit_empty|string',
            'city' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            if ($wantsJson) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $this->validator->getErrors()
                ]);
            } else {
                echo "<pre>";

                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }


        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'notes' => $this->request->getPost('notes'),
        ];

        $updated = $this->customerModel->update($customer['id'], $data);

        if ($updated) {
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Cliente atualizado: ' . $data['first_name'],
                'customers',
                $id,
                $customer,
                $data
            );

            if ($wantsJson) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Cliente atualizado com sucesso',
                ]);
            } else {
                return redirect()->to(base_url('dashboard/clients/') . $customer['customer_code'])->withInput()->with('success', 'Cliente atualizado com sucesso!');
            }
        }

        if ($wantsJson) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao actualizar cliente'
            ]);
        } else {
            // Redireciona com mensagem de erro
            return redirect()->back()->withInput()->with('error', 'Erro ao actualizar cliente');
        }
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
                "Cliente {$action}: " . $customer['first_name'],
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
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
