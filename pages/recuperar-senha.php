<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/enviar_email.php';

$erro = "";
$sucesso = false;
$email = "";

/**
 * Limpa e normaliza o e-mail
 */
function limparEmail(string $email): string
{
    $email = trim($email);

    $email = preg_replace(
        '/[\x{00A0}\x{200B}\x{FEFF}]/u',
        '',
        $email
    );

    $email = preg_replace('/\s+/', '', $email);

    return strtolower($email);
}

/**
 * Processa o formulario
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = limparEmail($_POST["email"] ?? "");

    if (
        $email === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $erro = "Digite um e-mail valido.";
    } else {

        $sql = "
            SELECT id, nome, email
            FROM usuarios
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {
            die("Erro ao preparar consulta: " . $conexao->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {

            $usuario = $resultado->fetch_assoc();

            $tokenBruto = bin2hex(random_bytes(32));

            $tokenHash = hash(
                'sha256',
                $tokenBruto
            );

            $expira = date(
                'Y-m-d H:i:s',
                time() + 3600
            );

            $sqlUpdate = "
                UPDATE usuarios
                SET
                    reset_token_hash = ?,
                    reset_token_expira = ?
                WHERE id = ?
            ";

            $stmtUpdate = $conexao->prepare($sqlUpdate);

            if (!$stmtUpdate) {
                die("Erro ao preparar atualizacao: " .
                    $conexao->error);
            }

            $stmtUpdate->bind_param(
                "ssi",
                $tokenHash,
                $expira,
                $usuario["id"]
            );

            if (!$stmtUpdate->execute()) {
                die("Erro ao salvar token: " .
                    $stmtUpdate->error);
            }

            $stmtUpdate->close();

            $urlBase = defined('URL_BASE')
                ? URL_BASE
                : "http://localhost/Ritmo-unico";

            $link = $urlBase .
                "/pages/redefinir-senha.php?token=" .
                urlencode($tokenBruto);

            $nomeSeguro = htmlspecialchars(
                $usuario["nome"],
                ENT_QUOTES,
                'UTF-8'
            );

            $linkSeguro = htmlspecialchars(
                $link,
                ENT_QUOTES,
                'UTF-8'
            );

            $corpo = "
                <p>Ola, {$nomeSeguro}!</p>

                <p>
                    Recebemos uma solicitacao para
                    redefinir sua senha no Ritmo Unico.
                </p>

                <p>
                    <a href=\"{$linkSeguro}\">
                        Clique aqui para criar uma nova senha
                    </a>
                </p>

                <p>
                    Este link e valido por 1 hora.
                    Se voce nao pediu isso,
                    pode ignorar este e-mail.
                </p>
            ";

            $envio = enviarEmail(
                $usuario["email"],
                $usuario["nome"],
                "Recuperacao de senha - Ritmo Unico",
                $corpo
            );

            if (!$envio) {
                $erro = "Nao foi possivel enviar o e-mail de recuperacao. Verifique as configuracoes do Gmail.";
            } else {
                $sucesso = true;
            }
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Recuperar senha | Ritmo Unico</title>

    <link
        rel="stylesheet"
        href="../css/login.css">

</head>

<body>

    <div class="login-page">

        <div class="login-glow login-glow-left"></div>
        <div class="login-glow login-glow-right"></div>

        <main class="login-container">

            <div class="login-content">

                <h1>Ritmo Unico</h1>

                <span class="login-tag">
                    Tecnologia para corredores
                </span>

                <div class="login-box">

                    <div class="recuperacao">

                        <h2>Recuperar senha</h2>

                        <?php if ($sucesso): ?>

                            <p
                                class="login-description"
                                style="color: #41D8FF;">
                                Se este e-mail estiver cadastrado,
                                enviamos um link de recuperacao para ele.
                                Verifique tambem a caixa de spam.
                            </p>

                        <?php else: ?>

                            <p class="login-description">
                                Digite o e-mail da sua conta.
                                Vamos te enviar um link para criar
                                uma nova senha.
                            </p>

                            <?php if ($erro !== ""): ?>

                                <div class="erro">
                                    <?= htmlspecialchars(
                                        $erro,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </div>

                            <?php endif; ?>

                            <form
                                method="POST"
                                action="recuperar-senha.php">

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
                                        value="<?= htmlspecialchars(
                                                    $email,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        required>

                                </div>

                                <button
                                    type="submit"
                                    class="login-button">
                                    Enviar link de recuperacao
                                </button>

                            </form>

                        <?php endif; ?>

                        <a
                            href="login.php"
                            class="back-home">
                             Voltar para o login
                        </a>

                    </div>

                </div>

            </div>

        </main>

    </div>

</body>

</html>

