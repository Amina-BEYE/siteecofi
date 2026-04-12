<?php

require_once __DIR__ . '/AdminController.php';

class Router
{
    public function resolve(string $route): array
    {
        $controller = new AdminController();
        return $controller->handle($route);
    }
}