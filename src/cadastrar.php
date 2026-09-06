<?php

require_once __DIR__ . "/../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acesso inválido.");
}

$nome = trim($_POST["nome"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$data_nascimento = $_POST["data_nascimento"] ?? "";
$senha = $_POST["senha"] ?? "";
$confirmar_senha = $_POST["confirmar_senha"] ?? "";
$termos = isset($_POST["termos"]);

/*
|--------------------------------------------------------------------------
| VALIDAÇÕES
|--------------------------------------------------------------------------
*/

if ($nome === "") {
    die("Digite seu nome completo.");
}

if (strlen($nome) < 3) {
    die("O nome deve ter pelo menos 3 caracteres.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Digite um e-mail válido.");
}

if ($data_nascimento === "") {
    die("Informe sua data de nascimento.");
}

if ($senha === "") {
    die("Digite uma senha.");
}

if (strlen($senha) < 6) {
    die("A senha deve ter pelo menos 6 caracteres.");
}

if ($senha !== $confirmar_senha) {
    die("As senhas não são iguais.");
}

if (!$termos) {
    die("Você precisa aceitar os termos de uso.");
}

/*
|--------------------------------------------------------------------------
| VERIFICAR SE O E-MAIL JÁ EXISTE
|--------------------------------------------------------------------------
*/

$sql = "SELECT id FROM usuarios WHERE email = ? LIMIT 1";

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

/*
|--------------------------------------------------------------------------
| CRIAR HASH DA SENHA
|--------------------------------------------------------------------------
*/

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

if ($senha_hash === false) {
    $conexao->close();

    die("Erro ao proteger a senha.");
}

/*
|--------------------------------------------------------------------------
| INSERIR USUÁRIO
|--------------------------------------------------------------------------
*/

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
    $erro = $conexao->error;

    $conexao->close();

    die("Erro ao preparar cadastro: " . $erro);
}

$stmt->bind_param(
    "ssss",
    $nome,
    $email,
    $data_nascimento,
    $senha_hash
);

/*
|--------------------------------------------------------------------------
| EXECUTAR CADASTRO
|--------------------------------------------------------------------------
*/

if (!$stmt->execute()) {

    $erro = $stmt->error;

    $stmt->close();
    $conexao->close();

    die("Erro ao cadastrar usuário: " . $erro);
}

/*
|--------------------------------------------------------------------------
| CADASTRO REALIZADO
|--------------------------------------------------------------------------
*/

$id_usuario = $conexao->insert_id;

$stmt->close();
$conexao->close();

/*
|--------------------------------------------------------------------------
| REDIRECIONAR PARA O LOGIN
|--------------------------------------------------------------------------
*/

header(
    "Location: ../pages/login.php?cadastro=sucesso&id=" .
    $id_usuario
);

exit;

?>
