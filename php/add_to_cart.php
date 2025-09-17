<?php
session_start();
require 'db.php'; // ligação ao MySQL

// Inicializa o carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Recebe o ID do produto via GET
$id = $_GET['id'] ?? null;

if ($id) {
    // Vai buscar o produto à base de dados
    $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produto = $result->fetch_assoc();

    if ($produto) {
        // Se já existe no carrinho → incrementa quantidade
        if (isset($_SESSION['carrinho'][$produto['id']])) {
            $_SESSION['carrinho'][$produto['id']]['quantidade']++;
        } else {
            $_SESSION['carrinho'][$produto['id']] = [
                'nome' => $produto['name'],
                'preco' => (float)$produto['price'],
                'quantidade' => 1
            ];
        }
    }
}

// Redireciona para o carrinho
header('Location: cart.php');
exit;
