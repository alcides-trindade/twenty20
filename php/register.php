<?php
session_start();
require 'db.php'; // conexão MySQLi via $conn

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$email || !$password) {
        $erro = "Preencha todos os campos!";
    } else {
        // Verificar se username ou email já existem
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = "Nome de utilizador ou e-mail já estão registados!";
        } else {
            $senhaHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $username, $email, $senhaHash);

            if ($stmt->execute()) {
                // Criar sessão automaticamente
                $userId = $stmt->insert_id;
                $_SESSION['usuario'] = [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email,
                    'role' => 'user'
                ];

                // Se o utilizador veio do checkout, manda-o de volta
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                } else {
                    header("Location: profile.php"); // ou outra página da tua escolha
                }
                exit;
            } else {
                $erro = "Erro ao registrar o usuário: " . $stmt->error;
            }
        }
        $stmt->close();
    }
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/css/lightbox.css" integrity="sha512-DKdRaC0QGJ/kjx0U0TtJNCamKnN4l+wsMdION3GG0WVK6hIoJ1UPHRHeXNiGsXdrmq19JJxgIubb/Z7Og2qJww==" crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <title>Registo de Utilizador</title>
</head>
<body>
    <div class="form-wrapper">
       
        <h2>Registo</h2>

        <div class="container p-5 ">
             <!-- Mensagens -->
        <?php if($erro): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <?php if($sucesso): ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
            
            <form method="post" action="register.php">
                <div class="mb-3"> 
                        <label for="username" class="form-label">Nome do Utilizador:</label><br>
                        <input type="text" id="username" name="username" class="form-control" required><br><br>
                </div>
               
                <div class="mb-3">
                     <label for="email" class="form-label">Email:</label><br>
                    <input type="email" id="email" name="email" class="form-control" required><br><br>
                </div>
            
                    <div class="mb-3">
                    <label for="password" class="form-label">Senha:</label><br>
                    <input type="password" id="password" name="password" class="form-control" required> <br>
                </div>
            
                <button type="submit" class="btn btn-primary">Registar</button> <br>
                <br>
                 <a class='btn btn-primary' href="login.php">Voltar para login</a>
            </form>
        </div>
    </div>
            <a href="index.html" class="btn btn-primary">Voltar à página inicial</a>

            <!-- <button type="button" class="btn btn-primary"><a href="index.html" class="btn-form">Voltar a página incial</a></button> -->
    
            <footer class="text-center bg-dark text-white py-3      mt-5">
                &copy; 2025 Twenty20 - Todos os direitos reservados
            </footer>
            <script src="JS/script.js"></script> 
</body>
</html>

                
                
                

