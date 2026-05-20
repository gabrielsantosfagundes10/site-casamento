<?php
include 'config.php';

// Define o cabeçalho para JSON (importante para o JavaScript entender a resposta)
header('Content-Type: application/json');

// ALTERAÇÃO AQUI: trim remove espaços das pontas e preg_replace limpa espaços duplos no meio
$nomeRaw = $_POST['nome'] ?? '';
$nome = trim(preg_replace('/\s+/', ' ', $nomeRaw));

if (!empty($nome)) {
    // 1. Buscamos APENAS quem ainda NÃO confirmou (confirmado = 0)
    $stmt = $conn->prepare("SELECT id, nome_completo FROM convidados WHERE nome_completo LIKE ? AND confirmado = 0");
    $busca = "%$nome%";
    $stmt->bind_param("s", $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    $convidados = [];
    while ($row = $result->fetch_assoc()) {
        $convidados[] = [
            'id' => $row['id'], 
            'nome' => $row['nome_completo']
        ];
    }

    // 2. Se encontramos alguém não confirmado
    if (count($convidados) > 0) {
        echo json_encode([
            'sucesso' => true, 
            'lista' => $convidados
        ]);
    } 
    // 3. Se não achamos ninguém pendente, verificamos se o nome já existe mas JÁ ESTÁ CONFIRMADO
    else {
        $stmt2 = $conn->prepare("SELECT nome_completo FROM convidados WHERE nome_completo LIKE ? AND confirmado = 1 LIMIT 1");
        $stmt2->bind_param("s", $busca);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        
        if($res2->num_rows > 0) {
             $row2 = $res2->fetch_assoc();
             echo json_encode([
                 'sucesso' => true, 
                 'ja_confirmado' => true, 
                 'nome' => $row2['nome_completo']
             ]);
        } else {
             echo json_encode(['sucesso' => false, 'erro' => 'Nenhum nome encontrado']);
        }
    }
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Nome vazio']);
}
?>