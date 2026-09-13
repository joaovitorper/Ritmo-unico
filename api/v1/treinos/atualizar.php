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

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Dados inválidos ou não enviados."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = $dados['id'] ?? null;
$usuario_id = $dados['usuario_id'] ?? null;
$titulo = trim($dados['titulo'] ?? '');
$descricao = trim($dados['descricao'] ?? '');
$tipo = trim($dados['tipo'] ?? '');
$distancia = $dados['distancia'] ?? 0;
$duracao = $dados['duracao'] ?? 0;
$intensidade = trim($dados['intensidade'] ?? '');
$data_treino = $dados['data_treino'] ?? '';
$concluido = $dados['concluido'] ?? false;

if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de treino válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

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
        "mensagem" => "O título do treino é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($data_treino === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A data do treino é obrigatória."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$concluido = filter_var(
    $concluido,
    FILTER_VALIDATE_BOOLEAN
) ? 1 : 0;

$sql = "SELECT id FROM treinos WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a consulta do treino."
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
        "mensagem" => "Treino não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

$stmt->close();

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

$sql = "UPDATE treinos SET
            usuario_id = ?,
            titulo = ?,
            descricao = ?,
            tipo = ?,
            distancia = ?,
            duracao = ?,
            intensidade = ?,
            data_treino = ?,
            concluido = ?
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
    "isssdissii",
    $usuario_id,
    $titulo,
    $descricao,
    $tipo,
    $distancia,
    $duracao,
    $intensidade,
    $data_treino,
    $concluido,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Treino atualizado com sucesso."
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar treino."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>