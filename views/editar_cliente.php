<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';

$cpf = $_GET['cpf'] ?? null;

$sql = $pdo->prepare("SELECT * FROM clientes WHERE cpf=?");
$sql->execute([$cpf]);
$c = $sql->fetch();
?>

<h2 class="mb-4">Editar Cliente</h2>

<div class="card shadow">
    <div class="card-header bg-warning te] ^~n xt-dark">
        <h5 class="mb-0">Atualizar Dados</h5>
    </div>

    <div class="card-body">

        <form action="../controllers/clienteEditar.php" method="POST">

            <input type="hidden" name="cpf" value="<?= $c['cpf'] ?>">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?= $c['nome'] ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="<?= $c['telefone'] ?>">
                </div>

                <div class="col-md-12 mb-3">
                    <label>Endereço</label>
                    <input type="text" name="endereco" class="form-control" value="<?= $c['endereco'] ?>">
                </div>

            </div>

            <button class="btn btn-success">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>

            <a href="clientes.php" class="btn btn-secondary">
                Voltar
            </a>

        </form>

    </div>
</div>

<?php include 'footer.php'; ?>