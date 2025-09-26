// controllers/AuthController.php
<?php
session_start();
require_once __DIR__ . "/../models/User.php";

$action = $_GET['action'] ?? null;

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $user = User::login($email, $senha);

    if ($user) {
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id'       => $user['id'],
            'nome'     => $user['nome'],
            'email'    => $user['email'],
            'setor'    => $user['setor'] ?? '',
            'is_admin' => isset($user['is_admin']) ? (int)$user['is_admin'] : 0,
            'setor' => $usuario['setor'],
        ];

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['nome'];
        $_SESSION['isloggedin'] = true;

        header("Location: ../index.php?page=home");
        exit;
    } else {
        $_SESSION['error'] = "Credenciais inválidas.";
        header("Location: ../index.php?page=auth/login");
        exit;
    }
}

if ($action === 'logout') {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header("Location: ../index.php?page=auth/login");
    exit;
}