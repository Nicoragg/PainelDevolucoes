<?php
require_once __DIR__ . "/../models/User.php";

class UserController {

    public static function index() {
        $usuarios = User::all();
        require "view/users/index.php";
    }

    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'nome'     => trim($_POST['nome']),
                'email'    => trim($_POST['email']),
                'senha'    => $_POST['senha'],
                'is_admin' => isset($_POST['is_admin']) ? 1 : 0
            ];

            if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
                $erro = "Todos os campos são obrigatórios.";
            } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                $erro = "E-mail inválido.";
            } elseif (User::findByEmail($dados['email'])) {
                $erro = "E-mail já está em uso.";
            }

            if (!isset($erro)) {
                User::create($dados);
                $_SESSION['success'] = "Usuário criado com sucesso!";
                header("Location: ../index.php?page=users/home");
                exit;
            }
        }
        require "view/users/create.php";
    }

public static function update($id) {
    $usuario = User::find($id);
    if (!$usuario) {
        die("Usuário não encontrado.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados = [
            'nome'     => trim($_POST['nome']),
            'email'    => trim($_POST['email']),
            'is_admin' => $_POST['is_admin'] == 1 ? 1 : 0,
        ];

        if (!empty($_POST['senha'])) {
            $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        }

        if (empty($dados['nome']) || empty($dados['email'])) {
            $erro = "Nome e e-mail são obrigatórios.";
        } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erro = "E-mail inválido.";
        }

        if (!isset($erro)) {
            User::update($id, $dados);
            $_SESSION['success'] = "Usuário atualizado com sucesso!";
            header("Location: ../index.php?page=users/home");
            exit;
        }
    }

    require "view/users/edit.php";
}

    public static function delete($id) {
        if ($id && User::delete($id)) {
            $_SESSION['success'] = "Usuário excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir usuário.";
        }
        header("Location: index.php?page=users/home");
        exit;
    }
}

$action = $_GET['action'] ?? 'index';
$id     = $_GET['id']     ?? null;

if (method_exists('UserController', $action)) {
    if ($id !== null) {
        UserController::$action($id);
    } else {
        UserController::$action();
    }
} else {
    echo "Ação inválida em UserController.";
}
