<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';

$movs = $pdo->query("
    SELECT m.nome, l.numero_lote, mov.tipo, mov.quantidade, mov.data, u.nome as usuario
    FROM movimentacoes mov
    JOIN medicamentos m ON mov.medicamento_id = m.id
    JOIN lotes l ON mov.lote_id = l.id
    JOIN usuarios u ON mov.usuario_cpf = u.cpf
    ORDER BY mov.data DESC
")->fetchAll();
?>

<h2>Movimentações</h2>

<table class="table table-bordered">
    <tr>
        <th>Medicamento</th>
        <th>Lote</th>
        <th>Tipo</th>
        <th>Qtd</th>
        <th>Usuário</th>
        <th>Data</th>
    </tr>

    <?php foreach ($movs as $m): ?>
        <tr>
            <td>
                <?= $m['nome'] ?>
            </td>
            <td>
                <?= $m['numero_lote'] ?>
            </td>
            <td>
                <?php if ($m['tipo'] == 'entrada'): ?>
                    <span class="badge bg-success">Entrada</span>
                <?php elseif ($m['tipo'] == 'saida'): ?>
                    <span class="badge bg-danger">Saída</span>
                <?php else: ?>
                    <span class="badge bg-warning">Ajuste</span>
                <?php endif; ?>
            </td>
            <td>
                <?= $m['quantidade'] ?>
            </td>
            <td>
                <?= $m['usuario'] ?>
            </td>
            <td>
                <?= date('d/m/Y H:i', strtotime($m['data'])) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include 'footer.php'; ?>