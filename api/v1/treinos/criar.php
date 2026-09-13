<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

// Permite somente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use POST."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Recebe os dados em JSON
$dados = json_decode(file_get_contents("php://input"), true);

// Se não for JSON, tenta receber por POST
if (!is_array($dados)) {
    $dados = $_POST;
}

// Recebe os campos
$usuario_id = $dados['usuario_id'] ?? null;
$titulo = trim($dados['titulo'] ?? '');
$descricao = trim($dados['descricao'] ?? '');
$tipo = trim($dados['tipo'] ?? '');
$distancia = $dados['distancia'] ?? 0;
$duracao = $dados['duracao'] ?? 0;
$intensidade = trim($dados['intensidade'] ?? '');
$data_treino = $dados['data_treino'] ?? '';
$concluido = $dados['concluido'] ?? false;

// Valida usuário
if ($usuario_id === null || !is_numeric($usuario_id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um usuário válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario_id = (int) $usuario_id;

// Valida título
if ($titulo === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "O título do treino é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Valida data
if ($data_treino === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A data do treino é obrigatória."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Converte valores numéricos
$distancia = is_numeric($distancia) ? (float) $distancia : 0;
$duracao = is_numeric($duracao) ? (int) $duracao : 0;

// Converte concluído para 0 ou 1
$concluido = filter_var(
    $concluido,
    FILTER_VALIDATE_BOOLEAN
) ? 1 : 0;

// Verifica se o usuário existe
$sql = "SELECT id FROM usuarios WHERE id = ?";

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

// Cadast
