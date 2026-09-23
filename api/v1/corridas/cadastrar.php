<?php

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../../config/conexao.php';

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

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
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
        'mensagem' => 'Informe um ID de usuário válido.'
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

$stmt = $conexao->prepare('SELECT id FROM usuarios WHERE id = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao preparar a consulta do usuário.'
    ], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não encontrado.'
    ], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    $conexao->close();
    exit;
}

$stmt->close();

$colunas = $conexao->query('SHOW COLUMNS FROM corridas');
$campos = [];
if ($colunas) {
    while ($coluna = $colunas->fetch_assoc()) {
        $campos[] = $coluna['Field'];
    }
}

$camposInsert = ['usuario_id', 'data_corrida', 'distancia', 'tempo'];
$valores = [$usuario_id, $data_corrida, $distancia, $tempo];
$tipos = 'isds';

if (in_array('ritmo', $campos, true)) {
    $camposInsert[] = 'ritmo';
    $valores[] = $ritmo;
    $tipos .= 's';
} elseif (in_array('pace', $campos, true)) {
    $camposInsert[] = 'pace';
    $valores[] = $ritmo;
    $tipos .= 's';
}

if (in_array('calorias', $campos, true) && $calorias !== null) {
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
        'mensagem' => 'Erro ao preparar o cadastro da corrida.'
    ], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$stmt->bind_param($tipos, ...$valores);

if ($stmt->execute()) {
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Corrida cadastrada com sucesso.',
        'corrida_id' => $stmt->insert_id
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao cadastrar corrida.',
        'debug' => $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conexao->close();

?>