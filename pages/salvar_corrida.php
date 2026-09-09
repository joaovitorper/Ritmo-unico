<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

function responderErro($mensagem, $codigo = 400)
{
    http_response_code($codigo);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => $mensagem,
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

function normalizarTempo($tempo)
{
    $tempo = trim((string) $tempo);

    if ($tempo === '') {
        return null;
    }

    $tempo = explode(".", $tempo)[0];
    $partes = explode(":", $tempo);

    if (count($partes) !== 3) {
        return null;
    }

    $horas = (int) $partes[0];
    $minutos = (int) $partes[1];
    $segundos = (int) $partes[2];

    if ($minutos < 0 || $minutos > 59 || $segundos < 0 || $segundos > 59) {
        return null;
    }

    return [
        "horas" => $horas,
        "minutos" => $minutos,
        "segundos" => $segundos,
        "tempo_limpo" => sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos),
    ];
}

if (!isset($_SESSION["usuario_id"])) {
    responderErro("Usuário não está logado.", 401);
}

require_once "../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderErro("Método inválido.", 405);
}

$dados = $_POST;

if (empty($dados)) {
    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    if (!is_array($dados)) {
        $dados = [];
    }
}

$usuario_id = (int) $_SESSION["usuario_id"];
$distancia = (float) ($dados["distancia"] ?? 0);
$tempo = trim((string) ($dados["tempo"] ?? ""));

if ($usuario_id <= 0) {
    responderErro("Usuário inválido.", 401);
}

if ($distancia <= 0) {
    responderErro("A distância precisa ser maior que zero.", 400);
}

if ($tempo === '') {
    responderErro("Informe o tempo da corrida.", 400);
}

$tempoFormatado = normalizarTempo($tempo);

if ($tempoFormatado === null) {
    responderErro("Formato de tempo inválido. Use HH:MM:SS.", 400);
}

$tempoTotalMinutos = ($tempoFormatado["horas"] * 60) + $tempoFormatado["minutos"] + ($tempoFormatado["segundos"] / 60);
$ritmo = 0;

if ($tempoTotalMinutos > 0) {
    $ritmo = $tempoTotalMinutos / $distancia;
}

$sql = "INSERT INTO corridas (usuario_id, data_corrida, distancia, tempo, ritmo)
        VALUES (?, NOW(), ?, ?, ?)";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    responderErro("Erro na preparação do banco de dados: " . $conexao->error, 500);
}

$stmt->bind_param("idsd", $usuario_id, $distancia, $tempoFormatado["tempo_limpo"], $ritmo);

if (!$stmt->execute()) {
    $mensagem = "Erro ao salvar corrida: " . $stmt->error;
    $stmt->close();
    $conexao->close();
    responderErro($mensagem, 500);
}

$id_corrida = $stmt->insert_id;

$stmt->close();
$conexao->close();

http_response_code(200);

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Corrida salva com sucesso!",
    "id_corrida" => $id_corrida,
], JSON_UNESCAPED_UNICODE);

?>