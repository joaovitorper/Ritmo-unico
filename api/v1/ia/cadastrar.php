<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use POST."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    $dados = $_POST;
}

$usuario_id = $dados['usuario_id'] ?? null;
$tipo = isset($dados['tipo']) && trim($dados['tipo']) !== '' ? trim($dados['tipo']) : null;
$analise = isset($dados['analise']) ? trim($dados['analise']) : '';
$recomendacao = isset($dados['recomendacao']) && trim($dados['recomendacao']) !== '' ? trim($dados['recomendacao']) : null;

if (!$usuario_id || !is_numeric($usuario_id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um usuario_id válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($analise === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A análise é obrigatória."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario_id = (int) $usuario_id;

$stmtUsuario = $conexao->prepare(
    "SELECT id FROM usuarios WHERE id = ?"
);

$stmtUsuario->bind_param("i", $usuario_id);
$stmtUsuario->execute();

$resultadoUsuario = $stmtUsuario->get_result();

if ($resultadoUsuario->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt = $conexao->prepare(
    "INSERT INTO analises_ia
        (usuario_id, tipo, analise, recomendacao)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "isss",
    $usuario_id,
    $tipo,
    $analise,
    $recomendacao
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao cadastrar análise."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Análise cadastrada com sucesso.",
    "id" => $stmt->insert_id
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$stmtUsuario->close();
$conexao->close();

?>