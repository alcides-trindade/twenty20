<?php
session_start();
include 'db.php'; // conexão à base de dados

// Verificar se é admin
if (!isset($_SESSION['utilizador']) || $_SESSION['utilizador']['tipo'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Função de escape
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Mensagens (usando sessão para persistir após redirecionamento)
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

// === AÇÕES ===

// Remover utilizador
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $stmt = $conn->prepare("DELETE FROM utilizadores WHERE id=?");
    $stmt->bind_param("i", $id);
    $_SESSION['msg'] = $stmt->execute() 
        ? "✅ Utilizador removido com sucesso!" 
        : "❌ Erro ao remover utilizador.";
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Remover produto
if (isset($_GET['delete_produto'])) {
    $id = intval($_GET['delete_produto']);
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id=?");
    $stmt->bind_param("i", $id);
    $_SESSION['msg'] = $stmt->execute() 
        ? "✅ Produto removido com sucesso!" 
        : "❌ Erro ao remover produto.";
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Processar encomenda
if (isset($_GET['processar_encomenda'])) {
    $id = intval($_GET['processar_encomenda']);
    $stmt = $conn->prepare("UPDATE encomendas SET status='processado' WHERE id=?");
    $stmt->bind_param("i", $id);
    $_SESSION['msg'] = $stmt->execute() 
        ? "✅ Encomenda marcada como processada!" 
        : "❌ Erro ao processar encomenda.";
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Adicionar produto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_produto'])) {
    $nome = e($_POST['nome']);
    $preco = floatval($_POST['preco']);
    $descricao = e($_POST['descricao']);

    $stmt = $conn->prepare("INSERT INTO produtos (nome, preco, descricao) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $nome, $preco, $descricao);
    $_SESSION['msg'] = $stmt->execute() 
        ? "✅ Produto adicionado com sucesso!" 
        : "❌ Erro ao adicionar produto.";
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
    <title>Administração</title>
</head>
<body class="container mt-4">

    <h1>Área de Administração</h1>

    <!-- Mensagem de feedback -->
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= e($msg) ?></div>
    <?php endif; ?>

    <!-- ================= UTILIZADORES ================= -->
    <h2>Utilizadores</h2>
    <table class="table table-bordered table-striped">
        <tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Ações</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM utilizadores ORDER BY id ASC");
        while ($u = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$u['id']}</td>
                    <td>".e($u['nome'])."</td>
                    <td>".e($u['email'])."</td>
                    <td>{$u['tipo']}</td>
                    <td>
                        <a href='editar_utilizador.php?id={$u['id']}' class='btn btn-sm btn-primary'>Editar</a>
                        <a href='?delete_user={$u['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Tem a certeza?\")'>Remover</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <!-- ================= ENCOMENDAS ================= -->
    <h2>Encomendas</h2>
    <table class="table table-bordered table-striped">
        <tr><th>ID</th><th>Utilizador</th><th>Data</th><th>Status</th><th>Ações</th></tr>
        <?php
        $res = $conn->query("SELECT e.*, u.nome 
                             FROM encomendas e 
                             JOIN utilizadores u ON e.utilizador_id = u.id
                             ORDER BY e.id DESC");
        while ($e = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$e['id']}</td>
                    <td>".e($e['nome'])."</td>
                    <td>{$e['data']}</td>
                    <td>{$e['status']}</td>
                    <td>
                        <a href='?processar_encomenda={$e['id']}' class='btn btn-sm btn-success'>Processar</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <!-- ================= PRODUTOS ================= -->
    <h2>Produtos</h2>
    <form method="post" class="mb-3">
        <input type="hidden" name="add_produto" value="1">
        <div class="mb-2"><input type="text" name="nome" placeholder="Nome" required class="form-control"></div>
        <div class="mb-2"><input type="number" name="preco" step="0.01" placeholder="Preço" required class="form-control"></div>
        <div class="mb-2"><textarea name="descricao" placeholder="Descrição" class="form-control"></textarea></div>
        <button type="submit" class="btn btn-success">Adicionar Produto</button>
    </form>

    <table class="table table-bordered table-striped">
        <tr><th>ID</th><th>Nome</th><th>Preço</th><th>Descrição</th><th>Ações</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM produtos ORDER BY id DESC");
        while ($p = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$p['id']}</td>
                    <td>".e($p['nome'])."</td>
                    <td>{$p['preco']}</td>
                    <td>".e($p['descricao'])."</td>
                    <td>
                        <a href='editar_produto.php?id={$p['id']}' class='btn btn-sm btn-primary'>Editar</a>
                        <a href='?delete_produto={$p['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Remover produto?\")'>Remover</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>

</body>
</html>
