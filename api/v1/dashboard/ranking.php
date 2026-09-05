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
            u.id,
            u.nome,
            COALESCE(SUM(c.distancia), 0) AS distancia_total,
            COUNT(c.id) AS total_corridas
        FROM usuarios u
        LEFT JOIN corridas c
            ON c.usuario_id = u.id
        GROUP BY u.id, u.nome
        ORDER BY distancia_total DESC
        LIMIT 20";

$resultado = $conexao->query($sql);

if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao carregar ranking."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$ranking = [];
$posicao = 1;

while ($usuario = $resultado->fetch_assoc()) {

    $usuario['posicao'] = $posicao;
    $usuario['distancia_total'] = (float) $usuario['distancia_total'];
    $usuario['total_corridas'] = (int) $usuario['total_corridas'];

    $ranking[] = $usuario;

    $posicao++;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($ranking),
    "ranking" => $ranking
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>