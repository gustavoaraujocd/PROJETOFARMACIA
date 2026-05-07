<?php
require_once "../config/auth.php";
require_once "../config/db.php";
require_once "../config/rbac.php";

include 'layout.php';

// 🔐 segurança
$user = $_SESSION['user'] ?? null;

if (!$user) {
    header("Location: ../index.php");
    exit;
}

// 🔔 ESTOQUE BAIXO
$baixo = $pdo->query("
    SELECT m.nome, l.numero_lote, l.quantidade 
    FROM lotes l
    JOIN medicamentos m ON l.medicamento_id = m.id
    WHERE l.quantidade <= 10
")->fetchAll();

// ⚠️ VENCIMENTO PRÓXIMO
$vencendo = $pdo->query("
    SELECT m.nome, l.validade
    FROM lotes l
    JOIN medicamentos m ON l.medicamento_id = m.id
    WHERE l.validade <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
")->fetchAll();
?>

<h2 class="mb-4">Dashboard</h2>

<div class="row">

    <?php if (temPermissao('gerenciar_usuarios')): ?>

        <!-- USUÁRIOS -->
        <div class="col-md-3">
            <div class="card shadow border-0 mb-3 text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h5>Usuários</h5>
                    <a href="usuarios.php" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if (temPermissao('gerenciar_medicamentos')): ?>

        <!-- MEDICAMENTOS -->
        <div class="col-md-3">
            <div class="card shadow border-0 mb-3 text-center">
                <div class="card-body">
                    <i class="fas fa-pills fa-2x text-success mb-2"></i>
                    <h5>Medicamentos</h5>
                    <a href="medicamentos.php" class="btn btn-success btn-sm">Acessar</a>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if (temPermissao('ver_relatorios')): ?>

        <!-- RELATÓRIOS -->
        <div class="col-md-3">
            <div class="card shadow border-0 mb-3 text-center">
                <div class="card-body">
                    <i class="fas fa-chart-bar fa-2x text-warning mb-2"></i>
                    <h5>Relatórios</h5>
                    <a href="relatorios.php" class="btn btn-warning btn-sm">Acessar</a>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if (temPermissao('registrar_entregas')): ?>

        <!-- ENTREGAS -->
        <div class="col-md-3">
            <div class="card shadow border-0 mb-3 text-center">
                <div class="card-body">
                    <i class="fas fa-truck fa-2x text-danger mb-2"></i>
                    <h5>Entregas</h5>
                    <a href="entregas.php" class="btn btn-danger btn-sm">Acessar</a>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<!-- ALERTAS -->
<?php if (!empty($baixo)): ?>
    <div class="alert alert-warning shadow-sm">
        <strong>⚠️ Estoque baixo:</strong>
        <ul class="mb-0">
            <?php foreach ($baixo as $b): ?>
                <li>
                    <?= $b['nome'] ?>
                    (Lote <?= $b['numero_lote'] ?>)
                    - <?= $b['quantidade'] ?> unidades
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($vencendo)): ?>
    <div class="alert alert-danger shadow-sm">
        <strong>⚠️ Próximo do vencimento:</strong>
        <ul class="mb-0">
            <?php foreach ($vencendo as $v): ?>
                <li>
                    <?= $v['nome'] ?>
                    - vence em <?= date('d/m/Y', strtotime($v['validade'])) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- GRÁFICO -->
<div class="card shadow mt-4">
    <div class="card-header bg-dark text-white">
        Estoque de Medicamentos
    </div>
    <div class="card-body">
        <canvas id="grafico"></canvas>
    </div>
</div>

<!-- LOADER -->
<div id="loader" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    z-index:9999;
    justify-content:center;
    align-items:center;
    color:white;
    font-size:20px;
">
    ⏳ Carregando...
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    fetch('../controllers/dadosGrafico.php')
        .then(res => res.json())
        .then(dados => {

            new Chart(document.getElementById('grafico'), {
                type: 'bar',
                data: {
                    labels: dados.nomes,
                    datasets: [{
                        label: 'Estoque',
                        data: dados.qtd
                    }]
                }
            });

        });

    // loader automático
    document.querySelectorAll('form').forEach(f => {
        f.addEventListener('submit', () => {
            document.getElementById('loader').style.display = 'flex';
        });
    });
</script>