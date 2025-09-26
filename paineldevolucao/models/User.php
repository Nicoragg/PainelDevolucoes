<?php
require_once __DIR__ . "/../config/Database.php";

class User {

    public static function all() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => trim($email)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public static function create($dados) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO users (nome, email, senha, is_admin)
            VALUES (:nome, :email, :senha, :is_admin)
        ");
        return $stmt->execute([
            'nome'     => $dados['nome'],
            'email'    => $dados['email'],
            'senha'    => $dados['senha'],
            'is_admin' => isset($dados['is_admin']) ? 1 : 0
        ]);
    }



    public static function update($id, $dados) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users 
            SET nome=:nome, email=:email, is_admin=:is_admin" 
            . (isset($dados['senha']) ? ", senha=:senha" : "") . "
            WHERE id=:id
        ");
        $params = [
            'nome'     => $dados['nome'],
            'email'    => $dados['email'],
            'is_admin' => $dados['is_admin'],
            'id'       => $id
        ];

        if (isset($dados['senha'])) {
            $params['senha'] = $dados['senha'];
        }

        return $stmt->execute($params);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function login($email, $senha) {
        $user = self::findByEmail($email);
        if ($user && $senha === $user['senha']) {
            return $user;
        }
        return false;
    }

    public static function count($where = "", $params = []) {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) as total FROM users $where";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function paginate($where = "", $params = [], $limit = 10, $offset = 0) {
        $db = Database::getConnection();

        $limit  = (int)$limit;
        $offset = (int)$offset;

        $sql = "SELECT * FROM users $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
        $stmt = $db->prepare($sql);
        $stmt = $db->prepare($sql);

        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
