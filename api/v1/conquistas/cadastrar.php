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

$nome = trim($dados['nome'] ?? '');
$descricao = trim($dados['descricao'] ?? '');
$icone = trim($dados['icone'] ?? '');
$requisito = trim($dados['requisito'] ?? '');

if ($nome === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "O nome da conquista é obrigatório."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$descricao = $descricao !== '' ? $descricao : null;
$icone = $icone !== '' ? $icone : null;
$requisito = $requisito !== '' ? $requisito : null;

$stmt = $conexao->prepare(
    "INSERT INTO conquistas
    (nome, descricao, icone, requisito)
    VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssss",
    $nome,
    $descricao,
    $icone,
    $requisito
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao cadastrar conquista."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();
    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Conquista cadastrada com sucesso.",
    "id" => $stmt->insert_id
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>