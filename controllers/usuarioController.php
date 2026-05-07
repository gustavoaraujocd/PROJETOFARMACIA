<?php
session_start();
require_once "../config/db.php";

$userLogado = $_SESSION['user']['cpf'] ?? null;

function logSistema($pdo, $usuario, $acao)
{
    $log = $pdo->prepare("INSERT INTO logs (usuario_cpf, acao) VALUES (?, ?)");
    $log->execute([$usuario, $acao]);
}

$acao = $_REQUEST['acao'] ?? null;

try {

    // =========================
    // 🔹 CRIAR
    // =========================
    if ($acao == 'criar') {

        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);

        $check = $pdo->prepare("SELECT cpf FROM usuarios WHERE cpf = ?");
        $check->execute([$cpf]);

        if ($check->rowCount()) {
            throw new Exception("CPF já cadastrado!");
        }

        if (empty($_POST['role_id'])) {
            throw new Exception("Selecione o tipo de usuário!");
        }

        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        $sql = $pdo->prepare("
            INSERT INTO usuarios 
            (cpf, nome, email, telefone, senha, role_id, status)
            VALUES (?, ?, ?, ?, ?, ?, 'ativo')
        ");

        $sql->execute([
            $cpf,
            $_POST['nome'],
            $_POST['email'],
            $_POST['telefone'],
            $senha,
            $_POST['role_id']
        ]);

        logSistema($pdo, $userLogado, "Cadastrou usuário $cpf");

        header("Location: ../views/usuarios.php?msg=Usuário criado com sucesso&tipo=success");
        exit;
    }

    // =========================
    // 🔹 EDITAR
    // =========================
    if ($acao == 'editar') {

        if (empty($_POST['role_id'])) {
            throw new Exception("Tipo de usuário inválido!");
        }

        if (!empty($_POST['senha'])) {

            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

            $sql = $pdo->prepare("
                UPDATE usuarios 
                SET nome=?, email=?, telefone=?, role_id=?, senha=?
                WHERE cpf=?
            ");

            $sql->execute([
                $_POST['nome'],
                $_POST['email'],
                $_POST['telefone'],
                $_POST['role_id'],
                $senha,
                $_POST['cpf']
            ]);

        } else {

            $sql = $pdo->prepare("
                UPDATE usuarios 
                SET nome=?, email=?, telefone=?, role_id=?
                WHERE cpf=?
            ");

            $sql->execute([
                $_POST['nome'],
                $_POST['email'],
                $_POST['telefone'],
                $_POST['role_id'],
                $_POST['cpf']
            ]);
        }

        logSistema($pdo, $userLogado, "Editou usuário " . $_POST['cpf']);

        header("Location: ../views/usuarios.php?msg=Atualizado com sucesso&tipo=success");
        exit;
    }

    // =========================
    // 🔹 ATIVAR / INATIVAR
    // =========================
    if ($acao == 'toggle') {

        $sql = $pdo->prepare("
            UPDATE usuarios 
            SET status = IF(status='ativo','inativo','ativo')
            WHERE cpf = ?
        ");

        $sql->execute([$_GET['cpf']]);

        logSistema($pdo, $userLogado, "Alterou status do usuário " . $_GET['cpf']);

        header("Location: ../views/usuarios.php?msg=Status alterado&tipo=warning");
        exit;
    }

    // =========================
    // 🔹 EXCLUIR (BLOQUEADO PROFISSIONAL)
    // =========================
    if ($acao == 'deletar') {

        throw new Exception("Exclusão não permitida! Apenas inative o usuário.");
    }

} catch (Exception $e) {

    header("Location: ../views/usuarios.php?msg=" . urlencode($e->getMessage()) . "&tipo=danger");
    exit;
}