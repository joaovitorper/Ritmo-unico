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

// IMPORTANTE:
// Não vamos confiar no usuario_id enviado pelo JavaScript.
// Pegamos o usuário diretamente da sessão.

$usuario_id = (int) $_SESSION['usuario_id'];

// =====================================================
// DISTÂNCIA
// =====================================================

$distancia = $dados['distancia'] ?? null;

if ($distancia === null || !is_numeric($distancia)) {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe uma distância válida.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$distancia = (float) $distancia;

// =====================================================
// TEMPO
// =====================================================

$tempo = $dados['tempo'] ?? null;

if ($tempo === null || !is_numeric($tempo)) {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Informe um tempo válido.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$tempo = (int) $tempo;

// =====================================================
// RITMO
// =====================================================

$ritmo = $dados['ritmo'] ?? null;

if ($ritmo !== null && $ritmo !== '') {

    if (!is_numeric($ritmo)) {

        http_response_code(400);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Informe um ritmo válido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $ritmo = (float) $ritmo;

} else {

    // Calcula automaticamente o ritmo
    // tempo = segundos
    // distância = quilômetros

    if ($distancia > 0) {

        $ritmo = round(
            ($tempo / 60) / $distancia,
            2
        );

    } else {

        $ritmo = null;

    }
}

// =====================================================
// VALIDAÇÕES
// =====================================================

if ($distancia < 0) {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'A distância não pode ser negativa.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($tempo < 0) {

    http_response_code(400);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'O tempo não pode ser negativo.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// =====================================================
// SALVAR NO MYSQL
// =====================================================

$sql = "
    INSERT INTO corridas
    (
        usuario_id,
        distancia,
        tempo,
        ritmo
    )
    VALUES
    (?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao preparar o salvamento da corrida.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// usuario_id = inteiro
// distancia = decimal
// tempo = inteiro
// ritmo = decimal

$stmt->bind_param(
    'idid',
    $usuario_id,
    $distancia,
    $tempo,
    $ritmo
);

// =====================================================
// EXECUTAR
// =====================================================

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
        'mensagem' => 'Erro ao salvar a corrida.'
    ], JSON_UNESCAPED_UNICODE);

}

$stmt->close();

$conexao->close();

?>