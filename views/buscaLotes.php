<?php

require_once "../config/db.php";

$medicamento_id = $_GET['medicamento_id'] ?? null;

// VALIDA
if (!$medicamento_id) {

    echo "
        <option value=''>
            Medicamento inválido
        </option>
    ";

    exit;
}

// BUSCA LOTES
$sql = $pdo->prepare("
    SELECT *
    FROM lotes
    WHERE medicamento_id = ?
    AND quantidade > 0
    ORDER BY validade ASC
");

$sql->execute([$medicamento_id]);

$lotes = $sql->fetchAll();

// SEM LOTES
if (!$lotes) {

    echo "
        <option value=''>
            Nenhum lote disponível
        </option>
    ";

    exit;
}

// MONTA OPTIONS
foreach ($lotes as $l) {

    // VERIFICA VENCIMENTO
    $vencido = (
        strtotime($l['validade']) < strtotime(date('Y-m-d'))
    );

    // STATUS
    $status = $vencido
        ? 'VENCIDO'
        : 'DISPONÍVEL';

    // OPTION
    echo '<option 
            value="' . $l['id'] . '"
            data-qtd="' . $l['quantidade'] . '"
            data-vencido="' . ($vencido ? 1 : 0) . '"
        >

        Lote ' . $l['numero_lote'] . '
        | Validade: ' . date('d/m/Y', strtotime($l['validade'])) . '
        | Qtd: ' . $l['quantidade'] . '
        | ' . $status . '

    </option>';
}
?>