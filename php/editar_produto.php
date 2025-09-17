<?php
session_start();
include 'db.php';

// Verifica se é admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Obter ID do produto
$id = intval($_GET['id']);

// Buscar dados do produto
$res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
$produto = mysqli_fetch_assoc($res);

// Atualizar dados se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $preco = floatval($_POST['preco']);
    $descricao = htmlspecialchars($_POST['descricao']);

    // Prepared statement
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=? WHERE id=?");
    $stmt->bind_param("sdsi", $nome, $preco, $descricao, $id); // s=string, d=decimal/float, i=inteiro
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit();
}

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
    <title>Editar Produto</title>
</head>
<body class="container mt-5">
    <h2>Editar Produto</h2>
    <form method="post">
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Preço:</label>
            <input type="number" name="preco" step="0.01" value="<?= $produto['preco'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Descrição:</label>
            <textarea name="descricao" class="form-control"><?= htmlspecialchars($produto['descricao']) ?></textarea>
        </div>
        <button class="btn btn-primary">Guardar</button>
        <a href="admin.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
