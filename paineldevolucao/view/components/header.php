<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$isLoggedIn = !empty($_SESSION['isloggedin']);
$isAdmin    = $isLoggedIn && !empty($_SESSION['usuario']['is_admin']) && $_SESSION['usuario']['is_admin'] == 1;
$username   = $isLoggedIn ? $_SESSION['usuario']['nome'] : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Devoluções</title>
    <link rel="stylesheet" href="public/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="public/icon.png">
</head>

<?php if ($isLoggedIn): ?>
<header>
    <div class="logo">
        <img src="public/logo.png" alt="Logo">
    </div>
    <nav>
        <div class="menu-left">
            <a href="?page=dashboard">Dashboard</a>
            <a href="?page=home">Devoluções</a>
            <?php if ($isAdmin): ?>
                <a href="?page=devolucoes/create">Cadastrar Devolução</a>
                <a href="?page=users/home">Usuários</a>
            <?php endif; ?>
        </div>
        <div class="menu-right">
            <span class="usuario-badge"><i class="fa fa-user"></i> <?= htmlspecialchars($username) ?></span>
            <a href="?page=auth/logout">Sair</a>
        </div>
    </nav>
</header>
<?php else: ?>
<header>
    <nav>
        <div class="logo">
            <img src="public/logo.png" alt="Logo">
        </div>
    </nav>
</header>
<?php endif; ?>

<hr>
