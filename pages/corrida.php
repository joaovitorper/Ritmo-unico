<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

session_start();

$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Corredor';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Corrida | Ritmo Único</title>


    <!-- GOOGLE FONT -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../css/corrida.css?v=3">


</head>


<body>


    <main class="corrida-page">


        <!-- EFEITOS DE FUNDO -->

        <div class="corrida-glow corrida-glow-left"></div>

        <div class="corrida-glow corrida-glow-right"></div>


        <div class="corrida-container">


            <div class="corrida-content">


                <!-- ==========================================
                 LOGO
            ========================================== -->

                <div class="corrida-logo">

                    <img
                        src="../assets/img/identidade Visual/Final logo.png"
                        alt="Ritmo Único">

                </div>


                <!-- SUBTÍTULO -->

                <span class="corrida-subtitle">
                    Tecnologia para corredores
                </span>


                <!-- TAG -->

                <span class="corrida-tag">
                    SEU TREINO
                </span>


                <!-- TÍTULO -->

                <h1>
                    Corrida
                </h1>


                <!-- DESCRIÇÃO -->

                <p class="corrida-description">

                    Comece sua corrida e acompanhe
                    seu desempenho em tempo real.

                </p>



                <!-- ==========================================
                 CARD PRINCIPAL
            ========================================== -->

                <section class="corrida-box">


                    <!-- CABEÇALHO -->

                    <div class="corrida-box-header">

                        <span class="corrida-label">
                            TREINO
                        </span>

                        <h2>
                            Corrida em andamento
                        </h2>

                        <p class="corrida-box-description">

                            Mantenha seu ritmo e acompanhe
                            suas métricas via GPS.

                        </p>

                    </div>



                    <!-- ======================================
                     TEMPO
                ====================================== -->

                    <div class="corrida-timer-box">

                        <div class="corrida-timer-label">
                            TEMPO DE CORRIDA
                        </div>

                        <div
                            id="tempo"
                            class="corrida-timer">
                            00:00:00
                        </div>

                    </div>



                    <!-- ======================================
                     MÉTRICAS
                ====================================== -->

                    <div class="corrida-metrics">


                        <!-- DISTÂNCIA -->

                        <div class="corrida-metric">

                            <span class="corrida-metric-label">
                                Distância
                            </span>

                            <div>

                                <strong
                                    id="distancia"
                                    class="corrida-metric-value">
                                    0,00
                                </strong>

                                <span class="corrida-metric-unit">
                                    km
                                </span>

                            </div>

                        </div>



                        <!-- PACE -->

                        <div class="corrida-metric">

                            <span class="corrida-metric-label">
                                Ritmo (Pace)
                            </span>

                            <div>

                                <strong
                                    id="pace"
                                    class="corrida-metric-value">
                                    --:--
                                </strong>

                                <span class="corrida-metric-unit">
                                    /km
                                </span>

                            </div>

                        </div>



                        <!-- CALORIAS -->

                        <div class="corrida-metric">

                            <span class="corrida-metric-label">
                                Calorias
                            </span>

                            <div>

                                <strong
                                    id="calorias"
                                    class="corrida-metric-value">
                                    0
                                </strong>

                                <span class="corrida-metric-unit">
                                    kcal
                                </span>

                            </div>

                        </div>


                    </div>



                    <!-- ======================================
                     STATUS GPS
                ====================================== -->

                    <div
                        id="gpsStatus"
                        class="corrida-gps-status">
                        📍 GPS aguardando
                    </div>



                    <!-- ======================================
                     BOTÕES
                ====================================== -->

                    <div class="corrida-actions">


                        <!-- INICIAR -->

                        <button
                            id="iniciarCorrida"
                            class="corrida-button"
                            type="button">

                            🏃 Iniciar corrida

                        </button>



                        <!-- PAUSAR -->

                        <button
                            id="pausarCorrida"
                            class="corrida-button corrida-button-secondary"
                            type="button"
                            style="display: none;">

                            ⏸ Pausar

                        </button>



                        <!-- FINALIZAR -->

                        <button
                            id="finalizarCorrida"
                            class="corrida-button corrida-button-secondary"
                            type="button"
                            style="display: none;">

                            ⏹ Finalizar

                        </button>



                        <!-- LIMPAR -->

                        <button
                            id="limparCorrida"
                            class="corrida-button corrida-button-secondary"
                            type="button">

                            🗑️ Limpar

                        </button>


                    </div>


                </section>



                <!-- ==========================================
                 META
            ========================================== -->

                <section class="corrida-goal">


                    <div class="corrida-goal-header">


                        <span class="corrida-goal-title">
                            Objetivo da corrida
                        </span>


                        <span
                            id="goalValue"
                            class="corrida-goal-value">
                            0,00 / 5,00 km
                        </span>


                    </div>



                    <div class="corrida-progress">


                        <div
                            id="progressBar"
                            class="corrida-progress-bar"></div>


                    </div>


                </section>



                <!-- ==========================================
                 NAVEGAÇÃO
            ========================================== -->

                <nav class="corrida-navigation">


                    <a
                        href="home.php"
                        class="corrida-nav-item">
                        Início
                    </a>


                    <a
                        href="dashboard.php"
                        class="corrida-nav-item">
                        Dashboard
                    </a>


                    <a
                        href="corrida.php"
                        class="corrida-nav-item active">
                        Corrida
                    </a>


                    <a
                        href="historico.php"
                        class="corrida-nav-item">
                        Histórico
                    </a>


                    <a
                        href="mapa.php"
                        class="corrida-nav-item">
                        Mapa
                    </a>


                    <a
                        href="perfil.php"
                        class="corrida-nav-item">
                        Perfil
                    </a>


                    <a
                        href="configuracoes.php"
                        class="corrida-nav-item">
                        Configurações
                    </a>


                </nav>



                <!-- ==========================================
                 VOLTAR
            ========================================== -->

                <a
                    href="home.php"
                    class="corrida-back">

                    ← Voltar para início

                </a>


            </div>

        </div>

    </main>



    <!-- ==========================================
     JAVASCRIPT
========================================== -->

    <script src="../js/corrida.js?v=3"></script>


</body>

</html>