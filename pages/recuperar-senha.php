<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../classes/Email.php';

$erro = "";
$sucesso = false;
$email = "";

function gerarTokenRecuperacao(int $bytes = 32): string
{
    if (function_exists('random_bytes')) {
        try {
            return bin2hex(random_bytes($bytes));
        } catch (Throwable $e) {
            // continua para o fallback
        }
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        $valor = openssl_random_pseudo_bytes($bytes);
        if ($valor !== false) {
            return bin2hex($valor);
        }
    }

    $alfa = '0123456789abcdef';
    $token = '';

    for ($i = 0; $i < ($bytes * 2); $i++) {
        $token .= $alfa[random_int(0, strlen($alfa) - 1)];
    }

    return $token;
}

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
        $erro = "Digite um e-mail válido.";
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

            $tokenBruto = gerarTokenRecuperacao(32);

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
                : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http')
                    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');

            $link = rtrim($urlBase, '/') .
                '/pages/redefinir-senha.php?token=' .
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
                <p>Olá, {$nomeSeguro}!</p>

                <p>Use o link abaixo para redefinir sua senha:</p>

                <p>
                    <a href=\"{$linkSeguro}\">
                        Redefinir senha
                    </a>
                </p>
            ";

            $envio = Email::enviar(
                $usuario["email"],
                "Recuperação de senha - Ritmo Único",
                $corpo,
                $usuario["nome"]
            );

            if (!$envio) {
                $detalhe = $GLOBALS['erro_email_detalhe'] ?? '';
                $erro = "Não foi possível enviar o e-mail de recuperação." . ($detalhe !== '' ? ' Detalhe: ' . $detalhe : ' Verifique as configurações do Gmail.');
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

    <title>Recuperar senha | Ritmo Único</title>

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

                <h1>Ritmo Único</h1>

                <span class="login-tag">
                    Tecnologia para corredores
                </span>

                <div class="login-box">

                    <div class="recuperacao">

                        <h2>Recuperar senha</h2>

                        <?php if ($sucesso): ?>

                            <div class="status-box success">
                                Se este e-mail estiver cadastrado,
                                enviamos um link de recuperação para ele.
                                Verifique também a caixa de spam.
                            </div>

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
                                    Enviar link de recuperação
                                </button>

                            </form>

                        <?php endif; ?>

                        <a
                            href="login.php"
                            class="back-home">
                            Voltar para o login
                        </a>

                    </div>

                </div>

            </div>

        </main>

    </div>

</body>

</html>