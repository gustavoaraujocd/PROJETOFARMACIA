<?php
require_once "../config/db.php";

$nome = uniqid() . "_" . $_FILES['arquivo']['name'];
move_uploaded_file($_FILES['arquivo']['tmp_name'], "../uploads/" . $nome);

$sql = $pdo->prepare("
INSERT INTO receitas (cliente_cpf, arquivo, data_emissao, validade, medico)
VALUES (?, ?, ?, ?, ?)
");

$sql->execute([
    $_POST['cliente_cpf'],
    $nome,
    $_POST['data_emissao'],
    $_POST['validade'],
    $_POST['medico']
]);

echo "Receita enviada!";

// dentro do entregaController
$receita = $pdo->prepare("
SELECT * FROM receitas 
WHERE cliente_cpf = ? 
AND status = 'aprovada'
AND validade >= CURDATE()
");

$receita->execute([$cliente]);

if ($receita->rowCount() == 0) {
    die("Cliente sem receita válida!");
}