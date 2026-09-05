<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use DELETE."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (is_array($dados)) {
        $id = $dados['id'] ?? null;
    }
}

if (!$id || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

$stmtVerificar = $conexao->prepare(
    "SELECT id FROM analises_ia WHERE id = ?"
);

$stmtVerificar->bind_param("i", $id);
$stmtVerificar->execute();

$resultado = $stmtVerificar->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Análise não encontrada."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt = $conexao->prepare(
    "DELETE FROM analises_ia WHERE id = ?"
);

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao excluir análise."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Análise excluída com sucesso."
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$stmtVerificar->close();
$conexao->close();

?>