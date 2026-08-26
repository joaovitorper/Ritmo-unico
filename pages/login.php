<?php
session_start();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "../config/conexao.php";

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (empty($email) || empty($senha)) {
        $erro = "Preencha o e-mail e a senha.";
    } else {
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario["senha"])) {
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nome"] = $usuario["nome"];
                $_SESSION["usuario_email"] = $usuario["email"];

                header("Location: ../index.php");
                exit;
            } else {
                $erro = "E-mail ou senha incorretos.";
            }
        } else {
            $erro = "E-mail ou senha incorretos.";
        }

        $stmt->close();
        $conexao->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/login.css">

    <title>Login | Ritmo Único</title>
</head>

<body>

    <div class="login-page">

        <div class="login-glow login-glow-left"></div>
        <div class="login-glow login-glow-right"></div>

        <main class="login-container">

            <div class="login-content">

                <img
                    src="../assets/img/identidade Visual/Final logo.png"
                    alt="Logo do Ritmo Único"
                    class="login-logo"
                >

                <span class="login-tag">
                    Tecnologia para corredores
                </span>

                <div class="login-box">

                    <div id="loginArea">

                        <h2>Bem-vindo de volta</h2>

                        <p class="login-description">
                            Entre na sua conta e continue acompanhando sua evolução na corrida.
                        </p>

                        <?php if (!empty($erro)): ?>
                            <small class="erro">
                                <?= htmlspecialchars($erro) ?>
                            </small>
                        <?php endif; ?>

                        <form id="formLogin" action="login.php" method="POST">

                            <div class="input-group">

                                <label for="email">
                                    E-mail
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Digite seu e-mail"
                                    required
                                    maxlength="150"
                                    autocomplete="email"
                                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                                >

                                <small
                                    class="erro"
                                    id="erroEmail"
                                ></small>

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
                                    minlength="6"
                                    maxlength="100"
                                    autocomplete="current-password"
                                >

                                <small
                                    class="erro"
                                    id="erroSenha"
                                ></small>

                            </div>

                            <div class="forgot-password">

                                <a
                                    href="#"
                                    id="recuperarSenha"
                                >
                                    Esqueceu sua senha?
                                </a>

                            </div>

                            <button
                                type="submit"
                                class="login-button"
                            >
                                Entrar
                            </button>

                        </form>

                    </div>

                    <div
                        id="recuperacao"
                        class="recuperacao"
                        style="display: none;"
                    >

                        <h2>Recuperar acesso</h2>

                        <p class="login-description">
                            Digite o e-mail cadastrado para recuperar sua senha.
                        </p>

                        <form id="formRecuperacao">

                            <div class="input-group">

                                <label for="emailRecuperacao">
                                    E-mail
                                </label>

                                <input
                                    type="email"
                                    id="emailRecuperacao"
                                    name="email_recuperacao"
                                    placeholder="Digite seu e-mail"
                                    required
                                    maxlength="150"
                                    autocomplete="email"
                                >

                                <small
                                    class="erro"
                                    id="erroRecuperacao"
                                ></small>

                            </div>

                            <button
                                type="submit"
                                class="login-button"
                            >
                                Recuperar senha
                            </button>

                        </form>

                        <button
                            type="button"
                            id="voltarLogin"
                            class="voltar-login"
                        >
                            ← Voltar para o login
                        </button>

                    </div>

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

    <script>

        const formLogin = document.getElementById("formLogin");
        const email = document.getElementById("email");
        const senha = document.getElementById("senha");

        const recuperarSenha =
            document.getElementById("recuperarSenha");

        const loginArea =
            document.getElementById("loginArea");

        const recuperacao =
            document.getElementById("recuperacao");

        const formRecuperacao =
            document.getElementById("formRecuperacao");

        const voltarLogin =
            document.getElementById("voltarLogin");

        const emailRecuperacao =
            document.getElementById("emailRecuperacao");

        function emailValido(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        formLogin.addEventListener("submit", function(event) {

            let valido = true;

            document.querySelectorAll(".erro").forEach(function(elemento) {
                elemento.textContent = "";
            });

            const emailValor = email.value.trim();

            if (!emailValor) {

                document.getElementById("erroEmail").textContent =
                    "Digite seu e-mail.";

                valido = false;

            } else if (!emailValido(emailValor)) {

                document.getElementById("erroEmail").textContent =
                    "Digite um e-mail válido.";

                valido = false;
            }

            if (!senha.value) {

                document.getElementById("erroSenha").textContent =
                    "Digite sua senha.";

                valido = false;

            } else if (senha.value.length < 6) {

                document.getElementById("erroSenha").textContent =
                    "A senha deve ter pelo menos 6 caracteres.";

                valido = false;
            }

            if (!valido) {
                event.preventDefault();
            }

        });

        recuperarSenha.addEventListener("click", function(event) {

            event.preventDefault();

            loginArea.style.display = "none";
            recuperacao.style.display = "block";

            emailRecuperacao.value = "";

            document.getElementById("erroRecuperacao").textContent = "";

        });

        voltarLogin.addEventListener("click", function() {

            recuperacao.style.display = "none";
            loginArea.style.display = "block";

            document.getElementById("erroRecuperacao").textContent = "";

        });

        formRecuperacao.addEventListener("submit", function(event) {

            event.preventDefault();

            const erroRecuperacao =
                document.getElementById("erroRecuperacao");

            const emailValor =
                emailRecuperacao.value.trim();

            erroRecuperacao.textContent = "";

            if (!emailValor) {

                erroRecuperacao.textContent =
                    "Digite seu e-mail.";

                return;
            }

            if (!emailValido(emailValor)) {

                erroRecuperacao.textContent =
                    "Digite um e-mail válido.";

                return;
            }

            alert(
                "Se esse e-mail estiver cadastrado, você receberá as instruções para recuperar sua senha."
            );

        });

    </script>

</body>

</html>