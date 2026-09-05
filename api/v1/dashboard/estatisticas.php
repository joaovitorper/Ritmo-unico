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

if (!isset($_GET['usuario_id']) || !is_numeric($_GET['usuario_id'])) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um usuario_id válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario_id = (int) $_GET['usuario_id'];

$stmt = $conexao->prepare(
    "SELECT
        COUNT(*) AS total_corridas,
        COALESCE(SUM(distancia), 0) AS distancia_total,
        COALESCE(AVG(distancia), 0) AS distancia_media
     FROM corridas
     WHERE usuario_id = ?"
);

$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$corridas = $stmt->get_result()->fetch_assoc();

$stmt->close();

$stmt = $conexao->prepare(
    "SELECT
        COUNT(*) AS total_treinos,
        COALESCE(SUM(concluido), 0) AS treinos_concluidos
     FROM treinos
     WHERE usuario_id = ?"
);

$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$treinos = $stmt->get_result()->fetch_assoc();

$stmt->close();

echo json_encode([
    "sucesso" => true,
    "estatisticas" => [
        "corridas" => [
            "total" => (int) $corridas['total_corridas'],
            "distancia_total" => (float) $corridas['distancia_total'],
            "distancia_media" => (float) $corridas['distancia_media']
        ],
        "treinos" => [
            "total" => (int) $treinos['total_treinos'],
            "concluidos" => (int) $treinos['treinos_concluidos']
        ]
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>