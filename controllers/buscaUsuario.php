
<?php
require_once "../config/db.php";


$busca = $_GET['busca'] ?? '';

$sql = $pdo->prepare("
    SELECT u.*, r.nome as role_nome
    FROM usuarios u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.nome LIKE ? OR u.cpf LIKE ?
    ORDER BY u.nome
");

$sql->execute([
    "%$busca%",
    "%$busca%"
]);

$usuarios = $sql->fetchAll();

foreach ($usuarios as $u):
    ?>
    <tr>
        <td>
            <?= $u['nome'] ?>
        </td>

        <td>
            <?= preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $u['cpf']) ?>
        </td>

        <td>
            <?= $u['role_nome'] ?? 'Sem função' ?>
        </td>

        <!-- STATUS -->
        <td>
            <?php if ($u['status'] == 'ativo'): ?>
                <span class="badge bg-success">Ativo</span>
            <?php else: ?>
                <span class="badge bg-secondary">Inativo</span>
            <?php endif; ?>
        </td>

        <!-- AÇÕES -->
        <td>

            <!-- EDITAR -->
            <a href="usuarios_editar.php?cpf=<?= $u['cpf'] ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i>
            </a>

            <!-- ATIVAR / INATIVAR -->
            <a href="../controllers/usuarioController.php?acao=toggle&cpf=<?= $u['cpf'] ?>"
                class="btn btn-sm <?= $u['status'] == 'ativo' ? 'btn-warning' : 'btn-success' ?>">

                <?php if ($u['status'] == 'ativo'): ?>
                    <i class="fas fa-user-slash"></i>
                <?php else: ?>
                    <i class="fas fa-user-check"></i>
                <?php endif; ?>
            </a>

        </td>
    </tr>
<?php endforeach; ?>