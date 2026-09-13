<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

// Verifica se o ID foi informado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de usuário válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $_GET['id'];

// Busca o usuário pelo ID
$sql = "SELECT id, nome, email, data_nascimento, criado_em
        FROM usuarios
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a consulta."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

$usuario = $resultado->fetch_assoc();

echo json_encode([
    "sucesso" => true,
    "usuario" => $usuario
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>