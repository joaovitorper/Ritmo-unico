<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$nomeUsuario = $_SESSION["usuario_nome"] ?? "Corredor";

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/dashboard.css"
    >

</head>

<body>

    <main class="dashboard-page">

        <div class="dashboard-glow dashboard-glow-left"></div>
        <div class="dashboard-glow dashboard-glow-right"></div>

        <div class="dashboard-container">

            <img
                src="../assets/img/identidade Visual/Final logo.png"
                alt="Logo Ritmo Único"
                class="dashboard-logo"
            >

            <span class="dashboard-tag">
                Tecnologia para corredores
            </span>

            <section class="dashboard-header">

                <div>

                    <span class="dashboard-label">
                        SEU DASHBOARD
                    </span>

                    <h1>
                        Olá, <?= htmlspecialchars($nomeUsuario) ?>! 👋
                    </h1>

                    <p>
                        Acompanhe seu desempenho e continue evoluindo no seu ritmo.
                    </p>

                </div>

            </section>

            <section class="dashboard-cards">

                <div class="dashboard-card">

                    <span class="card-icon">
                        🏃
                    </span>

                    <span class="card-label">
                        DISTÂNCIA
                    </span>

                    <strong id="totalDistancia">
                        0 km
                    </strong>

                    <p>
                        Nesta semana
                    </p>

                </div>

                <div class="dashboard-card">

                    <span class="card-icon">
                        ⏱️
                    </span>

                    <span class="card-label">
                        TEMPO
                    </span>

                    <strong id="tempoTotal">
                        0h 00min
                    </strong>

                    <p>
                        Tempo corrido
                    </p>

                </div>

                <div class="dashboard-card">

                    <span class="card-icon">
                        🔥
                    </span>

                    <span class="card-label">
                        CALORIAS
                    </span>

                    <strong id="totalCalorias">
                        0
                    </strong>

                    <p>
                        Calorias gastas
                    </p>

                </div>

                <div class="dashboard-card">

                    <span class="card-icon">
                        🎯
                    </span>

                    <span class="card-label">
                        OBJETIVO
                    </span>

                    <strong id="porcentagemObjetivo">
                        0%
                    </strong>

                    <p>
                        Meta semanal
                    </p>

                </div>

            </section>

            <section class="dashboard-box">

                <span class="dashboard-label">
                    DESEMPENHO
                </span>

                <h2>
                    Sua evolução
                </h2>

                <p>
                    Comece sua primeira corrida para acompanhar sua evolução.
                </p>

                <div class="evolucao-barra">

                    <div
                        class="evolucao-progresso"
                        id="progressoSemanal"
                        style="width: 0%;"
                    ></div>

                </div>

                <div class="evolucao-info">

                    <span>
                        Progresso semanal
                    </span>

                    <strong id="progressoTexto">
                        0%
                    </strong>

                </div>

            </section>

            <section class="dashboard-acoes">

                <a
                    href="corrida.php"
                    class="dashboard-button"
                >
                    🏃 Iniciar corrida
                </a>

                <a
                    href="historico.php"
                    class="dashboard-button secondary"
                >
                    📊 Ver histórico
                </a>

            </section>

            <nav class="dashboard-menu">

                <a href="home.php">
                    Início
                </a>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="corrida.php">
                    Corrida
                </a>

                <a href="historico.php">
                    Histórico
                </a>

                <a href="mapa.php">
                    Mapa
                </a>

                <a href="perfil.php">
                    Perfil
                </a>

                <a href="configuracoes.php">
                    Configurações
                </a>

            </nav>

            <a
                href="home.php"
                class="back-home"
            >
                ← Voltar para o início
            </a>

        </div>

    </main>

    <script src="../js/dashboard.js"></script>

</body>

</html>