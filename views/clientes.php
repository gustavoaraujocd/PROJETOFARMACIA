<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';
?>

<h2 class="mb-4">Clientes</h2>

<!-- BUSCA -->
<div class="mb-3">
    <input type="text" id="busca" class="form-control" placeholder="🔍 Buscar cliente por nome ou CPF...">
</div>

<!-- CADASTRO -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Cadastrar Cliente</h5>
    </div>

    <div class="card-body">
        <form action="../controllers/clienteController.php" method="POST">

            <input type="hidden" name="acao" value="salvar">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="cpf" maxlength="11" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Endereço</label>
                    <input type="text" name="endereco" class="form-control" required>
                </div>

            </div>

            <button class="btn btn-success">
                <i class="fas fa-save"></i> Salvar
            </button>

        </form>
    </div>
</div>

<?php
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome")->fetchAll();
?>

<!-- LISTA -->
<div class="card shadow">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Lista de Clientes</h5>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th width="180">Ações</th>
                </tr>
            </thead>

            <tbody id="tabela">

                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?= $c['nome'] ?></td>

                        <td>
                            <?= preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $c['cpf']) ?>
                        </td>

                        <td><?= $c['telefone'] ?></td>

                        <!-- ✅ STATUS -->
                        <td>
                            <?php if ($c['status'] == 'ativo'): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>

                        <td>

                            <!-- EDITAR -->
                            <a href="editar_cliente.php?cpf=<?= $c['cpf'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- ATIVAR / INATIVAR -->
                            <a href="../controllers/clienteController.php?acao=toggle&cpf=<?= $c['cpf'] ?>"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-user-slash"></i>
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

    </div>
</div>

<?php include 'footer.php'; ?>

<script>

    // CPF somente números
    document.getElementById('cpf').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // TELEFONE
    document.getElementById('telefone').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');

        if (v.length > 10) {
            v = v.replace(/^(\d{2})(\d{1})(\d{4})(\d{4}).*/, "($1) $2 $3-$4");
        }

        e.target.value = v;
    });

    // BUSCA AJAX MELHORADA
    document.getElementById('busca').addEventListener('keyup', function () {
        let valor = encodeURIComponent(this.value);

        fetch('../controllers/buscaCliente.php?busca=' + valor)
            .then(res => res.text())
            .then(data => {
                document.getElementById('tabela').innerHTML = data;
            });
    });

</script>