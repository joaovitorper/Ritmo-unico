<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

// Permite somente DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use DELETE."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Recebe o ID pela URL
$id = $_GET['id'] ?? null;

// Verifica o ID
if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de treino válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;

// Verifica se o treino existe
$sql = "SELECT id FROM treinos WHERE id = ?";

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
        "mensagem" => "Treino não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

$stmt->close();

// Exclui o treino
$sql = "DELETE FROM treinos WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a exclusão."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Treino excluído com sucesso."
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao excluir treino."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>