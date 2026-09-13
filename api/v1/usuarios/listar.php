<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

$sql = "SELECT id, nome, email, data_nascimento, criado_em
        FROM usuarios
        ORDER BY id DESC";

$resultado = $conexao->query($sql);

if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao buscar usuários."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuarios = [];

while ($usuario = $resultado->fetch_assoc()) {
    $usuarios[] = $usuario;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($usuarios),
    "usuarios" => $usuarios
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>