<?php
session_start();
require_once "../config/db.php";

$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!$cpf || !$senha) {
    header("Location: ../index.php?msg=Preencha todos os campos&tipo=danger");
    exit;
}

try {

    // 🔐 BUSCA USUÁRIO + ROLE
    $sql = $pdo->prepare("
        SELECT u.*, r.nome AS role_nome
        FROM usuarios u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE u.cpf = ?
        LIMIT 1
    ");

    $sql->execute([$cpf]);
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    // ❌ USUÁRIO NÃO EXISTE
    if (!$user) {
        header("Location: ../index.php?msg=Usuário não encontrado&tipo=danger");
        exit;
    }

    // ❌ USUÁRIO INATIVO
    if ($user['status'] != 'ativo') {
        header("Location: ../index.php?msg=Usuário inativo&tipo=danger");
        exit;
    }

    // ❌ SENHA INCORRETA
    if (!password_verify($senha, $user['senha'])) {
        header("Location: ../index.php?msg=Senha inválida&tipo=danger");
        exit;
    }

    // 🔐 SEGURANÇA EXTRA
    session_regenerate_id(true);

    // ✅ SALVA NA SESSÃO (COM RBAC)
    $_SESSION['user'] = [
        'cpf' => $user['cpf'],
        'nome' => $user['nome'],
        'role_id' => $user['role_id'],
        'role_nome' => $user['role_nome']
    ];

    // 🚀 REDIRECIONA
    header("Location: ../views/dashboard.php");
    exit;

} catch (Exception $e) {

    header("Location: ../index.php?msg=Erro no sistema&tipo=danger");
    exit;
}