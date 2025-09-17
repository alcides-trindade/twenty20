<?php
session_start();

// Inicializa carrinho
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Lê dados enviados
$acao = $_POST['acao'] ?? '';
$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? null;
$preco = isset($_POST['preco']) ? (float)$_POST['preco'] : null;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;

switch ($acao) {
    case 'adicionar':
        if ($id && $nome && $preco) {
            if (isset($_SESSION['carrinho'][$id])) {
                $_SESSION['carrinho'][$id]['quantidade'] += $quantidade;
            } else {
                $_SESSION['carrinho'][$id] = [
                    'nome' => $nome,
                    'preco' => $preco,
                    'quantidade' => $quantidade
                ];
            }
        }
        break;

    case 'remover':
        if ($id && isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
        }
        break;

    case 'atualizar':
        if ($id && isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade'] = max(1, $quantidade);
        }
        break;

    case 'listar':
        // só devolve o carrinho
        break;
}
    // Calcula total
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
}

echo json_encode([
    'carrinho' => $_SESSION['carrinho'],
    'total' => array_sum(array_map(function($item){
        return $item['preco'] * $item['quantidade'];
    }, $_SESSION['carrinho']))
]);
