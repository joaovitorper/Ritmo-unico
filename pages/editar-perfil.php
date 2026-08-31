<?php

session_start();

require_once "../config/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION["usuario_id"];

$sql = "SELECT id, nome, email, data_nascimento, objetivo
        FROM usuarios
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Usuário não encontrado.");
}

$mensagem = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $dataNascimento = $_POST["data_nascimento"] ?? "";
    $objetivo = trim($_POST["objetivo"] ?? "");

    if (strlen($nome) < 3) {

        $erro = "Digite um nome válido.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Digite um e-mail válido.";

    } elseif (empty($dataNascimento)) {

        $erro = "Informe sua data de nascimento.";

    } else {

        $sql = "UPDATE usuarios
                SET nome = ?,
                    email = ?,
                    data_nascimento = ?,
                    objetivo = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ssssi",
            $nome,
            $email,
            $dataNascimento,
            $objetivo,
            $id
        );

        if ($stmt->execute()) {

            $_SESSION["nome"] = $nome;

            $mensagem = "Perfil atualizado com sucesso!";

            $usuario["nome"] = $nome;
            $usuario["email"] = $email;
            $usuario["data_nascimento"] = $dataNascimento;
            $usuario["objetivo"] = $objetivo;

        } else {

            $erro = "Erro ao atualizar o perfil.";

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

    <title>Editar Perfil | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/editar_perfil.css"
    >

</head>

<body>

<main class="editar-perfil-page">

    <div class="editar-perfil-container">

        <h1>Ritmo Único</h1>

        <span class="editar-perfil-tag">
            Tecnologia para corredores
        </span>

        <div class="editar-perfil-box">

            <h2>Editar perfil</h2>

            <p class="descricao">
                Atualize seus dados pessoais e suas preferências.
            </p>

            <?php if (!empty($mensagem)): ?>

                <div class="mensagem sucesso">
                    <?= htmlspecialchars($mensagem) ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($erro)): ?>

                <div class="mensagem erro">
                    <?= htmlspecialchars($erro) ?>
                </div>

            <?php endif; ?>

            <form method="POST" action="">

                <div class="input-group">

                    <label for="nome">
                        Nome completo
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($usuario["nome"]) ?>"
                        required
                        minlength="3"
                        maxlength="100"
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
                        value="<?= htmlspecialchars($usuario["email"]) ?>"
                        required
                        maxlength="150"
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
                        value="<?= htmlspecialchars($usuario["data_nascimento"]) ?>"
                        required
                    >

                </div>

                <div class="input-group">

                    <label for="objetivo">
                        Objetivo
                    </label>

                    <select
                        id="objetivo"
                        name="objetivo"
                    >

                        <option value="">
                            Selecione seu objetivo
                        </option>

                        <option
                            value="saude"
                            <?= $usuario["objetivo"] === "saude" ? "selected" : "" ?>
                        >
                            Melhorar minha saúde
                        </option>

                        <option
                            value="performance"
                            <?= $usuario["objetivo"] === "performance" ? "selected" : "" ?>
                        >
                            Melhorar meu desempenho
                        </option>

                        <option
                            value="distancia"
                            <?= $usuario["objetivo"] === "distancia" ? "selected" : "" ?>
                        >
                            Correr maiores distâncias
                        </option>

                        <option
                            value="competicao"
                            <?= $usuario["objetivo"] === "competicao" ? "selected" : "" ?>
                        >
                            Me preparar para competições
                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="editar-button"
                >
                    Salvar alterações
                </button>

            </form>

            <a
                href="perfil.php"
                class="voltar"
            >
                ← Voltar para o perfil
            </a>

        </div>

    </div>

</main>

</body>

</html>