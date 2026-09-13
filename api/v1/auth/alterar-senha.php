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
$novaSenha = $_POST["nova_senha"] ?? "";

if ($email === "" || $novaSenha === "") {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe o e-mail e a nova senha."
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

if (strlen($novaSenha) < 6) {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A nova senha deve ter pelo menos 6 caracteres."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$sql = "SELECT id FROM usuarios WHERE email = ? LIMIT 1";

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
        "mensagem" => "Usuário não encontrado."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario = $resultado->fetch_assoc();
$stmt->close();

$senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

$sql = "UPDATE usuarios SET senha = ? WHERE id = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar a alteração da senha."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->bind_param("si", $senhaHash, $usuario["id"]);

if (!$stmt->execute()) {
    $stmt->close();
    $conexao->close();

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível alterar a senha."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->close();
$conexao->close();

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Senha alterada com sucesso."
], JSON_UNESCAPED_UNICODE);

exit;
