<?php
require_once "../config/db.php";

$acao = $_REQUEST['acao'] ?? null;

// CPF seguro
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? $_GET['cpf'] ?? '');

try {

    // =========================
    // 🔹 CRIAR OU ATUALIZAR
    // =========================
    if ($acao == 'salvar') {

        if (!$cpf || empty($_POST['nome'])) {
            throw new Exception("Dados inválidos!");
        }

        // 🔍 Verifica se já existe
        $check = $pdo->prepare("SELECT cpf FROM clientes WHERE cpf=?");
        $check->execute([$cpf]);

        if ($check->rowCount()) {

            // 🔄 ATUALIZA
            $update = $pdo->prepare("
                UPDATE clientes 
                SET nome=?, endereco=?, telefone=? 
                WHERE cpf=?
            ");

            $update->execute([
                $_POST['nome'],
                $_POST['endereco'],
                $_POST['telefone'],
                $cpf
            ]);

            header("Location: ../views/clientes.php?msg=Cliente atualizado (CPF já existia)&tipo=warning");
            exit;
        }

        // ➕ INSERE
        $insert = $pdo->prepare("
            INSERT INTO clientes (cpf, nome, endereco, telefone, status)
            VALUES (?, ?, ?, ?, 'ativo')
        ");

        $insert->execute([
            $cpf,
            $_POST['nome'],
            $_POST['endereco'],
            $_POST['telefone']
        ]);

        header("Location: ../views/clientes.php?msg=Cliente cadastrado com sucesso&tipo=success");
        exit;
    }

    // =========================
    // 🔹 ATIVAR / INATIVAR
    // =========================
    if ($acao == 'toggle') {

        if (!$cpf) {
            throw new Exception("CPF inválido!");
        }

        $sql = $pdo->prepare("
            UPDATE clientes 
            SET status = IF(status='ativo','inativo','ativo')
            WHERE cpf = ?
        ");

        $sql->execute([$cpf]);

        header("Location: ../views/clientes.php?msg=Status alterado&tipo=warning");
        exit;
    }

    // =========================
    // 🔹 BLOQUEAR EXCLUSÃO
    // =========================
    if ($acao == 'excluir') {
        throw new Exception("Exclusão não permitida! Apenas inative o cliente.");
    }

} catch (Exception $e) {

    header("Location: ../views/clientes.php?msg=" . urlencode($e->getMessage()) . "&tipo=danger");
    exit;
}