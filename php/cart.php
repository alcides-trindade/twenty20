<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($acao === 'atualizar' && $id !== null) {
        $quantidade = max(1, (int)($_POST['quantidade'] ?? 1));
        $_SESSION['carrinho'][$id]['quantidade'] = $quantidade;
    }

    if ($acao === 'remover' && $id !== null) {
        unset($_SESSION['carrinho'][$id]);
    }

    // Redireciona de volta ao carrinho
    header("Location: cart.php"); 
    exit;
}
$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
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
  <title>Carrinho de Compras</title>
</head>
<body class="container mt-5">
  <h1 class="text-center">Carrinho de Compras</h1>

  <?php if (empty($carrinho)): ?>
      <p>O carrinho está vazio.</p>
      <a href="../produtos.html" class="btn btn-primary mt-3">Voltar à Loja</a>
  <?php else: ?>
      <ul class="list-group mb-3">
          <?php foreach($carrinho as $id => $item): 
              $subtotal = $item['preco'] * $item['quantidade'];
              $total += $subtotal;
          ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                      <strong><?= htmlspecialchars($item['nome']) ?></strong><br>
                      Preço unitário: €<?= number_format($item['preco'], 2, ',', '.') ?><br>

                      <!-- Formulário para atualizar quantidade -->
                      <form action="cart.php" method="POST" class="d-flex mt-2">
                          <input type="hidden" name="acao" value="atualizar">
                          <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                          <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" class="form-control me-2" style="width:80px;">
                          <button type="submit" class="btn btn-primary btn-sm">Atualizar</button>
                      </form>
                  </div>

                  <div class="text-end">
                      <span><?= number_format($subtotal, 2, ',', '.') ?> €</span>
                      
                      <!-- Botão para remover -->
                      <form action="cart.php" method="POST" class="mt-2">
                          <input type="hidden" name="acao" value="remover">
                          <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                          <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                      </form>
                  </div>
              </li>
          <?php endforeach; ?>
      </ul>

      <h4>Total: <?= number_format($total, 2, ',', '.') ?> €</h4>
      <a href="checkout.php" class="btn btn-success mt-3">Finalizar Compra</a>
      <a href="../produtos.html" class="btn btn-secondary mt-3">Continuar Comprando</a>
  <?php endif; ?>
</body>
</html>
