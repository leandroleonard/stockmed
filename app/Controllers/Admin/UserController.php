<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $activityModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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
     * Lista todos os usuários
     */
    public function index()
    {
        // Verificar permissão
        if (!$this->hasPermission('users_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para visualizar usuários'
            ]);
        }

        $users = $this->userModel->getActiveUsers();

        return $this->response->setJSON([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Busca usuário por ID
     */
    public function show($id)
    {
        if (!$this->hasPermission('users_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $user = $this->userModel->getUserWithRole($id);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }

        // Remover senha do retorno
        unset($user['password_hash']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Cria novo usuário
     */
    public function create()
    {
        if (!$this->hasPermission('users_create')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para criar usuários'
            ]);
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'role_id' => 'required|integer',
            'phone' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password_hash' => $this->request->getPost('password'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => true
        ];

        $userId = $this->userModel->insert($data);

        if ($userId) {
            // Log criação
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Usuário criado: ' . $data['username'],
                'users',
                $userId,
                null,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuário criado com sucesso',
                'data' => ['id' => $userId]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar usuário'
        ]);
    }

    /**
     * Atualiza usuário
     */
    public function update($id)
    {
        if (!$this->hasPermission('users_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para editar usuários'
            ]);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'role_id' => 'required|integer',
            'phone' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'role_id' => $this->request->getPost('role_id')
        ];

        // Se senha foi fornecida, incluir
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = $password;
        }

        $updated = $this->userModel->update($id, $data);

        if ($updated) {
            // Log atualização
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Usuário atualizado: ' . $data['username'],
                'users',
                $id,
                $user,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar usuário'
        ]);
    }

    /**
     * Ativa/Desativa usuário
     */
    public function toggleStatus($id)
    {
        if (!$this->hasPermission('users_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }

        // Não permitir desativar próprio usuário
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível desativar seu próprio usuário'
            ]);
        }

        $newStatus = !$user['is_active'];
        $updated = $this->userModel->update($id, ['is_active' => $newStatus]);

        if ($updated) {
            $action = $newStatus ? 'ativado' : 'desativado';
            
            // Log alteração
            $this->activityModel->logActivity(
                session()->get('user_id'),
                "Usuário {$action}: " . $user['username'],
                'users',
                $id
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Usuário {$action} com sucesso"
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alterar status do usuário'
        ]);
    }

    /**
     * Busca usuários
     */
    public function search()
    {
        if (!$this->hasPermission('users_view')) {
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

        $users = $this->userModel->select('users.*, user_roles.name as role_name')
                                ->join('user_roles', 'user_roles.id = users.role_id')
                                ->like('users.username', $term)
                                ->orLike('users.first_name', $term)
                                ->orLike('users.last_name', $term)
                                ->orLike('users.email', $term)
                                ->where('users.is_active', true)
                                ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Busca roles disponíveis
     */
    public function getRoles()
    {
        if (!$this->hasPermission('users_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $roles = $this->roleModel->getActiveRoles();

        return $this->response->setJSON([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Perfil do usuário logado
     */
    public function profile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->getUserWithRole($userId);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }

        // Remover senha
        unset($user['password_hash']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Atualizar perfil
     */
    public function updateProfile()
    {
        $userId = session()->get('user_id');
        
        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'phone' => 'permit_empty|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone')
        ];

        $updated = $this->userModel->update($userId, $data);

        if ($updated) {
            // Atualizar sessão
            session()->set([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name']
            ]);

            // Log atualização
            $this->activityModel->logActivity(
                $userId,
                'Perfil atualizado',
                'users',
                $userId
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