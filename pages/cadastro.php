<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/conexao.php';

$erro = "";
$sucesso = "";

function limparEmail(string $email): string
{
    $email = trim($email);
    $email = preg_replace('/[\x{00A0}\x{200B}\x{FEFF}]/u', '', $email);
    $email = preg_replace('/\s+/', '', $email);

    return strtolower($email);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $email = limparEmail($_POST["email"] ?? "");
    $data_nascimento = $_POST["data_nascimento"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $confirmar_senha = $_POST["confirmar_senha"] ?? "";

    if ($nome === "" || $email === "" || $data_nascimento === "" || $senha === "" || $confirmar_senha === "") {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $sql_busca = "SELECT id FROM usuarios WHERE email = ? LIMIT 1";
        $stmt_busca = $conexao->prepare($sql_busca);

        if (!$stmt_busca) {
            $erro = "Erro no banco ao consultar: " . $conexao->error;
        } else {
            $stmt_busca->bind_param("s", $email);
            $stmt_busca->execute();
            $resultado = $stmt_busca->get_result();

            if ($resultado->num_rows > 0) {
                $erro = "Este e-mail já está cadastrado.";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $sql_insert = "INSERT INTO usuarios (nome, email, data_nascimento, senha) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conexao->prepare($sql_insert);

                if (!$stmt_insert) {
                    $erro = "Erro ao preparar cadastro: " . $conexao->error;
                } else {
                    $stmt_insert->bind_param("ssss", $nome, $email, $data_nascimento, $senha_hash);

                    if ($stmt_insert->execute()) {
                        $sucesso = "Cadastro realizado com sucesso! Agora você já pode entrar.";
                    } else {
                        $erro = "Erro ao cadastrar: " . $stmt_insert->error;
                    }

                    $stmt_insert->close();
                }
            }

            $stmt_busca->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Ritmo Único</title>
</head>

<body>
    <h1>Cadastro</h1>

    <?php if ($erro !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($sucesso !== ""): ?>
        <p style="color: green;">
            <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="cadastro.php">
        <label for="nome">Nome</label><br>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="email">E-mail</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="data_nascimento">Data de nascimento</label><br>
        <input type="date" id="data_nascimento" name="data_nascimento" required><br><br>

        <label for="senha">Senha</label><br>
        <input type="password" id="senha" name="senha" required><br><br>

        <label for="confirmar_senha">Confirmar senha</label><br>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required><br><br>

        <button type="submit">Cadastrar</button>
        <a href="login.php">Entrar</a>
    </form>
</body>

</html>
