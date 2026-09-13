<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/conexao.php';

$erro = "";
$sucesso = false;

$token = $_GET["token"] ?? $_POST["token"] ?? "";
$token = trim($token);

if ($token === "") {
    $erro = "Link de recuperação inválido.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $erro === "") {

    $senha = $_POST["senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";

    if (strlen($senha) < 8) {
        $erro = "A senha deve ter pelo menos 8 caracteres.";
    } elseif ($senha !== $confirmarSenha) {
        $erro = "As senhas não coincidem.";
    } else {

        $tokenHash = hash("sha256", $token);

        $sql = "SELECT id FROM usuarios
                WHERE reset_token_hash = ?
                AND reset_token_expira > NOW()
                LIMIT 1";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {
            $erro = "Erro ao consultar o token.";
        } else {

            $stmt->bind_param("s", $tokenHash);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($resultado->num_rows !== 1) {

                $erro = "Este link é inválido ou já expirou.";

            } else {

                $usuario = $resultado->fetch_assoc();

                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $sqlUpdate = "UPDATE usuarios
                              SET senha = ?,
                                  reset_token_hash = NULL,
                                  reset_token_expira = NULL
                              WHERE id = ?";

                $stmtUpdate = $conexao->prepare($sqlUpdate);

                if (!$stmtUpdate) {

                    $erro = "Erro ao preparar a alteração da senha.";

                } else {

                    $stmtUpdate->bind_param(
                        "si",
                        $senhaHash,
                        $usuario["id"]
                    );

                    if ($stmtUpdate->execute()) {
                        $sucesso = true;
                    } else {
                        $erro = "Não foi possível alterar a senha.";
                    }

                    $stmtUpdate->close();
                }
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

```
<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Redefinir senha | Ritmo Único</title>

<link rel="stylesheet" href="../css/login.css">
```

</head>

<body>

```
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

                <h2>Redefinir senha</h2>

                <?php if ($sucesso): ?>

                    <p
                        class="login-description"
                        style="color: #41D8FF;"
                    >
                        Sua senha foi redefinida com sucesso.
                        Agora você pode fazer login normalmente.
                    </p>

                    <a
                        href="login.php"
                        class="login-button"
                        style="display: inline-block; text-align: center; text-decoration: none;"
                    >
                        Ir para o login
                    </a>

                <?php else: ?>

                    <p class="login-description">
                        Digite sua nova senha abaixo.
                    </p>

                    <?php if ($erro !== ""): ?>

                        <div class="erro">
                            <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                    <?php endif; ?>

                    <?php if ($token !== ""): ?>

                        <form
                            method="POST"
                            action="redefinir-senha.php"
                        >

                            <input
                                type="hidden"
                                name="token"
                                value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                            >

                            <div class="input-group">

                                <label for="senha">
                                    Nova senha
                                </label>

                                <input
                                    type="password"
                                    id="senha"
                                    name="senha"
                                    placeholder="Digite sua nova senha"
                                    minlength="8"
                                    required
                                >

                            </div>

                            <div class="input-group">

                                <label for="confirmar_senha">
                                    Confirmar nova senha
                                </label>

                                <input
                                    type="password"
                                    id="confirmar_senha"
                                    name="confirmar_senha"
                                    placeholder="Confirme sua nova senha"
                                    minlength="8"
                                    required
                                >

                            </div>

                            <button
                                type="submit"
                                class="login-button"
                            >
                                Redefinir senha
                            </button>

                        </form>

                    <?php endif; ?>

                <?php endif; ?>

                <a
                    href="login.php"
                    class="back-home"
                >
                    ← Voltar para o login
                </a>

            </div>

        </div>

    </main>

</div>
```

</body>

</html>
