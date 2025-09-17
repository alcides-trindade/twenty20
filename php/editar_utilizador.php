<?php
session_start();
include 'db.php';

// Verifica se é admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Obter ID do utilizador
$id = intval($_GET['id']);

// Buscar dados do utilizador
$res = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
$utilizador = mysqli_fetch_assoc($res);

// Atualizar dados se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $tipo = $_POST['tipo'];

    // Prepared statement para maior segurança
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $nome, $email, $tipo, $id);
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
    <title>Editar Utilizador</title>
</head>
<body class="container mt-5">
    <h2>Editar Utilizador</h2>
    <form method="post">
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($utilizador['username']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($utilizador['email']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Tipo:</label>
            <select name="tipo" class="form-control">
                <option value="user" <?= $utilizador['role'] === 'user' ? 'selected' : '' ?>>Cliente</option>
                <option value="admin" <?= $utilizador['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <button class="btn btn-primary">Guardar</button>
        <a href="admin.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
