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
        id,
        tipo,
        meta,
        progresso,
        criado_em
     FROM objetivos
     WHERE usuario_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();

$objetivos = [];

while ($objetivo = $resultado->fetch_assoc()) {

    $meta = (float) $objetivo['meta'];
    $progresso = (float) $objetivo['progresso'];

    $porcentagem = 0;

    if ($meta > 0) {
        $porcentagem = ($progresso / $meta) * 100;
    }

    if ($porcentagem > 100) {
        $porcentagem = 100;
    }

    $objetivo['meta'] = $meta;
    $objetivo['progresso'] = $progresso;
    $objetivo['porcentagem'] = round($porcentagem, 2);

    $objetivos[] = $objetivo;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($objetivos),
    "objetivos" => $objetivos
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>