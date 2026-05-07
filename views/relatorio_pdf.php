<?php
require_once "../config/db.php";
require_once __DIR__ . "/../dompdf/autoload.inc.php";

use Dompdf\Dompdf;

$dompdf = new Dompdf();

// BUSCAR DADOS
$dados = $pdo->query("
SELECT 
c.nome AS cliente,
m.nome AS medicamento,
l.numero_lote,
l.validade,
e.quantidade,
e.data_entrega,
u.nome AS balconista,
e.responsavel
FROM entregas e
JOIN clientes c ON e.cliente_cpf = c.cpf
JOIN medicamentos m ON e.medicamento_id = m.id
JOIN lotes l ON e.lote_id = l.id
JOIN usuarios u ON e.balconista_cpf = u.cpf
ORDER BY e.data_entrega DESC
")->fetchAll();

// HTML DO PDF
$html = "
<h2 style='text-align:center'>Relatório de Entregas</h2>

<table border='1' width='100%' cellspacing='0' cellpadding='6'>
<tr style='background:#0d6efd; color:white'>
<th>Cliente</th>
<th>Medicamento</th>
<th>Lote</th>
<th>Validade</th>
<th>Qtd</th>
<th>Data</th>
<th>Balconista</th>
<th>Responsável</th>
</tr>
";

// LOOP
foreach ($dados as $d) {
    $html .= "
    <tr>
        <td>{$d['cliente']}</td>
        <td>{$d['medicamento']}</td>
        <td>{$d['numero_lote']}</td>
        <td>{$d['validade']}</td>
        <td>{$d['quantidade']}</td>
        <td>{$d['data_entrega']}</td>
        <td>{$d['balconista']}</td>
        <td>{$d['responsavel']}</td>
    </tr>
    ";
}

$html .= "</table>";

// GERAR PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // horizontal
$dompdf->render();
$dompdf->stream("relatorio_entregas.pdf", ["Attachment" => false]);