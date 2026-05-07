<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';

$user = $_SESSION['user'] ?? null;

if (!$user) {
    header("Location: ../index.php");
    exit;
}

// CLIENTES
$clientes = $pdo->query("
    SELECT *
    FROM clientes
    WHERE status='ativo'
    ORDER BY nome
")->fetchAll();

// MEDICAMENTOS
$meds = $pdo->query("
    SELECT *
    FROM medicamentos
    WHERE ativo = 1
    ORDER BY nome
")->fetchAll();

// ESTOQUE
$lotes = $pdo->query("
    SELECT
        l.*,
        m.nome
    FROM lotes l
    INNER JOIN medicamentos m
        ON l.medicamento_id = m.id
    ORDER BY m.nome, l.validade
")->fetchAll();
?>

<!-- SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- TEMA BOOTSTRAP -->
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />

<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    .estoque-baixo {
        background: #fff3cd;
    }

    .estoque-vencido {
        background: #f8d7da;
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }
</style>

<h2 class="mb-4">
    <i class="fas fa-truck"></i>
    Registrar Entrega
</h2>

<!-- ALERTAS -->
<?php if (isset($_GET['msg'])): ?>

    <div class="alert alert-<?= $_GET['tipo'] ?> alert-dismissible fade show">

        <?= $_GET['msg'] ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

    </div>

<?php endif; ?>

<!-- FORM -->
<div class="card shadow mb-4">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle"></i>
            Nova Entrega
        </h5>
    </div>

    <div class="card-body">

        <form action="../controllers/entregaController.php" method="POST">

            <div class="row">

                <!-- CLIENTE -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Cliente
                    </label>

                    <select name="cliente_cpf" id="cliente" class="form-select select2" required>

                        <option value=""></option>

                        <?php foreach ($clientes as $c): ?>

                            <option value="<?= $c['cpf'] ?>">

                                <?= $c['nome'] ?>
                                —
                                CPF:
                                <?= preg_replace(
                                    '/(\d{3})(\d{3})(\d{3})(\d{2})/',
                                    '$1.$2.$3-$4',
                                    $c['cpf']
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- MEDICAMENTO -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Medicamento
                    </label>

                    <select id="medicamento" name="medicamento_id" class="form-select select2" required>

                        <option value=""></option>

                        <?php foreach ($meds as $m): ?>

                            <option value="<?= $m['id'] ?>">
                                <?= $m['nome'] ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- LOTE -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Lote
                    </label>

                    <select id="lote" name="lote_id" class="form-select" required>

                        <option value="">
                            Selecione um medicamento
                        </option>

                    </select>

                </div>

                <!-- QUANTIDADE -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Quantidade
                    </label>

                    <input type="number" name="quantidade" id="quantidade" class="form-control" min="1"
                        placeholder="Informe a quantidade" required>

                    <small id="estoqueInfo" class="text-muted"></small>

                </div>

                <!-- RESPONSÁVEL -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Responsável (Sistema)
                    </label>

                    <input type="text" class="form-control" value="<?= $user['nome'] ?>" readonly>

                    <input type="hidden" name="balconista_cpf" value="<?= $user['cpf'] ?>">

                </div>

                <!-- COMPRADOR -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Responsável pela compra
                    </label>

                    <input type="text" name="comprador" class="form-control" placeholder="Opcional">

                </div>

            </div>

            <button class="btn btn-success">

                <i class="fas fa-save"></i>
                Registrar Entrega

            </button>

        </form>

    </div>

</div>

<!-- ESTOQUE -->
<div class="card shadow">

    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <i class="fas fa-boxes"></i>
            Estoque Atual
        </h5>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-hover table-bordered align-middle">

            <thead class="table-light">

                <tr>
                    <th>Medicamento</th>
                    <th>Lote</th>
                    <th>Validade</th>
                    <th>Quantidade</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($lotes as $l): ?>

                    <?php

                    $vencido = strtotime($l['validade']) < strtotime(date('Y-m-d'));

                    $baixo = $l['quantidade'] <= 10;

                    ?>

                    <tr class="
                        <?= $vencido ? 'estoque-vencido' : '' ?>
                        <?= (!$vencido && $baixo) ? 'estoque-baixo' : '' ?>
                    ">

                        <td><?= $l['nome'] ?></td>

                        <td><?= $l['numero_lote'] ?></td>

                        <td>
                            <?= date('d/m/Y', strtotime($l['validade'])) ?>
                        </td>

                        <td><?= $l['quantidade'] ?></td>

                        <td>

                            <?php if ($vencido): ?>

                                <span class="badge bg-danger">
                                    Vencido
                                </span>

                            <?php elseif ($baixo): ?>

                                <span class="badge bg-warning text-dark">
                                    Baixo
                                </span>

                            <?php else: ?>

                                <span class="badge bg-success">
                                    Disponível
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include 'footer.php'; ?>

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SELECT2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    // =====================================
    // SELECT2
    // =====================================

    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true
    });

    $('#cliente').select2({
        placeholder: 'Pesquisar cliente por nome ou CPF...',
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#medicamento').select2({
        placeholder: 'Pesquisar medicamento...',
        theme: 'bootstrap-5',
        width: '100%'
    });

    // =====================================
    // ELEMENTOS
    // =====================================

    const lote = document.getElementById('lote');
    const quantidade = document.getElementById('quantidade');
    const estoqueInfo = document.getElementById('estoqueInfo');

    let estoqueAtual = 0;

    // =====================================
    // CARREGAR LOTES
    // =====================================

    $('#medicamento').on('change select2:select', function () {

        let id = $(this).val();

        quantidade.value = '';
        quantidade.max = '';
        quantidade.disabled = false;

        estoqueInfo.innerHTML = '';

        // LOADING
        lote.innerHTML = `
            <option value="">
                Carregando lotes...
            </option>
        `;

        if (!id) {

            lote.innerHTML = `
                <option value="">
                    Selecione um medicamento
                </option>
            `;

            return;
        }

        // BUSCAR LOTES
        fetch('../controllers/buscaLotes.php?medicamento_id=' + id)

            .then(res => res.text())

            .then(data => {

                // CARREGA LOTES
                lote.innerHTML = data;

                // TODOS OS LOTES
                let opcoes = lote.querySelectorAll('option');

                let loteSelecionado = false;

                // PROCURA PRIMEIRO LOTE VÁLIDO
                for (let i = 0; i < opcoes.length; i++) {

                    let opcao = opcoes[i];

                    let qtd = parseInt(opcao.dataset.qtd || 0);
                    let vencido = opcao.dataset.vencido;

                    if (
                        opcao.value &&
                        qtd > 0 &&
                        vencido != '1'
                    ) {

                        // SELECIONA
                        lote.selectedIndex = i;

                        loteSelecionado = true;

                        break;
                    }
                }

                // ATUALIZA ESTOQUE
                if (loteSelecionado) {

                    atualizarEstoque();

                } else {

                    quantidade.disabled = true;

                    estoqueInfo.innerHTML = `
                        <span class="text-danger">
                            Nenhum lote disponível.
                        </span>
                    `;
                }

            });

    });

    // =====================================
    // ATUALIZAR ESTOQUE
    // =====================================

    function atualizarEstoque() {

        let option = lote.options[lote.selectedIndex];

        if (!option) return;

        let qtd = parseInt(option.dataset.qtd || 0);
        let vencido = option.dataset.vencido;

        estoqueAtual = qtd;

        quantidade.value = '';
        quantidade.max = estoqueAtual;

        // VENCIDO
        if (vencido == '1') {

            quantidade.disabled = true;

            estoqueInfo.innerHTML = `
                <span class="text-danger">
                    ⚠ Este lote está vencido.
                </span>
            `;

            return;
        }

        quantidade.disabled = false;

        estoqueInfo.innerHTML = `
            Estoque disponível:
            <strong>${estoqueAtual}</strong>
        `;
    }

    // =====================================
    // TROCA DE LOTE
    // =====================================

    lote.addEventListener('change', atualizarEstoque);

    // =====================================
    // VALIDAR QUANTIDADE
    // =====================================

    quantidade.addEventListener('input', function () {

        let valor = parseInt(this.value);

        if (!valor) return;

        if (valor > estoqueAtual) {

            alert('Quantidade acima do estoque.');

            this.value = estoqueAtual;
        }

        if (valor < 1) {

            this.value = 1;
        }

    });

</script>