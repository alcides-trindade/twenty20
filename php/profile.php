<?php
session_start();
require 'db.php';

// Verifica se usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];

// Busca dados completos do usuário no banco (usando MySQLi)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $usuario['id']);
$stmt->execute();
$result = $stmt->get_result();
$usuarioDados = $result->fetch_assoc();

if (!$usuarioDados) {
    // Usuário não encontrado no banco — faz logout por segurança
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
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
    <title>Página de Perfil</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Bem-vindo, <?= htmlspecialchars($usuarioDados['username']) ?>!</h1>
         <p>Data do Registro: <?= htmlspecialchars($usuarioDados['criado_em']) ?></p>
         <p>Esta é a sua página de perfil.</p>
         <a href="logout.php" class="btn btn-danger">Sair</a>
         <a href="../index.html" class="btn btn-primary">Inicio</a>
     </div>
     <!-- <button type="button" class="btn btn-primary"><a href="logout.php" class="btn-form">Sair</a></button> -->
 </body>
 </html>

