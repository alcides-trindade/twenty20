<?php
$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$password = 'alcidesTrindade12';
$dbname = 'loja_online';

// Ativar relatórios de erros MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Criar conexão
    $conn = new mysqli($host, $user, $password, $dbname, $port);
    
    // Definir charset para UTF-8 completo
    $conn->set_charset('utf8mb4');
    
} catch (mysqli_sql_exception $e) {
    // Se houver erro, mostrar mensagem
    die('Erro ao conectar à base de dados: ' . $e->getMessage());
}
?>