<?php
require_once "db.php";

function logSistema($usuario, $acao){
    global $pdo;

    $sql = $pdo->prepare("INSERT INTO logs (usuario, acao) VALUES (?, ?)");
    $sql->execute([$usuario, $acao]);
}
logSistema($_SESSION['user']['nome'], "Realizou entrega");