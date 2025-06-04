<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
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
     * Exibe formulário de login
     */
    public function login()
    {
        // Se já estiver logado, redirecionar para dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Processa login
     */
    public function authenticate()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Verificar credenciais
        $user = $this->userModel->verifyCredentials($username, $password);

        if (!$user) {
            // Log tentativa de login falhada
            $this->activityModel->logActivity(
                null,
                'Login falhado: ' . $username,
                'users',
                null
            );

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ]);
        }

        // Buscar dados completos do usuário com role
        $userWithRole = $this->userModel->getUserWithRole($user['id']);

        // Criar sessão
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role_id' => $user['role_id'],
            'role_name' => $userWithRole['role_name'],
            'permissions' => json_decode($userWithRole['permissions'], true),
            'isLoggedIn' => true
        ];

        session()->set($sessionData);

        // Atualizar último login
        $this->userModel->updateLastLogin($user['id']);

        // Log login bem-sucedido
        $this->activityModel->logActivity(
            $user['id'],
            'Login realizado',
            'users',
            $user['id']
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'redirect' => base_url('/dashboard')
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $userId = session()->get('user_id');

        // Log logout
        if ($userId) {
            $this->activityModel->logActivity(
                $userId,
                'Logout realizado',
                'users',
                $userId
            );
        }

        // Destruir sessão
        session()->destroy();

        return redirect()->to('/login')->with('message', 'Logout realizado com sucesso');
    }

    /**
     * Verifica se usuário está autenticado
     */
    public function checkAuth()
    {
        $isLoggedIn = session()->get('isLoggedIn');
        
        return $this->response->setJSON([
            'authenticated' => (bool)$isLoggedIn,
            'user' => $isLoggedIn ? [
                'id' => session()->get('user_id'),
                'username' => session()->get('username'),
                'first_name' => session()->get('first_name'),
                'last_name' => session()->get('last_name'),
                'role_name' => session()->get('role_name')
            ] : null
        ]);
    }

    /**
     * Alterar senha
     */
    public function changePassword()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Não autorizado'
            ]);
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userId = session()->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Verificar senha atual
        $user = $this->userModel->find($userId);
        if (!password_verify($currentPassword, $user['password_hash'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Senha atual incorreta'
            ]);
        }

        // Atualizar senha
        $updated = $this->userModel->update($userId, [
            'password_hash' => $newPassword // O model já faz o hash automaticamente
        ]);

        if ($updated) {
            // Log alteração de senha
            $this->activityModel->logActivity(
                $userId,
                'Senha alterada',
                'users',
                $userId
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Senha alterada com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alterar senha'
        ]);
    }

    /**
     * Recuperar senha (enviar email)
     */
    public function forgotPassword()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email inválido',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email não encontrado'
            ]);
        }

        // Gerar token de recuperação
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Salvar token (você pode criar uma tabela password_resets)
        // Por enquanto, vamos simular o envio do email
        
        // Log tentativa de recuperação
        $this->activityModel->logActivity(
            null,
            'Recuperação de senha solicitada: ' . $email,
            'users',
            $user['id']
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Instruções de recuperação enviadas para seu email'
        ]);
    }
}