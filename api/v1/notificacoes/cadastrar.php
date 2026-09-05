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
$titulo = trim($dados['titulo'] ?? '');
$mensagem = trim($dados['mensagem'] ?? '');
$tipo = trim($dados['tipo'] ?? '');
$lida = $dados['lida'] ?? 0;

if ($usuario_id === null || !is_numeric($usuario_id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de usuário válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario_id = (int) $usuario_id;

if ($titulo === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "O título da notificação é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($mensagem === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A mensagem da notificação é obrigatória."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($tipo === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "O tipo da notificação é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$lida = filter_var($lida, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

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

$sql = "INSERT INTO notificacoes (
            usuario_id,
            titulo,
            mensagem,
            tipo,
            lida
        ) VALUES (?, ?, ?, ?, ?)";

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
    "isssi",
    $usuario_id,
    $titulo,
    $mensagem,
    $tipo,
    $lida
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Notificação cadastrada com sucesso.",
        "notificacao_id" => $conexao->insert_id
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao cadastrar notificação."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>