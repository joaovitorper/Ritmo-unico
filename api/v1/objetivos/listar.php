<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

$sql = "SELECT
            id,
            usuario_id,
            tipo,
            meta,
            progresso,
            criado_em
        FROM objetivos
        ORDER BY id DESC";

$resultado = $conexao->query($sql);

if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao buscar objetivos."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$objetivos = [];

while ($objetivo = $resultado->fetch_assoc()) {
    $objetivos[] = $objetivo;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($objetivos),
    "objetivos" => $objetivos
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>