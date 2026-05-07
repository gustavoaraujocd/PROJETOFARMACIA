<?php
session_start();
require_once "../config/db.php";

// 🔒 função de retorno padrão
function redirect($msg, $tipo = 'success')
{
    header("Location: ../views/medicamentos.php?msg=" . urlencode($msg) . "&tipo=$tipo");
    exit;
}

$acao = $_REQUEST['acao'] ?? null;

try {

    // =========================
    // 🔹 CRIAR OU ATUALIZAR (ANTI DUPLICADO)
    // =========================
    if ($acao === 'criar') {

        $nome = strtolower(trim($_POST['nome'] ?? ''));
        $composicao = trim($_POST['composicao'] ?? '');
        $posologia = trim($_POST['posologia'] ?? '');
        $fabricante = trim($_POST['fabricante'] ?? '');

        if (empty($nome)) {
            throw new Exception("Nome é obrigatório");
        }

        // 🔍 verifica duplicidade
        $check = $pdo->prepare("
            SELECT id FROM medicamentos 
            WHERE LOWER(nome) = ?
            LIMIT 1
        ");
        $check->execute([$nome]);
        $med = $check->fetch();

        if ($med) {

            // 🔄 UPDATE automático
            $update = $pdo->prepare("
                UPDATE medicamentos 
                SET composicao = ?, posologia = ?, fabricante = ?, ativo = 1
                WHERE id = ?
            ");

            $update->execute([
                $composicao,
                $posologia,
                $fabricante,
                $med['id']
            ]);

            redirect("Medicamento já existia, foi atualizado", "warning");
        }

        // 🆕 INSERT
        $insert = $pdo->prepare("
            INSERT INTO medicamentos 
            (nome, composicao, posologia, fabricante, ativo)
            VALUES (?, ?, ?, ?, 1)
        ");

        $insert->execute([
            $nome,
            $composicao,
            $posologia,
            $fabricante
        ]);

        redirect("Cadastrado com sucesso");
    }

    // =========================
    // 🔹 EDITAR
    // =========================
    if ($acao === 'editar') {

        $id = $_POST['id'] ?? null;
        $nome = trim($_POST['nome'] ?? '');
        $composicao = trim($_POST['composicao'] ?? '');
        $posologia = trim($_POST['posologia'] ?? '');
        $fabricante = trim($_POST['fabricante'] ?? '');
        $ativo = isset($_POST['ativo']) ? (int) $_POST['ativo'] : 1;

        if (!$id || !is_numeric($id)) {
            throw new Exception("ID inválido");
        }

        if (empty($nome)) {
            throw new Exception("Nome é obrigatório");
        }

        $sql = $pdo->prepare("
            UPDATE medicamentos 
            SET nome = ?, composicao = ?, posologia = ?, fabricante = ?, ativo = ?
            WHERE id = ?
        ");

        $sql->execute([
            $nome,
            $composicao,
            $posologia,
            $fabricante,
            $ativo,
            $id
        ]);

        redirect("Atualizado com sucesso");
    }

    // =========================
    // 🔹 EXCLUIR (SOFT DELETE)
    // =========================
    if ($acao === 'excluir') {

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            throw new Exception("ID inválido");
        }

        $sql = $pdo->prepare("
            UPDATE medicamentos 
            SET ativo = 0 
            WHERE id = ?
        ");

        $sql->execute([$id]);

        redirect("Medicamento desativado", "warning");
    }

    // =========================
    // 🔹 TOGGLE STATUS
    // =========================
    if ($acao === 'toggle') {

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            throw new Exception("ID inválido");
        }

        $sql = $pdo->prepare("
            UPDATE medicamentos 
            SET ativo = IF(ativo = 1, 0, 1)
            WHERE id = ?
        ");

        $sql->execute([$id]);

        redirect("Status alterado", "warning");
    }

    // =========================
    // ❌ AÇÃO INVÁLIDA
    // =========================
    throw new Exception("Ação inválida");

} catch (Exception $e) {

    redirect("Erro: " . $e->getMessage(), "danger");
}