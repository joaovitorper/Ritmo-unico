<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use PUT."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (!is_array($dados)) {
    $dados = $_POST;
}

$id = $dados['id'] ?? null;
$usuario_id = $dados['usuario_id'] ?? null;
$tipo = trim($dados['tipo'] ?? '');
$meta = $dados['meta'] ?? null;
$progresso = $dados['progresso'] ?? null;

if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de objetivo válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($usuario_id === null || !is_numeric($usuario_id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de usuário válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

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

if ($progresso === null || !is_numeric($progresso)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um progresso válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;
$usuario_id = (int) $usuario_id;
$meta = (float) $meta;
$progresso = (float) $progresso;

$sql = "SELECT id FROM objetivos WHERE id = ?";

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
        "mensagem" => "Objetivo não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

$stmt->close();

$sql = "UPDATE objetivos
        SET usuario_id = ?,
            tipo = ?,
            meta = ?,
            progresso = ?
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a atualização."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$stmt->bind_param(
    "isddi",
    $usuario_id,
    $tipo,
    $meta,
    $progresso,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Objetivo atualizado com sucesso."
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar objetivo."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>