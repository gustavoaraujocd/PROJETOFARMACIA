<?php
session_start();
require_once "../config/db.php";

$medicamento_id = $_POST['medicamento_id'];
$numero_lote = $_POST['numero_lote'];
$validade = $_POST['validade'];
$fabricacao = $_POST['fabricacao'];
$quantidade = (int) $_POST['quantidade'];

try {

    $pdo->beginTransaction();

    // 🔍 VERIFICA SE JÁ EXISTE
    $check = $pdo->prepare("
        SELECT id, quantidade 
        FROM lotes 
        WHERE medicamento_id = ? AND numero_lote = ?
    ");

    $check->execute([$medicamento_id, $numero_lote]);
    $existe = $check->fetch();

    if ($existe) {

        // 🔄 ATUALIZA (SOMA)
        $update = $pdo->prepare("
            UPDATE lotes 
            SET quantidade = quantidade + ?, 
                validade = ?, 
                fabricacao = ?
            WHERE id = ?
        ");

        $update->execute([
            $quantidade,
            $validade,
            $fabricacao,
            $existe['id']
        ]);

        $msg = "Lote atualizado (quantidade somada)";

    } else {

        // ➕ INSERE NOVO
        $insert = $pdo->prepare("
            INSERT INTO lotes 
            (medicamento_id, numero_lote, validade, fabricacao, quantidade)
            VALUES (?, ?, ?, ?, ?)
        ");

        $insert->execute([
            $medicamento_id,
            $numero_lote,
            $validade,
            $fabricacao,
            $quantidade
        ]);

        $msg = "Novo lote cadastrado";
    }

    // 🔄 ATIVA MEDICAMENTO
    $ativar = $pdo->prepare("
        UPDATE medicamentos SET ativo = 1 WHERE id = ?
    ");
    $ativar->execute([$medicamento_id]);

    $pdo->commit();

    header("Location: ../views/lotes.php?msg=" . urlencode($msg) . "&tipo=success");

} catch (Exception $e) {

    $pdo->rollBack();

    header("Location: ../views/lotes.php?msg=" . urlencode($e->getMessage()) . "&tipo=danger");
}

// 📊 REGISTRAR MOVIMENTAÇÃO (ENTRADA)
$mov = $pdo->prepare("
    INSERT INTO movimentacoes 
    (medicamento_id, lote_id, tipo, quantidade, usuario_cpf, observacao)
    VALUES (?, ?, 'entrada', ?, ?, ?)
");

$mov->execute([
    $medicamento_id,
    $existe ? $existe['id'] : $pdo->lastInsertId(),
    $quantidade,
    $_SESSION['user']['cpf'],
    'Entrada de estoque'
]);