<?php
session_start();
require_once "../config/db.php";

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Acesso inválido");
    }

    $cliente = $_POST['cliente_cpf'] ?? null;
    $medicamento = $_POST['medicamento_id'] ?? null;
    $lote = $_POST['lote_id'] ?? null;
    $quantidade = (int) ($_POST['quantidade'] ?? 0);
    $balconista = $_POST['balconista_cpf'] ?? null;
    $comprador = $_POST['comprador'] ?? null;

    if (!$cliente || !$medicamento || !$lote || $quantidade <= 0) {
        throw new Exception("Preencha todos os campos obrigatórios");
    }

    // 🔒 BUSCA LOTE
    $sql = $pdo->prepare("SELECT quantidade FROM lotes WHERE id = ?");
    $sql->execute([$lote]);
    $l = $sql->fetch();

    if (!$l) {
        throw new Exception("Lote não encontrado");
    }

    // 🚫 BLOQUEIO DE ESTOQUE
    if ($l['quantidade'] < $quantidade) {
        throw new Exception("Estoque insuficiente. Disponível: " . $l['quantidade']);
    }

    // 🔄 TRANSAÇÃO
    $pdo->beginTransaction();

    // 📦 BAIXA NO ESTOQUE
    $update = $pdo->prepare("
        UPDATE lotes 
        SET quantidade = quantidade - ? 
        WHERE id = ?
    ");
    $update->execute([$quantidade, $lote]);

    // 📋 REGISTRA ENTREGA
    $insert = $pdo->prepare("
        INSERT INTO entregas
        (cliente_cpf, medicamento_id, lote_id, quantidade, balconista_cpf, responsavel, comprador)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $insert->execute([
        $cliente,
        $medicamento,
        $lote,
        $quantidade,
        $balconista,
        $_SESSION['user']['nome'],
        $comprador
    ]);

    $pdo->commit();

    header("Location: ../views/entregas.php?msg=Entrega registrada com sucesso&tipo=success");
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: ../views/entregas.php?msg=" . urlencode($e->getMessage()) . "&tipo=danger");
    exit;
}