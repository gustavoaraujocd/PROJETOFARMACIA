<?php
require_once "../config/auth.php"; // 🔒 garante login
require_once "../config/db.php";

$userLogado = $_SESSION['user']['cpf'] ?? null;

function logSistema($pdo, $usuario, $acao)
{
    $log = $pdo->prepare("INSERT INTO logs (usuario_cpf, acao) VALUES (?, ?)");
    $log->execute([$usuario, $acao]);
}

try {

    // 🔒 Permissão (RBAC)
    if (!function_exists('temPermissao') || !temPermissao('gerenciar_clientes')) {
        throw new Exception("Acesso negado");
    }

    // 🔒 Método correto
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Requisição inválida");
    }

    // 🔒 Validação
    if (empty($_POST['cpf']) || empty($_POST['nome'])) {
        throw new Exception("Preencha os campos obrigatórios");
    }

    // 🔢 Sanitização
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    // 🔍 Verifica existência
    $check = $pdo->prepare("SELECT nome FROM clientes WHERE cpf = ?");
    $check->execute([$cpf]);

    if ($check->rowCount() == 0) {
        throw new Exception("Cliente não encontrado");
    }

    $clienteAntigo = $check->fetch();

    // 🔄 Atualiza
    $sql = $pdo->prepare("
        UPDATE clientes 
        SET nome = ?, telefone = ?, endereco = ?
        WHERE cpf = ?
    ");

    $sql->execute([
        $nome,
        $telefone,
        $endereco,
        $cpf
    ]);

    // 📝 LOG PROFISSIONAL
    logSistema(
        $pdo,
        $userLogado,
        "Atualizou cliente $cpf ({$clienteAntigo['nome']} -> $nome)"
    );

    // ✅ Feedback padrão do sistema
    header("Location: ../views/clientes.php?msg=Cliente atualizado com sucesso&tipo=success");
    exit;

} catch (Exception $e) {

    header("Location: ../views/clientes.php?msg=" . urlencode($e->getMessage()) . "&tipo=danger");
    exit;
}