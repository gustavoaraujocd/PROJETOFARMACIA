<?php
require_once "../config/db.php";

$senha = $_POST['senha'];

// valida regra: 6 caracteres + letra
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', $senha)) {
    die("Senha inválida");
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$sql = $pdo->prepare("INSERT INTO usuarios 
(cpf, nome, telefone, email, tipo, senha) 
VALUES (?, ?, ?, ?, ?, ?)");

$sql->execute([
    $_POST['cpf'],
    $_POST['nome'],
    $_POST['telefone'],
    $_POST['email'],
    $_POST['tipo'],
    $senhaHash
]);

echo "Usuário criado!";