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

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

$stmt = $conexao->prepare(
    "SELECT
        id,
        usuario_id,
        tipo,
        analise,
        recomendacao,
        criado_em
     FROM analises_ia
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Análise não encontrada."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$analise = $resultado->fetch_assoc();

echo json_encode([
    "sucesso" => true,
    "analise" => $analise
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>