<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class ActivityLogController extends BaseController
{
    protected $activityModel;

    public function __construct()
    {
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
     * Lista logs de atividade
     */
    public function index()
    {
        if (!$this->hasPermission('logs_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para visualizar logs'
            ]);
        }

        // Filtros
        $filters = [];
        
        if ($this->request->getGet('user_id')) {
            $filters['user_id'] = $this->request->getGet('user_id');
        }
        
        if ($this->request->getGet('action')) {
            $filters['action'] = $this->request->getGet('action');
        }
        
        if ($this->request->getGet('table_name')) {
            $filters['table_name'] = $this->request->getGet('table_name');
        }
        
        if ($this->request->getGet('date_from')) {
            $filters['date_from'] = $this->request->getGet('date_from') . ' 00:00:00';
        }
        
        if ($this->request->getGet('date_to')) {
            $filters['date_to'] = $this->request->getGet('date_to') . ' 23:59:59';
        }

        // Paginação
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 50;

        $logs = $this->activityModel->getLogsWithUser($filters);

        // Aplicar paginação manual (ou usar o paginate do CI4)
        $total = count($logs);
        $offset = ($page - 1) * $perPage;
        $logs = array_slice($logs, $offset, $perPage);

        return $this->response->setJSON([
            'success' => true,
            'data' => $logs,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Busca logs de um registro específico
     */
    public function getRecordLogs($tableName, $recordId)
    {
        if (!$this->hasPermission('logs_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $logs = $this->activityModel->getRecordLogs($tableName, $recordId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Estatísticas de atividade
     */
    public function getStats()
    {
        if (!$this->hasPermission('logs_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $db = \Config\Database::connect();

        $dailyActivity = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM activity_logs 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ")->getResultArray();

        $topUsers = $db->query("
            SELECT u.first_name, u.last_name, COUNT(al.id) as activity_count
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            WHERE al.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY al.user_id
            ORDER BY activity_count DESC
            LIMIT 10
        ")->getResultArray();

        $topActions = $db->query("
            SELECT action, COUNT(*) as count
            FROM activity_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY action
            ORDER BY count DESC
            LIMIT 10
        ")->getResultArray();

        $todayCount = $db->query("
            SELECT COUNT(*) as count
            FROM activity_logs
            WHERE DATE(created_at) = CURDATE()
        ")->getRow()->count;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'daily_activity' => $dailyActivity,
                'top_users' => $topUsers,
                'top_actions' => $topActions,
                'today_count' => $todayCount
            ]
        ]);
    }

    /**
     * Exportar logs
     */
    public function export()
    {
        if (!$this->hasPermission('logs_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $format = $this->request->getGet('format') ?? 'csv';
        $filters = [];
        
        if ($this->request->getGet('user_id')) {
            $filters['user_id'] = $this->request->getGet('user_id');
        }
        
        if ($this->request->getGet('date_from')) {
            $filters['date_from'] = $this->request->getGet('date_from') . ' 00:00:00';
        }
        
        if ($this->request->getGet('date_to')) {
            $filters['date_to'] = $this->request->getGet('date_to') . ' 23:59:59';
        }

        $logs = $this->activityModel->getLogsWithUser($filters);

        if ($format === 'csv') {
            return $this->exportToCsv($logs);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Formato não suportado'
        ]);
    }

    /**
     * Exporta logs para CSV
     */
    private function exportToCsv($logs)
    {
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalhos
        fputcsv($output, [
            'Data/Hora',
            'Usuário',
            'Ação',
            'Tabela',
            'Registro ID',
            'IP'
        ]);
        
        // Dados
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['created_at'],
                $log['user_name'] ?? 'Sistema',
                $log['action'],
                $log['table_name'],
                $log['record_id'],
                $log['ip_address']
            ]);
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