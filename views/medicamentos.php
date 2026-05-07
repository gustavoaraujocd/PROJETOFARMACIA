<?php
include 'layout.php';
require_once "../config/db.php";

// LISTAR MEDICAMENTOS
$meds = $pdo->query("
    SELECT * 
    FROM medicamentos
    ORDER BY nome
")->fetchAll();
?>

<style>
    .card {
        border: none;
        border-radius: 12px;
    }

    .table th {
        white-space: nowrap;
    }

    .badge-status {
        font-size: 13px;
        padding: 7px 10px;
    }

    .campo-busca {
        border-radius: 10px;
        height: 45px;
        font-size: 15px;
    }

    .btn-sm {
        border-radius: 8px;
    }

    .table-hover tbody tr:hover {
        background: #f8f9fa;
    }

    .medicamento-inativo {
        background: #f8d7da !important;
    }
</style>

<h2 class="mb-4">
    <i class="fas fa-pills"></i> Medicamentos
</h2>

<!-- ALERTAS -->
<?php if (isset($_GET['msg'])): ?>

    <div class="alert alert-<?= $_GET['tipo'] ?> alert-dismissible fade show">
        <?= $_GET['msg'] ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert">
        </button>
    </div>

<?php endif; ?>

<!-- BUSCA -->
<div class="card shadow mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-12">

                <input type="text" id="busca" class="form-control campo-busca"
                    placeholder="🔍 Pesquisar medicamento por nome, fabricante ou composição...">

            </div>

        </div>

    </div>

</div>

<!-- CADASTRO -->
<div class="card shadow mb-4">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle"></i>
            Cadastrar Medicamento
        </h5>
    </div>

    <div class="card-body">

        <form action="../controllers/medicamentoController.php" method="POST">

            <input type="hidden" name="acao" value="criar">

            <div class="row">

                <!-- NOME -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Nome do medicamento
                    </label>

                    <input type="text" name="nome" class="form-control" maxlength="100" placeholder="Ex: Dipirona 500mg"
                        required>

                </div>

                <!-- FABRICANTE -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Fabricante
                    </label>

                    <input type="text" name="fabricante" class="form-control" maxlength="100" placeholder="Ex: EMS">

                </div>

                <!-- COMPOSIÇÃO -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Composição
                    </label>

                    <input type="text" name="composicao" class="form-control" maxlength="255"
                        placeholder="Ex: Dipirona monoidratada">

                </div>

                <!-- POSOLOGIA -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Posologia
                    </label>

                    <input type="text" name="posologia" class="form-control" maxlength="255"
                        placeholder="Ex: 1 comprimido a cada 8 horas">

                </div>

            </div>

            <button class="btn btn-success">

                <i class="fas fa-save"></i>
                Cadastrar

            </button>

        </form>

    </div>

</div>

<!-- LISTA -->
<div class="card shadow">

    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <i class="fas fa-list"></i>
            Lista de Medicamentos
        </h5>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-hover align-middle table-bordered">

            <thead class="table-light">

                <tr>
                    <th>Nome</th>
                    <th>Fabricante</th>
                    <th>Composição</th>
                    <th>Status</th>
                    <th width="220">Ações</th>
                </tr>

            </thead>

            <tbody id="tabela">

                <?php if (!$meds): ?>

                    <tr>
                        <td colspan="5" class="text-center">
                            Nenhum medicamento cadastrado.
                        </td>
                    </tr>

                <?php endif; ?>

                <?php foreach ($meds as $m): ?>

                    <tr class="<?= !$m['ativo'] ? 'medicamento-inativo' : '' ?>">

                        <!-- NOME -->
                        <td>
                            <strong>
                                <?= htmlspecialchars($m['nome']) ?>
                            </strong>
                        </td>

                        <!-- FABRICANTE -->
                        <td>
                            <?= htmlspecialchars($m['fabricante']) ?>
                        </td>

                        <!-- COMPOSIÇÃO -->
                        <td>
                            <?= htmlspecialchars($m['composicao']) ?>
                        </td>

                        <!-- STATUS -->
                        <td>

                            <?php if ($m['ativo']): ?>

                                <span class="badge bg-success badge-status">
                                    Ativo
                                </span>

                            <?php else: ?>

                                <span class="badge bg-danger badge-status">
                                    Inativo
                                </span>

                            <?php endif; ?>

                        </td>

                        <!-- AÇÕES -->
                        <td>

                            <!-- EDITAR -->
                            <a href="medicamento_editar.php?id=<?= $m['id'] ?>" class="btn btn-warning btn-sm"
                                title="Editar">

                                <i class="fas fa-edit"></i>

                            </a>

                            <!-- ATIVAR / DESATIVAR -->
                            <a href="../controllers/medicamentoController.php?acao=toggle&id=<?= $m['id'] ?>"
                                class="btn btn-secondary btn-sm"
                                onclick="return confirm('Deseja alterar o status deste medicamento?')"
                                title="Ativar / Desativar">

                                <i class="fas fa-power-off"></i>

                            </a>

                            <!-- EXCLUIR -->
                            <a href="../controllers/medicamentoController.php?acao=excluir&id=<?= $m['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Deseja realmente excluir este medicamento?')" title="Excluir">

                                <i class="fas fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include 'footer.php'; ?>

<!-- BUSCA AJAX -->
<script>

    let timerBusca;

    const campoBusca = document.getElementById('busca');
    const tabela = document.getElementById('tabela');

    campoBusca.addEventListener('keyup', function () {

        clearTimeout(timerBusca);

        let valor = encodeURIComponent(this.value);

        timerBusca = setTimeout(() => {

            fetch('../controllers/buscaMedicamento.php?busca=' + valor)

                .then(response => response.text())

                .then(data => {

                    tabela.innerHTML = data;

                })

                .catch(() => {

                    tabela.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger">
                                Erro ao pesquisar medicamentos.
                            </td>
                        </tr>
                    `;

                });

        }, 300);

    });

</script>