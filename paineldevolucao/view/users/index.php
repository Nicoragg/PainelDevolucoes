<?php
require_once __DIR__ . "/../../models/User.php";
$users = User::all();
?>

<div class="titulo-container">
    <h1>Lista de Usuários</h1>
    <a href="index.php?page=users/create"><button>+ Novo Usuário</button><a>
</div>
<?php if(isset($_SESSION['success'])): ?>
    <div class="mensagem sucesso"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="mensagem erro"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
<section class="tabela-container">


<table class="tabela-usuarios">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo de Usuário</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['nome']); ?></td>
            <td><?= htmlspecialchars($u['email']); ?></td>
            <td>
                    <?php if ($u['setor']): ?>
                        <span class="badge admin">Admin</span>
                    <?php else: ?>
                        <span class="badge usuario">Usuário</span>
                    <?php endif; ?>
            </td>
            <td class="acoes">
                <a href="index.php?page=users/edit&id=<?= $u['id']; ?>" class="btn editar">Editar</a>
                <a href="index.php?page=users/delete&id=<?= $u['id']; ?>" 
                   class="btn excluir"
                   onclick="return confirm('Deseja excluir?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</section>
