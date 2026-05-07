<?php if (isset($_GET['msg'])): ?>

    <?php
    $tipo = $_GET['tipo'] ?? 'success';

    $icone = 'fa-check';
    if ($tipo == 'danger')
        $icone = 'fa-times';
    if ($tipo == 'warning')
        $icone = 'fa-exclamation';
    ?>

    <div class="toast-container position-fixed top-0 end-0 p-3">

        <div id="toastMsg" class="toast align-items-center text-bg-<?= $tipo ?> border-0 show">
            <div class="d-flex">

                <div class="toast-body">
                    <i class="fas <?= $icone ?>"></i> <?= $_GET['msg'] ?>
                </div>

                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>

            </div>
        </div>

    </div>

    <script>
        setTimeout(() => {
            let toast = document.getElementById('toastMsg');
            if (toast) {
                toast.classList.remove('show');
            }
        }, 4000);
    </script>

<?php endif; ?>

<?php if (isset($_GET['msg'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            let tipo = "<?= $_GET['tipo'] ?>";
            let msg = "<?= $_GET['msg'] ?>";

            let cor = tipo === "success" ? "green" :
                tipo === "danger" ? "red" :
                    tipo === "warning" ? "orange" : "blue";

            let popup = document.createElement("div");
            popup.innerText = msg;

            popup.style = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${cor};
        color: white;
        padding: 15px;
        border-radius: 8px;
        z-index: 9999;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    `;

            document.body.appendChild(popup);

            setTimeout(() => popup.remove(), 3000);
        });
    </script>
<?php endif; ?>