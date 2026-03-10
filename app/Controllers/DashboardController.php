<?php

namespace App\Controllers;

use App\Services\AdminService;

class DashboardController
{
    public function handle(): array
    {
        $service = new AdminService();
        $products = $service->getProducts();
        $categories = $service->getCategories();

        return [
            'pageTitle' => 'Tableau de bord',
            'currentPage' => 'dashboard',
            'view' => 'dashboard.php',
            'products' => $products,
            'categories' => $categories
        ];
    }
}