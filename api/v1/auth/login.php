<?php

session_start();

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
$senha = $_POST["senha"] ?? "";

if ($email === "" || $senha === "") {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha o e-mail e a senha."
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

$sql = "SELECT id, nome, email, data_nascimento, senha
        FROM usuarios
        WHERE email = ?
        LIMIT 1";

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

    http_response_code(401);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "E-mail ou senha incorretos."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario = $resultado->fetch_assoc();

if (!password_verify($senha, $usuario["senha"])) {
    $stmt->close();

    http_response_code(401);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "E-mail ou senha incorretos."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

session_regenerate_id(true);

$_SESSION["usuario_id"] = $usuario["id"];
$_SESSION["usuario_nome"] = $usuario["nome"];
$_SESSION["usuario_email"] = $usuario["email"];

$stmt->close();
$conexao->close();

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Login realizado com sucesso.",
    "usuario" => [
        "id" => $usuario["id"],
        "nome" => $usuario["nome"],
        "email" => $usuario["email"],
        "data_nascimento" => $usuario["data_nascimento"]
    ]
], JSON_UNESCAPED_UNICODE);

exit;