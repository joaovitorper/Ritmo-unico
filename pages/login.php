<?php

session_start();

require_once __DIR__ . "/../config/conexao.php";

$erro = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = strtolower(trim($_POST["email"] ?? ""));
    $senha = $_POST["senha"] ?? "";

    if ($email === "" || $senha === "") {

        $erro = "Preencha o e-mail e a senha.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Digite um e-mail válido.";

    } else {

        $sql = "SELECT id, nome, email, data_nascimento, senha
                FROM usuarios
                WHERE LOWER(TRIM(email)) = ?
                LIMIT 1";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {

            $erro = "Erro no banco de dados: " . $conexao->error;

        } else {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {

                $usuario = $resultado->fetch_assoc();

                if (password_verify($senha, $usuario["senha"])) {

                    session_regenerate_id(true);

                    $_SESSION["usuario_id"] = $usuario["id"];
                    $_SESSION["usuario_nome"] = $usuario["nome"];
                    $_SESSION["usuario_email"] = $usuario["email"];

                    $stmt->close();
                    $conexao->close();

                    header("Location: home.php");
                    exit;

                } else {

                    $erro = "E-mail ou senha incorretos.";
                }

            } else {

                $erro = "E-mail ou senha incorretos.";
            }

            $stmt->close();
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

    <title>Entrar | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/login.css"
    >

</head>

<body>

<div class="login-page">

    <main class="login-container">

        <div class="login-content">

            <h1>Ritmo Único</h1>

            <span class="login-tag">
                Tecnologia para corredores
            </span>

            <div class="login-box">

                <h2>Bem-vindo de volta!</h2>

                <p class="login-description">
                    Entre na sua conta para acompanhar
                    sua evolução na corrida.
                </p>

                <?php if ($erro !== ""): ?>

                    <div class="erro">
                        <?= htmlspecialchars($erro) ?>
                    </div>

                <?php endif; ?>

                <form
                    action="login.php"
                    method="POST"
                >

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
                            value="<?= htmlspecialchars($email) ?>"
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
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="login-button"
                    >
                        Entrar
                    </button>

                </form>

                <div class="divider">
                    <span>ou</span>
                </div>

                <p class="create-account">
                    Ainda não possui uma conta?

                    <a href="cadastro.php">
                        Criar conta
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