<?php

require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acesso inválido.");
}

$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$data_nascimento = $_POST["data_nascimento"] ?? "";
$senha = $_POST["senha"] ?? "";
$confirmar_senha = $_POST["confirmar_senha"] ?? "";
$termos = isset($_POST["termos"]);

if (
    $nome === "" ||
    $email === "" ||
    $data_nascimento === "" ||
    $senha === ""
) {
    die("Preencha todos os campos obrigatórios.");
}

if (!$termos) {
    die("Você precisa aceitar os termos de uso.");
}

if ($senha !== $confirmar_senha) {
    die("As senhas não são iguais.");
}

if (strlen($senha) < 6) {
    die("A senha deve ter pelo menos 6 caracteres.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("E-mail inválido.");
}

$sql = "SELECT id FROM usuarios WHERE email = ?";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conexao->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $stmt->close();
    $conexao->close();

    die("Este e-mail já está cadastrado.");
}

$stmt->close();

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "
    INSERT INTO usuarios (
        nome,
        email,
        data_nascimento,
        senha
    )
    VALUES (?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    $conexao->close();

    die("Erro ao preparar cadastro: " . $conexao->error);
}

$stmt->bind_param(
    "ssss",
    $nome,
    $email,
    $data_nascimento,
    $senha_hash
);

if ($stmt->execute()) {

    $stmt->close();
    $conexao->close();

    header("Location: ../login.php?cadastro=sucesso");
    exit;

}

$erro = $stmt->error;

$stmt->close();
$conexao->close();

die("Erro ao cadastrar usuário: " . $erro);

?>