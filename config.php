<?php
// Configura o fuso horário no PHP para o horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Credenciais da Hostinger conforme a imagem enviada
$host = "localhost";
$usuario = "usuario"; // Credenciais alteradas por confidencialidade
$senha = "senha"; // Credenciais alteradas por confidencialidade
$banco = "banco"; // Credenciais alteradas por confidencialidade

$conn = new mysqli($host, $usuario, $senha, $banco);

// Definir charset para evitar erro de acentuação (opcional, mas recomendado)
$conn->set_charset("utf8mb4");

// Sincroniza o fuso horário da conexão do Banco de Dados com o de Brasília
$conn->query("SET time_zone = '-03:00'");

if ($conn->connect_error) {
    // Em produção, é melhor não mostrar o erro detalhado por segurança
    die("Erro na conexão com o banco de dados.");
}
?>
