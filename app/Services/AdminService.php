<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

class AdminService
{
    private CategoryRepository $categoryRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->productRepository = new ProductRepository();
    }

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function getProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function getCategoryById(int $id): ?array
    {
        return $this->categoryRepository->findById($id);
    }

    public function getProductById(int $id): ?array
    {
        return $this->productRepository->findById($id);
    }

    public function createCategory(string $nom, string $description): bool
    {
        return $this->categoryRepository->create($nom, $description);
    }

    public function updateCategory(int $id, string $nom, string $description): bool
    {
        return $this->categoryRepository->update($id, $nom, $description);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    public function createProduct(array $data): bool
    {
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}