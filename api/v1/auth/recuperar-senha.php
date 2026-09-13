<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método não permitido. Utilize POST."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$email = trim($_POST["email"] ?? "");

if ($email === "") {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe o e-mail."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Digite um e-mail válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$sql = "SELECT id, nome FROM usuarios WHERE email = ? LIMIT 1";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a consulta."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    $stmt->close();
    $conexao->close();

    http_response_code(404);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "E-mail não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario = $resultado->fetch_assoc();

$stmt->close();
$conexao->close();

echo json_encode([
    "sucesso" => true,
    "mensagem" => "E-mail encontrado. O usuário pode prosseguir para a alteração da senha.",
    "usuario" => [
        "id" => $usuario["id"],
        "nome" => $usuario["nome"],
        "email" => $email
    ]
], JSON_UNESCAPED_UNICODE);

exit;
