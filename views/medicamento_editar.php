<?php include 'layout.php'; ?>
<?php require_once "../config/db.php"; ?>

<?php
// 🔒 validação do ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: medicamentos.php?msg=ID inválido&tipo=danger");
    exit;
}

$id = $_GET['id'];

$sql = $pdo->prepare("SELECT * FROM medicamentos WHERE id = ?");
$sql->execute([$id]);
$m = $sql->fetch();

// 🚫 se não encontrar
if (!$m) {
    header("Location: medicamentos.php?msg=Medicamento não encontrado&tipo=danger");
    exit;
}
?>

<h2 class="mb-4">Editar Medicamento</h2>

<div class="card shadow">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Atualizar Dados</h5>
    </div>

    <div class="card-body">

        <form action="../controllers/medicamentoController.php" method="POST">

            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($m['nome']) ?>" class="form-control"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Fabricante</label>
                    <input type="text" name="fabricante" value="<?= htmlspecialchars($m['fabricante']) ?>"
                        class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Composição</label>
                    <input type="text" name="composicao" value="<?= htmlspecialchars($m['composicao']) ?>"
                        class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Posologia</label>
                    <input type="text" name="posologia" value="<?= htmlspecialchars($m['posologia']) ?>"
                        class="form-control">
                </div>

                <!-- STATUS -->
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select name="ativo" class="form-control">
                        <option value="1" <?= $m['ativo'] ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= !$m['ativo'] ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>

            </div>

            <div class="d-flex gap-2">

                <button class="btn btn-success">
                    <i class="fas fa-save"></i> Salvar
                </button>

                <a href="medicamentos.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>

            </div>

        </form>

    </div>
</div>

<?php include 'footer.php'; ?>