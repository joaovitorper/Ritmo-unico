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

$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$data_nascimento = trim($_POST["data_nascimento"] ?? "");
$senha = $_POST["senha"] ?? "";

if ($nome === "" || $email === "" || $data_nascimento === "" || $senha === "") {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha todos os campos."
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

if (strlen($senha) < 6) {
    http_response_code(400);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "A senha deve ter pelo menos 6 caracteres."
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

if ($resultado->num_rows > 0) {
    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Este e-mail já está cadastrado."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->close();

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios
        (nome, email, data_nascimento, senha)
        VALUES (?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao preparar o cadastro."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->bind_param(
    "ssss",
    $nome,
    $email,
    $data_nascimento,
    $senha_hash
);

if (!$stmt->execute()) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível realizar o cadastro."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$usuario_id = $stmt->insert_id;

$_SESSION["usuario_id"] = $usuario_id;
$_SESSION["usuario_nome"] = $nome;
$_SESSION["usuario_email"] = $email;

$stmt->close();
$conexao->close();

echo json_encode([
    "sucesso" => true,
    "mensagem" => "Cadastro realizado com sucesso.",
    "usuario" => [
        "id" => $usuario_id,
        "nome" => $nome,
        "email" => $email,
        "data_nascimento" => $data_nascimento
    ]
], JSON_UNESCAPED_UNICODE);

exit;
