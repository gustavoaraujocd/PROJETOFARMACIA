<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';

$cpf = $_GET['cpf'] ?? null;

if (!$cpf) {
    header("Location: usuarios.php");
    exit;
}

// 🔍 BUSCAR USUÁRIO
$sql = $pdo->prepare("SELECT * FROM usuarios WHERE cpf=?");
$sql->execute([$cpf]);
$u = $sql->fetch();

if (!$u) {
    header("Location: usuarios.php?msg=Usuário não encontrado&tipo=danger");
    exit;
}

// 🔍 BUSCAR ROLES
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<h2 class="mb-4">Editar Usuário</h2>

<div class="card shadow">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="fas fa-user-edit"></i> Editar Dados</h5>
    </div>

    <div class="card-body">

        <form action="../controllers/usuarioController.php" method="POST">

            <input type="hidden" name="acao" value="editar">
            <input type="hidden" name="cpf" value="<?= $u['cpf'] ?>">

            <div class="row">

                <!-- NOME -->
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?= $u['nome'] ?>" class="form-control" required>
                </div>

                <!-- EMAIL -->
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $u['email'] ?>" class="form-control" required>
                </div>

                <!-- TELEFONE -->
                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="<?= $u['telefone'] ?>" class="form-control"
                        required>
                </div>

                <!-- CARGO (RBAC) -->
                <div class="col-md-6 mb-3">
                    <label>Cargo</label>
                    <select name="role_id" class="form-control" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>>
                                <?= ucfirst($r['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SENHA -->
                <div class="col-md-12 mb-3">
                    <label>Nova Senha (opcional)</label>
                    <input type="password" name="senha" class="form-control"
                        placeholder="Deixe em branco para não alterar">
                </div>

            </div>

            <div class="d-flex justify-content-between">

                <a href="usuarios.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>

                <button class="btn btn-success">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>

            </div>

        </form>

    </div>
</div>

<!-- SCRIPT TELEFONE -->
<script>
    document.getElementById('telefone').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');

        if (v.length > 10) {
            v = v.replace(/^(\d{2})(\d{1})(\d{4})(\d{4}).*/, "($1) $2 $3-$4");
        }

        e.target.value = v;
    });
</script>

<?php include 'footer.php'; ?>