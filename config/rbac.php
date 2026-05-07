<?php
require_once "db.php";

function temPermissao($permissao)
{

    if (!isset($_SESSION['user']['role_id'])) {
        return false; // evita erro
    }

    global $pdo;

    $sql = $pdo->prepare("
        SELECT 1
        FROM role_permissao rp
        JOIN permissoes p ON rp.permissao_id = p.id
        WHERE rp.role_id = ? AND p.nome = ?
    ");

    $sql->execute([
        $_SESSION['user']['role_id'],
        $permissao
    ]);

    return $sql->rowCount() > 0;
}

function exigirPermissao($permissao)
{
    if (!temPermissao($permissao)) {
        header("Location: dashboard.php?msg=Acesso negado&tipo=danger");
        exit;
    }
}