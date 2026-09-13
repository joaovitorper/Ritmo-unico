<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

// Recebe os dados enviados
$dados = json_decode(file_get_contents("php://input"), true);

// Caso não venha JSON, tenta receber por POST
if (!$dados) {
    $dados = $_POST;
}

// Verifica o ID
$id = $dados['id'] ?? null;

if (!$id || !is_numeric($id)) {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de usuário válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

$nome = trim($dados['nome'] ?? '');
$email = trim($dados['email'] ?? '');
$data_nascimento = $dados['data_nascimento'] ?? '';

// Verifica os campos obrigatórios
if ($nome === '' || $email === '') {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Nome e e-mail são obrigatórios."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Valida o e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "E-mail inválido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Verifica se o usuário existe
$sql = "SELECT id FROM usuarios WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
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

// Verifica se o e-mail já pertence a outro usuário
$sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("si", $email, $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    http_response_code(409);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Este e-mail já está cadastrado para outro usuário."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

$stmt->close();

// Atualiza o usuário
$sql = "UPDATE usuarios
        SET nome = ?, email = ?, data_nascimento = ?
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar atualização."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();

    exit;
}

$stmt->bind_param(
    "sssi",
    $nome,
    $email,
    $data_nascimento,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Usuário atualizado com sucesso."
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar usuário."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>