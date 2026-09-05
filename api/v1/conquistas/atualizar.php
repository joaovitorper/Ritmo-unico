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
$nome = trim($dados['nome'] ?? '');
$descricao = trim($dados['descricao'] ?? '');
$icone = trim($dados['icone'] ?? '');
$requisito = trim($dados['requisito'] ?? '');

if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($nome === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "O nome da conquista é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

$descricao = $descricao !== '' ? $descricao : null;
$icone = $icone !== '' ? $icone : null;
$requisito = $requisito !== '' ? $requisito : null;

$verificar = $conexao->prepare(
    "SELECT id FROM conquistas WHERE id = ?"
);

$verificar->bind_param("i", $id);
$verificar->execute();

$resultado = $verificar->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Conquista não encontrada."
    ], JSON_UNESCAPED_UNICODE);

    $verificar->close();
    $conexao->close();
    exit;
}

$verificar->close();

$stmt = $conexao->prepare(
    "UPDATE conquistas
     SET nome = ?,
         descricao = ?,
         icone = ?,
         requisito = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "ssssi",
    $nome,
    $descricao,
    $icone,
    $requisito,
    $id
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar conquista."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();
    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Conquista atualizada com sucesso."
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>