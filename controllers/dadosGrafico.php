<?php
require_once "../config/db.php";

$sql = $pdo->query("
SELECT m.nome, SUM(l.quantidade) as total
FROM medicamentos m
LEFT JOIN lotes l ON l.medicamento_id = m.id
GROUP BY m.id
");

$nomes = [];
$qtd = [];

foreach ($sql as $row) {
    $nomes[] = $row['nome'];
    $qtd[] = (int) $row['total'];
}

echo json_encode([
    "nomes" => $nomes,
    "qtd" => $qtd
]);