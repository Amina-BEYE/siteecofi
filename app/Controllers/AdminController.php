<?php

class AdminController
{
    private function render($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../admin/Views/layouts/adminPage.php';
    }

    public function dashboard()
    {
        $this->render('/../admin/Views/layouts/dashboardv2.php', [
            'currentPage' => 'dashboard',
            'pageTitle'   => 'Tableau de bord',
            'view'        => '/dashboardv2.php',
        ]);
    }

    public function auth()
    {
        $this->render('partials/auth.php', [
            'currentPage' => 'auth',
            'pageTitle'   => 'Authentification & rôles',
            'view'        => 'partials/auth.php',
        ]);
    }

    public function clients()
    {
        $this->render('partials/clients.php', [
            'currentPage' => 'clients',
            'pageTitle'   => 'Clients & contacts',
            'view'        => 'partials/clients.php',
        ]);
    }

    public function products()
    {
        $this->render('partials/products.php', [
            'currentPage' => 'products',
            'pageTitle'   => 'Produits & stock',
            'view'        => 'partials/products.php',
        ]);
    }


    public function orders(){
        require_once __DIR__ . '/../admin/Models/OrderModel.php';

        $model = new OrderModel();

        $orders = $model->getAllOrders();
        $quotes = $model->getAllQuotes();

        $this->render('/../admin/Views/layouts/orders.php', [
            'currentPage' => 'orders',
            'pageTitle'   => 'Commandes & devis',
            'view'        => '/orders.php',
            'orders'      => $orders,
            'quotes'      => $quotes
        ]);

    }

    public function employees()
    {
        $this->render('partials/employees.php', [
            'currentPage' => 'employees',
            'pageTitle'   => 'Personnel',
            'view'        => 'partials/employees.php',
        ]);
    }

    public function notifications()
    {
        $this->render('partials/notifications.php', [
            'currentPage' => 'notifications',
            'pageTitle'   => 'Notifications',
            'view'        => 'partials/notifications.php',
        ]);
    }

    public function notFound()
    {
        $this->render('partials/404.php', [
            'currentPage' => '',
            'pageTitle'   => 'Page introuvable',
            'view'        => 'partials/404.php',
        ]);
    }

    
}