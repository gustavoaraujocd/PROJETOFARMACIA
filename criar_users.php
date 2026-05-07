<?php
require_once "config/db.php";

// SENHAS
$senhaAdmin = password_hash("admin1", PASSWORD_DEFAULT);
$senhaBalconista = password_hash("balconista1", PASSWORD_DEFAULT);

// ADMIN
$sql = $pdo->prepare("
INSERT INTO usuarios (cpf, nome, telefone, email, tipo, senha)
VALUES (?, ?, ?, ?, ?, ?)
");

$sql->execute([
    "11111111111",
    "Administrador",
    "(61) 9 9999-9999",
    "admin@farmacia.com",
    "admin",
    $senhaAdmin
]);

// BALCONISTA
$sql->execute([
    "22222222222",
    "Balconista",
    "(61) 9 8888-8888",
    "balconista@farmacia.com",
    "balconista",
    $senhaBalconista
]);

echo "Usuários criados com sucesso!";