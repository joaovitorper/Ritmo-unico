O ChatGPT disse:
Sim. Então vamos começar pelo pages/cadastro.php.

Abra pages/cadastro.php, apague tudo que estiver lá e cole este código:

<?php

session_start();

require_once __DIR__ . "/../config/conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $data_nascimento = $_POST["data_nascimento"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $confirmar_senha = $_POST["confirmar_senha"] ?? "";
    $termos = isset($_POST["termos"]);

    // VALIDAÇÕES

    if ($nome === "") {
        $erro = "Digite seu nome completo.";

    } elseif (strlen($nome) < 3) {
        $erro = "O nome deve ter pelo menos 3 caracteres.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";

    } elseif ($data_nascimento === "") {
        $erro = "Informe sua data de nascimento.";

    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";

    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não são iguais.";

    } elseif (!$termos) {
        $erro = "Você precisa aceitar os termos de uso.";
    }

    // VERIFICAR E-MAIL

    if ($erro === "") {

        $sql = "SELECT id FROM usuarios WHERE email = ? LIMIT 1";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {

            $erro = "Erro no banco: " . $conexao->error;

        } else {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $erro = "Este e-mail já está cadastrado.";
            }

            $stmt->close();
        }
    }

    // SALVAR NO BANCO

    if ($erro === "") {

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios
                (nome, email, data_nascimento, senha)
                VALUES (?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {

            $erro = "Erro ao preparar cadastro: " . $conexao->error;

        } else {

            $stmt->bind_param(
                "ssss",
                $nome,
                $email,
                $data_nascimento,
                $senha_hash
            );

            if ($stmt->execute()) {

                $_SESSION["cadastro_sucesso"] =
                    "Conta criada com sucesso!";

                $stmt->close();
                $conexao->close();

                header("Location: login.php");
                exit;

            } else {

                $erro =
                    "Erro ao salvar no banco: " .
                    $stmt->error;

                $stmt->close();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Criar conta | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/cadastro.css"
    >

</head>

<body>

<div class="cadastro-page">

    <main class="cadastro-container">

        <div class="cadastro-content">

            <h1>Ritmo Único</h1>

            <span class="cadastro-tag">
                Tecnologia para corredores
            </span>

            <div class="cadastro-box">

                <h2>Crie sua conta</h2>

                <p class="cadastro-description">
                    Crie sua conta e comece a acompanhar
                    sua evolução na corrida.
                </p>

                <?php if ($erro !== ""): ?>

                    <div class="erro">
                        <?= htmlspecialchars($erro) ?>
                    </div>

                <?php endif; ?>

                <form
                    action="cadastro.php"
                    method="POST"
                >

                    <div class="input-group">

                        <label for="nome">
                            Nome completo
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            placeholder="Digite seu nome completo"
                            maxlength="100"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label for="email">
                            E-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            maxlength="150"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label for="data_nascimento">
                            Data de nascimento
                        </label>

                        <input
                            type="date"
                            id="data_nascimento"
                            name="data_nascimento"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label for="senha">
                            Senha
                        </label>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Digite sua senha"
                            minlength="6"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label for="confirmar_senha">
                            Confirmar senha
                        </label>

                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            placeholder="Digite a senha novamente"
                            minlength="6"
                            required
                        >

                    </div>

                    <label class="terms">

                        <input
                            type="checkbox"
                            name="termos"
                            value="1"
                            required
                        >

                        <span>
                            Aceito os termos de uso e a política de privacidade.
                        </span>

                    </label>

                    <button
                        type="submit"
                        class="cadastro-button"
                    >
                        Criar minha conta
                    </button>

                </form>

                <div class="divider">
                    <span>ou</span>
                </div>

                <p class="already-account">

                    Já possui uma conta?

                    <a href="login.php">
                        Entrar
                    </a>

                </p>

            </div>

            <a
                href="../index.php"
                class="back-home"
            >
                ← Voltar para o início
            </a>

        </div>

    </main>

</div>

</body>

</html>