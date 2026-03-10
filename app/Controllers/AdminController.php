<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AdminService;
use Exception;

class AdminController extends Controller
{
    private AdminService $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    public function handle(): array
    {
        $message = '';
        $messageType = 'success';

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                if ($action === 'add_category') {
                    $nom = $this->clean($_POST['nom'] ?? '');
                    $description = $this->clean($_POST['description'] ?? '');

                    if ($nom === '') {
                        throw new Exception('Le nom de la catégorie est obligatoire.');
                    }

                    $this->adminService->createCategory($nom, $description);
                    $message = 'Catégorie ajoutée avec succès.';
                }

                if ($action === 'update_category') {
                    $id = (int)($_POST['id'] ?? 0);
                    $nom = $this->clean($_POST['nom'] ?? '');
                    $description = $this->clean($_POST['description'] ?? '');

                    if ($id <= 0 || $nom === '') {
                        throw new Exception('Informations catégorie invalides.');
                    }

                    $this->adminService->updateCategory($id, $nom, $description);
                    $message = 'Catégorie modifiée avec succès.';
                }

                if ($action === 'delete_category') {
                    $id = (int)($_POST['id'] ?? 0);

                    if ($id <= 0) {
                        throw new Exception('ID catégorie invalide.');
                    }

                    $this->adminService->deleteCategory($id);
                    $message = 'Catégorie supprimée avec succès.';
                }

                if ($action === 'add_product') {
                    $data = $this->buildProductData($_POST);
                    $this->adminService->createProduct($data);
                    $message = 'Produit ajouté avec succès.';
                }

                if ($action === 'update_product') {
                    $id = (int)($_POST['id'] ?? 0);

                    if ($id <= 0) {
                        throw new Exception('ID produit invalide.');
                    }

                    $data = $this->buildProductData($_POST);
                    $this->adminService->updateProduct($id, $data);
                    $message = 'Produit modifié avec succès.';
                }

                if ($action === 'delete_product') {
                    $id = (int)($_POST['id'] ?? 0);

                    if ($id <= 0) {
                        throw new Exception('ID produit invalide.');
                    }

                    $this->adminService->deleteProduct($id);
                    $message = 'Produit supprimé avec succès.';
                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'error';
        }

        $editCategory = null;
        $editProduct = null;

        if (isset($_GET['edit_category'])) {
            $editCategory = $this->adminService->getCategoryById((int)$_GET['edit_category']);
        }

        if (isset($_GET['edit_product'])) {
            $editProduct = $this->adminService->getProductById((int)$_GET['edit_product']);
        }

        return [
            'message' => $message,
            'messageType' => $messageType,
            'categories' => $this->adminService->getCategories(),
            'products' => $this->adminService->getProducts(),
            'editCategory' => $editCategory,
            'editProduct' => $editProduct
        ];
    }

    private function buildProductData(array $post): array
    {
        $categorie_id = (int)($post['categorie_id'] ?? 0);
        $nom = $this->clean($post['nom'] ?? '');

        if ($categorie_id <= 0 || $nom === '') {
            throw new Exception('La catégorie et le nom du produit sont obligatoires.');
        }

        return [
            'categorie_id' => $categorie_id,
            'nom' => $nom,
            'description' => $this->clean($post['description'] ?? ''),
            'prix' => $this->toNullableDecimal($post['prix'] ?? ''),
            'ancien_prix' => $this->toNullableDecimal($post['ancien_prix'] ?? ''),
            'image' => $this->clean($post['image'] ?? ''),
            'note' => $this->toNullableDecimal($post['note'] ?? ''),
            'nb_avis' => (int)($post['nb_avis'] ?? 0),
            'type_media' => $this->clean($post['type_media'] ?? 'image'),
            'media_src' => $this->clean($post['media_src'] ?? ''),
            'actif' => isset($post['actif']) ? 1 : 0
        ];
    }
}