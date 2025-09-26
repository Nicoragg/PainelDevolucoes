<?php
require_once __DIR__ . "/../config/Database.php";

class Devolucao {

    public static function getResumo($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT COUNT(*) as qtd, SUM(valor) as soma 
                FROM devolucoes $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['qtd' => 0, 'soma' => 0];
    }

    public static function getPendentes($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT COUNT(*) as qtd, SUM(valor) as soma 
                FROM devolucoes 
                $where " . ($where ? " AND " : "WHERE ") . " status != 'PAGAMENTO CONFIRMADO'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['qtd' => 0, 'soma' => 0];
    }

    public static function getStatus($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT status, COUNT(*) as qtd 
                FROM devolucoes $where 
                GROUP BY status";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPorLoja($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT loja, COUNT(*) as qtd, SUM(valor) as soma
                FROM devolucoes $where
                GROUP BY loja";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPorFornecedor($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT fornecedor, COUNT(*) as qtd, SUM(valor) as soma
                FROM devolucoes $where
                GROUP BY fornecedor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEvolucaoPorData($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT DATE(data_emissao) as data, SUM(valor) as soma
                FROM devolucoes $where
                GROUP BY DATE(data_emissao)
                ORDER BY data ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM devolucoes ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM devolucoes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public static function create($dados) {
    $db = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO devolucoes 
    (fornecedor, loja, nf, nf_origem, valor, data_emissao, data_recebimento, data_finalizacao, status, motivo, 
     forma_pagamento, observacao, responsavel, id_chamado, data_abertura, comprovante) 
    VALUES (:fornecedor, :loja, :nf, :nf_origem,:valor, :data_emissao, :data_recebimento, :data_finalizacao, :status, :motivo, 
     :forma_pagamento, :observacao, :responsavel, :id_chamado, :data_abertura, :comprovante)");

    return $stmt->execute([
        'fornecedor'       => $dados['fornecedor'],
        'loja'             => $dados['loja'],
        'nf'               => $dados['nf'],
        'nf_origem'        => $dados['nf_origem'],
        'valor'            => $dados['valor'],
        'data_emissao'     => $dados['data_emissao'],
        'data_recebimento' => $dados['data_recebimento'],
        'data_finalizacao' => $dados['data_finalizacao'],
        'status'           => $dados['status'],
        'motivo'           => $dados['motivo'],
        'observacao'       => $dados['observacao'],
        'responsavel'      => $dados['responsavel'],
        'id_chamado'       => $dados['id_chamado'],
        'data_abertura'    => $dados['data_abertura'],
        'forma_pagamento'  => $dados['forma_pagamento'],
        'comprovante'      => $dados['comprovante']
    ]);
}

public static function update($id, $dados) {
    $db = Database::getConnection();
    $stmt = $db->prepare("UPDATE devolucoes 
        SET fornecedor=:fornecedor, loja=:loja, nf=:nf, nf_origem=:nf_origem,valor=:valor, 
            data_emissao=:data_emissao, data_recebimento=:data_recebimento, data_finalizacao=:data_finalizacao,
            status=:status, motivo=:motivo, observacao=:observacao, 
            responsavel=:responsavel, id_chamado=:id_chamado, data_abertura=:data_abertura,
            forma_pagamento=:forma_pagamento, comprovante=:comprovante
        WHERE id=:id"); 
    
    return $stmt->execute([
        'fornecedor'       => $dados['fornecedor'],
        'loja'             => $dados['loja'],
        'nf'               => $dados['nf'],
        'nf_origem'        => $dados['nf_origem'],
        'valor'            => $dados['valor'],
        'data_emissao'     => $dados['data_emissao'],
        'data_recebimento' => $dados['data_recebimento'],
        'data_finalizacao' => $dados['data_finalizacao'],
        'status'           => $dados['status'],
        'motivo'           => $dados['motivo'],
        'observacao'       => $dados['observacao'],
        'responsavel'      => $dados['responsavel'],
        'id_chamado'       => $dados['id_chamado'],
        'data_abertura'    => $dados['data_abertura'],
        'forma_pagamento'  => $dados['forma_pagamento'],
        'comprovante'      => $dados['comprovante'],
        'id'               => $id
    ]);
}

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM devolucoes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function count($where = "", $params = []) {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) as total FROM devolucoes $where";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function paginate($where = "", $params = [], $limit = 10, $offset = 0) {
        $db = Database::getConnection();
        $sql = "SELECT * FROM devolucoes $where ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(count($params) + 1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, (int)$offset, PDO::PARAM_INT);

        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function allFiltered($filtros) {
        $pdo = Database::getConnection();
        [$where, $params] = self::montarWhere($filtros);

        $sql = "SELECT * FROM devolucoes $where ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function montarWhere($filtros) {
        $condicoes = [];
        $params = [];

        if (!empty($filtros['fornecedor'])) {
            $condicoes[] = "fornecedor LIKE ?";
            $params[] = "%" . $filtros['fornecedor'] . "%";
        }
        if (!empty($filtros['loja'])) {
            $condicoes[] = "loja = ?";
            $params[] = $filtros['loja'];
        }
        if (!empty($filtros['motivo'])) {
            $condicoes[] = "motivo = ?";
            $params[] = $filtros['motivo'];
        }
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $condicoes[] = "data_emissao BETWEEN ? AND ?";
            $params[] = $filtros['data_inicio'];
            $params[] = $filtros['data_fim'];
        } elseif (!empty($filtros['data_inicio'])) {
            $condicoes[] = "data_emissao >= ?";
            $params[] = $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {
            $condicoes[] = "data_emissao <= ?";
            $params[] = $filtros['data_fim'];
        }

        $where = $condicoes ? "WHERE " . implode(" AND ", $condicoes) : "";
        return [$where, $params];
    }



}
