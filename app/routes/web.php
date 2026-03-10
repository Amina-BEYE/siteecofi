<?php

use App\Controllers\ProductController;

$uri = $_SERVER['REQUEST_URI'];

if ($uri == "/produits") {

    $controller = new ProductController();
    $controller->index();

}