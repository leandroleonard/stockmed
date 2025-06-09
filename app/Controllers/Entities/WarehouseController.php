<?php

namespace App\Controllers\Entities;

use App\Controllers\BaseController;
use App\Models\WarehouseModel;
use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class WarehouseController extends BaseController
{
    protected $warehouseModel;
    protected $activityModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
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
     * Lista todos os armazéns
     */
    public function index()
    {
        if (!$this->hasPermission('warehouses_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para visualizar armazéns'
            ]);
        }

        // Filtros
        $filters = [];
        
        if ($this->request->getGet('status')) {
            $filters['is_active'] = $this->request->getGet('status') === 'active';
        }

        if ($this->request->getGet('type')) {
            $filters['warehouse_type'] = $this->request->getGet('type');
        }

        // Paginação
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;

        $warehouses = $this->warehouseModel->getWarehousesWithFilters($filters, $page, $perPage);
        $total = $this->warehouseModel->getWarehousesCount($filters);

        return view('storage/index');
        return $this->response->setJSON([
            'success' => true,
            'data' => $warehouses,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Busca armazém por ID
     */
    public function show($id)
    {
        if (!$this->hasPermission('warehouses_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $warehouse = $this->warehouseModel->find($id);

        if (!$warehouse) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Armazém não encontrado'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $warehouse
        ]);
    }

    /**
     * Cria novo armazém
     */
    public function create()
    {
        if (!$this->hasPermission('warehouses_create')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para criar armazéns'
            ]);
        }

        $rules = [
            'name' => 'required|max_length[255]|is_unique[warehouses.name]',
            'warehouse_type' => 'required|in_list[main,secondary,pharmacy,storage]',
            'address' => 'required|string',
            'city' => 'required|max_length[100]',
            'state' => 'required|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'country' => 'permit_empty|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'manager_name' => 'permit_empty|max_length[255]',
            'manager_phone' => 'permit_empty|max_length[20]',
            'manager_email' => 'permit_empty|valid_email|max_length[255]',
            'capacity' => 'permit_empty|decimal',
            'temperature_controlled' => 'permit_empty|in_list[0,1]',
            'min_temperature' => 'permit_empty|decimal',
            'max_temperature' => 'permit_empty|decimal',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'warehouse_code' => $this->warehouseModel->generateWarehouseCode(),
            'name' => $this->request->getPost('name'),
            'warehouse_type' => $this->request->getPost('warehouse_type'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country') ?: 'Brasil',
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'manager_name' => $this->request->getPost('manager_name'),
            'manager_phone' => $this->request->getPost('manager_phone'),
            'manager_email' => $this->request->getPost('manager_email'),
            'capacity' => $this->request->getPost('capacity') ?: null,
            'temperature_controlled' => (bool)$this->request->getPost('temperature_controlled'),
            'min_temperature' => $this->request->getPost('min_temperature') ?: null,
            'max_temperature' => $this->request->getPost('max_temperature') ?: null,
            'notes' => $this->request->getPost('notes'),
            'is_active' => true
        ];

        // Validar temperaturas se controle de temperatura estiver ativo
        if ($data['temperature_controlled']) {
            if (empty($data['min_temperature']) || empty($data['max_temperature'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Temperaturas mínima e máxima são obrigatórias para armazéns com controle de temperatura'
                ]);
            }

            if ($data['min_temperature'] >= $data['max_temperature']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Temperatura mínima deve ser menor que a máxima'
                ]);
            }
        }

        $warehouseId = $this->warehouseModel->insert($data);

        if ($warehouseId) {
            // Log criação
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Armazém criado: ' . $data['name'],
                'warehouses',
                $warehouseId,
                null,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Armazém criado com sucesso',
                'data' => ['id' => $warehouseId, 'warehouse_code' => $data['warehouse_code']]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao criar armazém'
        ]);
    }

    /**
     * Atualiza armazém
     */
    public function update($id)
    {
        if (!$this->hasPermission('warehouses_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para editar armazéns'
            ]);
        }

        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Armazém não encontrado'
            ]);
        }

        $rules = [
            'name' => "required|max_length[255]|is_unique[warehouses.name,id,{$id}]",
            'warehouse_type' => 'required|in_list[main,secondary,pharmacy,storage]',
            'address' => 'required|string',
            'city' => 'required|max_length[100]',
            'state' => 'required|max_length[100]',
            'postal_code' => 'permit_empty|max_length[20]',
            'country' => 'permit_empty|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'manager_name' => 'permit_empty|max_length[255]',
            'manager_phone' => 'permit_empty|max_length[20]',
            'manager_email' => 'permit_empty|valid_email|max_length[255]',
            'capacity' => 'permit_empty|decimal',
            'temperature_controlled' => 'permit_empty|in_list[0,1]',
            'min_temperature' => 'permit_empty|decimal',
            'max_temperature' => 'permit_empty|decimal',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'warehouse_type' => $this->request->getPost('warehouse_type'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'manager_name' => $this->request->getPost('manager_name'),
            'manager_phone' => $this->request->getPost('manager_phone'),
            'manager_email' => $this->request->getPost('manager_email'),
            'capacity' => $this->request->getPost('capacity') ?: null,
            'temperature_controlled' => (bool)$this->request->getPost('temperature_controlled'),
            'min_temperature' => $this->request->getPost('min_temperature') ?: null,
            'max_temperature' => $this->request->getPost('max_temperature') ?: null,
            'notes' => $this->request->getPost('notes')
        ];

        // Validar temperaturas
        if ($data['temperature_controlled']) {
            if (empty($data['min_temperature']) || empty($data['max_temperature'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Temperaturas mínima e máxima são obrigatórias para armazéns com controle de temperatura'
                ]);
            }

            if ($data['min_temperature'] >= $data['max_temperature']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Temperatura mínima deve ser menor que a máxima'
                ]);
            }
        }

        $updated = $this->warehouseModel->update($id, $data);

        if ($updated) {
            // Log atualização
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Armazém atualizado: ' . $data['name'],
                'warehouses',
                $id,
                $warehouse,
                $data
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Armazém atualizado com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao atualizar armazém'
        ]);
    }

    /**
     * Ativa/Desativa armazém
     */
    public function toggleStatus($id)
    {
        if (!$this->hasPermission('warehouses_edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Armazém não encontrado'
            ]);
        }

        // Verificar se é o armazém principal
        if ($warehouse['warehouse_type'] === 'main' && $warehouse['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível desativar o armazém principal'
            ]);
        }

        $newStatus = !$warehouse['is_active'];
        
        // Se ativando, verificar se há stock no armazém
        if ($newStatus) {
            $stockModel = new \App\Models\StockLevelModel();
            $hasStock = $stockModel->where('warehouse_id', $id)->first();
            
            if ($hasStock) {
                // Verificar se há produtos com quantidade > 0
                $stockCount = $stockModel->where('warehouse_id', $id)
                                        ->where('quantity >', 0)
                                        ->countAllResults();
                
                if ($stockCount > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Não é possível desativar armazém com produtos em estoque'
                    ]);
                }
            }
        }

        $updated = $this->warehouseModel->update($id, ['is_active' => $newStatus]);

        if ($updated) {
            $action = $newStatus ? 'ativado' : 'desativado';
            
            // Log alteração
            $this->activityModel->logActivity(
                session()->get('user_id'),
                "Armazém {$action}: " . $warehouse['name'],
                'warehouses',
                $id
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Armazém {$action} com sucesso"
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alterar status do armazém'
        ]);
    }

    /**
     * Remove armazém (soft delete)
     */
    public function delete($id)
    {
        if (!$this->hasPermission('warehouses_delete')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para excluir armazéns'
            ]);
        }

        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Armazém não encontrado'
            ]);
        }

        // Não permitir excluir armazém principal
        if ($warehouse['warehouse_type'] === 'main') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível excluir o armazém principal'
            ]);
        }

        // Verificar se há stock
        $stockModel = new \App\Models\StockLevelModel();
        $stockCount = $stockModel->where('warehouse_id', $id)
                                ->where('quantity >', 0)
                                ->countAllResults();

        if ($stockCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Não é possível excluir. Armazém possui {$stockCount} produto(s) em estoque."
            ]);
        }

        // Verificar movimentações de stock
        $movementModel = new \App\Models\StockMovementModel();
        $movementsCount = $movementModel->where('warehouse_id', $id)->countAllResults();

        if ($movementsCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Não é possível excluir. Armazém possui {$movementsCount} movimentação(ões) de stock."
            ]);
        }

        $deleted = $this->warehouseModel->delete($id);

        if ($deleted) {
            // Log exclusão
            $this->activityModel->logActivity(
                session()->get('user_id'),
                'Armazém excluído: ' . $warehouse['name'],
                'warehouses',
                $id,
                $warehouse,
                null
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Armazém excluído com sucesso'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao excluir armazém'
        ]);
    }

    /**
     * Busca armazéns
     */
    public function search()
    {
        if (!$this->hasPermission('warehouses_view')) {
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

        $warehouses = $this->warehouseModel->searchWarehouses($term);

        return $this->response->setJSON([
            'success' => true,
            'data' => $warehouses
        ]);
    }

    /**
     * Stock do armazém
     */
    public function getStock($id)
    {
        if (!$this->hasPermission('warehouses_view') || !$this->hasPermission('stock_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Armazém não encontrado'
            ]);
        }

        $stockModel = new \App\Models\StockLevelModel();
        $stock = $stockModel->getWarehouseStock($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'warehouse' => $warehouse,
                'stock' => $stock
            ]
        ]);
    }

    /**
     * Movimentações do armazém
     */
    public function getMovements($id)
    {
        if (!$this->hasPermission('warehouses_view') || !$this->hasPermission('stock_movements')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $warehouse = $this->warehouseModel->find($id);
        if (!$warehouse) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Armazém não encontrado'
            ]);
        }

        $movementModel = new \App\Models\StockMovementModel();
        
        // Filtros de data
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        
        $movements = $movementModel->getWarehouseMovements($id, $dateFrom, $dateTo);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'warehouse' => $warehouse,
                'movements' => $movements
            ]
        ]);
    }

    /**
     * Estatísticas do armazém
     */
    public function getStats($id)
    {
        if (!$this->hasPermission('warehouses_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $stats = $this->warehouseModel->getWarehouseStats($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Transferir stock entre armazéns
     */
    public function transferStock()
    {
        if (!$this->hasPermission('stock_movements')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão para movimentar stock'
            ]);
        }

        $rules = [
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id' => 'required|integer|differs[from_warehouse_id]',
            'product_id' => 'required|integer',
            'quantity' => 'required|decimal|greater_than[0]',
            'notes' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $fromWarehouseId = $this->request->getPost('from_warehouse_id');
        $toWarehouseId = $this->request->getPost('to_warehouse_id');
        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity');
        $notes = $this->request->getPost('notes');

        // Verificar se armazéns existem e estão ativos
        $fromWarehouse = $this->warehouseModel->find($fromWarehouseId);
        $toWarehouse = $this->warehouseModel->find($toWarehouseId);

        if (!$fromWarehouse || !$fromWarehouse['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Armazém de origem inválido ou inativo'
            ]);
        }

        if (!$toWarehouse || !$toWarehouse['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Armazém de destino inválido ou inativo'
            ]);
        }

        // Verificar stock disponível
        $stockModel = new \App\Models\StockLevelModel();
        $currentStock = $stockModel->where('warehouse_id', $fromWarehouseId)
                                  ->where('product_id', $productId)
                                  ->first();

        if (!$currentStock || $currentStock['quantity'] < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stock insuficiente no armazém de origem'
            ]);
        }

        // Realizar transferência
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Reduzir stock do armazém origem
            $stockModel->adjustStock($fromWarehouseId, $productId, -$quantity, 'transfer_out', $notes);
            
            // Aumentar stock do armazém destino
            $stockModel->adjustStock($toWarehouseId, $productId, $quantity, 'transfer_in', $notes);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro na transferência');
            }

            // Log transferência
            $this->activityModel->logActivity(
                session()->get('user_id'),
                "Transferência de stock: {$quantity} unidades do produto {$productId} de {$fromWarehouse['name']} para {$toWarehouse['name']}",
                'stock_movements',
                null
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transferência realizada com sucesso'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao realizar transferência: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exportar armazéns
     */
    public function export()
    {
        if (!$this->hasPermission('warehouses_view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $format = $this->request->getGet('format') ?? 'csv';
        $warehouses = $this->warehouseModel->where('is_active', true)->findAll();

        if ($format === 'csv') {
            return $this->exportToCsv($warehouses);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Formato não suportado'
        ]);
    }

    /**
     * Exporta para CSV
     */
    private function exportToCsv($warehouses)
    {
        $filename = 'armazens_' . date('Y-m-d_H-i-s') . '.csv';
        
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
            'Endereço',
            'Cidade',
            'Estado',
            'Gerente',
            'Telefone',
            'Email',
            'Capacidade',
            'Controle Temperatura',
            'Data Cadastro'
        ], ';');
        
        // Dados
        foreach ($warehouses as $warehouse) {
            $warehouseTypes = [
                'main' => 'Principal',
                'secondary' => 'Secundário',
                'pharmacy' => 'Farmácia',
                'storage' => 'Depósito'
            ];

            fputcsv($output, [
                $warehouse['warehouse_code'],
                $warehouse['name'],
                $warehouseTypes[$warehouse['warehouse_type']] ?? $warehouse['warehouse_type'],
                $warehouse['address'],
                $warehouse['city'],
                $warehouse['state'],
                $warehouse['manager_name'],
                $warehouse['phone'],
                $warehouse['email'],
                $warehouse['capacity'] ? number_format($warehouse['capacity'], 2, ',', '.') : '',
                $warehouse['temperature_controlled'] ? 'Sim' : 'Não',
                date('d/m/Y', strtotime($warehouse['created_at']))
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