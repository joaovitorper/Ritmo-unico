<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/conexao.php';

// =====================================================
// VERIFICAR LOGIN
// =====================================================

if (!isset($_SESSION['usuario_id'])) {

    http_response_code(401);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// =====================================================
// ACEITAR SOMENTE POST
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método não permitido. Use POST.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// =====================================================
// RECEBER DADOS
// =====================================================

$dados = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($dados)) {
    $dados = $_POST;
}

// =====================================================
// USUÁRIO
// =====================================================

$usuario_id = (int) $_SESSION['usuario_id'];

// =====================================================
// DADOS DA CORRIDA
// =====================================================

$data_corrida = $dados['data_corrida'] ?? date('Y-m-d H:i:s');
$distancia = $dados['distancia'] ?? null;
$tempo = $dados['tempo'] ?? null;
$ritmo = $dados['ritmo'] ?? $dados['pace'] ?? null;
$calorias = $dados['calorias'] ?? null;

if ($distancia === null || !is_numeric($distancia)) {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe uma distância válida.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$distancia = (float) $distancia;

if ($tempo === null || $tempo === '') {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe um tempo válido.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$tempo = (string) $tempo;

if ($ritmo === null || $ritmo === '') {
    $ritmo = ($distancia > 0 && $tempo > 0) ? round(((float) $tempo / 60) / $distancia, 2) : 0;
} else {
    $ritmo = (string) $ritmo;
}

$calorias = ($calorias === null || $calorias === '') ? null : (float) $calorias;

if ($distancia < 0) {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'A distância não pode ser negativa.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($tempo !== '' && !is_numeric($tempo)) {
    $tempo = (string) $tempo;
}

// =====================================================
// SALVAR NO MYSQL
// =====================================================

$colunas = $conexao->query('SHOW COLUMNS FROM corridas');
$camposDisponiveis = [];
if ($colunas) {
    while ($coluna = $colunas->fetch_assoc()) {
        $camposDisponiveis[] = $coluna['Field'];
    }
}

$camposInsert = ['usuario_id', 'data_corrida', 'distancia', 'tempo'];
$valores = [$usuario_id, $data_corrida, $distancia, $tempo];
$tipos = 'isds';

if (in_array('ritmo', $camposDisponiveis, true)) {
    $camposInsert[] = 'ritmo';
    $valores[] = $ritmo;
    $tipos .= 's';
} elseif (in_array('pace', $camposDisponiveis, true)) {
    $camposInsert[] = 'pace';
    $valores[] = $ritmo;
    $tipos .= 's';
}

if (in_array('calorias', $camposDisponiveis, true) && $calorias !== null) {
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
        'mensagem' => 'Erro ao preparar o salvamento da corrida.',
        'debug' => $conexao->error
    ], JSON_UNESCAPED_UNICODE);

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