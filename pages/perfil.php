<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/conexao.php";

$id_usuario = $_SESSION["usuario_id"];

$sql = "
    SELECT
        id,
        nome,
        email,
        data_nascimento
    FROM usuarios
    WHERE id = ?
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conexao->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    $stmt->close();
    $conexao->close();

    session_destroy();

    header("Location: login.php");
    exit;
}

$usuario = $resultado->fetch_assoc();

$stmt->close();
$conexao->close();

$nome = $usuario["nome"];
$email = $usuario["email"];
$data_nascimento = $usuario["data_nascimento"];

$idade = "";

if (!empty($data_nascimento)) {
    $dataNascimento = new DateTime($data_nascimento);
    $hoje = new DateTime();

    $idade = $hoje->diff($dataNascimento)->y;
}

$inicial = strtoupper(substr($nome, 0, 1));

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="../css/perfil.css"
    >

    <title>Perfil | Ritmo Único</title>

</head>

<body>

    <div class="perfil-page">

        <div class="perfil-glow perfil-glow-left"></div>
        <div class="perfil-glow perfil-glow-right"></div>

        <main class="perfil-container">

            <div class="perfil-content">

                <img
                    src="../assets/img/identidade Visual/Final logo.png"
                    alt="Logo do Ritmo Único"
                    class="perfil-logo"
                >

                <span class="perfil-tag">
                    Tecnologia pra corredores
                </span>

                <section class="perfil-box">

                    <div class="perfil-foto">

                        <div
                            class="avatar"
                            id="avatar"
                            aria-label="Avatar do usuário"
                        >

                            <span id="avatarInicial">
                                <?= htmlspecialchars($inicial) ?>
                            </span>

                        </div>

                    </div>

                    <h2>Meu perfil</h2>

                    <p
                        class="perfil-email"
                        id="perfilMensagem"
                    >
                        <?= htmlspecialchars($email) ?>
                    </p>

                    <form
                        id="perfilForm"
                        method="POST"
                        action="editar_perfil.php"
                    >

                        <div class="perfil-info">

                            <div class="info-item">

                                <label for="nome">
                                    Nome
                                </label>

                                <input
                                    type="text"
                                    id="nome"
                                    name="nome"
                                    placeholder="Digite seu nome"
                                    autocomplete="name"
                                    value="<?= htmlspecialchars($nome) ?>"
                                    required
                                >

                            </div>

                            <div class="info-item">

                                <label for="email">
                                    E-mail
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Digite seu e-mail"
                                    autocomplete="email"
                                    value="<?= htmlspecialchars($email) ?>"
                                    required
                                >

                            </div>

                            <div class="info-item">

                                <label for="idade">
                                    Idade
                                </label>

                                <input
                                    type="number"
                                    id="idade"
                                    name="idade"
                                    placeholder="Digite sua idade"
                                    min="1"
                                    max="120"
                                    value="<?= htmlspecialchars($idade) ?>"
                                    readonly
                                >

                            </div>

                            <div class="info-item">

                                <label for="objetivo">
                                    Objetivo
                                </label>

                                <input
                                    type="text"
                                    id="objetivo"
                                    name="objetivo"
                                    placeholder="Ex: Melhorar meu desempenho"
                                >

                            </div>

                        </div>

                        <p
                            class="perfil-status"
                            id="perfilStatus"
                            aria-live="polite"
                        ></p>

                        <div class="perfil-botoes">

                            <button
                                type="submit"
                                class="perfil-button"
                            >
                                Salvar perfil
                            </button>

                            <a
                                href="logout.php"
                                class="logout-button"
                                id="logoutButton"
                            >
                                Sair
                            </a>

                        </div>

                    </form>

                </section>

                <a
                    href="../index.php"
                    class="back-home"
                >
                    ← Voltar para o início
                </a>

            </div>

        </main>

    </div>

    <script src="../js/perfil.js"></script>

</body>

</html>