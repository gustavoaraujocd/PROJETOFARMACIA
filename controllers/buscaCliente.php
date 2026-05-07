<?php
require_once "../config/db.php";

$busca = $_GET['busca'] ?? '';

$sql = $pdo->prepare("
    SELECT * FROM clientes 
    WHERE nome LIKE ? OR cpf LIKE ?
    ORDER BY nome
");

$sql->execute([
    "%$busca%",
    "%$busca%"
]);

$clientes = $sql->fetchAll();

foreach ($clientes as $c) {
    ?>
    <tr>
        <td><?= $c['nome'] ?></td>

        <td>
            <?= preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $c['cpf']) ?>
        </td>

        <td><?= $c['telefone'] ?></td>

        <!-- STATUS -->
        <td>
            <?php if ($c['status'] == 'ativo'): ?>
                <span class="badge bg-success">Ativo</span>
            <?php else: ?>
                <span class="badge bg-secondary">Inativo</span>
            <?php endif; ?>
        </td>

        <td>
            <a href="editar_cliente.php?cpf=<?= $c['cpf'] ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i>
            </a>

            <a href="../controllers/clienteController.php?acao=toggle&cpf=<?= $c['cpf'] ?>"
                class="btn btn-secondary btn-sm">
                <i class="fas fa-user-slash"></i>
            </a>
        </td>
    </tr>
    <?php
}