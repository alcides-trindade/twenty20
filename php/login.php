<?php
session_start();
require 'db.php'; // conexão MySQLi via $conn

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $erro = "Preencha todos os campos!";
    } else {
        $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role']
        ];
            
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // limpar para não redirecionar sempre
            header("Location: $redirect");
        exit;
            }       
            header('Location: profile.php');
            exit;
        } else {
            $erro = "Usuário ou senha inválidos!";
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
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="../CSS/style.css">
<link href="https://fonts.googleapis.com/css2?family=Abel&display=swap" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/css/lightbox.css" integrity="sha512-DKdRaC0QGJ/kjx0U0TtJNCamKnN4l+wsMdION3GG0WVK6hIoJ1UPHRHeXNiGsXdrmq19JJxgIubb/Z7Og2qJww==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Login</title>
</head>
<body>
     <div class="form-wrapper">
         <h2>Fazer Login</h2>
            <?php if ($erro): ?>
            <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>
            <div class="container p-5 ">
                <form method="post" action="login.php">
                     <div class="mb-3">
                         <label for="name" class="form-label">Nome do Utilizador:</label> <br>
                         <input type="text" name="username" placeholder="Nome de usuário" class="form-control" required/> <br>
                    </div>
                     
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha:</label><br>
                         <input type="password" name="password" placeholder="Senha" class="form-control" required/> <br>
                    </div>
                        <button type="submit" class="btn btn-primary">Entrar</button> 
                          <button class="btn btn-danger">
                    <a href="logout.php" class="text-white text-decoration-none">Terminar sessão</a></button>
                        <br>
                        <br>
                         <p>Ainda não tem uma conta?</p>
                         <a class='btn btn-primary' href="register.php">Registe-se</a>
                        </form>
           </div>
   </div>
         <button type="button" class="btn btn-primary"><a href="../index.html" class="btn-form">Voltar a página incial</a></button>
         
    
            <footer class="text-center bg-dark text-white py-3      mt-5">
                &copy; 2025 Twenty20 - Todos os direitos reservados
            </footer>
            <script src="JS/script.js"></script> 
</body>
</html>
   

       

                    
                    