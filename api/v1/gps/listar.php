<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use GET."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$sql = "SELECT
            id,
            usuario_id,
            corrida_id,
            latitude,
            longitude,
            altitude,
            velocidade,
            distancia,
            registrado_em
        FROM gps
        ORDER BY id DESC";

$resultado = $conexao->query($sql);

if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao listar registros GPS."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$gps = [];

while ($registro = $resultado->fetch_assoc()) {
    $gps[] = $registro;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($gps),
    "gps" => $gps
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>