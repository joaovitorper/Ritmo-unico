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

$usuario_id = $dados['usuario_id'] ?? null;
$corrida_id = $dados['corrida_id'] ?? null;
$latitude = $dados['latitude'] ?? null;
$longitude = $dados['longitude'] ?? null;
$altitude = $dados['altitude'] ?? null;
$velocidade = $dados['velocidade'] ?? null;
$distancia = $dados['distancia'] ?? null;

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

$usuario_id = (int) $usuario_id;

if ($corrida_id !== null && $corrida_id !== '') {
    if (!is_numeric($corrida_id)) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Informe um corrida_id válido."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $corrida_id = (int) $corrida_id;
} else {
    $corrida_id = null;
}

$latitude = (float) $latitude;
$longitude = (float) $longitude;

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

$stmtUsuario = $conexao->prepare(
    "SELECT id FROM usuarios WHERE id = ?"
);

$stmtUsuario->bind_param("i", $usuario_id);
$stmtUsuario->execute();

$resultadoUsuario = $stmtUsuario->get_result();

if ($resultadoUsuario->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    $stmtUsuario->close();
    $conexao->close();

    exit;
}

$stmtUsuario->close();

if ($corrida_id !== null) {

    $stmtCorrida = $conexao->prepare(
        "SELECT id FROM corridas WHERE id = ?"
    );

    $stmtCorrida->bind_param("i", $corrida_id);
    $stmtCorrida->execute();

    $resultadoCorrida = $stmtCorrida->get_result();

    if ($resultadoCorrida->num_rows === 0) {

        http_response_code(404);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Corrida não encontrada."
        ], JSON_UNESCAPED_UNICODE);

        $stmtCorrida->close();
        $conexao->close();

        exit;
    }

    $stmtCorrida->close();
}

$stmt = $conexao->prepare(
    "INSERT INTO gps
        (usuario_id, corrida_id, latitude, longitude, altitude, velocidade, distancia)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "iiddddd",
    $usuario_id,
    $corrida_id,
    $latitude,
    $longitude,
    $altitude,
    $velocidade,
    $distancia
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao cadastrar registro GPS."
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conexao->close();

    exit;
}

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Registro GPS cadastrado com sucesso.",
    "id" => $stmt->insert_id
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$stmt->close();
$conexao->close();

?>