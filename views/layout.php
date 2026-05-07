<?php
require_once "../config/auth.php";
require_once "../config/rbac.php";

$user = $_SESSION['user'];
$pagina = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Sistema Farmácia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f1f5f9;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #1e293b;
            color: white;
        }

        .sidebar h4 {
            padding: 20px;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #cbd5f5;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: #334155;
            color: white;
        }

        .sidebar a.active {
            background: #0d6efd;
            color: white;
        }

        /* TOPBAR */
        .topbar {
            margin-left: 250px;
            height: 60px;
            background: #0f172a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        /* CONTENT */
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        /* ALERT */
        .alert-custom {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 9999;
            min-width: 250px;
        }

        /* RESPONSIVO */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .topbar,
            .content {
                margin-left: 200px;
            }
        }
    </style>

</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4><i class="fas fa-clinic-medical"></i> Farmácia</h4>

        <a href="dashboard.php" class="<?= $pagina == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <?php if (temPermissao('gerenciar_usuarios')): ?>
            <a href="usuarios.php" class="<?= $pagina == 'usuarios.php' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Usuários
            </a>
        <?php endif; ?>

        <?php if (temPermissao('gerenciar_clientes')): ?>
            <a href="clientes.php" class="<?= $pagina == 'clientes.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Clientes
            </a>
        <?php endif; ?>

        <?php if (temPermissao('gerenciar_medicamentos')): ?>
            <a href="medicamentos.php" class="<?= $pagina == 'medicamentos.php' ? 'active' : '' ?>">
                <i class="fas fa-pills"></i> Medicamentos
            </a>
        <?php endif; ?>

        <?php if (temPermissao('gerenciar_lotes')): ?>
            <a href="lotes.php" class="<?= $pagina == 'lotes.php' ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Estoque
            </a>
        <?php endif; ?>

        <?php if (temPermissao('ver_relatorios')): ?>
            <a href="relatorios.php" class="<?= $pagina == 'relatorios.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i> Relatórios
            </a>
        <?php endif; ?>

        <?php if (temPermissao('registrar_entregas')): ?>
            <a href="entregas.php" class="<?= $pagina == 'entregas.php' ? 'active' : '' ?>">
                <i class="fas fa-truck"></i> Entregas
            </a>
        <?php endif; ?>

    </div>

    <!-- TOPBAR -->
    <div class="topbar">
        <span>
            <i class="fas fa-user-circle"></i>
            <?= $user['nome'] ?>
        </span>

        <a href="../controllers/logout.php" class="btn btn-danger btn-sm">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- ALERTA BONITO -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= $_GET['tipo'] ?? 'info' ?> alert-dismissible fade show alert-custom">
            <?= $_GET['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- CONTEÚDO -->
    <div class="content">