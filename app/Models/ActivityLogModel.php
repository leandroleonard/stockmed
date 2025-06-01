<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    // Validation
    protected $validationRules = [
        'action' => 'required|max_length[100]',
        'table_name' => 'permit_empty|max_length[100]',
        'record_id' => 'permit_empty|integer'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Registra atividade
     */
    public function logActivity($userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null)
    {
        $request = \Config\Services::request();
        
        return $this->insert([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString()
        ]);
    }

    /**
     * Busca logs com detalhes do usuário
     */
    public function getLogsWithUser($filters = [])
    {
        $builder = $this->select('activity_logs.*, CONCAT(users.first_name, " ", users.last_name) as user_name')
                        ->join('users', 'users.id = activity_logs.user_id', 'left');

        if (isset($filters['user_id'])) {
            $builder->where('activity_logs.user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $builder->like('activity_logs.action', $filters['action']);
        }

        if (isset($filters['table_name'])) {
            $builder->where('activity_logs.table_name', $filters['table_name']);
        }

        if (isset($filters['date_from'])) {
            $builder->where('activity_logs.created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $builder->where('activity_logs.created_at <=', $filters['date_to']);
        }

        return $builder->orderBy('activity_logs.created_at', 'DESC')->findAll();
    }

    /**
     * Busca logs de um registro específico
     */
    public function getRecordLogs($tableName, $recordId)
    {
        return $this->select('activity_logs.*, CONCAT(users.first_name, " ", users.last_name) as user_name')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->where('activity_logs.table_name', $tableName)
                    ->where('activity_logs.record_id', $recordId)
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->findAll();
    }
}