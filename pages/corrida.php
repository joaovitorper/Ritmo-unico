<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Corrida | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/corrida.css"
    >
</head>

<body>

<main class="corrida-page">

    <div class="corrida-glow corrida-glow-left"></div>
    <div class="corrida-glow corrida-glow-right"></div>

    <div class="corrida-container">

        <img
            src="../assets/img/identidade Visual/Final logo.png"
            alt="Logo Ritmo Único"
            class="corrida-logo"
        >

        <span class="corrida-tag">
            Tecnologia para corredores
        </span>

        <section class="corrida-header">

            <span class="corrida-label">
                SEU TREINO
            </span>

            <h1>
                Corrida
            </h1>

            <p>
                Comece sua corrida e acompanhe seu desempenho em tempo real.
            </p>

        </section>

        <section class="corrida-box">

            <div class="corrida-status" id="statusCorrida">
                Pronto para correr
            </div>

            <div class="corrida-tempo">

                <span>
                    Tempo
                </span>

                <strong id="tempoCorrida">
                    00:00:00
                </strong>

            </div>

            <div class="corrida-info">

                <div class="corrida-card">

                    <span>
                        Distância
                    </span>

                    <strong id="distanciaCorrida">
                        0,00 km
                    </strong>

                </div>

                <div class="corrida-card">

                    <span>
                        Ritmo
                    </span>

                    <strong id="ritmoCorrida">
                        0:00 /km
                    </strong>

                </div>

                <div class="corrida-card">

                    <span>
                        Calorias
                    </span>

                    <strong id="caloriasCorrida">
                        0 kcal
                    </strong>

                </div>

            </div>

            <div class="corrida-gps">

                <span id="statusGPS">
                    📍 GPS aguardando
                </span>

            </div>

            <div class="corrida-acoes">

                <button
                    type="button"
                    id="btnIniciar"
                    class="corrida-button"
                >
                    🏃 Iniciar corrida
                </button>

                <button
                    type="button"
                    id="btnPausar"
                    class="corrida-button secondary"
                    disabled
                >
                    ⏸️ Pausar
                </button>

                <button
                    type="button"
                    id="btnFinalizar"
                    class="corrida-button finalizar"
                    disabled
                >
                    ⏹️ Finalizar
                </button>

            </div>

        </section>

        <section class="corrida-mapa">

            <a
                href="mapa.php"
                class="corrida-link"
            >
                🗺️ Ver mapa da corrida
            </a>

        </section>

        <nav class="corrida-menu">

            <a href="home.php">
                Início
            </a>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a
                href="corrida.php"
                class="active"
            >
                Corrida
            </a>

            <a href="historico.php">
                Histórico
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
            ← Voltar para início
        </a>

    </div>

</main>

<script src="../js/corrida.js"></script>

</body>
</html>