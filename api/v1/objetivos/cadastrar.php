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
$tipo = trim($dados['tipo'] ?? '');
$meta = $dados['meta'] ?? null;
$progresso = $dados['progresso'] ?? 0;

if ($usuario_id === null || !is_numeric($usuario_id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de usuário válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario_id = (int) $usuario_id;

if ($tipo === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "O tipo do objetivo é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($meta === null || !is_numeric($meta)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe uma meta válida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (!is_numeric($progresso)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um progresso válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$meta = (float) $meta;
$progresso = (float) $progresso;

$sql = "SELECT id FROM usuarios WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a consulta do usuário."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$stmt->bind_param("i", $usuario_id);
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

$stmt->close();

$sql = "INSERT INTO objetivos (
            usuario_id,
            tipo,
            meta,
            progresso
        ) VALUES (?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar o cadastro."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$stmt->bind_param(
    "isdd",
    $usuario_id,
    $tipo,
    $meta,
    $progresso
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Objetivo cadastrado com sucesso.",
        "objetivo_id" => $conexao->insert_id
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao cadastrar objetivo."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>