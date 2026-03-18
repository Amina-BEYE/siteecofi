<?php
require_once __DIR__ . '/../Controllers/AdminController.php';

$page = $_GET['page'] ?? 'dashboard';
$controller = new AdminController();

switch ($page) {
    case 'dashboard':
        $controller->dashboard();
        break;

    case 'auth':
        $controller->auth();
        break;

    case 'clients':
        $controller->clients();
        break;

    case 'products':
        $controller->products();
        break;

    case 'orders':
        $controller->orders();
        break;

    case 'employees':
        $controller->employees();
        break;

    case 'notifications':
        $controller->notifications();
        break;

    default:
        http_response_code(404);
        $controller->notFound();
        break;
}