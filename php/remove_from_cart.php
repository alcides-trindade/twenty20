<?php
session_start();

// Verifica se existe o carrinho na sessão
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Verifica se foi enviado o ID do produto para remover
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Remove o item do carrinho, se existir
    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
    }
}

// Redireciona de volta para o carrinho
header('Location: cart.php');
exit;
