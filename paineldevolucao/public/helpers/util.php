<?php

function setor($nome) {
    return isset($_SESSION['usuario']['setor']) && $_SESSION['usuario']['setor'] === $nome;
}