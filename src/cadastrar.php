<?php

require_once __DIR__ . "/../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/cadastro.php");
    exit;
}

$nome = trim($_POST["nome"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$data_nascimento = $_POST["data_nascimento"] ?? "";
$senha = $_POST["senha"] ?? "";
$confirmar_senha = $_POST["confirmar_senha"] ?? "";


// =========================
// VALIDAR DADOS
// =========================

if ($nome === "") {
    die("Digite seu nome.");

}

if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Digite um e-mail válido.");

}

if ($data_nascimento === "") {
    die("Digite sua data de nascimento.");

}

if ($senha === "") {
    die("Digite uma senha.");

}

if (strlen($senha) < 6) {
    die("A senha deve ter pelo menos 6 caracteres.");

}

if ($senha !== $confirmar_senha) {
    die("As senhas não coincidem.");

}


// =========================
// VERIFICAR SE E-MAIL JÁ EXISTE
// =========================

$sql = "SELECT id FROM usuarios WHERE LOWER(TRIM(email)) = ? LIMIT 1";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conexao->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $stmt->close();

    die("Este e-mail já está cadastrado.");

}

$stmt->close();


// =========================
// PROTEGER SENHA
// =========================

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

if ($senha_hash === false) {
    die("Erro ao proteger a senha.");
}


// =========================
// INSERIR USUÁRIO
// =========================

$sql = "INSERT INTO usuarios
        (nome, email, data_nascimento, senha)
        VALUES (?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar cadastro: " . $conexao->error);
}

$stmt->bind_param(
    "ssss",
    $nome,
    $email,
    $data_nascimento,
    $senha_hash
);


// =========================
// SALVAR NO BANCO
// =========================

if ($stmt->execute()) {

    $stmt->close();
    $conexao->close();

    header("Location: ../pages/login.php?cadastro=sucesso");
    exit;

}


// =========================
// ERRO
// =========================

$erro = $stmt->error;

$stmt->close();
$conexao->close();

die("Erro ao cadastrar usuário: " . $erro);