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
$data_corrida = $dados['data_corrida'] ?? '';
$distancia = $dados['distancia'] ?? null;
$tempo = $dados['tempo'] ?? '';
$ritmo = $dados['ritmo'] ?? '';

if ($id === null || !is_numeric($id)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um ID de corrida válido."
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

if ($data_corrida === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe a data da corrida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($distancia === null || !is_numeric($distancia)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe uma distância válida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($tempo === '' || $ritmo === '') {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe o tempo e o ritmo da corrida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;
$usuario_id = (int) $usuario_id;
$distancia = (float) $distancia;

$sql = "SELECT id FROM corridas WHERE id = ?";

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
        "mensagem" => "Corrida não encontrada."
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

$sql = "UPDATE corridas
        SET usuario_id = ?,
            data_corrida = ?,
            distancia = ?,
            tempo = ?,
            ritmo = ?
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
    "isdssi",
    $usuario_id,
    $data_corrida,
    $distancia,
    $tempo,
    $ritmo,
    $id
);

if ($stmt->execute()) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Corrida atualizada com sucesso."
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar corrida."
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>