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

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Dados inválidos ou não enviados."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = $dados['id'] ?? null;
$usuario_id = $dados['usuario_id'] ?? null;
$corrida_id = $dados['corrida_id'] ?? null;
$latitude = $dados['latitude'] ?? null;
$longitude = $dados['longitude'] ?? null;
$altitude = $dados['altitude'] ?? null;
$velocidade = $dados['velocidade'] ?? null;
$distancia = $dados['distancia'] ?? null;

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

if ($latitude === null || !is_numeric($latitude)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe uma latitude válida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($longitude === null || !is_numeric($longitude)) {

    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe uma longitude válida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = (int) $id;
$usuario_id = (int) $usuario_id;
$latitude = (float) $latitude;
$longitude = (float) $longitude;

if ($corrida_id !== null && $corrida_id !== '') {
    $corrida_id = (int) $corrida_id;
} else {
    $corrida_id = null;
}

if ($altitude !== null && $altitude !== '') {
    $altitude = (float) $altitude;
} else {
    $altitude = null;
}

if ($velocidade !== null && $velocidade !== '') {
    $velocidade = (float) $velocidade;
} else {
    $velocidade = null;
}

if ($distancia !== null && $distancia !== '') {
    $distancia = (float) $distancia;
} else {
    $distancia = null;
}

$stmtVerificar = $conexao->prepare(
    "SELECT id FROM gps WHERE id = ?"
);

$stmtVerificar->bind_param("i", $id);
$stmtVerificar->execute();

$resultado = $stmtVerificar->get_result();

if ($resultado->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Registro GPS não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $stmtVerificar->close();
    $conexao->close();

    exit;
}

$stmtVerificar->close();

$stmt = $conexao->prepare(
    "UPDATE gps SET
        usuario_id = ?,
        corrida_id = ?,
        latitude = ?,
        longitude = ?,
        altitude = ?,
        velocidade = ?,
        distancia = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "iidddddi",
    $usuario_id,
    $corrida_id,
    $latitude,
    $longitude,
    $altitude,
    $velocidade,
    $distancia,
    $id
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao atualizar registro GPS."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Registro GPS atualizado com sucesso."
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>