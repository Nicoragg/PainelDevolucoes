<div class="login-section">
    <div class="login-box">

        <h2>Bem-vindo ao Sistema de <br>Controle de Devoluções</h2>
        <p class="subtitle">Acesse com suas credenciais para continuar</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <p class="mensagem erro"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="post" action="controllers/AuthController.php?action=login" class="login-form">
            <input type="text" name="email" placeholder="E-mail" required>

            <div class="password-wrapper">
                <input type="password" id="senha" name="senha" placeholder="Senha" required>
                <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
            </div>

            <button type="submit">Entrar</button>
        </form>

    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("senha");
    const icon = document.querySelector(".toggle-password");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>