<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';

// MEDICAMENTOS ATIVOS
$meds = $pdo->query("
    SELECT * 
    FROM medicamentos 
    WHERE ativo = 1
    ORDER BY nome
")->fetchAll();

// LOTES
$lotes = $pdo->query("
    SELECT 
        l.*,
        m.nome
    FROM lotes l
    INNER JOIN medicamentos m
        ON l.medicamento_id = m.id
    ORDER BY l.validade ASC
")->fetchAll();
?>

<!-- SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding-top: 4px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }

    .select2-selection__arrow {
        height: 38px !important;
    }

    .estoque-vencido {
        background-color: #f8d7da !important;
    }

    .estoque-baixo {
        background-color: #fff3cd !important;
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }
</style>

<h2 class="mb-4">
    <i class="fas fa-boxes"></i>
    Controle de Lotes
</h2>

<!-- ALERTAS -->
<?php if (isset($_GET['msg'])): ?>

    <div class="alert alert-<?= $_GET['tipo'] ?> alert-dismissible fade show">

        <?= $_GET['msg'] ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>

<!-- CADASTRO -->
<div class="card shadow mb-4">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle"></i>
            Novo Lote
        </h5>
    </div>

    <div class="card-body">

        <form action="../controllers/loteController.php" method="POST">

            <input type="hidden" name="acao" value="criar">

            <div class="row">

                <!-- MEDICAMENTO PESQUISÁVEL -->
                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Medicamento
                    </label>

                    <select name="medicamento_id" class="form-control select2" required>

                        <option value="">
                            Pesquisar medicamento...
                        </option>

                        <?php foreach ($meds as $m): ?>

                            <option value="<?= $m['id'] ?>">

                                <?= $m['nome'] ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- LOTE -->
                <div class="col-md-2 mb-3">

                    <label class="form-label">
                        Número do Lote
                    </label>

                    <input type="text" name="numero_lote" class="form-control" maxlength="50" placeholder="Ex: LT2025"
                        required>

                </div>

                <!-- FABRICAÇÃO -->
                <div class="col-md-2 mb-3">

                    <label class="form-label">
                        Fabricação
                    </label>

                    <input type="date" name="fabricacao" class="form-control" required>

                </div>

                <!-- VALIDADE -->
                <div class="col-md-2 mb-3">

                    <label class="form-label">
                        Validade
                    </label>

                    <input type="date" name="validade" class="form-control" required>

                </div>

                <!-- QUANTIDADE -->
                <div class="col-md-2 mb-3">

                    <label class="form-label">
                        Quantidade
                    </label>

                    <input type="number" name="quantidade" class="form-control" min="1" placeholder="0" required>

                </div>

            </div>

            <button class="btn btn-success">

                <i class="fas fa-save"></i>
                Cadastrar Lote

            </button>

        </form>

    </div>

</div>

<!-- ESTOQUE -->
<div class="card shadow">

    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <i class="fas fa-warehouse"></i>
            Estoque Atual
        </h5>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-hover table-bordered align-middle">

            <thead class="table-light">

                <tr>
                    <th>Medicamento</th>
                    <th>Lote</th>
                    <th>Fabricação</th>
                    <th>Validade</th>
                    <th>Quantidade</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

                <?php if (!$lotes): ?>

                    <tr>

                        <td colspan="6" class="text-center">

                            Nenhum lote cadastrado.

                        </td>

                    </tr>

                <?php endif; ?>

                <?php foreach ($lotes as $l): ?>

                    <?php

                    $vencido = strtotime($l['validade']) < strtotime(date('Y-m-d'));
                    $baixo = $l['quantidade'] <= 10;

                    ?>

                    <tr class="
                        <?= $vencido ? 'estoque-vencido' : '' ?>
                        <?= (!$vencido && $baixo) ? 'estoque-baixo' : '' ?>
                    ">

                        <!-- MEDICAMENTO -->
                        <td>

                            <strong>
                                <?= $l['nome'] ?>
                            </strong>

                        </td>

                        <!-- LOTE -->
                        <td>

                            <?= $l['numero_lote'] ?>

                        </td>

                        <!-- FABRICAÇÃO -->
                        <td>

                            <?= date('d/m/Y', strtotime($l['fabricacao'])) ?>

                        </td>

                        <!-- VALIDADE -->
                        <td>

                            <?= date('d/m/Y', strtotime($l['validade'])) ?>

                        </td>

                        <!-- QUANTIDADE -->
                        <td>

                            <span class="badge bg-primary">

                                <?= $l['quantidade'] ?>

                            </span>

                        </td>

                        <!-- STATUS -->
                        <td>

                            <?php if ($vencido): ?>

                                <span class="badge bg-danger">

                                    <i class="fas fa-times-circle"></i>
                                    Vencido

                                </span>

                            <?php elseif ($baixo): ?>

                                <span class="badge bg-warning text-dark">

                                    <i class="fas fa-exclamation-triangle"></i>
                                    Estoque Baixo

                                </span>

                            <?php else: ?>

                                <span class="badge bg-success">

                                    <i class="fas fa-check-circle"></i>
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

    // =========================
    // SELECT2 PESQUISÁVEL
    // =========================

    $('.select2').select2({

        width: '100%',
        placeholder: 'Pesquisar medicamento...',
        allowClear: true

    });

</script>