<?php
session_start();
require 'db.php'; // ligação ao MySQL

$carrinho = $_SESSION['carrinho'] ?? [];

// Calcular total
$total = 0;
foreach ($carrinho as $item) {
    $preco = isset($item['preco']) ? (float)$item['preco'] : 0;
    $quantidade = isset($item['quantidade']) ? (int)$item['quantidade'] : 0;
    $total += $preco * $quantidade;
}

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['usuario'])) {
        $_SESSION['redirect_after_login'] = 'checkout.php';
        header("Location: login.php");
        exit;
    }

    $userId = $_SESSION['usuario']['id'];
    $dataHora = date('Y-m-d H:i:s');
    $status = "pendente";
    $metodoPagamento = $_POST['metodo_pagamento'] ?? "Indefinido";

    // 🔹 Verificar se o utilizador ainda existe na tabela users
    $checkUser = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $checkUser->bind_param("i", $userId);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows === 0) {
        $mensagem = "⚠️ Não foi possível finalizar a compra. A sua conta foi removida por motivos de segurança.";
    } else {
        try {
            // Inserir o pedido
            $stmt = $conn->prepare("INSERT INTO pedidos (utilizador_id, total, data, status, metodo_pagamento) 
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idsss", $userId, $total, $dataHora, $status, $metodoPagamento);
            $stmt->execute();
            $pedidoId = $stmt->insert_id;

            // Inserir os itens do pedido
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                        VALUES (?, ?, ?, ?)");

            foreach ($carrinho as $item) {
                $produtoId = $item['id'] ?? null;
                $quantidade = $item['quantidade'] ?? 0;
                $preco = $item['preco'] ?? 0;

                if ($produtoId === null) continue;

                $stmtItem->bind_param("iiid", $pedidoId, $produtoId, $quantidade, $preco);
                $stmtItem->execute();
            }

            $_SESSION['carrinho'] = []; // limpa o carrinho
            $mensagem = "✅ Compra finalizada com sucesso! Obrigado pela preferência. Valor total: " 
                        . number_format($total, 2, ',', '.') . " €";
        } catch (mysqli_sql_exception $e) {
            $mensagem = "⚠️ Erro ao processar a compra. Tente novamente mais tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Alcides Trindade">
    <meta name="description" content="Twenty20">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abel&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Checkout</title>
</head>
<body class="container mt-5">

<h1 class="text-center">Checkout</h1>

<?php if (!empty($mensagem)): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($mensagem) ?></div>
    <a href="../produtos.html" class="btn btn-primary">Voltar à Loja</a>
<?php elseif(empty($carrinho)): ?>
    <p>O carrinho está vazio.</p>
    <a href="../produtos.html" class="btn btn-primary">Voltar à Loja</a>
<?php else: ?>
    <ul class="list-group mb-3">
        <?php foreach($carrinho as $item): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($item['nome']) ?> x <?= $item['quantidade'] ?>
                <span><?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?> €</span>
            </li>
        <?php endforeach; ?>
    </ul>

    <h4>Total: <?= number_format($total, 2, ',', '.') ?> €</h4>

    <form method="POST">
        <h5>Método de Pagamento:</h5>
<div class="mb-3">
    <input type="radio" name="metodo_pagamento" value="Multibanco" required> Multibanco <br>
    <input type="radio" name="metodo_pagamento" value="MB WAY"> MB WAY <br>
    <input type="radio" name="metodo_pagamento" value="PayPal"> PayPal <br>
    <input type="radio" name="metodo_pagamento" value="Cartão de Crédito"> Cartão de Crédito <br>
</div>

        <button type="submit" class="btn btn-success mt-3">Finalizar Compra</button>
        <a href="cart.php" class="btn btn-secondary mt-3">Voltar ao Carrinho</a>
    </form>
<?php endif; ?>

</body>
</html>
