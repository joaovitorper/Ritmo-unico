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

if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de publicação válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

$sql = "SELECT
            id,
            usuario_id,
            titulo,
            conteudo,
            criado_em,
            atualizado_em
        FROM comunidade
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a consulta."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Publicação não encontrada."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

$publicacao = $resultado->fetch_assoc();

echo json_encode([
    "sucesso" => true,
    "publicacao" => $publicacao
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>