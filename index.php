<?php session_start(); ?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Farmácia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 350px;
            border-radius: 12px;
        }

        .login-header {
            background: #0d6efd;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 12px 12px 0 0;
        }

        .input-group-text {
            cursor: pointer;
        }
    </style>

</head>


<body>


    <div class="card login-card shadow">

        <div class="login-header">
            <h4><i class="fas fa-user-circle"></i> Login</h4>
        </div>

        <div class="card-body">

            <form action="controllers/login.php" method="POST">

                <div class="mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" placeholder="Digite seu CPF" required>
                </div>

                <div class="mb-3">
                    <label>Senha</label>
                    <div class="input-group">
                        <input type="password" name="senha" id="senha" class="form-control"
                            placeholder="Digite sua senha" required>
                        <span class="input-group-text" onclick="toggleSenha()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button class="btn btn-primary w-100">Entrar</button>

            </form>

        </div>

    </div>

    <script>
        // mostrar senha
        function toggleSenha() {
            let input = document.getElementById("senha");
            input.type = input.type === "password" ? "text" : "password";
        }

        // CPF só números
        document.getElementById('cpf').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>

</body>

</html>

<div class="modal fade" id="erroModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Erro</h5>
            </div>
            <div class="modal-body">
                CPF ou senha inválidos
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_GET['erro'])): ?>
    <script>
        var myModal = new bootstrap.Modal(document.getElementById('erroModal'));
        myModal.show();
    </script>
<?php endif; ?>