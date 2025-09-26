<?php
session_start();

$page = $_GET['page'] ?? 'home';
$publicPages = ['auth/login', 'auth/logout'];

if (!in_array($page, $publicPages) && empty($_SESSION['isloggedin'])) {
    header("Location: index.php?page=auth/login");
    exit;
}

require_once "view/components/header.php";

$routes = [
    'dashboard'        => 'view/home.php',

    'home'             => 'view/devolucoes/index.php',
    'devolucoes/create'=> 'view/devolucoes/create.php',
    'devolucoes/edit'  => 'view/devolucoes/edit.php',
    'devolucoes/delete'=> 'view/devolucoes/delete.php',

    'users/home'       => 'view/users/index.php',
    'users/create'     => 'view/users/create.php',
    'users/edit'       => 'view/users/edit.php',
    'users/delete'     => 'controllers/UserController.php?action=delete&id=' . ($_GET['id'] ?? ''),

    'auth/login'       => 'view/auth/login.php',
    'auth/logout'      => 'view/auth/logout.php',
];

if (array_key_exists($page, $routes)) {
    require_once $routes[$page];
} else {
    echo "ERROR 404 - Página não encontrada.";
}

require_once "view/components/footer.php";
