<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserRoleModel;
use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserRoleController extends BaseController
{
    protected $roleModel;
    protected $activityModel;

    public function __construct()
    {
        $this->roleModel = new UserRoleModel();
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
     * Lista todos os roles
     */
    public function index()
    {
        if (!$this->hasPermission('roles_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para visualizar perfis'
            ]);
        }

        $roles = $this->roleModel->getActiveRoles();

        return $this->response->setJSON([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Busca role por ID
     */
    public function show($id)
    {
        if (!$this->hasPermission('roles_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $role = $this->roleModel->find($id);

        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $role
        ]);
    }

    /**
     * Cria novo role
     */
    public function create()
    {
        if (!$this->hasPermission('roles_create')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para criar perfis'
            ]);
        }

        $rules = [
            'name' => 'required|max_length[50]|is_unique[user_roles.name]',
            'description' => 'permit_empty|string',
            'permissions' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $permissions = $this->request->getPost('permissions');
        
        // Validar permissões
        if (!is_array($permissions) || empty($permissions)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pelo menos uma permissão deve ser selecionada'
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions)
        ];

        $roleId = $this->roleModel->insert($data);

        if ($roleId) {
            // Log criação
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Perfil criado: ' . $data['name'],
                'user_roles',
                $roleId,
                null,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Perfil criado com sucesso',
                'data' => ['id' => $roleId]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar perfil'
        ]);
    }

    /**
     * Atualiza role
     */
    public function update($id)
    {
        if (!$this->hasPermission('roles_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para editar perfis'
            ]);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ]);
        }

        // Não permitir editar role de Administrador (ID 1)
        if ($id == 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível editar o perfil de Administrador'
            ]);
        }

        $rules = [
            'name' => "required|max_length[50]|is_unique[user_roles.name,id,{$id}]",
            'description' => 'permit_empty|string',
            'permissions' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $permissions = $this->request->getPost('permissions');
        
        if (!is_array($permissions) || empty($permissions)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pelo menos uma permissão deve ser selecionada'
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions)
        ];

        $updated = $this->roleModel->update($id, $data);

        if ($updated) {
            // Log atualização
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Perfil atualizado: ' . $data['name'],
                'user_roles',
                $id,
                $role,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar perfil'
        ]);
    }

    /**
     * Remove role
     */
    public function delete($id)
    {
        if (!$this->hasPermission('roles_delete')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para excluir perfis'
            ]);
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ]);
        }

        // Não permitir excluir roles padrão
        if (in_array($id, [1, 2, 3, 4])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível excluir perfis padrão do sistema'
            ]);
        }

        // Verificar se há usuários usando este role
        $userModel = new \App\Models\UserModel();
        $usersCount = $userModel->where('role_id', $id)->countAllResults();

        if ($usersCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Não é possível excluir. Existem {$usersCount} usuário(s) com este perfil."
            ]);
        }

        $deleted = $this->roleModel->delete($id);

        if ($deleted) {
            // Log exclusão
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Perfil excluído: ' . $role['name'],
                'user_roles',
                $id,
                $role,
                null
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Perfil excluído com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir perfil'
        ]);
    }

    /**
     * Lista todas as permissões disponíveis
     */
    public function getAvailablePermissions()
    {
        if (!$this->hasPermission('roles_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $permissions = [
            // Usuários
            'users_view' => 'Visualizar usuários',
            'users_create' => 'Criar usuários',
            'users_edit' => 'Editar usuários',
            'users_delete' => 'Excluir usuários',
            
            // Perfis
            'roles_view' => 'Visualizar perfis',
            'roles_create' => 'Criar perfis',
            'roles_edit' => 'Editar perfis',
            'roles_delete' => 'Excluir perfis',
            
            // Clientes
            'customers_view' => 'Visualizar clientes',
            'customers_create' => 'Criar clientes',
            'customers_edit' => 'Editar clientes',
            'customers_delete' => 'Excluir clientes',
            
            // Fornecedores
            'suppliers_view' => 'Visualizar fornecedores',
            'suppliers_create' => 'Criar fornecedores',
            'suppliers_edit' => 'Editar fornecedores',
            'suppliers_delete' => 'Excluir fornecedores',
            
            // Armazéns
            'warehouses_view' => 'Visualizar armazéns',
            'warehouses_create' => 'Criar armazéns',
            'warehouses_edit' => 'Editar armazéns',
            'warehouses_delete' => 'Excluir armazéns',
            
            // Produtos
            'products_view' => 'Visualizar produtos',
            'products_create' => 'Criar produtos',
            'products_edit' => 'Editar produtos',
            'products_delete' => 'Excluir produtos',
            
            // Stock
            'stock_view' => 'Visualizar stock',
            'stock_edit' => 'Editar stock',
            'stock_movements' => 'Movimentações de stock',
            'stock_adjustments' => 'Ajustes de stock',
            
            // Compras
            'purchases_view' => 'Visualizar compras',
            'purchases_create' => 'Criar pedidos de compra',
            'purchases_edit' => 'Editar pedidos de compra',
            'purchases_approve' => 'Aprovar pedidos de compra',
            'purchases_receive' => 'Receber mercadorias',
            
            // Vendas
            'sales_view' => 'Visualizar vendas',
            'sales_create' => 'Realizar vendas',
            'sales_edit' => 'Editar vendas',
            'sales_cancel' => 'Cancelar vendas',
            
            // Inventários
            'inventories_view' => 'Visualizar inventários',
            'inventories_create' => 'Criar inventários',
            'inventories_edit' => 'Editar inventários',
            'inventories_count' => 'Realizar contagens',
            
            // Relatórios
            'reports_sales' => 'Relatórios de vendas',
            'reports_stock' => 'Relatórios de stock',
            'reports_financial' => 'Relatórios financeiros',
            'reports_audit' => 'Relatórios de auditoria',
            
            // Configurações
            'settings_view' => 'Visualizar configurações',
            'settings_edit' => 'Editar configurações',
            
            // Logs
            'logs_view' => 'Visualizar logs de atividade',
            
            // Acesso total
            'all' => 'Acesso total ao sistema'
        ];

        // Agrupar permissões por módulo
        $groupedPermissions = [
            'Usuários e Perfis' => [
                'users_view', 'users_create', 'users_edit', 'users_delete',
                'roles_view', 'roles_create', 'roles_edit', 'roles_delete'
            ],
            'Entidades' => [
                'customers_view', 'customers_create', 'customers_edit', 'customers_delete',
                'suppliers_view', 'suppliers_create', 'suppliers_edit', 'suppliers_delete',
                'warehouses_view', 'warehouses_create', 'warehouses_edit', 'warehouses_delete'
            ],
            'Produtos' => [
                'products_view', 'products_create', 'products_edit', 'products_delete'
            ],
            'Stock' => [
                'stock_view', 'stock_edit', 'stock_movements', 'stock_adjustments'
            ],
            'Compras' => [
                'purchases_view', 'purchases_create', 'purchases_edit', 'purchases_approve', 'purchases_receive'
            ],
            'Vendas' => [
                'sales_view', 'sales_create', 'sales_edit', 'sales_cancel'
            ],
            'Inventários' => [
                'inventories_view', 'inventories_create', 'inventories_edit', 'inventories_count'
            ],
            'Relatórios' => [
                'reports_sales', 'reports_stock', 'reports_financial', 'reports_audit'
            ],
            'Sistema' => [
                'settings_view', 'settings_edit', 'logs_view', 'all'
            ]
        ];

        $result = [];
        foreach ($groupedPermissions as $group => $perms) {
            $result[$group] = [];
            foreach ($perms as $perm) {
                $result[$group][$perm] = $permissions[$perm];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Verifica se usuário tem permissão específica
     */
    public function checkPermission($permission)
    {
        $hasPermission = $this->hasPermission($permission);

        return $this->response->setJSON([
            'success' => true,
            'has_permission' => $hasPermission
        ]);
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