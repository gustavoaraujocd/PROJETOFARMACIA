<?php
require_once "../config/db.php";

$busca = $_GET['busca'] ?? '';
$busca = "%" . $busca . "%";

$sql = $pdo->prepare("
    SELECT * FROM medicamentos 
    WHERE nome LIKE ? OR fabricante LIKE ?
    ORDER BY nome
");

$sql->execute([$busca, $busca]);

$dados = $sql->fetchAll();

if (!$dados) {
    echo "<tr><td colspan='4' class='text-center text-muted'>Nenhum medicamento encontrado</td></tr>";
    exit;
}

foreach ($dados as $m) {

    $status = $m['ativo']
        ? "<span class='badge bg-success'>Ativo</span>"
        : "<span class='badge bg-danger'>Inativo</span>";

    echo "
    <tr>
        <td>{$m['nome']}</td>
        <td>{$m['fabricante']}</td>
        <td>{$status}</td>
        <td>

            <!-- EDITAR -->
            <a href='medicamento_editar.php?id={$m['id']}' 
               class='btn btn-warning btn-sm'>
               <i class='fas fa-edit'></i>
            </a>

            <!-- ATIVAR / DESATIVAR -->
            <a href='../controllers/medicamentoController.php?acao=toggle&id={$m['id']}' 
               class='btn btn-secondary btn-sm'>
               <i class='fas fa-power-off'></i>
            </a>

            <!-- DESATIVAR -->
            <a href='../controllers/medicamentoController.php?acao=excluir&id={$m['id']}' 
               class='btn btn-danger btn-sm'
               onclick=\"return confirm('Deseja desativar este medicamento?')\">
               <i class='fas fa-trash'></i>
            </a>

        </td>
    </tr>
    ";
}