<?php

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/conexao.php';

// Busca todos os treinos
$sql = "SELECT
            id,
            usuario_id,
            titulo,
            descricao,
            tipo,
            distancia,
            duracao,
            intensidade,
            data_treino,
            concluido,
            criado_em
        FROM treinos
        ORDER BY id DESC";

$resultado = $conexao->query($sql);

if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao buscar treinos."
    ], JSON_UNESCAPED_UNICODE);

    $conexao->close();
    exit;
}

$treinos = [];

while ($treino = $resultado->fetch_assoc()) {
    $treinos[] = $treino;
}

echo json_encode([
    "sucesso" => true,
    "total" => count($treinos),
    "treinos" => $treinos
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conexao->close();

?>