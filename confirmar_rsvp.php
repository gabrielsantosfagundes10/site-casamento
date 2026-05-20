<?php
include 'config.php';

$id = $_POST['id'] ?? '';

if (!empty($id)) {
    $stmt = $conn->prepare("UPDATE convidados SET confirmado = 1, data_confirmacao = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['sucesso' => false]);
    }
}
?>