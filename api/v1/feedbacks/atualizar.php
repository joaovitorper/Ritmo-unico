<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

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
$mensagem = trim($dados['mensagem'] ?? '');
$avaliacao = $dados['avaliacao'] ?? null;

if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($usuario_id === null || !is_numeric($usuario_id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um usuario_id válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($mensagem === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A mensagem é obrigatória."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;
$usuario_id = (int) $usuario_id;

if ($avaliacao !== null && $avaliacao !== '') {

    if (!is_numeric($avaliacao)) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "A avaliação deve ser um número."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $avaliacao = (int) $avaliacao;

} else {
    $avaliacao = null;
}

$verificar = $conexao->prepare(
    "SELECT id FROM feedbacks WHERE id = ?"
);

$verificar->bind_param("i", $id);
$verificar->execute();

$resultado = $verificar->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Feedback não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $verificar->close();
    $conexao->close();
    exit;
}

$verificar->close();

$verificarUsuario = $conexao->prepare(
    "SELECT id FROM usuarios WHERE id = ?"
);

$verificarUsuario->bind_param("i", $usuario_id);
$verificarUsuario->execute();

$resultadoUsuario = $verificarUsuario->get_result();

if ($resultadoUsuario->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $verificarUsuario->close();
    $conexao->close();
    exit;
}

$verificarUsuario->close();

$stmt = $conexao->prepare(
    "UPDATE feedbacks
     SET usuario_id = ?,
         tipo = ?,
         mensagem = ?,
         avaliacao = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "issii",
    $usuario_id,
    $tipo,
    $mensagem,
    $avaliacao,
    $id
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar feedback."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();
    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Feedback atualizado com sucesso."
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>