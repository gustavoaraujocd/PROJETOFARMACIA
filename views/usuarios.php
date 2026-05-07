<?php
require_once "../config/auth.php";
require_once "../config/db.php";
include 'layout.php';
?>

<h2 class="mb-4">Usuários</h2>

<input type="text" id="busca" class="form-control mb-3" placeholder="🔍 Buscar usuário por nome ou CPF...">

<!-- CADASTRO -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Cadastrar Usuário</h5>
    </div>

    <div class="card-body">
        <form action="../controllers/usuarioController.php" method="POST">

            <input type="hidden" name="acao" value="criar">

            <div class="row">

                <!-- NOME -->
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control" required>
                </div>

                <!-- CPF -->
                <div class="col-md-6 mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" required>
                </div>

                <!-- EMAIL -->
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <!-- TELEFONE -->
                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control" required>
                </div>

                <!-- CARGO -->
                <div class="col-md-6 mb-3">
                    <label>Cargo</label>
                    <select name="role_id" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="1">Admin</option>
                        <option value="2">Supervisor</option>
                        <option value="3">Balconista</option>
                    </select>
                </div>

                <!-- SENHA -->
                <div class="col-md-6 mb-3">
                    <label>Senha</label>
                    <input type="password" name="senha" class="form-control" minlength="6" required>
                </div>

            </div>

            <button class="btn btn-success">
                <i class="fas fa-save"></i> Cadastrar
            </button>

        </form>
    </div>
</div>

<!-- LISTA -->
<?php
$usuarios = $pdo->query("
    SELECT u.*, r.nome as role_nome
    FROM usuarios u
    LEFT JOIN roles r ON u.role_id = r.id
")->fetchAll();
?>

<div class="card shadow">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Lista de Usuários</h5>
    </div>

    <div class="card-body table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Cargo</th>
                    <th>Status</th>
                    <th width="180">Ações</th>
                </tr>
            </thead>

            <tbody id="tabela">
                <?php foreach ($usuarios as $u): ?>
                    <tr>

                        <td><?= $u['nome'] ?></td>

                        <!-- CPF FORMATADO -->
                        <td>
                            <?= preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $u['cpf']) ?>
                        </td>

                        <td>
                            <span class="badge bg-info text-dark">
                                <?= $u['role_nome'] ?? 'Sem cargo' ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($u['status'] == 'ativo'): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="usuarios_editar.php?cpf=<?= $u['cpf'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="../controllers/usuarioController.php?acao=toggle&cpf=<?= $u['cpf'] ?>"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-power-off"></i>
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

    // =======================
    // 🔍 BUSCA COM DEBOUNCE
    // =======================
    let timer;

    document.getElementById('busca').addEventListener('keyup', function () {
        clearTimeout(timer);

        let valor = encodeURIComponent(this.value);

        timer = setTimeout(() => {
            fetch('../controllers/buscaUsuario.php?busca=' + valor)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('tabela').innerHTML = data;
                });
        }, 300);
    });

    // =======================
    // 👤 NOME (SÓ LETRAS)
    // =======================
    document.getElementById('nome').addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
    });

    // =======================
    // 🆔 CPF (MÁSCARA)
    // =======================
    document.getElementById('cpf').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);

        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        e.target.value = v;
    });

    // =======================
    // 📞 TELEFONE (BR)
    // =======================
    document.getElementById('telefone').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);

        if (v.length > 10) {
            v = v.replace(/^(\d{2})(\d{1})(\d{4})(\d{4}).*/, "($1) $2 $3-$4");
        } else if (v.length > 6) {
            v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
        } else if (v.length > 2) {
            v = v.replace(/^(\d{2})(\d+)/, "($1) $2");
        }

        e.target.value = v;
    });

</script>