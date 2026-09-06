
<?php

session_start();

require_once __DIR__ . "/../config/conexao.php";

$erro = "";

/* =========================================================
   PROCESSAR LOGIN
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Receber dados
    $email = strtolower(trim($_POST["email"] ?? ""));
    $senha = $_POST["senha"] ?? "";

    // VALIDAÇÕES
    if ($email === "") {

        $erro = "Digite seu e-mail.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Digite um e-mail válido.";

    } elseif ($senha === "") {

        $erro = "Digite sua senha.";

    }

    // BUSCAR USUÁRIO
    if ($erro === "") {

        $sql = "SELECT id, nome, email, data_nascimento, senha
                FROM usuarios
                WHERE email = ?
                LIMIT 1";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {

            $erro = "Erro no banco de dados: " . $conexao->error;

        } else {

            $stmt->bind_param("s", $email);

            $stmt->execute();

            $resultado = $stmt->get_result();

            // VERIFICAR SE O USUÁRIO EXISTE
            if ($resultado->num_rows === 0) {

                $erro = "E-mail ou senha incorretos.";

            } else {

                $usuario = $resultado->fetch_assoc();

                // VERIFICAR SENHA
                if (password_verify($senha, $usuario["senha"])) {

                    // Criar sessão
                    $_SESSION["usuario_id"] = $usuario["id"];
                    $_SESSION["usuario_nome"] = $usuario["nome"];
                    $_SESSION["usuario_email"] = $usuario["email"];

                    // Regenerar ID da sessão por segurança
                    session_regenerate_id(true);

                    $stmt->close();
                    $conexao->close();

                    // Redirecionar para a página inicial
                    header("Location: home.php");
                    exit;

                } else {

                    $erro = "E-mail ou senha incorretos.";

                }
            }

            $stmt->close();
        }
    }
}

/* =========================================================
   MENSAGEM DE CADASTRO
========================================================= */

$cadastro_sucesso = "";

if (isset($_SESSION["cadastro_sucesso"])) {

    $cadastro_sucesso = $_SESSION["cadastro_sucesso"];

    unset($_SESSION["cadastro_sucesso"]);
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

            <!-- LOGO / NOME -->

            <h1>Ritmo Único</h1>

            <span class="login-tag">
                Tecnologia para corredores
            </span>


            <!-- CAIXA DE LOGIN -->

            <div class="login-box">

                <h2>Bem-vindo de volta!</h2>

                <p class="login-description">
                    Entre na sua conta para acompanhar
                    sua evolução na corrida.
                </p>


                <!-- MENSAGEM DE CADASTRO -->

                <?php if ($cadastro_sucesso !== ""): ?>

                    <div class="sucesso">

                        <?= htmlspecialchars($cadastro_sucesso) ?>

                    </div>

                <?php endif; ?>


                <!-- MENSAGEM DE ERRO -->

                <?php if ($erro !== ""): ?>

                    <div class="erro">

                        <?= htmlspecialchars($erro) ?>

                    </div>

                <?php endif; ?>


                <!-- FORMULÁRIO -->

                <form
                    action="login.php"
                    method="POST"
                >

                    <!-- E-MAIL -->

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
                            value="<?= htmlspecialchars($email ?? '') ?>"
                            required
                        >

                    </div>


                    <!-- SENHA -->

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


                    <!-- ESQUECI A SENHA -->

                    <div class="forgot-password">

                        <a href="#">
                            Esqueci minha senha
                        </a>

                    </div>


                    <!-- BOTÃO -->

                    <button
                        type="submit"
                        class="login-button"
                    >

                        Entrar

                    </button>

                </form>


                <!-- DIVISOR -->

                <div class="divider">

                    <span>ou</span>

                </div>


                <!-- CADASTRO -->

                <p class="create-account">

                    Ainda não possui uma conta?

                    <a href="cadastro.php">
                        Criar conta
                    </a>

                </p>

            </div>


            <!-- VOLTAR -->

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

