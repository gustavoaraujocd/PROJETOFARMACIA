<?php
require_once "../config/db.php";

$medicamento_id = $_GET['medicamento_id'] ?? 0;

$sql = $pdo->prepare("
    SELECT * FROM lotes 
    WHERE medicamento_id = ?
    AND validade >= CURDATE()   -- 🔒 BLOQUEIA VENCIDOS
    AND quantidade > 0
    ORDER BY validade ASC
");

$sql->execute([$medicamento_id]);

if ($sql->rowCount() == 0) {
    echo "<option value=''>Nenhum lote válido disponível</option>";
    exit;
}

foreach ($sql->fetchAll() as $l) {

    echo "<option value='{$l['id']}'>
        Lote: {$l['numero_lote']} | Val: " . date('d/m/Y', strtotime($l['validade'])) . " | Qtd: {$l['quantidade']}
    </option>";
}   