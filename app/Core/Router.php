<?php

namespace App\Core;

use App\Controllers\DashboardController;
use App\Controllers\ProductController;
use App\Controllers\ClientController;
use App\Controllers\AuthController;
use App\Controllers\OrderController;
use App\Controllers\EmployeeController;
use App\Controllers\NotificationController;

class Router
{
    public function resolve(string $route): array
    {
        switch ($route) {
            case 'dashboard':
                $controller = new DashboardController();
                break;

            case 'products':
                $controller = new ProductController();
                break;

            case 'clients':
                $controller = new ClientController();
                break;

            case 'auth':
                $controller = new AuthController();
                break;

            case 'orders':
                $controller = new OrderController();
                break;

            case 'employees':
                $controller = new EmployeeController();
                break;

            case 'notifications':
                $controller = new NotificationController();
                break;

            default:
                $controller = new DashboardController();
                break;
        }

        return $controller->handle();
    }
}