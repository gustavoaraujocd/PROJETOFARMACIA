<?php
session_start();
require_once "../config/db.php";

$clientes = $pdo->query("SELECT * FROM clientes")->fetchAll();
?>

<h2>Enviar Receita</h2>

<form action="../controllers/receitaController.php" method="POST" enctype="multipart/form-data">

<select name="cliente_cpf" required>
    <option value="">Selecione o cliente</option>
    <?php foreach($clientes as $c): ?>
        <option value="<?= $c['cpf'] ?>"><?= $c['nome'] ?></option>
    <?php endforeach; ?>
</select>

<input type="file" name="arquivo" required>
<input type="date" name="data_emissao" required>
<input type="date" name="validade" required>
<input type="text" name="medico" placeholder="Nome do médico" required>

<button type="submit">Enviar</button>
</form>