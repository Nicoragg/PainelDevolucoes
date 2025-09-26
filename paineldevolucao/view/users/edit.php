<?php
require_once __DIR__ . "/../../models/User.php";
$id = $_GET['id'] ?? null;
$user = $id ? User::find($id) : null;

if (!$user) {
    echo "<p>Usuário não encontrado.</p>";
    return;
}
?>

<div class="titulo-container">
    <h1>Editar Usuário</h1>
</div>
<section>
    <?php if (isset($erro)): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="controllers/UserController.php?action=update&id=<?= $user['id'] ?>">

        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Digite nova senha se quiser alterar">

        <label for="is_admin">Administrador</label>
        <select id="is_admin" name="is_admin" required>
            <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>Não</option>
            <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>Sim</option>
        </select>

        <br><br>
        <button type="submit">Atualizar</button>
    </form>

    <br>
    <a href="index.php?page=users/home">Voltar</a>
</section>
