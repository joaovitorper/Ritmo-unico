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

$colunas = $conexao->query('SHOW COLUMNS FROM corridas');
$camposDisponiveis = [];
if ($colunas) {
    while ($coluna = $colunas->fetch_assoc()) {
        $camposDisponiveis[] = $coluna['Field'];
    }
}

$camposSelecionados = [];
foreach (['id', 'usuario_id', 'data_corrida', 'distancia', 'tempo', 'ritmo', 'pace', 'calorias'] as $campo) {
    if (in_array($campo, $camposDisponiveis, true)) {
        $camposSelecionados[] = $campo;
    }
}

if (empty($camposSelecionados)) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Estrutura da tabela de corridas não foi encontrada.'
    ], JSON_UNESCAPED_UNICODE);
    $conexao->close();
    exit;
}

$sql = 'SELECT ' . implode(', ', $camposSelecionados) . ' FROM corridas WHERE usuario_id = ? ORDER BY id DESC';

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
    if (!isset($corrida['calorias'])) {
        $corrida['calorias'] = 0;
    }
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