<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Use GET."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$sql = "SELECT
            id,
            usuario_id,
            notificacoes,
            gps,
            criado_em
        FROM configuracoes
        ORDER BY id DESC";

$resultado = $conexao->query($sql);

if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao buscar configurações."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$configuracoes = [];

while ($configuracao = $resultado->fetch_assoc()) {
    $configuracoes[] = $configuracao;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($configuracoes),
    "configuracoes" => $configuracoes
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>