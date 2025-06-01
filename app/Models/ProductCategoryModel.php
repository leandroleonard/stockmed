<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCategoryModel extends Model
{
    protected $table = 'product_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'description',
        'parent_id',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'parent_id' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'O nome da categoria é obrigatório'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca categorias ativas
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', true)->findAll();
    }

    /**
     * Busca categorias principais (sem pai)
     */
    public function getMainCategories()
    {
        return $this->where('parent_id', null)
                    ->where('is_active', true)
                    ->findAll();
    }

    /**
     * Busca subcategorias de uma categoria
     */
    public function getSubCategories($parentId)
    {
        return $this->where('parent_id', $parentId)
                    ->where('is_active', true)
                    ->findAll();
    }

    /**
     * Busca categoria com hierarquia
     */
    public function getCategoryHierarchy()
    {
        $categories = $this->where('is_active', true)->findAll();
        return $this->buildHierarchy($categories);
    }

    /**
     * Constrói hierarquia de categorias
     */
    private function buildHierarchy($categories, $parentId = null)
    {
        $branch = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildHierarchy($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $branch[] = $category;
            }
        }
        return $branch;
    }
}