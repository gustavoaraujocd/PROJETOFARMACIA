<?php
require_once "../config/db.php";

$cpf = $_GET['cpf'];

$sql = $pdo->prepare("DELETE FROM clientes WHERE cpf = ?");
$sql->execute([$cpf]);

header("Location: ../views/clientes_lista.php");

session_start();

if ($_SESSION['user']['tipo'] != 'admin' && $_SESSION['user']['tipo'] != 'supervisor') {
    die("Acesso negado");
}