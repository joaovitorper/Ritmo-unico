<?php

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido. Use POST.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

$usuario_id = $dados['usuario_id'] ?? $_SESSION['usuario_id'];
$data_corrida = $dados['data_corrida'] ?? date('Y-m-d H:i:s');
$distancia = $dados['distancia'] ?? null;
$tempo = $dados['tempo'] ?? null;
$ritmo = $dados['ritmo'] ?? $dados['pace'] ?? null;
$calorias = $dados['calorias'] ?? null;

if ($usuario_id === null || !is_numeric($usuario_id)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe um usuário válido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($distancia === null || !is_numeric($distancia)) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe uma distância válida.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($tempo === null || $tempo === '') {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe o tempo da corrida.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($ritmo === null || $ritmo === '') {
    $ritmo = ($tempo > 0 && $distancia > 0) ? round((float) $tempo / (float) $distancia, 2) : 0;
}

$usuario_id = (int) $usuario_id;
$distancia = (float) $distancia;
$tempo = (string) $tempo;
$ritmo = (string) $ritmo;
$calorias = ($calorias === null || $calorias === '') ? null : (float) $calorias;

$campos = $conexao->query('SHOW COLUMNS FROM corridas');
$colunas = [];
while ($campo = $campos->fetch_assoc()) {
    $colunas[] = $campo['Field'];
}

$camposInsert = ['usuario_id', 'data_corrida', 'distancia', 'tempo'];
$valores = [$usuario_id, $data_corrida, $distancia, $tempo];
$tipos = 'isds';

if (in_array('ritmo', $colunas, true)) {
    $camposInsert[] = 'ritmo';
    $valores[] = $ritmo;
    $tipos .= 's';
} elseif (in_array('pace', $colunas, true)) {
    $camposInsert[] = 'pace';
    $valores[] = $ritmo;
    $tipos .= 's';
}

if (in_array('calorias', $colunas, true) && $calorias !== null) {
    $camposInsert[] = 'calorias';
    $valores[] = $calorias;
    $tipos .= 'd';
}

$sql = 'INSERT INTO corridas (' . implode(', ', $camposInsert) . ') VALUES (' . implode(', ', array_fill(0, count($camposInsert), '?')) . ')';
$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao preparar o registro da corrida.'
    ], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param($tipos, ...$valores);

if ($stmt->execute()) {
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Corrida salva com sucesso!',
        'id' => $stmt->insert_id
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao salvar a corrida.',
        'debug' => $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>