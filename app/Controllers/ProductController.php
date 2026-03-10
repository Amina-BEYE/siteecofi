<?php

namespace App\Controllers;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;

class ProductController
{
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;

    public function __construct()
    {
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    public function handle(): array
    {
        $products = $this->productRepo->getAll();
        $categories = $this->categoryRepo->getAll();

        return [
            'pageTitle' => 'Gestion des produits et du stock',
            'currentPage' => 'products',
            'view' => 'products.php',
            'products' => $products,
            'categories' => $categories,
            'message' => '',
            'messageType' => 'success',
            'editProduct' => null,
            'editCategory' => null,
        ];
    }
}