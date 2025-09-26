<?php
session_start();
require_once __DIR__ . "/../models/Devolucao.php";

class DevolucaoController {

    public static function dashboard() {
        $filtros = [
            'fornecedor' => $_GET['fornecedor'] ?? '',
            'loja'       => $_GET['loja'] ?? '',
            'motivo'     => $_GET['motivo'] ?? '',
            'data_inicio'=> $_GET['data_inicio'] ?? '',
            'data_fim'   => $_GET['data_fim'] ?? ''
        ];

        $dados = [
            'resumo'      => Devolucao::getResumo($filtros),
            'pendentes'   => Devolucao::getPendentes($filtros),
            'status'      => Devolucao::getStatus($filtros),
            'porLoja'     => Devolucao::getPorLoja($filtros),
            'porFornecedor'=> Devolucao::getPorFornecedor($filtros),
            'evolucao'    => Devolucao::getEvolucaoPorData($filtros)
        ];

        require __DIR__ . "/../view/dashboard.php";
    }

    private static function formatarValor($valor) {
        $valor = str_replace(['R$', '.', ' '], '', $valor);
        return str_replace(',', '.', $valor);
    }

    private static function handleUpload($campo = 'comprovante', $arquivoAtual = null) {
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $pasta = __DIR__ . "/../uploads/";
            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }
            $nomeArquivo = uniqid() . "_" . basename($_FILES[$campo]['name']);
            $caminho = $pasta . $nomeArquivo;
            if (move_uploaded_file($_FILES[$campo]['tmp_name'], $caminho)) {
                return $nomeArquivo;
            }
        }
        return $arquivoAtual;
    }



    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'id_chamado'       => trim($_POST['id_chamado']),
                'data_abertura'    => trim($_POST['data_abertura']),
                'fornecedor'       => trim($_POST['fornecedor']),
                'loja'             => trim($_POST['loja']),
                'nf'               => trim($_POST['nf']),
                'nf_origem'        => trim($_POST['nf_origem']),
                'valor'            => self::formatarValor($_POST['valor']),
                'data_emissao'     => $_POST['data_emissao'],
                'forma_pagamento'  => $_POST['forma_pagamento'],
                'data_recebimento' => $_POST['data_recebimento'] ?: null,
                'data_finalizacao' => $_POST['data_finalizacao'] ?: null,
                'responsavel'      => trim($_POST['responsavel']),
                'status'           => $_POST['status'],
                'motivo'           => $_POST['motivo'],
                'observacao'       => $_POST['observacao'] ?? '',
                'comprovante'      => self::handleUpload()
            ];

            Devolucao::create($dados);
            $_SESSION['success'] = "Devolução cadastrada com sucesso!";
            header("Location: ../index.php?page=home");
            exit;
        }

        require __DIR__ . "/../view/devolucoes/create.php";
    }


    public static function edit($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $dados = [
                'id_chamado'       => trim($_POST['id_chamado']),
                'data_abertura'    => trim($_POST['data_abertura']),
                'fornecedor'       => trim($_POST['fornecedor']),
                'loja'             => trim($_POST['loja']),
                'nf'               => trim($_POST['nf']),
                'nf_origem'        => trim($_POST['nf_origem']),
                'valor'            => self::formatarValor($_POST['valor']),
                'data_emissao'     => $_POST['data_emissao'],
                'forma_pagamento'  => $_POST['forma_pagamento'],
                'data_recebimento' => $_POST['data_recebimento'] ?: null,
                'data_finalizacao' => $_POST['data_finalizacao'] ?: null,
                'responsavel'      => trim($_POST['responsavel'] ?? ''),
                'status'           => $_POST['status'],
                'motivo'           => $_POST['motivo'],
                'observacao'       => $_POST['observacao'] ?? '',
                'comprovante'      => self::handleUpload('comprovante', $_POST['comprovante_atual'] ?? null)
            ];

            Devolucao::update($id, $dados);
            $_SESSION['success'] = "Devolução atualizada com sucesso!";

            if (!empty($_SESSION['filtros_devolucoes'])) {
                $queryString = http_build_query($_SESSION['filtros_devolucoes']);
                header("Location: ../index.php?page=home&" . $queryString);
            } else {
                header("Location: ../index.php?page=home");
            }
            exit;
        }

        $devolucao = Devolucao::find($id);
        require __DIR__ . "/../view/devolucoes/edit.php";
    }


    public static function delete($id) {
        Devolucao::delete($id);
        $_SESSION['success'] = "Devolução excluída!";
        header("Location: ../index.php?page=home");
        exit;
    }
}


if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;

    if (method_exists('DevolucaoController', $action)) {
        $id ? DevolucaoController::$action($id) : DevolucaoController::$action();
    } else {
        echo "Ação inválida!";
    }
}
