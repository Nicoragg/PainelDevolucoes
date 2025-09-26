<div class="titulo-container">
    <h1>Criar Usuário</h1>
</div>

<section>
<?php if (isset($erro)): ?>
    <div class="erro"><?= $erro ?></div>
<?php endif; ?>

<form method="POST" action="controllers/UserController.php?action=create">

    <label for="nome">Nome</label>
    <input type="text" id="nome" name="nome" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="senha">Senha</label>
    <input type="password" id="senha" name="senha" required>

    <label for="is_admin">Administrador</label>
    <select id="is_admin" name="is_admin" required>
        <option value="0" selected>Não</option>
        <option value="1">Sim</option>
    </select>
    <button type="submit">Salvar</button>
</form>

<br>
<a href="index.php?page=users/home">Voltar</a>
</section>