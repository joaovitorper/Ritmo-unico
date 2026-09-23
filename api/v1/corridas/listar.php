<?php

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido. Use GET.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];

$sql = "SELECT
            id,
            usuario_id,
            data_corrida,
            distancia,
            tempo,
            ritmo,
            calorias
        FROM corridas
        WHERE usuario_id = ?
        ORDER BY id DESC";

$stmt = $conexao->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao preparar a consulta de corridas.'
    ], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$corridas = [];
while ($corrida = $resultado->fetch_assoc()) {
    $corridas[] = $corrida;
}

$stmt->close();
$conexao->close();

echo json_encode([
    'sucesso' => true,
    'total' => count($corridas),
    'corridas' => $corridas
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>