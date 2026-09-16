<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
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

    <title>Histórico | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/historico.css"
    >

</head>

<body>

<main class="historico-page">

    <div class="historico-glow historico-glow-left"></div>
    <div class="historico-glow historico-glow-right"></div>


    <div class="historico-container">


        <!-- LOGO -->

        <img
            src="../assets/img/identidade Visual/Final logo.png"
            alt="Logo Ritmo Único"
            class="historico-logo"
        >


        <span class="historico-tag">
            Tecnologia para corredores
        </span>


        <!-- CABEÇALHO -->

        <section class="historico-header">

            <span class="historico-label">
                SEU HISTÓRICO
            </span>

            <h1>
                Histórico de corridas
            </h1>

            <p>
                Acompanhe suas corridas e sua evolução.
            </p>

        </section>


        <!-- RESUMO -->

        <section class="historico-resumo">


            <div class="resumo-card">

                <span>
                    Distância
                </span>

                <strong id="distanciaTotal">
                    0,00 km
                </strong>

                <p>
                    Distância percorrida
                </p>

            </div>


            <div class="resumo-card">

                <span>
                    Corridas
                </span>

                <strong id="totalCorridas">
                    0
                </strong>

                <p>
                    Atividades realizadas
                </p>

            </div>


            <div class="resumo-card">

                <span>
                    Tempo
                </span>

                <strong id="tempoTotal">
                    0h 00min
                </strong>

                <p>
                    Tempo em atividade
                </p>

            </div>


        </section>


        <!-- HISTÓRICO -->

        <section class="historico-box">


            <div class="historico-box-header">

                <div>

                    <span class="historico-label">
                        ATIVIDADES
                    </span>

                    <h2>
                        Suas corridas
                    </h2>

                </div>


                <a
                    href="corrida.php"
                    class="nova-corrida"
                >
                    🏃 Nova corrida
                </a>

            </div>


            <!-- LISTA DAS CORRIDAS -->

            <div
                id="listaHistorico"
                class="historico-lista"
            ></div>


            <!-- HISTÓRICO VAZIO -->

            <div
                id="historicoVazio"
                class="historico-vazio"
            >

                <div class="historico-vazio-icon">
                    🏃
                </div>

                <h3>
                    Nenhuma corrida registrada
                </h3>

                <p>
                    Comece sua primeira corrida para acompanhar sua evolução.
                </p>

                <a
                    href="corrida.php"
                    class="historico-button"
                >
                    Começar primeira corrida
                </a>

            </div>


            <!-- BOTÃO LIMPAR -->

            <div class="historico-acoes">

                <button
                    type="button"
                    id="limparHistorico"
                    class="limpar-historico"
                >
                    🗑 Limpar histórico
                </button>

            </div>


        </section>


        <!-- MENU -->

        <nav class="historico-menu">

            <a href="home.php">
                Início
            </a>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="corrida.php">
                Corrida
            </a>

            <a
                href="historico.php"
                class="active"
            >
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


        <!-- VOLTAR -->

        <a
            href="home.php"
            id="voltar"
            class="back-home"
        >
            ← Voltar para início
        </a>


    </div>

</main>


<!-- JAVASCRIPT -->

<script src="../js/historico.js"></script>

</body>

</html>
